<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\MaterialTest */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('init();', $this::POS_READY);
?>

<div class="material-test-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <div class="container-planificacion">
        <div class="row">

            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">										
                <?= $form->field($model, 'Crop_Id')->textInput() ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => 50]) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= $form->field($model, 'CodeType')->dropDownList(['Final' => 'Final', 'Provisional']) ?>
            </div>
             
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'Owner')->textInput(['maxlength' => 50]) ?>
            </div>
            
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= $form->field($model, 'HeteroticGroup')->textInput(['maxlength' => 2]) ?>
            </div>
            
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?php //= $form->field($model, 'Generation')->textInput(['maxlength' => 50]) ?>
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'Pedigree')->textInput(['maxlength' => 150]) ?>
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'Type')->textInput(['maxlength' => 50]) ?>
            </div>
            
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    init = function()
    {
        $('#materialtest-crop_id').kendoDropDownList({
            //filter: "startswith",
            optionLabel: "Select Material..",
            dataTextField: "Name",
            dataValueField: "Crop_Id",
            //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
            dataSource: {
                type: "json",
                transport: {
                    read: {
                        url: "<?= Yii::$app->homeUrl ?>crop/get-crops-enabled",
                        //type: "jsonp"
                    }
                }

            },
            //serverFiltering: true,
        });

        
    }
</script>
    
