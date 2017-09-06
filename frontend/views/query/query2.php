<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\HighchartsAsset;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\MarkerType;
use common\models\MapType;
use common\models\MapTypeByCrop;
use common\models\Crop;
use common\models\Cropbyuser;
//use common\models\Fingerprint;
//use common\models\FingerprintSearch;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use common\models\SnpLabSearch;

HighchartsAsset::register($this)->withScripts([]);
HighchartsAsset::register($this)->withScripts(['highstock', 'modules/exporting']);
$this->registerJs('init();', $this::POS_READY);
?>
<?php $form = ActiveForm::begin([
    'action' => ['query2'],
    'method' => 'get',
    'id'     => 'Polymorfism',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
]); ?>
<h1> Polymorphism Search</h1>

<div id="menu_float"> 
    <a href="javascript: selectAll();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
    <a href="#" onclick="return regraphic();" class ="user export" ><span class="glyphicon glyphicon-equalizer size12" aria-hidden="true"> </span> Redraw</a>
    <div class="btn-group margin-up">
        <button type="button" class="user export dropdown-toggle"
                data-toggle="dropdown">
          Export Selection <span class="glyphicon glyphicon-triangle-bottom size12"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
         <!-- <li><a href="#" onclick="return exportPartial(1);">FP</a></li> -->
          
          <li><a href="#" onclick="return exportPartial(1);">Box Search</a></li>
          <li class="divider"></li>
          <li><a href="#" onclick="return exportToExcel(2);">FP</a></li>
        </ul>
    </div>
    <div class="btn-group margin-up">
         <button type="button" class="user export dropdown-toggle"
                 data-toggle="dropdown">
           Export All <span class="glyphicon glyphicon-triangle-bottom size12"></span>
         </button>

         <ul class="dropdown-menu" role="menu">
           <li><a href="#" onclick="return exportToExcel(1);">Box Search</a></li>
           <li class="divider"></li>
           <li><a href="#" onclick="return exportToExcel(2);">FP</a></li>
           
         </ul>
    </div>
    <!-- <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a> -->
    
</div>

    <div id="filters-confondo">
    
    <div class="row">
        <div class="col-md-2">
          <?= $form->field($Fingerprint, 'Method')->dropDownList([1=>'Polymorphic', 2=>'Monomorphic', 3=> 'Both']) ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($Fingerprint, 'MarkerType')->dropDownList([1=> "SNP", 2 => "MicroSatellite",3=>"Both"  ]) ?>
        </div>
        <div class="col-md-2" style="<?= Yii::$app->session->get('cropId') != null ? 'display:none;' : '' ?>" >
                       <?= $form->field($Fingerprint, 'Crop')->dropDownList(ArrayHelper::map(Cropbyuser::getCropsByUser(), 'Crop_Id', 'crop.Name'), ['prompt'=>'-- Select Crop --']) ?>
        </div>
        <div class="col-md-2">
         <?php 
            echo $form->field($Fingerprint, 'MapTypeId')->widget(DepDrop::classname(), [
                                                                                //'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => $mapsType != ""? ArrayHelper::map($mapsType, "MapTypeId", "mapType.Name"  ): [], //hay que meterle un array helper no queda otra
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['fingerprintsearch-crop'],
                                                                                'placeholder'=>'Select...',
                                                                                'Loading' => false,
                                                                                'url'=>Url::to(['/map/maps-types'])
                                                                                ],
                                                                                
                                                                    ]); 
        ?>
        </div>
        <div class="col-md-3">
         <?php 
        echo $form->field($Fingerprint, 'Map')->widget(DepDrop::classname(), [
                                                                                //'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => [$Fingerprint->Map => Yii::$app->session['map']],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['fingerprintsearch-maptypeid','fingerprintsearch-crop'],
                                                                                'placeholder'=>'Select',
                                                                                'loading' => false,
                                                                                'url'=>Url::to(['/map/maps-availables'])
                                                                                ],
                                                                                
                                                                    ]); 
         ?>
        </div>
        
    </div> 
    <div class="row">
        
        <div class="col-md-3">
          <?php //= $form->field($Fingerprint, 'Fingerprint_Material_Id')->dropDownList(ArrayHelper::map($materials, 'id', 'name'), ['prompt'=>'-- Select Line --',"onChange" => "js:materials(this)"]) ?>
        <?php 
        echo $form->field($Fingerprint, 'Fingerprint_Material_Id')->widget(DepDrop::classname(), [
                                                                                'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => [$Fingerprint->Fingerprint_Material_Id => Yii::$app->session['material1']],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['fingerprintsearch-crop'],
                                                                                'placeholder'=>'Select',
                                                                                'loading'=>false,
                                                                                'url'=>Url::to(['/query/fp_materials-by-crop'])
                                                                                ],
                                                                                
                                                                    ]); 
        ?>
        </div>
        <div class="col-md-3">
        <?php     echo $form->field($Fingerprint, 'Fingerprint_Material_Id2')->widget(DepDrop::classname(), [
                                                                                'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => [$Fingerprint->Fingerprint_Material_Id2 => Yii::$app->session['material2']],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['fingerprintsearch-crop'],
                                                                                'placeholder'=>'Select',
                                                                                'loading' => false,
                                                                                'url'=>Url::to(['/query/fp_materials-by-crop'])
                                                                                ],
                                             
                                                                    ]); 
        ?>
         <?php //= $form->field($Fingerprint, 'Fingerprint_Material_Id2')->dropDownList(ArrayHelper::map($materials, 'id', 'name'), ['prompt'=>'-- Select Line --', "onChange" => "js:materials(this)"]) ?>
        </div>
        
    </div>
    
    <div class="row" >
        
        
        <div class="col-md-3">
            <div id="result"></div>
        </div>
        
        <div class="col-md-3">
            <div id="result2"></div>
        </div>
      
    </div>
