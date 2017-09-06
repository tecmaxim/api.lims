<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup2">
    <h1><?= Html::encode($this->title) ?></h1>

  
	
    <div class="row">
      <div class="snp-lab-form">
            <?php $form = ActiveForm::begin(['id' => 'itemForm','enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
                <?= $form->field($model, 'Username') ?>
                <?= $form->field($model, 'Role')->dropDownList(ArrayHelper::map($role, 'name', 'data'), ['prompt'=>'-- Select rol --'],  [ "class" => "mySelectBoxClass"]) ?>
                 <?php if(Yii::$app->user->getIdentity()->ItemName == "admin" ) 
                        $options = ['placeholder' => '-- Select Crop --',
                                 'multiple' => true,
                                ];
                    else
                        $options = ['placeholder' => '-- Select Crop --',
                                 'multiple' => true,
                                 'disabled' => true
                                ];
                  ?> 
                <?= $form->field($model, 'Crop')->widget(Select2::className(),[
                        'name' => 'Crop',
                        'data' => ArrayHelper::map($crop, "Crop_Id", "Name"),
                        'options' => $options,
                    ]); 
                ?>
                
                <?= $form->field($model, 'Email')->textInput() ?>
                <?= $form->field($model, 'Password')->passwordInput() ?>
                <?= $form->field($model, 'PasswordConfirm')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::Button('Create', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Cancel'); ?></button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
