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
    <div class="row">
    <?=  $this->render('_header-steps');  ?>
    <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
    </div>
    <div class="row marker-blue-container" >
        <?php if(Yii::$app->user->getIdentity()->itemName == "breeder"): ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-warning">
                    The laboratory has not yet received the samples.
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Cancel'); ?></button>
        </div>
        <?php else: ?>
            <?php  $form6 = ActiveForm::begin(["id"=>"form_step7"]); ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-10 alert-info">
                    <b>Project Name: </b> <?= $project->Name ?>    
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?= $form6->field($reception, 'LabReception')->widget(\yii\jui\DatePicker::classname(), 
                               [
                                'model' => $reception,
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
                <div style="display: none"><?= $form6->field($reception, 'ProjectId')->hiddenInput(); ?> </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= Html::button('Save',['type'=>'submit','class' => 'btn btn-primary in-nuevos-reclamos',]) ?>
                </div>
                <div class="col-xs-12">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Cancel'); ?></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $("#step1").removeClass("selected");
    $("#step7").addClass("selected");

$("#cancel").click(function()
                         {
                            window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $reception->ProjectId ?>";
                         });
   
</script>