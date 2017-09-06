<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
//use kartik\grid\ActionColumn;
//use kartik\grid\SerialColumn;



//$this->registerJs('init();', $this::POS_READY);
/* @var $this yii\web\View */
/* @var $searchModel common\models\SnpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Markers');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('init();', $this::POS_READY);
?>
<div class="snp-index">
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <p>
                
               <?= Html::button(Yii::t('app', 'Create {modelClass}', [
                   'modelClass' => Yii::t('app', 'Snp'),
               ])
               , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
               , 'data-toggle' => 'modal'
               , 'data-target' => '#modal'

               , 'data-url' => Url::to('create')])
           ?>
                
           </p>
        </div>
    </div>
   
   <?= $this->render('_search', ['model' => $searchModel]); ?>
    
    <?php if ($dataProvider->count > 0){ ?>
    

<!-- ----------------  MENU FLOTANTE   -------------------- -->
    <div id="menu_float"> 
        <a href="javascript: selectAll();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
        <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
        <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a>
        <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
    </div>
<!-- ------------------------------------------------------- -->
    
    
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right">
            <div id="menu_float2">
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
                <a href="#" onclick="return downloadTempalte();" class ="user export template" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Template </a>
                <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
                <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a>
                <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
            </div>
        </div>
    </div>
    
      <?php 
        $columns = [
                  
                    'Snp_Id',
                    'Name',
                    'AdvCm',
                    'AdvLinkageGroup',
                    ['label' => 'Crop', 'value'=>function ($data) {                                       
                           return \common\models\CropSearch::findOne(["Crop_Id"=>$data["Crop_Id"]])->Name;
                                    
                                }, 
                    ],
                    
                    /*[
                        'attribute'=>'publish_date',
                        'filterType'=>GridView::FILTER_DATE,
                        'format'=>'raw',
                        'width'=>'170px',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['format'=>'yyyy-mm-dd']
                        ],
                    ],*/
                    [
                        'class'=>'kartik\grid\BooleanColumn',
                        'attribute'=>'IsActive', 
                        'vAlign'=>'middle',
                    ],
                    [
                        'class'=>'kartik\grid\ActionColumn',
                        'dropdown'=>false,
                        //'order'=>DynaGrid::ORDER_FIX_RIGHT
                    ],
                    ['class'=>'kartik\grid\CheckboxColumn'],
            ];
   ?>


        <?php $dynagrid= DynaGrid::begin([
            'columns'=>$columns,
            'theme'=>'panel-info',
            'showPersonalize'=>true,
            'storage'=>'cookie',
            'enableMultiSort'=> true,
            'showSort' => true,
            'gridOptions'=>[
                'dataProvider'=>$dataProvider,
                'filterModel'=>$searchModel,
                'showPageSummary'=>true,
                'floatHeader'=>true,
                'pjax'=>true,
                'panel'=>[
                    'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i>  Library</h3>',
                    'before' =>  '<div style="padding-top: 7px;"><em>* The table header sticks to the top in this demo as you scroll</em></div>',
                    'after' => false
                ],        
                'toolbar' =>  [
                    ['content'=>
                        Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Book', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['dynagrid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                    ],
                    ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                    '{export}',
                ]
            ],
            'options'=>['id'=>'dynagrid-1'] // a unique identifier is important
        ]);
        if (substr($dynagrid->theme, 0, 6) == 'simple') {
            $dynagrid->gridOptions['panel'] = false;
        }  
        
        DynaGrid::end(); ?>

         <?php /*= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
                'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
                'beforeHeader'=>[
                    [
                        'columns'=>[
                                    ['content'=>'Header Before 1', 'options'=>['colspan'=>8, 'class'=>'text-center warning']], 
                                    
                                    
                 ],
                      //  'options'=>['class'=>'skip-export'] // remove this row from export
                    ]
                ],
                'toolbar' =>  [
                  
                    '{export}',
                    '{toggleData}'
                ],
              
                'toggleDataContainer' => ['class' => 'btn-group-sm'],
                'exportContainer' => ['class' => 'btn-group-sm'],
                'pjax' => true,
                
                //'showPersonalize' => true,
                'bordered' => true,
                'striped' => false,
                'condensed' => true,
                'responsive' => true,
                'hover' => true,
                'floatHeader' => true,
                'enableMultiSort'=> true,
                'showSort' => true,
                //'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
                'showPageSummary' => true,
                'panel' => [
                    'type' => GridView::TYPE_SUCCESS,
                ],
                 
            ]);*/
        ?>
    <?php } else{  echo "<p>No ".  strtoupper(Yii::$app->controller->id)."s found.</p>"; }?>

</div>
 
<script>
    init = function()
	{
                    
         
        var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
      /*  var $pos = 330;
        $win.scroll(function () {
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         }); */
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
         
        }
    exportToExcel = function(){
        if(confirm("Are you sure you want to import all items? This action can take several minutes. "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("#searchSnp").serialize(); return false;
        return false;
   
    };
     exportPartial = function()
    {
         if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            $('#actionsCombo option:first').attr("selected",true);
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("input[name='vCheck[]']").serialize(); return false; 
        };
    };
    
    selectAll = function()
    {   console.log($("input[name='vCheck[]']:checked").length);
        if ( $("input[name='vCheck[]']:checked").length == 0){
		$("input[name='vCheck[]']").prop("checked",true);	
	}else{
		$("input[name='vCheck[]']").prop("checked", false);	
	}
        //return false;
    };
    deleteSelection = function()
    {
          if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            return false;             
        }else{
           if( confirm("Are you sure to delete these items ?"))
                window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/delete-selection?"+$("input[name='vCheck[]']").serialize(); return false; 
        };
        
    }
    
    downloadTempalte = function()
    {
        window.location = "<?= Yii::$app->homeUrl; ?>snp/download-template"; return false; 
    }


</script>