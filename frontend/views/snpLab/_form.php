<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Snp;

/* @var $this yii\web\View */
/* @var $model common\models\SnpLab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="snp-lab-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm','enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'Marker_Id')->dropDownList(ArrayHelper::map($markers, 'Marker_Id', 'Name'), ['prompt'=>'-- Select Snp --']) ?>

    <?= $form->field($model, 'LabName')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'PurchaseSequence')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'AlleleFam')->dropDownList(['A' => 'A', 'T' => 'T'], ['prompt'=>'-- Select Allele Fam --']) ?>

    <?= $form->field($model, 'AlleleVicHex')->dropDownList(['C' => 'C', 'G' => 'G'], ['prompt'=>'-- Select Allele Vic Hex --']) ?>

    <?= $form->field($model, 'ValidatedStatus')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'Quality')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'Box')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'PositionInBox')->textInput(['maxlength' => 5]) ?>

    <?= $form->field($model, 'PIC')->textInput(['maxlength' => 14]) ?>

    <?= $form->field($model, 'Observation')->textArea(['maxlength' => 150]) ?>

    
    
    <div class="form-group">
        <?= Html::Button('Create', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
         <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Cancel'); ?></button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
