<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ResearchStation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Research Station',
]) . ' ' . $model->ResearchStationId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Research Stations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ResearchStationId, 'url' => ['view', 'id' => $model->ResearchStationId]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="research-station-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
