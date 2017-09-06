<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Snp */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Marker',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Markers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="marker-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
