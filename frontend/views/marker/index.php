<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
//use kartik\dynagrid\DynaGrid;
//use kartik\grid\GridView;
//use kartik\grid\ActionColumn;
//use kartik\grid\SerialColumn;



//$this->registerJs('init();', $this::POS_READY);
/* @var $this yii\web\View */
/* @var $searchModel common\models\MarkerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Markers');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('init();', $this::POS_READY);
?>
<div class="marker-index">
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <p>
                
               <?= Html::button(Yii::t('app', 'Create {modelClass}', [
                   'modelClass' => Yii::t('app', 'Marker'),
               ])
               , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
               , 'data-toggle' => 'modal'
               , 'data-target' => '#modal'
               , 'data-url' => Url::to('create')])
           ?>
                
           </p>
        </div>
    </div>
   
   <?= $this->render('_search', ['model' => $searchModel, 'fromDashboard' => $fromDashboard]); ?>
    
    <?php if ($dataProvider != null){ ?>
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
        <?php } ?>
     <?php  Pjax::begin(['id' => 'itemList']) ; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        
        'columns' => [
            [
           	'label'=>'',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                        
					 return '<input type="checkbox" name="vCheck[]" value="'.$data["Marker_Id"].'" >';
					},
            ],
            ['class' => 'frontend\widgets\RowLinkColumn'],
            //'Marker_Id',
            'Name',
           
            //'AdvCm',
            /*[
           	'label'=>'Shortsequence',
           	'format' => 'raw',
       		'value'=>function ($data) {
                                        
					return substr($data->ShortSequence, 0, 30 )."...";
					},
            ],
            /*[
           	'label'=>'LongSequence',
           	'format' => 'raw',
       		'value'=>function ($data) {
                                        
					return substr($data->LongSequence, 0, 30 )."...";
					},
            ],*/
            ['label' => 'Crop', 'value'=>function ($data) {                                       
                           return \common\models\CropSearch::findOne(["Crop_Id"=>$data["Crop_Id"]])->Name;
                                    
                                }, 
            ],
            ['label' => 'Marker Type', 'value'=> function ($data) {         
                                    
                                return \common\models\MarkerType::findOne(["Marker_Type_Id"=>$data["Marker_Type_Id"]])->Name;
                           }, 
            ],                     
            //'Marker_Type_Id',
                                        
           // ["label" => "Adv cM", "value" => 'AdvCm'],
            
            //'PhysicalPosition',
            // 'IsActive',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php  Pjax::end(); ?>

    <?php } else{  echo "<p>Please complete the necessary filters and click Search </p>"; }?>

</div>
 
<script>
    
    init = function()
	{
                    
         
        var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
       var $pos = 330;
        $win.scroll(function () {
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         });
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
         
        }
    exportToExcel = function(){
        if(confirm("Are you sure you want to import all items? This action can take several minutes. "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("#searchMarker").serialize(); return false;
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
        if ( $("input[name='vCheck[]']:checked").length == 0)
        {
		$("input[name='vCheck[]']").prop("checked",true);	
	}else
        {
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
        window.location = "<?= Yii::$app->homeUrl; ?>marker/download-template"; return false; 
    }
    
    reset = function()
    {
        $('#searchMarker').find("input[type=text], textarea").val("");
        $('#searchMarker').find("select").val("");
    }


</script>