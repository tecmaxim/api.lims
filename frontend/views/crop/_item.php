<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row">
	<div class="col-md-10">
    	<div class="row">
			<div class="col-md-4">
				<strong>
					<?= Html::button(Yii::t('app', 'Crop') .': ' .$data->Name, [
						'class' => 'btn btn-link', 
						'data-toggle' => 'modal', 
						'data-target' => '#modal', 
						'data-url' => Url::to(['view', 'id' => $data->Crop_Id])
					]);?>
				</strong>
			</div>
		</div>
		<div class="row">

        	<div class="col-md-2"><strong><?= Yii::t('app', 'Crop_Id');?>: </strong><?=$data->Crop_Id ?></div>
                
        	<div class="col-md-2"><strong><?= Yii::t('app', 'Name');?>: </strong><?=$data->Name ?></div>
                
        	<div class="col-md-2"><strong><?= Yii::t('app', 'ShortName');?>: </strong><?=$data->ShortName ?></div>
                
        	<div class="col-md-2"><strong><?= Yii::t('app', 'LatinName');?>: </strong><?=$data->LatinName ?></div>
                
		</div>
	</div>
</div>
