<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Crop;

/* @var $this yii\web\View */
/* @var $model common\models\SnpSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="mar2">
    <div role="tabpanel" class="tabpanel" id="mar">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="search">
                <?php $form = ActiveForm::begin([
                                                'id' => 'searchMarker',
                                                'action' => ['index'],
                                                'method' => 'get',
                                                'options' => ['class' => 'form-search form-search-planificacion']
                                                ] ); 
                ?>
                <div class="col-lg-11">
                    <?= $form->field($model, 'search')->textInput(['class' => 'form-control', 'placeholder' => 'Enter a full or partial Marker name to search..'])->label(false)?>
                </div>       
                <span class="input-group-btn">
                    <?= Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                   <!-- <a href="#" class='btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create' onclick="return reset();" > Reset</a> -->
                </span>
                <a class="collapse-reclamo asd" data-toggle="collapse" href="#tipo-de-reclamo" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
                <div class="collapse" id="tipo-de-reclamo">
                    <div class="container-selects margin-top">										
                       <?= $form->field($model, 'Marker_Type_Id')->dropDownList([1=> "SNP", 2 => "MicroSatellite",3=>"Both"  ]) ?>
                    </div>
                    <div class="container-selects margin-top" style="<?= $fromDashboard ? 'display:none;' : '' ?>">
                        <?= $form->field($model, 'Crop_Id')->dropDownList(ArrayHelper::map(Crop::findAll(["IsActive" => 1]), 'Crop_Id', 'Name'), ['prompt'=>'-- Select Crop --']) ?>
                    </div>
             <!--       <div class="container-selects margin-top">										
                        <?php //= $form->field($model, 'ShortSequence') ?>
                    </div>
                    
                     <div class="container-selects margin-top">										
                      <?php //= $form->field($model, 'PublicCm') ?>
                    </div>
                    <div class="container-selects margin-top">										
                      <?php //= $form->field($model, 'AdvLinkageGroup') ?>
                    </div>
                    <div class="container-selects margin-top">										
                      <?php //= $form->field($model, 'PhysicalPosition') ?>
                    </div> -->
                    <div class="container-selects margin-top">										
                        <?= $form->field($model, 'Pagination')->dropDownList(['50' => '50', '100' => '100', '500' => '500', 'false' => 'All']) ?>
                    </div>
                </div>
                 <?php $form->end(); ?>
            </div>
                
        </div>
    </div>
</div>


    <?php /*

    <?= $form->field($model, 'Snp_Id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'ShortSequence') ?>

    <?= $form->field($model, 'LongSequence') ?>

    <?= $form->field($model, 'PublicLinkageGroup') ?>

    <?php // echo $form->field($model, 'PublicCm') ?>

    <?php // echo $form->field($model, 'AdvLinkageGroup') ?>

    <?php // echo $form->field($model, 'AdvCm') ?>

    <?php // echo $form->field($model, 'PhysicalPosition') ?>

    <?php // echo $form->field($model, 'IsActive') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    
    
    
   */ ?>
