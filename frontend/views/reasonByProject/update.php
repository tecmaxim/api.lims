<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ReasonByProject */

$this->title = 'Update Reason By Project: ' . ' ' . $model->ProjectId;
$this->params['breadcrumbs'][] = ['label' => 'Reason By Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ProjectId, 'url' => ['view', 'id' => $model->ProjectId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reason-by-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
