<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Report */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('init_report_view("'.Yii::$app->request->baseUrl.'");', $this::POS_READY);
?>

<div class="report-form">
    <h1> Upload Report </h1>
    
    <div class="row" id="msj" style="display:none">
        
    </div>
    <div id='loading_modal_report' style='text-align:center; top:200px;display: none'>
        <img src='<?= Yii::$app->request->baseUrl; ?>/images/loading.gif' width='60'/><br>
            <span style='color:#333'>Wait please..</span>
    </div>
    <?php $form = ActiveForm::begin([ 'options' => ['enctype' => 'multipart/form-data'],
                                    //'enableAjaxValidation' => true,
                                    //'enableClientScript' => true,
                                    'id' => 'FormField',
                                    'action' => null,
                                    ]); ?>

    <?= $form->field($model, 'ProjectId')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'ReportTypeId')->textInput() ?>

    <?= $form->field($model, 'file')->fileInput(['class' => 'btn btn-primary', ])->label("Select File") ?>

    <?php // $form->field($model, 'IsActive')->checkbox() ?>
    
    <div class="form-group">
        <?= Html::submitButton( '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload ', ['class' => 'btn btn-primary in-nuevos-reclamos', 'id'=>'upload-button']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $("#upload-button").click( function (e)
    {
        e.preventDefault();
        $("#FormField").slideUp();
        $("#loading_modal_report").show();
        var formData = new FormData($('form')[0]);
        //console.log(); return false;
        submitReport(<?= $model->ProjectId?> , formData);
    });
    
   
</script>