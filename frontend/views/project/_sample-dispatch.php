<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="container">
    <?php // if(Yii::$app->controller->action->id === "get-shipment-data"):?>
    <div class="row">
        <?=  $this->render('_header-steps');  ?>
        <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
    </div>
    <?php //   endif; ?>
    <div class="row marker-blue-container" >
        <?php if(isset($notSent)):?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-danger">
            <b>This proyect is not yet sent.</b><br>
            You must send the project from Jobs Lists.
        </div>
        <?php else:?>
        <div id="content_form">
            <?php  $form5 = ActiveForm::begin(["id"=>"form_step6"]); ?>
            <div class="row" style="margin: 20px 0 10px 0">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-10 alert-info">
                    <b>Job Name: </b> <?= $project->Name ?>    
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-10 alert-info">
                    <b>Job Code: </b> <?= $project->ProjectCode ?>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <?= $form5->field($dispatch, 'Carrier')->textInput(); ?>

                </div>
                <div class="col-xs-12 col-sm-4">
                    <?= $form5->field($dispatch, 'TrackingNumber')->textInput(); ?>

                </div>
                <div class="col-xs-12 col-sm-4">
                    <?= $form5->field($dispatch, 'Date')->widget(\yii\jui\DatePicker::classname(), 
                               [
                                'model' => $dispatch,
                                'attribute' => 'Date',
                                //'changeMonth' => true,
                                //'changeYear' => true,
                                'language' => 'es',
                                'options' => ['class' => 'form-control' ],
                                'dateFormat' => 'yyyy-MM-dd',
                                'clientOptions' => ['showAnim' => 'slideDown']
                                ]);
                                ?>

                </div>
                <div style="display: none"><?= $form5->field($dispatch, 'ProjectId')->hiddenInput(); ?> </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= Html::button('Save',['type'=>'submit','class' => 'btn btn-primary in-nuevos-reclamos',]) ?>
                </div>
                
        <?php endif;?>
                <div class="col-xs-12 ">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= isset($notSent)? 'Back':Yii::t('app', 'Cancel'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
   $("#step1").removeClass("selected");
   $("#step6").addClass("selected");
    <?php if(!isset($notSent)): ?>
        $("#cancel").click(function()
                         {
                            window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $dispatch->ProjectId ?>";
                         });
    <?php else: ?>
    $("#cancel").click(function()
                         {
                            window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $id;?>";
                         });
    <?php endif; ?>
   
</script>
