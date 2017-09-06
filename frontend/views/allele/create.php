<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Allele */

$this->title = 'Create Allele';
$this->params['breadcrumbs'][] = ['label' => 'Alleles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="allele-create">

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 in-nuevo-reclamo">
			<?= Html::a('Cancel', ['index'], ['class' => 'btn btn-gray margin-top-30']) ?>
		    <h1><?= Html::encode($this->title) ?></h1>
		</div>
	</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
