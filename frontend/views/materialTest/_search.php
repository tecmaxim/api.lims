<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\MaterialTestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row" id="searchForm">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div role="tabpanel" class="tabpanel" id="searchPanel">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="search">
                    <?php $form = ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => ['class'=>'form-search form-search-planificacion'],
                    ]); ?>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-11">
                                    <?=  $form->field($model, 'Name')->textInput(['type'=>'search','class'=>'form-control' ,'placeholder'=>Yii::t('app', 'Search by Name...')])->label(false); ?> 
                                </div>

                                <div class="col-md-1">
                                    <span class="input-group-btn hidden-xs hidden-sm">
                                        <?=  Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <a class="collapse-reclamo moreFilters" data-toggle="collapse" href="#material-test-collapse" aria-expanded="false" aria-controls="material-test-collapse"><?=Yii::t('app', 'More Filters');?></a>
                                </div>

                                    <div class="col-md-12 collapse" id="material-test-collapse">
                                        <div class="row">
                                                                                                
                                            <div class="col-md-2">
                                                <?php  echo $form->field($model, 'Generation') ?>
                                            </div> 
                                                                                                                
                                            <div class="col-md-2">
                                                <?= $form->field($model, 'CodeType') ?>
                                            </div>
                                                                                                                
                                            <div class="col-md-2">
                                                <?= $form->field($model, 'PreviousCode') ?>
                                            </div>
                                            
                                                                                                                
                                            <div class="col-md-2">
                                                <?= $form->field($model, 'Owner') ?>
                                            </div>
                                                                                                                
                                            
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'HeteroticGroup') ?>
                                            <!-- </div> -->
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'cms') ?>
                                            <!-- </div> -->
                                                                                                        
                                            <div class="col-md-2">
                                                <?php echo $form->field($model, 'Pedigree') ?>
                                            </div>
                                           
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'Origin') ?>
                                            <!-- </div> -->
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'Country') ?>
                                            <!-- </div> -->
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'Type') ?>
                                            <!-- </div> -->
                                                                                                        
                                            <!-- <div class="col-md-3">-->
                                                <?php // echo $form->field($model, 'IsActive') ?>
                                            <!-- </div> -->
                                                                                                
                                        </div>
                                    </div>
                                <div class="col-xs-2 pull-right find">
                                    <span class="hidden-md hidden-lg">
                                        <?=  Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>       
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
