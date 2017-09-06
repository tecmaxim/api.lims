
<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

?>
    
    <div class="container header-login-forgot" style=" background-color:rgba(255,255,255,0.7); border-radius:7px;  width:100%; height:50%;">
       <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="wrapper-login">
                            <?php if(isset($success) and $success ==1){ ?>	
                                <div class="alert alert-success">
                                    Your password was successfully resetada . Check your email to confirm. Thank You
                                </div>
				<?php } elseif(isset($success) and $success == 0){?> 
                                <div class="alert alert-danger">
                                    <strong> The mail does not exist in the system. Please check it</strong>
                                </div>
                                <?php }else { ?>
                                        <div class="alert alert-info">
                                            <strong> The new password will be sent to your email</strong>
                                        </div>
                                <?php } ?>
					<h1>Recover Password</h1>
					<?php $form = ActiveForm::begin(['id' => 'itemForm']); ?>
						<?= $form->field($model, 'Email')->textInput(['class' => 'form-control', 'placeholder' => 'Email'])->label(false)?>
                        
                        	<?= Html::button('Back to Login'
                                        ,['class' => "btn btn-help"
                                        ,'data-dismiss' => 'modal' ])?>
                               
			        <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary pull-right ', 'name' => 'request-button']) ?>
                                       
				<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>		
		
	</div>