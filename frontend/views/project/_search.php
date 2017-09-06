<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div role="tabpanel" class="tabpanel" id="searchPanel">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="search">
                    <?php $form = ActiveForm::begin([
                        'action' => ['select-project'],
                        'method' => 'get',
                        'options' => ['class'=>'form-search form-search-planificacion'],
                    ]); ?>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-2">
                                    <?= $form->field($model, 'ProjectCode') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'Parent1_donnor')->dropDownList(ArrayHelper::map($pDonnor, 'Material_Test_Id', 'materialTest.Name' ), ['prompt' =>'Select...'] )  ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'Parent2_receptor')->dropDownList(ArrayHelper::map($pReceptor, 'Material_Test_Id', 'materialTest.Name' ), ['prompt' =>'Select...'] )  ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'GenerationId')->dropDownList(yii\helpers\ArrayHelper::map(common\models\Generation::findAll(["IsActive" => 1]), 'GenerationId', 'Description'),['prompt' =>'Select...']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'StepProjectId')->dropDownList(yii\helpers\ArrayHelper::map(common\models\StepProject::find()->where(['>','StepProjectId',0])->andWhere(['<','StepProjectId',10])->all(), 'StepProjectId', 'Name'),['prompt' =>'Select...']) ?>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="input-group-btn">
                                        <?=  Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find', 'style' => 'margin-top:26px']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>       
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>