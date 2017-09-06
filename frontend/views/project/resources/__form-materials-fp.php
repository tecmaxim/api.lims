<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

//This form is used in project definition.
//To copy in batch materials to FP projects
//-----------------------------------------
?>

<div class="assay-by-project-form">
    <h1> Copy Materials in batch </h1>
   
    <!-- Show the errors messages --> 
   <div class="row" id="msj" style="display:none"></div>
   
   <!-- Loading gif to show when submit -->
    <div id='loading_modal_report' style='text-align:center; top:200px;display: none'>
        <img src='<?= Yii::$app->request->baseUrl; ?>/images/loading.gif' width='60'/><br>
            <span style='color:#333'>Wait please..</span>
    </div>
    
    <?php $form = ActiveForm::begin([ 
                                    //'enableAjaxValidation' => true,
                                    //'enableClientScript' => true,
                                    'id' => 'form-materials',
                                    
                                    ]); ?>

    
    <?= $form->field($model, "CopyMaterials")->textarea(["rows" =>6]) ?>

    <div class="form-group">
        <?= Html::submitButton( '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload ', ['class' => 'btn btn-primary in-nuevos-reclamos', 'id'=>'control-materials']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    
    $("#control-materials").click( function (e)
    {
        e.preventDefault();
        $("#form-materials").slideUp();
        $("#loading_modal_report").show();
        var formData = new FormData($('form')[1]);
        //console.log(); return false;
        controlMaterials(formData, $("#project-crop_id").val());
    });
    
</script>