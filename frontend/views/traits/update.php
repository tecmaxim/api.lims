<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Traits */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Traits',
]) . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Traits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->TraitsId]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="traits-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
