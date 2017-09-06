<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fingerprint */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Fingerprint',
]) . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fingerprints'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Fingerprint_Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="fingerprint-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
