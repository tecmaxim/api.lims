<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Generation */

$this->title = $model->Description;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Generation'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generation-view">
    <div class="row">    
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->GenerationId])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->GenerationId]),
             
                ]) ?>
            </p>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'GenerationId',
            'Description',
            [
             'attribute' => 'IsF1',
             'format'=>'raw', 
             'value' =>  $model->IsF1 == 0 ? "NO" : "YES",
                        
            ],
            //'IsActive:boolean',
        ],
    ]) ?>

</div>
