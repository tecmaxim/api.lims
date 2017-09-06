<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ResearchStation */

$this->title = Yii::t('app', 'Create Research Station');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Research Stations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="research-station-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
