<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PlateHistoryByProject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plate-history-by-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'PlateId')->textInput() ?>

    <?= $form->field($model, 'ProjectId')->textInput() ?>

    <?= $form->field($model, 'Date')->textInput() ?>

    <?= $form->field($model, 'IsActive')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
