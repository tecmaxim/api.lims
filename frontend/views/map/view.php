<?php

use yii\helpers\Html;
//use yii\grid\GridView;
//use yii\widgets\ListView;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Map */

$this->title = $model->Name;

?>

<div class="map-view">

    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <a href="#" onclick="return downloadMap( <?php echo $model->Map_Id; ?> );" class ="user export template" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Export </a>
            <p class="pull-right">
                <?= Html::a(Yii::t('app', 'Index'), ['index', 'id' => $model->Map_Id], ['class' => 'btn btn-gray']) ?>
                
                
                <?= Html::button(Yii::t('app', 'Update')
                                        , ['class' => 'btn btn-primary'
                                        , 'data-reload' => '#modal'
                                        , 'data-url' => Url::to(['update', 'id' => $model->Map_Id])]
                        ) ?>
                <?= Html::button(Yii::t('app', 'Delete'),
                            [ 'class' => 'btn btn-danger',
                              'data-reload' => '#modal',
                              'data-url' => Url::to(['delete', 'id' => $model->Map_Id]),
                               ]); ?>
               
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
   
  <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'Map_Id',
            ["value"=>$model->crop->Name, "label"=> "Crop"],
            'Name',
            //'type',
            ["value"=>$model->mapResults[0]->MappedPopulation, "label"=> "MappedPopulation"],
            ["value"=>$model->mapResults[0]->MappingTeam, "label"=> "MappedPopulation"],
            'Date',
            [   
                "value"=>$model->IsCurrent == 1 ? '<span class="glyphicon glyphicon-ok size12"  aria-hidden="true"> </span>':'<span class="glyphicon glyphicon-remove size12"  aria-hidden="true"> </span>' ,
                "attribute"=> "Is Current",
                "format"=>"raw",
            ],
            //'IsActive',
        ],
    ]) ?>

</div>
