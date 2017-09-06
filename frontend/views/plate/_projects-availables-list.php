<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="generation-view">
    <h1> List of Available Jobs </h1>
    <div class="row">

        <?php if($projectsList):?>
            <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
            <?php foreach($projectsList as $project):?>       
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?= $form->field($model, 'ProjectId')->radio(['label' => $project['name'], 'value' => $project['id'], 'uncheck' => null]) ?>
            </div>
            <?php endforeach; ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else:?>
            <?php if(isset($hasMoreThanOneParent)):?>
                <div class="alert alert-danger">
                    <strong>Warning!</strong>
                    The Plate is asociated to more than one project! 
                </div>
            <?php else:?>
                 <div class="alert alert-danger">
                   No projects available
                </div>
            <?php endif;?>
        <?php endif;?>

    </div>
</div>