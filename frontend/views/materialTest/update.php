<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MaterialTest */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Material',
]) . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Material Tests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Material_Test_Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="material-test-update">

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 in-nuevo-reclamo">
		    <h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>

    <?= $this->render('_form', [
        'model' => $model, 'crop' => $crop
    ]) ?>

</div>
