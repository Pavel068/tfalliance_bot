<?php

use app\models\TopicReplies;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TopicRepliesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Topic Replies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topic-replies-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Topic Replies', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'user_id',
            'topic_id',
            'bot_message_id',
            'message:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TopicReplies $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
