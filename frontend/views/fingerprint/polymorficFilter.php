<?php
use yii\helpers\Html;

$this->title = Yii::t('app', 'Polymorfism {modelClass}', [
    'modelClass' => 'Fingerprint',
]);
?>

<div class="fingerprint-create">

    <h1><?= Html::encode($this->title) ?></h1>
	
    <?php //$form = ActiveForm::begin(); 
			//$materials = Materials::find()->all();
			$i = 0;
			foreach($data as $d)
			{
				print_r($i++ .'-'.$d['LabName']);
				echo "<br>";
			}
	?>
</div>
