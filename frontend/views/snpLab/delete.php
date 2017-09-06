<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php  if(isset($ok)): ?>
	<div class="alert alert-success">The item was deleted succesfully.</div>
<?php  else: ?>
	<div class="alert alert-warning">Are you sure you want to delete?</div>
	<?php  $form = ActiveForm::begin(['id' => 'itemForm']); ?>
		<div style="display:none;">
			<?php  $form->field($model, 'Snp_lab_Id')->hiddenInput() ?>
		</div>
		<div class="container-planificacion">
		    <div class="row">
		        <div class="form-group">
		            <?=  Html::submitButton(Yii::t('app', 'Yes'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
					<button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
		        </div>
		    </div>
		</div>
	<?php  ActiveForm::end(); ?>
<?php  endif; ?>