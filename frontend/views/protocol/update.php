<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Protocol */

$this->title = 'Update Protocol: ' . ' ' . $model->ProtocolId;
$this->params['breadcrumbs'][] = ['label' => 'Protocols', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ProtocolId, 'url' => ['view', 'id' => $model->ProtocolId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="protocol-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
