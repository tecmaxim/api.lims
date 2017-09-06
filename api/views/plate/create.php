<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PlateHistoryByProject */

$this->title = 'Create Plate History By Project';
$this->params['breadcrumbs'][] = ['label' => 'Plate History By Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plate-history-by-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
