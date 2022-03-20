<?php

namespace app\helpers;

use app\models\BotActions;
use app\models\TopicReplies;
use app\models\Topics;
use app\models\Users;
use skrtdev\Telegram\Message;

class BotService
{
    private $bot;

    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    private function setBotAction($chat_id, $action, $data = null)
    {
        $ba = BotActions::find()->where(['chat_id' => $chat_id])->one();
        if (!$ba) {
            $ba = new BotActions();
            $ba->load([
                'chat_id' => $chat_id,
                'action' => $action,
                'data' => $data
            ], '');
        } else {
            $ba->action = $action;

            if ($data) {
                $new_data = json_decode($data, true);
                $old_data = $ba->data ? json_decode($ba->data, true) : [];
                $ba->data = json_encode(array_merge($new_data, $old_data));
            } else {
                $ba->data = null;
            }
        }

        $ba->save();
    }

    private function getBotData($chat_id)
    {
        $bd = BotActions::find()->where(['chat_id' => $chat_id])->one();
        return json_decode($bd->data, true);
    }

    private function getBotAction($chat_id)
    {
        $ba = BotActions::find()->where(['chat_id' => $chat_id])->one();
        if ($ba) {
            return $ba->action;
        }

        return null;
    }

    /**
     * @param $user_id
     * @return array
     */
    private function getMyTopics($user_id): array
    {
        return Topics::find()->where(['user_id' => $user_id])->all();
    }

    public function processCommands()
    {
        $bot = $this->bot;

        $bot->onCommand('start', function (Message $message) {
            $user = Users::find()->where(['bot_chat_id' => $message->from->id])->one();
            if ($user) {
                $message->reply('Привет, ' . $user->name . '! Для получения списка команд введите /help');
            } else {
                $message->reply('Похоже, что вы тут впервые =). Давайте зарегистрируемся. Введите свою роль: 1 - менеджер, 2 - пользователь');
                $this->setBotAction($message->chat->id, 'signup:role');
            }

        });

        $bot->onCommand('help', function (Message $message) use ($bot) {
            $message->reply('Список команд: \start - Начало работы, \help - Помощник, \create - Создать топик, \topics - Просмотр своих топиков, \posts - Просмотр топиков (у пользователя), \replies - Просмотр ответов на топики (для менеджера)');
        });

        $bot->onCommand('create', function (Message $message) use ($bot) {
            $user = Users::find()->where(['bot_chat_id' => $message->from->id])->one();

            if ($user && $user->is_manager) {
                $this->setBotAction($message->chat->id, 'create-topic:name');
                $bot->sendMessage($message->chat->id, 'Введите название топика');
            } else {
                $bot->sendMessage($message->chat->id, 'Вы не являетесь менеджером. Для использования функций менеджера зарегистрируйтесь как менеджер через команду /start.');
            }
        });

        $bot->onCommand('topics', function (Message $message) use ($bot) {
            $user = Users::find()->where(['bot_chat_id' => $message->from->id])->one();

            if ($user) {
                $topics = $this->getMyTopics($user->id);
                $formatted = array_map(function ($item) {
                    return $item->name;
                }, $topics);

                $bot->sendMessage($message->chat->id, 'Ваши топики: ' . implode(', ', $formatted));
            }
        });

        $bot->onCommand('posts', function (Message $message) use ($bot) {
            $posts = Topics::find()->orderBy('created_at DESC')->all();
            foreach ($posts as $post) {
                $msg = "
                ==|{$post->bot_message_id}|==
                
                <b>{$post->name}</b>
                
                {$post->message}
                
                Ключевые слова: <i>{$post->keywords}</i>
                ";
                $bot->sendMessage($message->chat->id, $msg);
            }
        });

        $bot->onCommand('replies', function (Message $message) use ($bot) {
            $replies = TopicReplies::find()->with(['user'])->asArray()->all();
            foreach ($replies as $reply) {
                $msg = "{$reply['message']}
                
@{$reply['user']['tg_username']}
                ";

                $bot->sendMessage($message->chat->id, $msg);
            }
        });
    }

