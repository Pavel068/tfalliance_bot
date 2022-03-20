<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Topics */

$this->title = 'Create Topics';
$this->params['breadcrumbs'][] = ['label' => 'Topics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topics-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
