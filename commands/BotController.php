<?php

namespace app\commands;

use app\models\Topics;
use yii\console\Controller;
use yii\console\ExitCode;
use skrtdev\NovaGram\Bot;
use skrtdev\Telegram\Message;
use app\models\BotActions;

class BotController extends Controller
{

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
     * @param $chat_id
     * @return array
     */
    private function getMyTopics($chat_id): array
    {
        return Topics::find()->where(['user_id' => 1])->all();
    }

    /**
     * @throws \skrtdev\NovaGram\Exception
     */
    public function actionIndex()
    {
        $bot = new Bot($_ENV['TG_BOT_TOKEN']);

        $bot->onCommand('start', function (Message $message) {
            $message->reply('Привет! Для получения списка команд введите /help');
        });

        $bot->onCommand('help', function (Message $message) use ($bot) {
            $message->reply('Список команд: \start, \help, \create, \topics');
        });

        $bot->onCommand('create', function (Message $message) use ($bot) {
            $this->setBotAction($message->chat->id, 'create-topic:name');
            $bot->sendMessage($message->chat->id, 'Введите название топика');
        });

        $bot->onCommand('topics', function (Message $message) use ($bot) {
            $topics = $this->getMyTopics($message->chat->id);
            $formatted = array_map(function ($item) {
                return $item->name;
            }, $topics);

            $bot->sendMessage($message->chat->id, 'Ваши топики: ' . implode(', ', $formatted));
        });

        $bot->onCommand('replies', function (Message $message) use ($bot) {

        });

        $bot->onMessage(function (Message $message) use ($bot) {
            var_dump($message);
            if ($message->text[0] != '/') {
                $currentAction = $this->getBotAction($message->chat->id);
                if ($currentAction) {
                    if ($currentAction == 'create-topic:name') {
                        $this->setBotAction($message->chat->id, 'create-topic:message', json_encode(['name' => $message->text]));
                        $bot->sendMessage($message->chat->id, 'Введите текст сообщения');
                    } else if ($currentAction == 'create-topic:message') {
                        $this->setBotAction($message->chat->id, 'create-topic:keywords', json_encode(['message' => $message->text]));
                        $bot->sendMessage($message->chat->id, 'Введите ключевые слова (через запятую)');
                    } else if ($currentAction == 'create-topic:keywords') {
                        $this->setBotAction($message->chat->id, 'create-topic:finish', json_encode(['keywords' => $message->text]));

                        // Create topic
                        $topic = new Topics();
                        $topic->load(array_merge($this->getBotData($message->chat->id), ['user_id' => 1]), '');
                        $topic->save();

                        $this->setBotAction($message->chat->id, null);
                        $bot->sendMessage($message->chat->id, 'Топик успешно создан!');
                    }
                }
            }
        });

        $bot->start();
    }
}