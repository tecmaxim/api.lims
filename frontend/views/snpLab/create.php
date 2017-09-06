<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SnpLab */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Snp Lab',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Snp Labs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="snp-lab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'markers' => $markers,
    ]) ?>

</div>
