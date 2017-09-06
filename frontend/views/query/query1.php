
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Crop;
use common\models\Cropbyuser;
use miloschuman\highcharts\HighchartsAsset;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use common\models\MapType;
use common\models\MapTypeByCrop;
use yii\helpers\Url;

use common\models\SnpLabSearch;

HighchartsAsset::register($this)->withScripts([]);
HighchartsAsset::register($this)->withScripts(['highstock', 'modules/exporting']);

$this->registerJs('init();', $this::POS_READY);
//$this->registerJs('init2();', $this::POS_LOAD);
?>
<h1>Markers Selection</h1>
<?php //$formu = ActiveForm::begin(); ?>
<?php $formu = ActiveForm::begin([
    'id' => 'searchSnpLab',
    'action' => ['query1'],
    'method' => 'get',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
   
]); ?>
       
<?php if(isset($IsLims)) 
{
   echo '<input type="hidden" name="SnpLabSearch[hiddenField]" id="snplabsearch-hiddenfield" value="'.$IsLims.'">';
   
}   ?>
<div id="filters_query">
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
         
            
          <!--  <div class="col-md-2">
                
            </div> -->
            <div class="col-md-2">
                <?= $formu->field($searchModel, 'MarkerType')->dropDownList([1=> "SNP", 2 => "MicroSatellite",3=>"Both"  ]) ?>
            </div>
             <div class="col-md-2" style="<?= Yii::$app->session->get('cropId') != null ? 'display:none;' : '' ?>">
                <?= $formu->field($searchModel, 'Crop')->dropDownList(ArrayHelper::map(Cropbyuser::getCropsByUser(), 'Crop_Id', 'crop.Name'), ['prompt'=>'-- Select Crop --']) ?>
            </div>
            
            <div class="col-md-3">
         <?php  echo $formu->field($searchModel, 'MapTypeId')->widget(DepDrop::classname(), [
                                                                                //'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => $searchModel->MapTypeId != ""? ArrayHelper::map($mapTypes, "MapTypeId", "mapType.Name"  ): [], //hay que meterle un array helper no queda otra
                                                                                //'options' => ['class' => 'oranges'],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['snplabsearch-crop'],
                                                                                'placeholder'=>'Select...',
                                                                                'loading' => false,
                                                                                'url'=>Url::to(['/map/maps-types'])
                                                                                ],
                                                                                
                                                                    ]); 
        ?>
        </div>
        <div class="col-md-3">
         <?php 
        echo $formu->field($searchModel, 'Map')->widget(DepDrop::classname(), [
                                                                                //'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => [$searchModel->Map => Yii::$app->session['map']],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['snplabsearch-maptypeid','snplabsearch-crop'],
                                                                                'placeholder'=>'Select',
                                                                                'loading' => false,
                                                                                'url'=>Url::to(['/map/maps-availables'])
                                                                                ],
                                                                                
                                                                    ]); 
        ?>
        </div>
            <div class="col-md-2">										
                <?= $formu->field($searchModel, 'Pagination')->dropDownList(['50' => '50', '100' => '100', '500' => '500', 'false' => 'All']) ?>
            </div>
            
            
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
         
            <div class="col-md-12">
                <?= $formu->field($searchModel, 'batch')->textInput(["placeholder" => "Insert SNP LABS separated by non-breaking space (' '), ',' or ';'  "]) ?>
            </div>
            
        </div>
        
    </div>
    <a class="collapse-reclamo asd" data-toggle="collapse" href="#more-filters" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
    <div class="collapse" id="more-filters">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


                 <div class="col-md-10">
                    <?= $formu->field($searchModel, 'PurchaseSequence') ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12col-lg-12">
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'LinkageGroupFrom') ?> 
                </div>            
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'LinkageGroupTo') ?>
                </div>
                <div class="col-md-1">
                    
                </div>
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'PositionFrom') ?> 
                </div>
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'PositionTo') ?>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'AlleleFam')->dropDownList(['A' => 'A', 'T' => 'T'], ['prompt'=>' Select Allele Fam ']) ?>
                </div>
                <div class="col-md-2">
                    <?= $formu->field($searchModel, 'AlleleVicHex')->dropDownList(['C' => 'C', 'G' => 'G'], ['prompt'=>'Select Allele Vic Hex']) ?>
                </div>
                <div class="col-md-3">
                    <?= $formu->field($searchModel, 'assayBrandName') ?>
                </div>
                
                <div class="col-md-3">
                     <?= $formu->field($searchModel, 'Quality') ?>
                </div>
            </div>
        </div>
    </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="mar2">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-nuevo-reclamo  btn-nuevo-create']) ?>
            <a href="#" class='btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create' onclick="return reset();" > Reset</a>
        <?php //= Html::resetButton (Yii::t('app', 'Reset'), ['class' => 'btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create']) ?>

     </div>
    
    
