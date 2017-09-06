<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use barcode\barcode\BarcodeGenerator;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GenerationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Reception Plates');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('init_plate_reception("'.Yii::$app->request->baseUrl.'");',$this::POS_READY);
?>

<div class="generation-index">
    <div id="divWhite"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1> 
        </div>
        <div class="col-xs-12 col-md-3" style="margin-top: 50px;">
            <?= Html::a("Save Receptions", "#",['class' => 'btn btn-primary btn-render-content', "id"=>"save-receptions"] ); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info">
                <b> <i class="glyphicon glyphicon-info-sign"></i> In this section, You should scan the bar codes of the plates you have received on the place.</b>
            </div>
        </div>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
            <?php  Pjax::begin(['id' => 'itemList']);?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
                'columns' => [
                    ['class' => 'frontend\widgets\RowLinkColumn'],
                    //'PlateId',
                    [
                        'label' => '',
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'display:none'],
                        'contentOptions' => ['class' => 'no-link', 'style' => 'display:none'],
                        //'visible' =>  false,
                        'value' => function ($data) {
                                            return '<input type="checkbox" name="vCheck[]" value="' . $data->PlateId . '" id="TP'.  sprintf('%06d', $data->PlateId).'">';
                        },
                    ],
                    [
                       'attribute' => 'PlateId',
                        'format'=>'raw', 
                        'value' => function($data){ return 'TP'.sprintf("%'.06d\n",$data->PlateId); },
                    ],
                    [
                        'attribute' => 'Barcode',
                        'format'=>'raw', 
                        'value'=> function($model){
                            return '<img src="'.Url::home(true).'/BarcodeGenerator.php?text=TP'.sprintf('%06d',$model->PlateId).'&code=code39&size=30" />';
                        },
                    ],
                    [
                        'attribute' => 'Date',
                        'format'=>'raw', 
                        'value' => function($data)
                                   {
                                       $newdate = strtotime($data->Date);
                                       return date( 'd-m-Y', $newdate );
                                   },
                    ],
                    [
                        'attribute' => 'Plate Status',
                        'format' => 'raw',
                        'value' => function($data)
                                   {
                                       return  $data->getStatusName();
                                   },
                    ],
                   
                    //['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
            <?php  Pjax::end();?> 
            </div>
        </div>
        <div class="col-md-5">
            <input type="text" name="inputCode" class="input-codebar" />
        </div>
    </div>
</div>
<script>
    $(function(){
        setTimeout(function(){
                        $('#flash_msj').fadeOut('850');
                            }, 3000);
    });
    
    $(".input-codebar").focus();
    
    setInterval(function(){
        $(".input-codebar").focus();
        
    }, 50);
    
    /*
    $("input[name='inputCode']").on("change", function() {
        console.log("Change to " + this.value);
    });
    */
   
</script>
