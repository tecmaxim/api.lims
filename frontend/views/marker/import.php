<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<h1>Importaci&oacute;n de Snp</h1>
				
		<?php if(isset($error)): ?>	
			<div class="alert alert-danger">
				El archivo no se ha podido subir. Por favor, verifique el archivo e intente nuevamente.
			</div>
   		<?php endif;?>

		<div id="form">
	   		<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'onsubmit' => 'js:submitForm()']]) ?>
				<?= $form->field($model, 'file')->fileInput() ?>
	 			<?= Html::submitButton( 'Upload', ['class'=> 'btn btn-primary']); ?>
			<?php ActiveForm::end(); ?>
		</div>
		<div id="loading" style="display:none;">
   			<img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="30"/>
		</div>
	</div>
</div>

<script>
	submitForm = function()
	{
		$('#form').hide();
		$('#loading').show();
	}
</script>