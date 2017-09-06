<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Fingerprint */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Fingerprint',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fingerprints'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fingerprint-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
