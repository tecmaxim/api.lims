<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Plate */
$url = isset($update) ? 'update-method': 'select-method'
?>
<div class="methods-view">
    <div class="row">    
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
            <h1>Select Method</h1>
        </div>
    </div>
    <div class="row">
        <?php if(isset($error)): ?>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 alert alert-danger">
             There aren't enought plates to join on Genspin method.           
        </div>
        <?php endif; ?>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2">
            
            <?=  Html::button(Yii::t('app', 'NucleoSpin'),[
                'class' => 'btn btn-success in-nuevos-reclamos width100 padding-10 margin10-auto-10-auto',
                'data-reload' => '#modal',
                'data-url' => Url::to([$url, 'id' => $model->PlateId, 'adnMethod' => 'NucleoSpin']),
                //'data-url' => Url::to(['adn-extraction', 'id' => $model->PlateId, 'adnMethod' => 'NucleoSpin'])
            ]); ?>
            
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2">
            
            <?=  Html::button(Yii::t('app', 'CTAB'),[
                'class' => 'btn btn-warning in-nuevos-reclamos width100 padding-10 margin10-auto-10-auto',
                'data-reload' => '#modal',
                'data-url' => Url::to([$url, 'id' => $model->PlateId, 'adnMethod' => 'CTAB']),
                //'data-url' => Url::to(['adn-extraction', 'id' => $model->PlateId, 'adnMethod' => 'CTAB']),
            ]) ?>
        </div>
    </div>
    <div class="row">    
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2">
            <?=  Html::button(Yii::t('app', 'GenSpin'),[
                'class' => 'btn btn-info in-nuevos-reclamos width100 padding-10 margin10-auto-10-auto',
                'data-reload' => '#modal',
                'data-url' => Url::to([$url, 'id' => $model->PlateId, 'adnMethod' => 'GenSpin']),
                //'data-url' => Url::to(['create-genspin-method', 'id' => $model->PlateId]),

            ]) ?>
        </div>
    </div>
    </div>
</div>

