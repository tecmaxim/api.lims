<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Plate History By Jobs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plate-history-by-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Plate History By Job', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'PlateHistoryByProjectId',
            'PlateId',
            'ProjectId',
            'Date',
            'IsActive',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
