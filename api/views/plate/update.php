<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PlateHistoryByProject */

$this->title = 'Update Plate History By Project: ' . ' ' . $model->PlateHistoryByProjectId;
$this->params['breadcrumbs'][] = ['label' => 'Plate History By Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PlateHistoryByProjectId, 'url' => ['view', 'id' => $model->PlateHistoryByProjectId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="plate-history-by-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
