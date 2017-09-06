<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>
<div class="cause-by-discarted-plates-create">

    <h1>Select Plate Fail Reason</h1>

    <div class="cause-by-discarted-plates-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'PlateId')->hiddenInput()->label(false) ?>
        
    <?= $form->field($model, 'CauseByDiscartedPlatesId')->dropDownList(ArrayHelper::map($causes,  'CauseByDiscartedPlatesId', 'Name'), ['prompt'=>'-- Select --']) ?>

    <?= $form->field($model, 'Comments')->textarea(['rows' => 3]) ?>
        
    <?php // $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>

