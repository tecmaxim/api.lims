<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ResearchStationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="research-station-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ResearchStationId') ?>

    <?= $form->field($model, 'CountryId') ?>

    <?= $form->field($model, 'CityId') ?>

    <?= $form->field($model, 'Short') ?>

    <?= $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
