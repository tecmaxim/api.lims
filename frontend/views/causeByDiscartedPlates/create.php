<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CauseByDiscartedPlates */

$this->title = 'Create Plate fail reason';
//$this->params['breadcrumbs'][] = ['label' => 'Cause By Discarted Plates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-by-discarted-plates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