</div>
<div class="col-md-12 exa ">
    <div class="pull-right">
        <a href="#" onclick="return reset();" class="btn btn-gray-clear btn-nuevo-reclamo2"> Reset</a>
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-nuevo-reclamo2']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php if(!$hasFP){?>
        <div id="divBlack2">
            <h1> There are no FingerPrints loaded</h1>
        </div>
<?php }?>
<?php if($dataProvider){?>

<div id="container"></div> <!-- highcharts -->


<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div id="menu_float2"> 
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
                <a href="#" onclick="return regraphic();" class ="user export" ><span class="glyphicon glyphicon-equalizer size12" aria-hidden="true"> </span> Redraw</a>
                <div class="btn-group margin-up">
                    <button type="button" class="user export dropdown-toggle" accesskey="" data-toggle="dropdown">
                        Export Selection <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                       <!-- <li><a href="#" onclick="return exportPartial(1);">FP</a></li> -->
                        <li><a href="#" onclick="return exportPartial();">Box Search</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="return exportToExcel(2);">FP</a></li>
                    </ul>
                 </div>
                 <div class="btn-group margin-up">
                    <button type="button" class="user export dropdown-toggle"
                            data-toggle="dropdown">
                      Export All <span class="glyphicon glyphicon-triangle-bottom size12"></span>
                    </button>
                <ul class="dropdown-menu" role="menu">
                   <li><a href="#" onclick="return exportToExcel(1);">Box Search</a></li>
                   <li class="divider"></li>
                   <li><a href="#" onclick="return exportToExcel(2);">FP</a></li>
                </ul>
                </div>
                <!-- <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a> -->
                
            </div>
        </div>
    
</div>
<?php if($selectedDataProvider != null)
    { 
    
       foreach($selectedDataProvider->getModels() as $sel )
          $array_selected[] = $sel->Marker_Id;
       
       Yii::$app->session->set('sd', $array_selected);
 }else{unset( Yii::$app->session['sd'] );} ?>
      
<?=  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $Fingerprint ,
    'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
    'columns' => [
         ['class' => 'frontend\widgets\RowLinkColumn'],
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
       
        //'Marker_Id',
        ['label'=>'Marker Name','value'=>'Name'],
        //['label'=>'Lab Name','value'=>'LabName' == NULL ? 1: 2],
        [
            'label'=>'Lab Name',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['LabName'] == null ? "<kbd>None</kbd>" : $data['LabName'];
                                    },
        ],
        [
            'label'=>'Box',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['Box'] == null ? "<kbd>None</kbd>" : $data['Box'];
                                    },
        ],
        [
            'label'=>'PositionInBox',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['PositionInBox'] == null ? "<kbd>None</kbd>" : $data['PositionInBox'];
                                    },
        ],
        //'PositionInBox',
        //'PIC',
        [
            'label'=>'PIC',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['PIC'] == null ? "<kbd>None</kbd>" : $data['PIC'];
                                    },
        ],
        //'Quality',
        'Position',
        'LinkageGroup',                                   
        [
                    'label'=>'Result',
                    'format' => 'raw',
                    'visible' => $both, 
                    'value'=>function ($data) {

                                    return $data['Result'] == 1 ? "Polymorphic" : "Monomorphic";
                                    },
        ],
        [
            'label'=>'Quality',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['Quality'] == null ? "<kbd>None</kbd>" : $data['Quality'];
                                    },
        ],
    ],
]); ?>



