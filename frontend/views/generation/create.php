<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Generation */

$this->title = Yii::t('app', 'Create Generation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Generations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
