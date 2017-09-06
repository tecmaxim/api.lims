<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = 'Update Job: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->ProjectId]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div id="divBlack" style="display:inline;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<script>
    
    var pageHeight = $( document ).height();
    var pageWidth = $(window).width();
     //alert(pageHeight); exit;
    $('#divBlack').css({"height": pageWidth});
    </script>
<div class="project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