<?php  } ?>
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
        /**********************   Dinamic radios by FP Name*****************************/
     
             <?php if($fp_material1 != "" or $fp_material2 != ""): ?>
                    
            if($("#fingerprintsearch-fingerprint_material_id").val() != ""  )
            {
                
                var $fp_id1= $("#fingerprintsearch-fingerprint_material_id").val();
                                 
                        $("#result").show();
                        var divRes  = $('<div/>').attr('id', 'result1');
                        var idFp    = "<?= $fp_material1["Fingerprint_Id"]  ?>";
                        var tagId   = 1;
                         divRes.css('background-color','#B2F269');

                        divRes.html("<input type='radio' value='<?= $fp_material1["Fingerprint_Material_Id"] ?>' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$fp_id1+","+idFp+",\""+tagId+"\")' checked />\n\
                                   Fingerprint:<strong><?= $fp_material1["Name"] ?></strong>\n\
                                    <br>"); 
                        $("#result").append(divRes);               
            }
            
            if($("#fingerprintsearch-fingerprint_material_id2").val() != "" )
            {
                var $fp_id1= $("#fingerprintsearch-fingerprint_material_id2").val();
                        
                        $("#result2").show();
                        var divRes  = $('<div/>').attr('id', 'result1');
                        var idFp    = "<?= $fp_material2["Fingerprint_Id"] ?>";
                        var tagId   = 2;
                        divRes.css('background-color','#B2F269');
                        
                        divRes.html("<input type='radio' value='<?= $fp_material2["Fingerprint_Material_Id"] ?>' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$fp_id1+","+idFp+",\""+tagId+"\")' checked />\n\
                                   Fingerprint:<strong><?= $fp_material2["Name"] ?></strong>\n\
                                    <br>"); 
                        $("#result2").append(divRes);               
            }
       <?php endif;?>
 
       $("#Polymorfism").submit(function(e)
                        {
                          $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');}) 
                           
                          if(typeof $("#radio1").val() != "undefined" )
                          { 
                                 if($("#radio1").is(":checked"))
                                 {
                                     $("#result div").css('background-color','#B2F269');
                                 }else
                                 {
                                    $("#result div").css('background-color','#EF4242');
                                     e.preventDefault();
                                     $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});                 
                                 }

                                 if($("#radio2").is(":checked"))
                                 {
                                     $("#result2 div").css('background-color','#B2F269');
                                 }else
                                 {
                                    $("#result2 div").css('background-color','#EF4242');
                                     e.preventDefault();
                                     $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});                    
                                 }

                                  if($("#radio2").is(":checked") && $("#radio1").is(":checked"))
                                     $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})  
                            }
                                  
                        }); 

    /****************************  FLOAT MENU  ***********************************/    
    var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
       
        $win.scroll(function () {
            var i = $("#container").text(); 
            if(i == "")            //if($("#more-filters").css('height')== "270px")
                var $pos = 600;
            else
                var $pos = 900;
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         });
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
	

        /***************  highcharts ************************/
        $('#container').highcharts({
	
        credits:{'enabled':false},  
        chart: {
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: ' Polymorphism ( <?= $dataProvider == NULL ? "" : count($dataProvider->getModels())?> of <?= $totalCompared ?> compared)'
        },
        // subtitle: {
            // text: 'SNP'
        // },
        xAxis: {
            title: {
                enabled: true,
                text: 'Chromosome'
            },
            startOnTick: true,
            endOnTick: true,
            allowDecimals:false,
            tickInterval: 1
            
        },
        yAxis: {
            //allowDecimals:true,
            min: 0,
            tickInterval: 10,
           
            title: {
                text: 'cM'
            },
            //showLastLabel: true
        },
        legend: {
            enabled:false,
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 100,
            y: 70,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            borderWidth: 1
        },
        plotOptions: {
            scatter: {
                marker: {
                    radius: 5,
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
                    pointFormat: '<b>Chromosome:</b> {point.x} <br/><b>Position:</b> {point.y} cm'
                }
            }
        },
	series: [
             /****************  MAXIMOS****************/
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
		
	    color: 'rgba(119, 152, 191, .5)',
            data: [
		
			
           <?php
           if($dataProvider):
                $dataProvider->setPagination(false);
                $dataProvider->refresh();
                $data = $dataProvider->getModels();
                foreach($data as $marker)
                {
                    $ll = $marker['Position'] == '' ? 0 :$marker['Position'];
                    $ll2 = $marker['LinkageGroup'] == '' ? 0 :$marker['LinkageGroup'];
                    echo '{name: "'.$marker['Name'].'", x: ' .$ll2 .', y: ' .$ll .'},';
                }
               endif; 
            ?>//
                ],
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
                
            ],
    });

    // Trigger change event on crop if cropId is in session
     <?php if(Yii::$app->session->get('cropId') != null ): ?>
      <?php if($fp_material1 == ""): ?>
        setTimeout(function(){
        $('#fingerprintsearch-crop').trigger('change');
      }, 100);
    
      <?php endif;?>
    <?php endif;?>
    }
    
    exportToExcel = function(id){
        if(confirm("Are you sure you want to import all items? This action can take several minutes. "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel-polymorfism?"+$("#Polymorfism").serialize()+"&id="+id; return false;
    }
    
    regraphic = function()
    {
        if ($("input[name='vCheck[]']:checked").length == 0){
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/query2";
           // $('#actionsCombo option:first').attr("selected",true);
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/search-by-select?"+$("input[name='vCheck[]']").serialize()+"&"+$("#Polymorfism").serialize(); return false; 
        };
    }
    
    exportPartial = function(id)
    {
         if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            $('#actionsCombo option:first').attr("selected",true);
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel-polymorfism?"+$("input[name='vCheck[]']").serialize()+"&"+$("#Polymorfism").serialize()+"&id="+id; return false; 
        };
    }
    
    selectAll = function()
    {   console.log($("input[name='vCheck[]']:checked").length);
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
    
    materials = function(e)
    {
            var divLoading = $('<div/>').attr('id', 'modalLoading');
            var imgLoading = $('<img/>').attr('src', '../images/loading.gif').attr('width', 30);
            divLoading.append(imgLoading);
            
            if($(e).attr("id") == "fingerprintsearch-fingerprint_material_id" )
            {
               
                if($(e).val() != "" && $(e).val() != "Loading ..." )
                {
                    $.ajax({url: "<?= Yii::$app->homeUrl; ?>query/data-materials?id="+$(e).val(),
                            beforeSend: function() {
                                $("#result").show().html(divLoading);
                            }
                          })
                    .done(function(result) {
                        $("#modalLoading").remove();
                        //console.log(result); return false;
                        for(var i=0; i < result[0].length; i++ )
                        {
                            var divRes  = $('<div/>').attr('id', 'result'+i);
                            var idFp    = result[0][i].Fingerprint_Id;
                            var tagId   = 1;
                           
                            divRes.html("<input type='radio' value='"+$(e).val()+"' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$(e).val()+","+idFp+",\""+tagId+"\")' />\n\
                                       Fingerprint:<strong>"+result[0][i].Name+"</strong><br>Markers:<strong>"+result[1][i]+"</strong>\n\
                                        <br>"); 
                            $("#result").append(divRes);
                        }
                    })
                }else
                {
                    $("#result").hide();
                }
            }else
            {
                if($(e).val() != "" && $(e).val() != "Loading ...")
                {
                    $.ajax({url: "<?= Yii::$app->homeUrl; ?>query/data-materials?id="+$(e).val(),
                            beforeSend: function() {
                                $("#result2").show().html(divLoading);
                            }
                          })
                    .done(function(result) {
                        $("#modalLoading").remove();
                        //console.log(result); return false;
                        for(var i=0; i < result[0].length; i++ )
                        {
                            var divRes  = $('<div/>').attr('id', 'result'+i);
                            var idFp    = result[0][i].Fingerprint_Id;
                            var tagId   = 2;
                           
                            divRes.html("<input type='radio' value='"+$(e).val()+"' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$(e).val()+","+idFp+",\""+tagId+"\")' />\n\
                                       Fingerprint:<strong>"+result[0][i].Name+"</strong><br>Markers:<strong>"+result[1][i]+"</strong>\n\
                                        <br>"); 
                            $("#result2").append(divRes);
                        }
                    })
                }else
                {
                    $("#result2").hide();
                }
                    
//                if($(e).val() != "")
//                {
//                    $.ajax({url: "<?= Yii::$app->homeUrl; ?>query/data-materials?id="+$(e).val(),
//                            beforeSend: function() {
//
//                                $("#result2").show().html(divLoading);
//                            }
//                           })
//                    .done(function(result){
//                     $("#result2").show().html("Fingerprint:<strong>"+result[0].Name+"</strong><br>Snps:<strong>"+result[1]+"</strong>"); 
//                    })
//                }else
//                {
//                    $("#result2").hide();
//                }
                            
            }
    }
    
    alerts = function(idMaterial, idFp, tag)
    {
        //console.log(idMaterial+"--"+idFp+"--"+tag); return false;
        $.ajax( "<?= Yii::$app->homeUrl; ?>query/id-by-fp-material?id="+idMaterial+"&idfp="+idFp+"")

                 .done(function(result) {
                    //console.log(result); return false;

                    $("#radio"+tag+":checked" ).val(result);
                     return false;
                 })
    }
    
    reset = function()
    {
        $('#Polymorfism').find("select").val("");
        //document.reload();
        window.location=  "<?= Yii::$app->homeUrl; ?>query/query2"
    }
    
    
    
</script>
