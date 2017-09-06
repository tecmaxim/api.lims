<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Protocol Results';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="protocol-result-index">

   <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?= Html::button(Yii::t('app', 'Create {modelClass}', [
                   'modelClass' => Yii::t('app', 'Result Protocol'),
               ])
               , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
               , 'data-toggle' => 'modal'
               , 'data-target' => '#modal'
               , 'data-url' => Url::to('create')])
           ?>
                
         </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
            <?php  Pjax::begin(['id' => 'itemList']);?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
                'columns' => [
                    ['class' => 'frontend\widgets\RowLinkColumn'],

                    //'ProtocolResultId',
                    'Name',
                    'Description:ntext',
                    //'IsActive:boolean',

                    //['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
            <?php  Pjax::end();?> 
            </div>
        </div>
    </div>
</div>
