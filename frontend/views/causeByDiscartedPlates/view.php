<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CauseByDiscartedPlates */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Cause By Discarted Plates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-by-discarted-plates-view">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->CauseByDiscartedPlatesId])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->CauseByDiscartedPlatesId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
                ]) ?>
            </p>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'CauseByDiscartedPlatesId',
            'Name',
            'Description',
            //'IsActive:boolean',
        ],
    ]) ?>

</div>
