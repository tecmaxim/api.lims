<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CancelCauses */

$this->title = 'Update Cancellation Causes: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Cancel Causes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->CancelCausesId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cancel-causes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
