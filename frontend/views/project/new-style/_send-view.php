<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h1>Shipment</h1>
</div>

<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
    <div class="alert alert-info"> 
        <strong> <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> This project is ready to send</strong>
    </div>
    <?php 
    if(Yii::$app->user->getIdentity()->itemName == "breeder") 
    {
        $action_button = '<span class="glyphicon glyphicon-download-alt"></span> CREATE PLATES AND BARCODES SHEET';
        $array_class = ['class' => 'btn btn-success btn-lg btn-block font-white'];
        $array_id = "";
    }
    else
    {
        $action_button ='<span class="glyphicon glyphicon-send"></span> SEND';
        $array_class = ['class' => 'btn btn-success btn-lg btn-block font-white',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal'
                       ];
        $array_id = ['id' => 'itemForm'];
    }
    $form = ActiveForm::begin($array_id); 

    echo $form->field($project, 'ProjectId')->hiddenInput()->label(false);
    
    echo Html::submitButton(Yii::t('app', $action_button), $array_class );
    
    ActiveForm::end(); 
    ?>
</div>

<script>
    $('body').on('hidden.bs.modal','#modal', function (e) {
            var pageHeight = $( document ).height();
            $('#divBlack').css({"height": pageHeight});
            
            $("#divBlack").show();
            
            window.location.reload();
        });
    <?php if(Yii::$app->user->getIdentity()->itemName == "breeder"): ?>
    
    $('form').submit(function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'get-shipment-data?id='+$("#project-projectid").val(),
            data: $('form').serialize(),
            beforeSend: function(){
                var pageHeight = $( document ).height();
                $('#divBlack').css({"height": pageHeight});

                $("#divBlack").show();
            },
            success:function(response){
                $("#divBlack").hide();
                //console.log(response); return false;
                window.location = "<?= Yii::$app->urlManager->baseUrl; ?>/project/download-zip?zipname="+response;
                setTimeout(function(){location.reload()}, 1500);
            },
            error:function(e)
            {
                console.log(e); return false;
            }
        });
       
    });
    <?php endif; ?>
</script>