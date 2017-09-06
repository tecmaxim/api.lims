<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row">
	<div class="col-md-10">
    	<div class="row">
			<div class="col-md-4">
				<strong>
					<?= Html::button(Yii::t('app', 'MaterialTest') .': ' .$data->Name, [
						'class' => 'btn btn-link', 
						'data-toggle' => 'modal', 
						'data-target' => '#modal', 
						'data-url' => Url::to(['view', 'id' => $data->Material_Test_Id])
					]);?>
				</strong>
			</div>
		</div>
		<div class="row">

        	<div class="col-md-1"><strong><?= Yii::t('app', 'Material_Test_Id');?>: </strong><?=$data->Material_Test_Id ?></div>
                
        	<div class="col-md-1"><strong><?= Yii::t('app', 'Name');?>: </strong><?=$data->Name ?></div>
                
        	<div class="col-md-1"><strong><?= Yii::t('app', 'CodeType');?>: </strong><?=$data->CodeType ?></div>
                
        	<div class="col-md-1"><strong><?= Yii::t('app', 'OldCode_1');?>: </strong><?=$data->OldCode_1 ?></div>
                
        	<div class="col-md-1"><strong><?= Yii::t('app', 'OldCode_2');?>: </strong><?=$data->OldCode_2 ?></div>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Owner');?>: </strong><?=$data->Owner ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Material');?>: </strong><?=$data->Material ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'HeteroticGroup');?>: </strong><?=$data->HeteroticGroup ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'cms');?>: </strong><?=$data->cms ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Pedigree');?>: </strong><?=$data->Pedigree ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Origin');?>: </strong><?=$data->Origin ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Country');?>: </strong><?=$data->Country ?></div> 
*/?>
                
        	<?php /*
 <div class="col-md-1"><strong><?= Yii::t('app', 'Type');?>: </strong><?=$data->Type ?></div> 
*/?>
                
		</div>
	</div>
</div>
