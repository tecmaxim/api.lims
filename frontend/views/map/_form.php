<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Map */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="map-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm',  'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?php //= $form->field($model, 'Crop_Id')->textInput(['maxlength' => 10]) ?>
    <?= $form->field($model, 'Crop_Id')->dropDownList(ArrayHelper::map($crop, 'Crop_Id', 'Name'), ['prompt'=>'-- Select Crop --']) ?>
    <?= $form->field($model, 'Name')->textInput(['maxlength' => 100]) ?>

    <?php //= $form->field($model, 'Date')->textInput() ?>
    <?php //= $form->field($model, 'Type')->textInput() ?>
    <?= $form->field($model, 'Type')->dropDownList(["PARTIAL" => "PARTIAL","CONSENSUS" => "CONSENSUS"]) ?>
     <label>Date Created</label> 
    <?= DatePicker::widget([
     'model' => $model,
     'attribute' => 'Date',
     //'changeMonth' => true,
     //'changeYear' => true,
     'language' => 'es',
     'options' => ['class' => 'form-control'],
     'dateFormat' => 'yyyy-MM-dd',
     'clientOptions' => ['showAnim' => 'slideDown']
     ]);
     ?>                 

    <?php //= $form->field($model, 'IsActive')->textInput(['maxlength' => 1]) ?>
    <?= $form->field($model, 'IsCurrent')->dropDownList([1 => "Yes", 0 => "No"]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
