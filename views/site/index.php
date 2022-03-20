<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Привет, привет!</h1>

        <p class="lead">Добро пожаловать к нам.</p>

        <p><a class="btn btn-lg btn-success" href="https://t.me/<?= $_ENV['TG_BOT_NAME'] ?>" target="_blank">Наш бот в телеграм</a></p>
    </div>
</div>
