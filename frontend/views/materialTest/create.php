<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MaterialTest */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Material',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Material Tests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-test-create">

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 in-nuevo-reclamo">
		    <h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>
    
    
    <?= $this->render('_form', [
        'model' => $model, 'crop' => $crop
    ]) ?>

</div>
