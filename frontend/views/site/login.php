
<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

?>

<style>
	.left{
		float:left;
		}
</style>
<div class="site-login"  style=" height:10%;">
    <h1><?= Html::encode($this->title) ?></h1>

   <div class="container header-login-forgot" style=" background-color:rgba(255,255,255,0.7); border-radius:7px;  width:50%;  height:50%;">
		<div class="row" style=" margin:60px 0px 0px 0px;">
			<div class=" col-sm-12 col-md-12 col-lg-12">
				<img src="<?= Yii::$app->urlManager->baseUrl ?>/images/logo-header.png" alt="" class="logo-forgot">
			</div>
		</div>
		
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="wrapper-login">
				<h1>Login</h1>
				<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'Username')->textInput(['class' => 'form-control', 'placeholder' => 'Nombre'])->label(false)?>
               			<?= $form->field($model, 'Password')->passwordInput(['class' => 'form-control', 'placeholder' => 'Password'])->label(false) ?>
                        <div align="left">
               			<?= $form->field($model, 'RememberMe')->checkbox() ?>
                        </div>
						
                        <?= Html::button(Yii::t('app', 'forgot your password?'),
                                                      ['class' => 'btn btn-help'
                                                        , 'data-toggle' => 'modal'
                                                        , 'data-target' => '#modal'
                                                        , 'data-url' => Url::to(['user/request']),
                                                      ]); ?>
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary pull-right ', 'name' => 'login-button']) ?>
                        
				  <?php ActiveForm::end(); ?>
			</div>
		</div>
		
	</div>
</div>
