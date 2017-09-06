<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AssayByProject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="assay-by-project-form">
    <h1> Upload Assay Markers </h1>
    
    <div class="row" id="msj" style="display:none">
        
    </div>
    
    <?php $form = ActiveForm::begin([ 'options' => ['enctype' => 'multipart/form-data'],
                                    //'enableAjaxValidation' => true,
                                    //'enableClientScript' => true,
                                    'id' => 'FormField',
                                    'action' => null,
                                    ]) ?>

    <?= $form->field($model, 'ProjectId')->hiddenInput()->label(false) ?>

    <?php // $form->field($model, 'BarcodeAssay')->textInput() ?>

    <?php // $form->field($model, 'Path')->textInput(['maxlength' => 250]) ?>
    <?= $form->field($model, 'file')->fileInput(['class' => 'btn btn-primary', ])->label("Select File") ?>
    <div class="alert alert-danger" id="error_msj" style=" display:none">
        Only extensions .xls y .xlsx
    </div>

    <?= $form->field($model, 'Comments')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton( '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload ', ['class' => 'btn btn-primary in-nuevos-reclamos', 'id'=>'upload-button']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $("#upload-button").click( function (e)
    {
        e.preventDefault();
        //alert('asd1');
        /*var file = $(':file').files[0];
        var name = file.name;
        var size = file.size;
        var type = file.type;*/
        var formData = new FormData($('form')[0]);
        //console.log(formData);
        $.ajax({
            url: "../assaybyproject/upload-assay?ProjectId="+$("#uploadfile-projectid").val(),  //Server script to process data
            type: 'POST',
            // Form data
            data: formData,

            beforeSend: function(){ 
                //$("#FormField").hide('500');
                console.log('esperando');
            }, // its a function which you have to define

            success: function(response) {
                //alert(response);
                $("#msj").show();
                
                if(response === 'ok' )
                {
                    $("#msj").html(
                            '<div class="col-lg-12 col-md-12 col-sm-12 alert alert-success" >Upload Successfully</div>'
                            );
                    setTimeout(function () {
                     $('#modal').modal('toggle');
                 }, 3000);
                 location.reload();
                }else
                {
                    $("#msj").html(
                                '<div class="col-lg-12 col-md-12 col-sm-12 alert alert-danger" >'+response+'</div>'
                                );
                }
                
                
                return false;
                
            },

            error: function(error){
                alert('error');
                //$(".modal-body").html(error);
            },


            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false
        });
        
    });
</script>