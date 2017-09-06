<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Traits */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('init();', $this::POS_READY);
?>

<div class="traits-form">

    <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>

    <?= $form->field($model, 'Crop_Id')->textInput()->label('Crop') ?>

    <?= $form->field($model, 'Name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'Description')->textInput(['maxlength' => 50]) ?>

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
    $('#traits-crop_id').kendoDropDownList({
                    //filter: "startswith",
                    optionLabel: "Select Crop",
                    dataTextField: "name",
                    dataValueField: "id",
                    //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                    dataSource: {
                       //type: "json",
                        transport: {
                            read: {
                                url: "<?= Yii::$app->homeUrl ?>crop/get-crops-enabled-by-kendo",
                                //type: "jsonp"
                            }
                        }

                    }
                    //serverFiltering: true,
                });            
}
</script>