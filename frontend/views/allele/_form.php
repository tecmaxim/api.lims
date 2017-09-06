<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Allele */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="allele-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-planificacion">
        <div class="row">

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                <?= $form->field($model, 'LongDescription')->textInput(['maxlength' => 255]) ?>
            </div>
            
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-gray in-nuevos-reclamos']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
