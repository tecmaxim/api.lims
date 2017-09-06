<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CancelCausesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cancel Causes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cancel-causes-index">

    <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?= Html::button(Yii::t('app', 'Create {modelClass}', [
                   'modelClass' => Yii::t('app', 'Cancellation Cause'),
               ])
               , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
               , 'data-toggle' => 'modal'
               , 'data-target' => '#modal'
               , 'data-url' => Url::to('create')])
           ?>
                
         </div>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
                <?php  Pjax::begin(['id' => 'itemList']);?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],
                        ['class' => 'frontend\widgets\RowLinkColumn'],
                        //'CancelCausesId',
                        'Description',
                        'Name',
                        //'IsActive:boolean',

                        //['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
                <?php  Pjax::end();?>
            </div>
        </div>
    </div>

</div>
