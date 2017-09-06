<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Marker */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Marker',
]) . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Markers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Marker_Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="marker-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
