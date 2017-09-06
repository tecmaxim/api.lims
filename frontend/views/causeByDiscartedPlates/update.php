<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CauseByDiscartedPlates */

$this->title = 'Update Cause By Discarted Plates: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Cause By Discarted Plates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->CauseByDiscartedPlatesId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cause-by-discarted-plates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
