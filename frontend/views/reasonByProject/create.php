<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ReasonByProject */

$this->title = 'Create Reason By Project';
$this->params['breadcrumbs'][] = ['label' => 'Reason By Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reason-by-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
