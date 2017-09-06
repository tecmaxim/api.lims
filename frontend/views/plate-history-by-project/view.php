<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PlateHistoryByProject */

$this->title = $model->PlateHistoryByProjectId;
$this->params['breadcrumbs'][] = ['label' => 'Plate History By Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plate-history-by-project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->PlateHistoryByProjectId], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->PlateHistoryByProjectId], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'PlateHistoryByProjectId',
            'PlateId',
            'ProjectId',
            'Date',
            'IsActive',
        ],
    ]) ?>

</div>