    public function processMessages()
    {
        $bot = $this->bot;

        $bot->onMessage(function (Message $message) use ($bot) {
            if ($message->text[0] != '/') {
                // Reply
                if ($message->reply_to_message && $message->reply_to_message->text) {
                    preg_match_all('/==|[0-9]+|==/', $message->reply_to_message->text, $matches);
                    if ($matches && $matches[0][1]) {
                        $bot_message_id = $matches[0][1];
                        $topic = Topics::find()->where(['bot_message_id' => $bot_message_id])->one();
                        $user = Users::find()->where(['bot_chat_id' => $message->from->id])->one();
                        if ($topic && $user) {
                            $reply = new TopicReplies();
                            $reply->load([
                                'user_id' => $user->id,
                                'topic_id' => $topic->id,
                                'bot_message_id' => $message->message_id,
                                'message' => $message->text
                            ], '');
                            $reply->save();

                            $bot->sendMessage($message->chat->id, 'Ваш отклик принят!');
                        }
                    }
                } else { // Dialog
                    $currentAction = $this->getBotAction($message->chat->id);
                    switch ($currentAction) {
                        case 'create-topic:name':
                            $this->setBotAction($message->chat->id, 'create-topic:message', json_encode(['name' => $message->text]));
                            $bot->sendMessage($message->chat->id, 'Введите текст сообщения');
                            break;
                        case 'create-topic:message':
                            $this->setBotAction($message->chat->id, 'create-topic:keywords', json_encode(['message' => $message->text]));
                            $bot->sendMessage($message->chat->id, 'Введите ключевые слова (через запятую)');
                            break;
                        case 'create-topic:keywords':
                            $this->setBotAction($message->chat->id, 'create-topic:finish', json_encode(['keywords' => $message->text]));

                            // Create topic
                            $user = Users::find()->where(['bot_chat_id' => $message->from->id])->one();

                            if ($user) {
                                $topic = new Topics();
                                $topic->load(array_merge($this->getBotData($message->chat->id), [
                                    'user_id' => $user->id,
                                    'bot_message_id' => $message->message_id
                                ]), '');
                                $topic->save();
                            }

                            $this->setBotAction($message->chat->id, null);
                            $bot->sendMessage($message->chat->id, 'Топик успешно создан!');
                            break;
                        case 'signup:role':
                            if ($message->text == 1) {
                                $this->setBotAction($message->chat->id, 'signup:credentials');
                                $bot->sendMessage($message->chat->id, 'Введите ваше email и пароль через пробел. Пример: user@test.com 123456');
                            } else if ($message->text == 2) {
                                $user = new Users();
                                $user->load([
                                    'name' => $message->from->first_name . ' ' . $message->from->last_name,
                                    'bot_chat_id' => $message->from->id,
                                    'tg_username' => $message->from->username,
                                    'is_admin' => 0,
                                    'is_manager' => 0
                                ]);
                                $user->save();
                                $this->setBotAction($message->chat->id, null);

                                $bot->sendMessage($message->chat->id, 'Вы успешно зарегистрированы!');
                            } else {
                                $bot->sendMessage($message->chat->id, 'Кто ты, воин?');
                            }
                            break;
                        case 'signup:credentials':
                            $credentials = explode(' ', $message->text);
                            $user = new Users();
                            $user->load([
                                'name' => $message->from->first_name . ' ' . $message->from->last_name,
                                'email' => $credentials[0],
                                'password' => $credentials[1],
                                'bot_chat_id' => $message->from->id,
                                'tg_username' => $message->from->username,
                                'is_admin' => 0,
                                'is_manager' => 1
                            ], '');
                            $user->save();
                            $this->setBotAction($message->chat->id, null);

                            $bot->sendMessage($message->chat->id, 'Вы успешно зарегистрированы! Вам доступен личный кабинет в веб-интерфейсе: http://127.0.0.1:8080');
                            break;
                        default:
                            break;
                    }
                }
            }
        });
    }
}