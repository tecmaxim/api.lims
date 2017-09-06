<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProtocolResult */

$this->title = 'Update: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Protocol Results', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->ProtocolResultId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="protocol-result-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
