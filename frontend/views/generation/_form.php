<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Generation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="generation-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'Description')->textInput(['maxlength' => 50]) ?>
    
    <?= $form->field($model, 'IsF1')->checkbox() ?>

    <?php //= $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?= Yii::t('app', 'Cancel'); ?> </button>
    </div>
    <?php ActiveForm::end(); ?>

</div>
