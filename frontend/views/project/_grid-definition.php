<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>

 <div class="container">
    <div class="row">

    <?=  $this->render('_header-steps');  ?>
    <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
    </div>
    <div class="row marker-blue-container" >
        
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-info">
               <!-- <div class="col-md-9 font16">Project Name:<b> <?= $project->Name ?></b></div> -->
                <div class="col-md-4 col-lg-4 font16">Project Code: <b><?= $project->ProjectCode ?></b></div>
                <div class="col-md-4 col-lg-4 font16"><?= $parents == null ? "": "Pollen Donnor <b>".$parents[0]."</b>" ?></div>
                <div class="col-md-4 col-lg-4 font16"><?= $parents == null ? "": "Pollen Receptor <b>".$parents[1]."</b>" ?></div>
        </div>
        
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-30 ">
            <?php  $form4 = ActiveForm::begin(["id"=>"form_step4"]); ?>
            
            <div style="display: none"><?= $form4->field($samplesByProject, 'ProjectId')->hiddenInput(); ?> </div>
            
             <?= $form4->field($samplesByProject, 'PlateIdList')->widget(Select2::className(),[
                                                                            'name' => 'PlateId',
                                                                            'data' => ArrayHelper::map($plates, "PlateId", "PlateId"),
                                                                            'options' => [
                                                                                            'placeholder' => 'Select Others Plates',
                                                                                            'multiple' => true,
                                                                                         ],
                                                                                ]); ?>
            <?= $form4->field($samplesByProject, 'IsTemplate')->checkbox(['id' => 'check-template']);?>
            <div id="template-fields" class="hidden">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form4->field($samplesByProject, 'ColumnNumbers') ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form4->field($samplesByProject, 'CharSeparator') ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form4->field($samplesByProject, 'ColumnPivote') ?>
                </div>
            </div>
            
            <?= $form4->field($samplesByProject, "SampleName")->textarea(["rows" =>6]);
              ?>
        </div>

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
           <!-- <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc" id="BackToStep2"><?= Yii::t('app', 'Back To Previous Step'); ?></button> -->
        </div>
         <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <?php //= Html::a('Next <span class="glyphicon glyphicon-arrow-down"></span>', ['create/#'], ['class' => 'btn btn-primary in-nuevos-reclamos', "id"=>"saveStep2"]) ?>
             <?=  Html::Button('Save </span>', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']);  ?>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Cancel Grid Definition'); ?></button>
        </div>              
            <?php  ActiveForm::end();?>                
    </div>
 </div>
<script>
    var pageHeight = $( document ).height();
    var pageWidth = $(window).width();
     //alert(pageHeight); exit;
    $('#divBlack').css({"height": pageWidth});

    $("#step1").removeClass("selected");
    $("#step4").addClass("selected");
    
    $("#cancel").click(function()
                         {
                            window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $project->ProjectId ?>";
                         });
    $("#form_step4").submit(function()
    {
        $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');});
    });
    $("#check-template").click( function(){
        if( $(this).is(':checked') )
        {
            $("#template-fields").hide();
            $("#template-fields").removeClass('hidden');
            $("#template-fields").fadeIn('700');
        }else
        {
            $("#template-fields").fadeOut('700');
        }
     });
     $(function(){
         if( $("#check-template").is(':checked') )
        {
            $("#template-fields").hide();
            $("#template-fields").removeClass('hidden');
            $("#template-fields").fadeIn('700');
        }
     });

    
</script>