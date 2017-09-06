<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\MapType;

/* @var $this yii\web\View */
/* @var $model common\models\Crop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crop-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <div class="container-planificacion">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => 255]) ?>
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'ShortName')->textInput(['maxlength' => 150]) ?>
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'LatinName')->textInput(['maxlength' => 255]) ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= $form->field($model, 'Map')->widget(Select2::className(),[
                'name' => 'Maps Category',
                'data' => ArrayHelper::map(MapType::getNamesMapTypes(), "MapTypeId", "Name" ),
                'options' => ['placeholder' => '',
                                 'multiple' => true,
                                 
                                ],
                ]); 
            ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
