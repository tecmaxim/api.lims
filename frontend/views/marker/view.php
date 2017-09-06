<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Marker */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Markers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
 
<div class="marker-view view">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <p class="pull-right">
                <?= Html::a(Yii::t('app', 'Index'), ['index', 'id' => $model->Marker_Id], ['class' => 'btn btn-gray']) ?>

                <?= Html::button(Yii::t('app', 'Update')
                                        , ['class' => 'btn btn-primary'
                                        , 'data-reload' => '#modal'
                                        , 'data-url' => Url::to(['update', 'id' => $model->Marker_Id])]
                        ) ?>
                <?= Html::button(Yii::t('app', 'Delete'),
                            [ 'class' => 'btn btn-danger',
                              'data-reload' => '#modal',
                              'data-url' => Url::to(['delete', 'id' => $model->Marker_Id]),
                               ]); ?>

            </p>
        </div>
    </div>
   
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'Marker_Id',
            'Name',
            ["label" => "Crop", "value" => $model->crop->Name],
            ["label" => "Marker Type", "value" => $model->markerType->Name],
            ["label" => "Position", "value" => $model->mapResults == [] ? "None" : $model->mapResults[0]->Position],
            ["label" => "LG", "value" => $model->mapResults == [] ? "None" : $model->mapResults[0]->LinkageGroup],
                      
            //["label" => "Adv LG", "value" => $model->AdvLinkageGroup],                                            
            //["label" => "Adv cM", "value" => $model->AdvCm],
            //["label" => "Public LG", "value" => $model->PublicLinkageGroup],
            //["label" => "Public cM", "value" => $model->PublicCm],
            //'PhysicalPosition',
            //'IsActive',
        ],
    ]) ?>
   
</div>
