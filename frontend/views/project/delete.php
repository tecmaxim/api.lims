<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>
<?php  if(isset($ok)): ?>
	<div class="alert alert-success">The project was <?= isset($finished) ? 'changed its status': 'deleted' ?> succesfully.</div>
        <script>
            $(function(){
                setTimeout(function(){
                window.location = "<?= Yii::$app->homeUrl; ?>project/";
            }, 2000);
            });
        </script>
<?php  else: ?>
	<div class="alert alert-warning">Are you sure you want to <?= isset($finished) ? 'change status of': 'delete' ?> this project?</div>
	<?php  $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
		<div style="display:none;">
			<?=  $form->field($model, 'ProjectId')->hiddenInput() ?>
		</div>
                <?php if(isset($causes)): ?>
                    <?= $form->field($model, 'CancelCauses')->dropDownList(ArrayHelper::map($causes, 'CancelCausesId', 'Name'), ["prompt" => "Select Cancel Cause.."]) ?>
                <?php endif;?>
                <?php if(isset($finished)): ?>
                    <?= $form->field($model, 'CommentToChangeStatus')->textarea(['rows' => 6]) ?>
                <?php endif;?>
                
                <?php if(isset($plates)): ?>
                    <?= $form->field($model, 'Plates')->checkboxList(ArrayHelper::map($plates, 'PlateId', 'Formated')) ?>
                <?php endif;?>
		
                <div class="form-group">
                    <?=  Html::submitButton(Yii::t('app', 'Yes'), ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                    <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
                </div>
		    
		
	<?php  ActiveForm::end(); ?>
<?php  endif; ?>