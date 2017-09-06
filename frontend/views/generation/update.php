<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Generation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Generation',
]) . ' ' . $model->GenerationId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Generations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->GenerationId, 'url' => ['view', 'id' => $model->GenerationId]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="generation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
