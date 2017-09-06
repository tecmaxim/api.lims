<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProtocolSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Protocols';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="protocol-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Protocol', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ProtocolId',
            'Code',
            'ProjectId',
            'ProtocolResultId',
            'Comments:ntext',
            // 'IsActive:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
