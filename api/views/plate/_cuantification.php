<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Plate */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Save Quantification & Dilution');

?>
<div class="country-create">

    <h1><?= Html::encode($this->title) ?></h1>

</div>
<?php  $form = ActiveForm::begin(['id' => 'itemForm','enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
<div class="row">
            <?= $form->field( $adnModel, 'AdnExtractionId')->hiddenInput()->label(false); ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?=  $form->field( $adnModel, 'Dilution')->textInput(); ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= $form->field( $adnModel, 'Cuantification')->textInput(); ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= Html::submitButton($adnModel->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?= Yii::t('app', 'Cancel'); ?> </button>
            </div>
        
</div>
<?php  ActiveForm::end(); ?>