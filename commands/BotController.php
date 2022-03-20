<?php

namespace app\commands;

use app\helpers\BotService;
use app\models\TopicReplies;
use app\models\Topics;
use app\models\Users;
use yii\console\Controller;
use yii\console\ExitCode;
use skrtdev\NovaGram\Bot;
use skrtdev\Telegram\Message;
use app\models\BotActions;

class BotController extends Controller
{
    /**
     * @throws \skrtdev\NovaGram\Exception
     */
    public function actionIndex()
    {
        $bot = new Bot($_ENV['TG_BOT_TOKEN']);
        $botService = new BotService($bot);

        $botService->processCommands();
        $botService->processMessages();

        $bot->start();
    }
}