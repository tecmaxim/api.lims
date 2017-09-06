<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Generation */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<!-- /divBlack-->

<div class="traits-by-markers-form">
    <div class="container">
    <div class="row">
            
        <?=  $this->render('../_header-steps');  ?>
        <?=  $this->render('__float-title',[ "projectName"=>$projectName]);  ?>
    </div>
    <div class="row marker-blue-container" >
        <div class="row text-center">
            <h1>Markers associated with Traits</h1>
        </div>
        <?php $form = ActiveForm::begin([
                                        'id'=> 'tratisByMarkers',
                                         //'enableAjaxValidation' => true,    
                                         //'enableClientScript' => true,
                                         'action' => "select-traits-by-markers"]); ?>

        <?= $form->field($model, 'ProjectId')->hiddenInput()->label(false); ?>
        
        <?php 
        if($update == 1)
        {
            echo "<input type='hidden' name='update' value='1' /> ";
        }
            
        foreach($markers as $marker): ?>
            <div class="row">
                <div class="col-md-2 col-md-offset-3 text-center padding-10">
                    <?= "<input type='text' name='markers[marker][]' class='text-center border-none' value='".$marker['LabName']."' readonly='radonly' /> "; ?>
                </div>
                <div class="col-md-3 text-center padding-10">
                    <?php echo $form->field($model, 'Trait['.$marker['MarkersByProjectId'].']')->dropDownList(\yii\helpers\ArrayHelper::map($traits, "TraitsByMaterialId", "traits.Name"), ["prompt" => "Select Trait..."])->label(false); ?>
                </div>
                <?php
                    //print_r($traits); exit;
                    /*foreach($traits as $trait)
                    {
                        echo "<div class='col-md-1' style='padding:14px 5px 10px 5px;'>";
                        echo "<input type='checkbox' name='markersByProjectIds[".$marker['MarkersByProjectId']."][]' value='".$trait->TraitsByMaterialId."' /> ". $trait->traits->Name;
                        echo "</div>";
                        
                    }*/
                ?>
            </div>
        <?php endforeach; ?>

        <?php //= $form->field($model, 'IsActive')->checkbox() ?>
            <div class="row" style="margin-top: 40px;">
                <div class="form-group">
                    <div class="col-md-4 col-md-offset-4">
                    <?= Html::submitButton(Yii::t('app', 'Save') , ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                    <!-- <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?= Yii::t('app', 'Cancel'); ?> </button> -->
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
    </div>
    </div>

</div>
<script>
    $("#step1").removeClass("selected");
    $("#step2").addClass("selected");
        <?php //$data = \common\models\TraitsByMarkersByProject::getTraitsBybProjectByKendo($model->ProjectId) ?>
        /*$(function() {
            $("#1").kendoMultiSelect({
                placeholder: "Select products...",
                dataTextField: "Name",
                dataValueField: "TraitsId",
                change: function(e){console.log(e)},
                        
                //autoBind: false,
                dataSource: {
                    //type: "odata",
                    serverFiltering: true,
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>traits/get-traits-by-kendo?idProject=<?= $model->ProjectId?>",
                        },
                    }
                },
                value: <?php// json_encode($data); ?>
            }); 
            });
            
          */
    $(function(){var pageHeight = $( document ).height();
    //var pageWidth = $(window).width();
    $('#divBlack').css({"height": pageHeight});   
    });
    $("#tratisByMarkers").submit(function(){
        $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');});
    });
</script>