</div>
            
<?php if(!$hasMap && isset(Yii::$app->session['cropId'])){?>
        <div id="divBlack2">
            <h1> There are no Maps loaded</h1>
        </div>
<?php }?>
<?php ActiveForm::end(); ?>
<?php if($dataProvider != "") { ?>

<div id="container"></div>
    <div class="row">
        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 pull-right">
            <div id="menu_float"> 
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
                <a href="#" onclick="return regraphic();" class ="user export" ><span class="glyphicon glyphicon-equalizer size12" aria-hidden="true"> </span> Redraw</a>
                <div class="btn-group margin-up">
                    <button type="button" class="user export dropdown-toggle"
                    data-toggle="dropdown">
                    Export Selection <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="return exportPartial(1);">Purchase</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="return exportPartial(2);">Box Search</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="return exportPartial(3);">All</a></li>
                    </ul>
                </div>
                <div class="btn-group margin-up">
                    <button type="button" class="user export dropdown-toggle"
                            data-toggle="dropdown">
                      Export All <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                    </button>

                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#" onclick="return exportToExcel(1);">Purchase</a></li>
                      <li class="divider"></li>
                      <li><a href="#" onclick="return exportToExcel(2);">Box Search</a></li>
                      <li class="divider"></li>
                      <li><a href="#" onclick="return exportToExcel(3);">All</a></li>
                    </ul>
                </div>
                <a href="#" onclick="return deleteSelection();" class ="user export" > <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
    
            </div>
            
            
            <div id="menu_float2">
                      <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
                     <!-- <a href="#" onclick="return downloadTempalte();" class ="user export template" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Template </a> -->
                      <a href="#" onclick="return regraphic();" class ="user export" ><span class="glyphicon glyphicon-equalizer size12" aria-hidden="true"> </span> Redraw</a>
                      
                   <div class="btn-group margin-up">
                        <button type="button" class="user export dropdown-toggle"
                                data-toggle="dropdown">
                          Export Selection <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#" onclick="return exportPartial(1);">Purchase</a></li>
                          <li class="divider"></li>
                          <li><a href="#" onclick="return exportPartial(2);">Box Search</a></li>
                          <li class="divider"></li>
                          <li><a href="#" onclick="return exportPartial(3);">All</a></li>
                        </ul>
                   </div>
                   <div class="btn-group margin-up">
                        <button type="button" class="user export dropdown-toggle"
                                data-toggle="dropdown">
                          Export All <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#" onclick="return exportToExcel(1);">Purchase</a></li>
                          <li class="divider"></li>
                          <li><a href="#" onclick="return exportToExcel(2);">Box Search</a></li>
                          <li class="divider"></li>
                          <li><a href="#" onclick="return exportToExcel(3);">All</a></li>
                        </ul>
                   </div>
                
                
                <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
                    </ul>
            </div>
        </div>
    </div>
<?php if($selectedDataProvider != null) 
    { 
        ?>
    
   <?php    foreach($selectedDataProvider->getModels() as $sel )
          $array_selected[] = $sel['Marker_Id'];
       
       Yii::$app->session->set('sd', $array_selected);
    }else{unset( Yii::$app->session['sd'] );} ?>
<?= 
    GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => [
            
        [
           	'label'=>'',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                       if( Yii::$app->session['sd'])
                                       {
                                        if( in_array( $data['Marker_Id'] ,  Yii::$app->session['sd']))
                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" checked>';
                                        else
                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" >';
                                       }else
                                           return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" >';
					},
        ],
        ['class' => 'frontend\widgets\RowLinkColumn'],
        // 'Snp_lab_Id',
                                               
        'Name',
        [
            'label'=>'LabName',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['LabName'] == null ? "<kbd>None</kbd>" : $data['LabName'];
                                    },
        ],
        ['label'=>'Barcode','value'=>'Number'],
        ['label' => 'cM','value'=>function ($data) {                                       
					return $data['Position'];
					}, 
        ],
        ['label' => 'LG','value'=>function ($data) {                                       
                			return $data['LinkageGroup'];
					}, 
        ],
       'Box',
       'PositionInBox',
        [
            'label'=>'Quality',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['Quality'] == null ? "<kbd>None</kbd>" : $data['Quality'];
                                    },
        ],
    ],
]); ?>
 <div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<script>
	init = function()
	{
            
//             $('#searchSnpLab').yiiActiveForm({
//                    "afterValidate": function(e, m){ 
//                    console.log(e);
//                    console.log(m);
//                   }
//                       });
         
        var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
       
        $win.scroll(function () {
           if($("#more-filters").css('height')== "270px")
                var $pos = 600;
            else
                var $pos = 1000;
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         });
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
         
         $('#searchSnpLab').submit(function(e){ 
                $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
         });
         /******************* higcharts***********************/       
	$('#container').highcharts({
        credits:{'enabled':false},  
        chart: {
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: 'SNP'
        },
        xAxis: {
            title: {
                enabled: true,
                text: 'Chromosome'
            },
            startOnTick: true,
            endOnTick: true,
            showLastLabel: true,
            tickInterval: 1,
            min: 1,
            max: <?= count($limitsXcM) ?>,
            allowDecimals: false,
        },
        yAxis: {
            
            min: 0,
            //tickInterval: 10,
            title: {
                text: 'cM'
            }
        },
        legend: {
            enabled: false,
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 100,
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            borderWidth: 1
        },
        plotOptions: {
            scatter: {
                marker: {
                    radius: 5,
                    // symbol: 'square',
                    states: {
                        hover: {
                            enabled: true,
                            lineColor: 'rgb(100,100,100)'
                        }
                    }
                },
                states: {
                    hover: {
                        marker: {
                            enabled: false
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: '<b>Chromosome:</b> {point.x} <br/><b>Position:</b> {point.y} cM'
                }
            },
            series:
            {
                turboThreshold: 0
            }
        },
        series: 
        [
         /****************  MAXIMOS  ****************/
     <?php if(isset($limitsXcM)): ?>
        {
            //name: 'Max cM',
            color: 'rgba(255, 0, 0, .7)',
           
            data: [
            <?php
                $Linkage =1;
                $cont = 0;
                foreach($limitsXcM as $limit)
                {
                    echo '{name: "Max", x: ' .$limit['LinkageGroup'].', y: ' .$limit ['MAX(Position)'].'},';
                   $Linkage++; 
                }
            ?>//
            ],
            marker:{
                symbol:"triangle-down",
                radius:7,
            }
           
        },
        
        <?php endif;?>
    
        {
            color: 'rgba(119, 152, 191, .6)',
            data: 
            [
            <?php
                $dataProvider->setPagination(false);
                $dataProvider->refresh();
                $data = $dataProvider->getModels();
                foreach($data as $marker)
                {
                    $ll = $marker['Position'] == '' ? 0 :$marker['Position'];
                    $ll2 = $marker['LinkageGroup'] == '' ? 0 :$marker['LinkageGroup'];
                    echo '{name: "'.$marker['LabName'].'", x: ' .$ll2 .', y: ' .$ll .'},';
                }
                
            ?>//
            ],
            marker:{
                radius:4,
            }
        }, 
        <?php if(isset($selectedDataProvider)):
            $selectedDataProvider->setPagination(false);
            $selectedDataProvider->refresh();
            $data2 = $selectedDataProvider->getModels();?>//
            {
                //name: 'Max cM',
                color: 'rgba(255, 137, 0, .7)',
               
                data: [
                <?php
                foreach($data2 as $marker2)
                {
                   
                    foreach($marker2->mapResults as $mr )
                    {
                        $cero = $mr->Position == '' ? 0 : $mr->Position;
                        $cero2 = $mr->LinkageGroup == '' ? 0 : $mr->LinkageGroup;
                    }
                   
                    echo '{name: "'.$marker2->Name.'", x: ' .$cero2 .', y: ' .$cero .'},';
                }
            ?>//
            ],
            marker:{
                symbol:"circle",
                radius:8,
            }
           
        }
        
        <?php endif;?>
   
        ]
    });
    
    }
            
    exportToExcel = function(id){
        
        if(confirm("Are you sure you want to import all items? This action can take several minutes. "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("#searchSnpLab").serialize()+"&id="+id; return false;
        return false;
    }
    regraphic = function()
    {
        if ($("input[name='vCheck[]']:checked").length == 0){
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/query1";
           // $('#actionsCombo option:first').attr("selected",true);
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/search-by-select?"+$("input[name='vCheck[]']").serialize()+"&"+$("#searchSnpLab").serialize(); return false; 
        };
    }
    
    exportPartial = function(id)
    {
         if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            $('#actionsCombo option:first').attr("selected",true);
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("input[name='vCheck[]']").serialize()+"&id="+id; return false; 
        };
    }
    
    selectAll = function()
    {   //console.log($("input[name='vCheck[]']:checked").length);
        if ( $("input[name='vCheck[]']:checked").length == 0){
		$("input[name='vCheck[]']").prop("checked",true);	
	}else{
		$("input[name='vCheck[]']").prop("checked", false);	
	}
        //return false;
    }
    
    deleteSelection = function()
    {
          if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            return false;             
        }else{
           if( confirm("Are you sure to delete these items ?"))
                window.location = "<?= Yii::$app->homeUrl; ?>snplab/delete-selection?"+$("input[name='vCheck[]']").serialize(); return false; 
        };
        
    }
    
    downloadTempalte = function()
    {
        window.location = "<?= Yii::$app->homeUrl; ?>snplab/download-template"; return false; 
    }
    
    reset = function()
    {
        $('#searchSnpLab').find("input[type=text], textarea").val("");
        $('#searchSnpLab').find("select").val("");
        window.location=  "<?= Yii::$app->homeUrl; ?>query/query1";
    }
</script>
<?php 
    }else{  echo "<h1><p>No Markers  found</p></h1>";
?>
<div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<script>
    
init = function()
{
    
$('#searchSnpLab').submit(function(e){ 
        
                $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
              //  console.log(e); return false;
               // alert("de afuera"); 
                var has_error = $("#filters_query").find(".has-error").text();
                if(!has_error)
                    $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
         });
        <?php if(Yii::$app->session->get('cropId') != null ): ?>
            <?php if(!$dataProvider): ?>
               
               setTimeout(function(){
                $('#snplabsearch-crop').trigger('change');
                }, 100);
    
            <?php endif;?>
        <?php endif;?>
}
reset = function()
{
    $('#searchSnpLab').find("input[type=text], textarea").val("");
    $('#searchSnpLab').find("select").val("");
    
    window.location=  "<?= Yii::$app->homeUrl; ?>query/query1";
}




</script>
<?php } ?>