<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\City */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('init();', $this::POS_READY);
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'CountryId')->textInput() ?>
    
    <?= $form->field($model, 'Name')->textInput() ?>

    <?php //= $form->field($model, 'IsActive')->checkbox() ?>

    <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
            <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
 init = function()
    {
        $('#city-countryid').kendoDropDownList({
                        //filter: "startswith",
                        optionLabel: "Select Country",
                        dataTextField: "Name",
                        dataValueField: "CountryId",
                        //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                        dataSource: {
                           //type: "json",
                          
                            transport: {
                                read: {
                                    url: "<?= Yii::$app->homeUrl ?>country/get-countrys-by-json",
                                    //type: "jsonp"
                                }
                            }
                            
                        }
                        //serverFiltering: true,
                    });            
    }
</script>