<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MarkerType;

/* @var $this yii\web\View */
/* @var $model common\models\Snp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="marker-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm',  'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>


    <?= $form->field($model, 'Name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'ShortSequence')->textInput(['maxlength' => 500]) ?>

    <?= $form->field($model, 'LongSequence')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'Marker_Type_Id')->dropDownList(ArrayHelper::map(MarkerType::find()->all(), 'Marker_Type_Id', 'Name')) ?>

    <?php //= $form->field($model, 'PublicLinkageGroup')->textInput() ?>

    <?php //= $form->field($model, 'PublicCm')->textInput(['maxlength' => 17]) ?>

    <?php //= $form->field($model, 'AdvLinkageGroup')->textInput() ?>

    <?php //= $form->field($model, 'AdvCm')->textInput(['maxlength' => 14]) ?>

    <?php //= $form->field($model, 'PhysicalPosition')->textInput(['maxlength' => 255]) ?>

    <?php // = $form->field($model, 'IsActive')->textInput() ?>

    <div class="form-group">
        <?= Html::Button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Cancel'); ?></button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
