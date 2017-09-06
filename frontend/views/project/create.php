<div id="divBlack" style="display:none;">
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
<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = 'Create Job';
$this->params['breadcrumbs'][] = ['label' => 'Jobs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="project-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1> -->
      
    <?= $this->render('_form', [
        'model' => $model,
//        'searchModel'   => $searchModel, 
//        'hasMap'        => $hasMap,
//        'dataProvider'  => $dataProvider,
//        'mapTypes'      => $mapTypes,
//        'limitsXcM'     => $limitsXcM,
//        'BackToQuery'    => $BackToLims,
    ]) ?>

</div>
