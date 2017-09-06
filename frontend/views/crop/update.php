<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Crop */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Crop/Vegetables',
]) . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Crop_Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="crop-update">

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 in-nuevo-reclamo">
		    <h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
