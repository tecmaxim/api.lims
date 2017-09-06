<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fingerprint */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fingerprint-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm','enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'Name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'DateCreated')->textInput() ?>

    <?php //= $form->field($model, 'Project_Id')->textInput(['maxlength' => 10]) ?>

    <?php //= $form->field($model, 'IsActive')->textInput() ?>

    <div class="form-group">
        <?= Html::Button('Create', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                    <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Cancel'); ?></button>
    </div>

    <?php ActiveForm::end(); ?>
    

</div>
