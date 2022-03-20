<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TopicReplies */

$this->title = 'Update Topic Replies: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Topic Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="topic-replies-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
