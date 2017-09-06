<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        
    </div>
    <?php  Pjax::begin(['id' => 'itemList']);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
            'columns' => [
                ['class' => 'frontend\widgets\RowLinkColumn'],
            'ReportId',
            'ProjectId',
            'ReportTypeId',
            'Url:url',
            'IsActive:boolean',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php  Pjax::end();?> 

</div>
