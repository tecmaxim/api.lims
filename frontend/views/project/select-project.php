<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GenerationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Select Job to Work');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="generation-index">

    <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
    
        <?= $this->render('_search', ['model' => $searchModel, 'pDonnor' => $pDonnor,'pReceptor' => $pReceptor]); ?>
   
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
            <?php  Pjax::begin(['id' => 'itemList']);?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                //'tableOptions' => [ 'id' => 'haeder-bold'],
                'columns' => [
                    //['class' => 'frontend\widgets\RowLinkColumn'],
                    [
                        'label'=>'Select',
                        'format' => 'raw',
                        'options' => ['width' =>80],
                        'contentOptions' => ['class' => 'no-link'],
                        'value'=>function ($data) {
                                            return '<input type="radio" name="vCheck[]" style="margin-left:20px; border-radius:5px;" value="'.$data['ProjectId'].'" >';
                                         },
                    ],
                    'ProjectCode',
                    [
                        'attribute' => 'Pollen Donnor',
                        'format'=>'raw', 
                        'value' => function($data)
                                   {
                                        return $data->getParentByType(common\models\MaterialsByProject::POLLEN_DONOR);
                                   },
                    ],
                    [
                        'attribute' => 'Pollen Receptor',
                        'format'=>'raw', 
                        'value' => function($data)
                                   {
                                        return $data->getParentByType(common\models\MaterialsByProject::POLLEN_RECEPTOR);
                                   },
                    ],                                           
                    [
                        'attribute'=>'Step Project',
                        'value' => function($data)
                                   {
                                        return $data->stepProject->Name;
                                   },
                    ],
                    [
                        'attribute' => 'Generation',
                        'format'=>'raw', 
                        'value' => function($data)
                                   {
                                        return $data->generation->Description;
                                   },
                    ],
                    //['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
            <?php  Pjax::end();?> 
            </div>
        </div>
    </div>
</div>
<script>
    setInterval(function(){
     $("input[name='vCheck[]']").each(function(a,i,e)
                {
                    $(this).click(function(e){
                                console.log('retusdasd');
                                window.location = "<?= Yii::$app->homeUrl ?>project/job-continued?id="+$(this).val(); 
                                })
                                //.css("outline", "3px solid red");
                });
                }, 1000);
</script>
