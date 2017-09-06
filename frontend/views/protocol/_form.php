<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Protocol */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('action_form_protocol();', $this::POS_READY);
?>

<div class="protocol-form">
    <h1> Add Protocol </h1>
    
    <div class="row" id="msj" style="display:none"></div>
    
    <?php $form = ActiveForm::begin([ 'options' => ['enctype' => 'multipart/form-data'],
                                    //'enableAjaxValidation' => true,
                                    //'enableClientScript' => true,
                                    'id' => 'FormField',
                                    'action' => null,
                                    ]); ?>
    
    <?php // $form->field($model, 'ProjectId')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'Code')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'ProtocolResultId')->textInput() ?>
    
    <?php // $form->field($model, 'ProtocolFile')->fileInput(['class' => 'btn btn-primary', ])->label("Select File") ?>

    <?= $form->field($model, 'Comments')->textarea(['rows' => 6]) ?>

    <?php // $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => 'btn btn-primary in-nuevos-reclamos', 'id'=>'upload-button-report']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $("#upload-button-report").click( function (e)
    {
        e.preventDefault();
        
        var formData = new FormData($('form')[0]);
        //console.log(); return false;
        <?php if($model->ProtocolId != ""):?>
            var url = "../protocol/update?id=<?= $model->ProtocolId?>";
        <?php else:?>
            var url = "../protocol/add-protocol?ProjectId=<?= $model->ProjectId ?>";
        <?php endif; ?>
        
        /*  External resource */
        sendProtocol(url, formData);
    });
    
</script>