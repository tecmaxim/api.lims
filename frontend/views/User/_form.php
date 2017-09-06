<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <?php $form = ActiveForm::begin(['id' => 'itemForm','enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
        <?= $form->field($model, 'Username') ?>
    <?php if(Yii::$app->user->getIdentity()->ItemName == "admin" ) 
                    $optionsCrop = ['placeholder' => 'Select Crop',
                                 'multiple' => true,
                                ];
              else
                    $optionsCrop = ['placeholder' => 'Select Crop',
                                 'multiple' => true,
                                 'disabled' => true
                                ];
        ?>
    <?php if(Yii::$app->user->getIdentity()->ItemName == "admin" ) 
                    $optionsRole = ['placeholder' => 'Select Role',
                                ];
              else
                    $optionsRole = ['placeholder' => 'Select Role',
                                 'disabled' => true
                                ];
        ?>
        <?= $form->field($model, Yii::$app->controller->action->id == 'update' ? 'ItemName':'Role')->widget(Select2::className(),[
                'name' => 'Role',
                'data' => ArrayHelper::map($role, 'name', 'data'),
                'options' => $optionsRole,
            ]); ?>
            
       
        <?= $form->field($model, 'Crop')->widget(Select2::className(),[
                'name' => 'Crop',
                'data' => ArrayHelper::map($crop, "Crop_Id", "Name"),
                'options' => $optionsCrop,
            ]); 
        ?>
        <?= $form->field($model, 'Email')->textInput() ?>
        <?= $form->field($model, 'PasswordHash')->passwordInput() ?>
        <?= $form->field($model, 'PasswordConfirm')->passwordInput() ?>

    <div class="form-group">
      <?= Html::Button('Update', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Cancel'); ?></button>
    </div>

    <?php ActiveForm::end(); ?>

</div>

