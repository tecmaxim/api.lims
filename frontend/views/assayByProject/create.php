<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AssayByProject */

$this->title = 'Create Assay By Project';
$this->params['breadcrumbs'][] = ['label' => 'Assay By Job', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assay-by-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
