<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProtocolResult */

$this->title = 'Create Protocol Result';
$this->params['breadcrumbs'][] = ['label' => 'Protocol Results', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="protocol-result-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
