<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AssayByProject */

$this->title = 'Update Assay By Project: ' . ' ' . $model->AssayByProjectId;
$this->params['breadcrumbs'][] = ['label' => 'Assay By Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->AssayByProjectId, 'url' => ['view', 'id' => $model->AssayByProjectId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="assay-by-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
