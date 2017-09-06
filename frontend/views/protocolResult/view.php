<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ProtocolResult */

$this->title = $model->Name;

?>
<div class="protocol-result-view">
    <div class="row">
        
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->ProtocolResultId])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->ProtocolResultId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
                ]) ?>
            </p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'ProtocolResultId',
            'Name',
            'Description:ntext',
            //'IsActive:boolean',
        ],
    ]) ?>

</div>
