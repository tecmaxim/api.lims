<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CancelCausesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

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
                                    <?=  $form->field($model, 'Name')->textInput(['type'=>'search','class'=>'form-control' ,'placeholder'=>Yii::t('app', 'Search Cause...')])->label(false); ?> 
                                </div>
                                <div class="col-md-1">
                                    <span class="input-group-btn hidden-xs hidden-sm">
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