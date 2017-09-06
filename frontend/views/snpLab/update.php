<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SnpLab */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Snp Lab',
]) . ' ' . $model->Snp_lab_Id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Snp Labs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Snp_lab_Id, 'url' => ['view', 'id' => $model->Snp_lab_Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="snp-lab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'markers' => $markers,
    ]) ?>

</div>
