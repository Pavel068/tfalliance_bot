<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TopicReplies */

$this->title = 'Create Topic Replies';
$this->params['breadcrumbs'][] = ['label' => 'Topic Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-replies-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
