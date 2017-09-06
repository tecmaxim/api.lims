<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AlleleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Alleles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="allele-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= Html::a('Create Allele', ['create'], ['class' => 'btn btn-primary btn-nuevo-reclamo']) ?>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-condensed table-reclamos table-con-link'],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'frontend\widgets\RowLinkColumn'],

            'Allele_Id',
            'LongDescription',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

            </div>
        </div>
    </div>
</div>
