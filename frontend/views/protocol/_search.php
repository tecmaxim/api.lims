<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProtocolSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="protocol-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ProtocolId') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'ProjectId') ?>

    <?= $form->field($model, 'ProtocolResultId') ?>

    <?= $form->field($model, 'Comments') ?>

    <?php // echo $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
