<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php $form = ActiveForm::begin(); ?>
   		<?= $form->field($model, 'ItemName')->dropDownList(ArrayHelper::map($role, 'name', 'name'), ['prompt'=>'-- Select rol --'],  [ "class" => "mySelectBoxClass"]) ?>

        <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success in-nuevos-reclamos' : 'btn btn-primary in-nuevos-reclamos']) ?>
    </div>

    <?php ActiveForm::end(); ?>

        </div>
</div>