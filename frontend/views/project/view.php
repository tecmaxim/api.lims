<?php
use yii\jui\Accordion;
use yii\widgets\DetailView;
//use common\models\StepProject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;


//$this->registerJs('loadingShow();', $this::POS_HEAD );
$this->registerJs('init_viewproject('.$model->ProjectId.',"'.Yii::$app->request->baseUrl.'");',$this::POS_READY);

?>
<div id="divBlack" style="display:inline; overflow-y: hidden ">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<div class="map-index">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7" style="padding-left:80px !important;">
            <h1><?= $model->Name ?></h1>       
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left:80px !important;">
            
            <?=  Html::button(Yii::t('app', 'On Hold'),[
                        'class' => 'btn btn-warning btn-action-project',
                        //'id' => 'onHold_button',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['on-hold', 'id' => $model->ProjectId]),
                        // 'data' => [
                        //    'method' => 'post',
                        // ],
                        ]) ?>
        <?php if ($model->StepProjectId >= 1 && $model->StepProjectId < \common\models\StepProject::GENOTYPED) : ?>
                    
                 <?=  Html::button(Yii::t('app', 'Cancel'),[
                        'class' => 'btn btn-default btn-action-project grey-ccc reset',
                        //'id' => 'onHold_button',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['cancel', 'id' => $model->ProjectId]),
                        // 'data' => [
                        //    'method' => 'post',
                        // ],
                    ]) ?>
            
        <?php endif; ?>
        
        <?php if($model->StepProjectId >= \common\models\StepProject::PROJECT_DEFINITION && $model->StepProjectId < \common\models\StepProject::SENT): ?>
            
                 <?=  Html::button(Yii::t('app', 'Delete Project'),[
                        'class' => 'btn btn-danger btn-action-project',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['delete', 'id' => $model->ProjectId]),
                        // 'data' => [
                        //    'method' => 'post',
                        // ],
                    ]) ?>
            
        <?php elseif ($model->StepProjectId >= \common\models\StepProject::GENOTYPED && $model->StepProjectId < \common\models\StepProject::ON_HOLD): ?>
                 <?=  Html::button(Yii::t('app', 'Finish Project'),[
                        'class' => 'btn btn-action-project brown',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['finish', 'id' => $model->ProjectId]),
                        // 'data' => [
                        //    'method' => 'post',
                        // ],
                    ]) ?>
        <?php endif; ?>
        </div>
        
    </div>
    <?= Accordion::widget() ?>
        <div id="accordion" style="display:none">  
            <div class="row wer"> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Project Definition </div> 
                 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action"> 
                    <?php if($model->StepProjectId >= 1 ): ?> 
                            <span class='glyphicon glyphicon-ok pull-right'></span>
                            <?php if($model->StepProjectId < \common\models\StepProject::SENT ): ?>
                                <button id="<?= Yii::$app->homeUrl ?>project/update?id=<?= $model->ProjectId ?>" class='btn-edit-project pull-right'>
                                  <span class='glyphicon glyphicon-pencil'></span>
                                </button>
                            <?php endif; ?>
                    <?php else: ?>
                            <input type="button" id="<?= $model->StepProjectId; ?>?id=<?= $model->ProjectId; ?>" value='To Do' class='buton-action gray-888-text pull-right' />
                    <?php endif; ?>
                </div>
            </div> <!-- header project -->              
            <div style="clear: both;">
                <div style="  float: right; width: 95%; padding: 10px">
                    <table id="detail-project">
                    <tr >
                        <th >Project Name:</th><td style="width: 40%"> <?= $model->Name?></td>
                        <th >Project Code: </th><td><?= $model->ProjectCode ?></td>
                    </tr>                  
                   
                    <tr>
                        <th>Project Type:</th><td> <?= $model->projectType->Name?></td>
                        <th>Priority: </th><td><?= $model->getPriority() ?></td>
                    </tr>
                    <tr>
                        <th>Dead Line: </th><td><?= $model->DeadLine ?></td>
                        <th>Research Station:</th> <td><?= $model->researchStation->Short ?></td>
                    </tr>
                    <tr>
                        <th>Flowering Expected Date:</th><td> <?= $model->FloweringExpectedDate?></td>
                        <th>Sowing Date:</th><td> <?= $model->SowingDate ?></td>
                    </tr>
                    <tr>
                        <th> Materials:</th>
                        <td > 
                            <div style="max-height: 300px !important; overflow-y: auto;">
                                <?php 
                                    $array_mats = "";

                                    foreach($model->materialsByProjects as $mats)
                                    {
                                        if($mats->ParentTypeId == 3)
                                            $array_mats .="<div class='borded'>".$mats->materialTest->Name."</div> ";
                                        else
                                        {
                                            $array_mats .="<div class='borded'>".$mats->materialTest->Name." (".$mats->parentType->Type.") ";
                                            $traits = \common\models\TraitsByMaterials::getTraitsByMaterialByProjectAsString( $mats->MaterialsByProject);
                                            $array_mats .= "<b>Traits</b>: ".$traits;
                                            $array_mats .= "</div> ";
                                        }
                                    }
                                    echo $array_mats == "" ? "Not asigned":$array_mats;
                                ?>
                            </div>
                        </td>
                        <th> Users: </th>
                        
                        <td> 
                            <?= $model->user->Username; ?>
                            <?php
                                foreach($model->userByProjects as $user)
                                {
                                    echo ', '.$user->user->Username;
                                }
                            ?>
                        </td>
                    </tr>
                    </table>
                </div>
                <div style="clear: both;"></div>
            </div> <!-- content project -->  
            <div class="row wer">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Markers Selected</div> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action"> 
                        <?php if($model->StepProjectId >= 2 ): /*Marker Selection*/?> 
                            <?php if($model->markersByProjects != null): ?>
                                <span class='glyphicon glyphicon-ok pull-right'></span> 
                                <?php if($model->StepProjectId < \common\models\StepProject::SENT ): /*Marker Selection*/?> 
                                    <button id="<?= Yii::$app->homeUrl ?>project/update-select-markers?idProject=<?= $model->ProjectId ?>" class='btn-edit-project pull-right'>
                                      <span class='glyphicon glyphicon-pencil'></span>
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                              <span class='glyphicon glyphicon-remove pull-right'></span>
                              <button id="<?= Yii::$app->homeUrl; ?>project/update-select-markers?idProject=<?= $model->ProjectId; ?>" class='btn-edit-project pull-right'>
                                <span class='glyphicon glyphicon-pencil'></span>
                              </button>
                            <?php endif; ?>
                        <?php else: ?>
                              <input type='button' id="<?=\common\models\StepProject::PROJECT_DEFINITION ?>?id=<?= $model->ProjectId;?>" value='To Do' class='buton-action gray-888-text pull-right' />
                        <?php endif; ?>      
                </div>
            </div> <!-- header markers -->  
            <div style="clear: both; height:auto !important;">
                <div class="row">
                    <?php if(isset($markers)): ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php
                                    $i = 0;
                                    $total = 0;
                                    $lenght = count ($markers);
                                    foreach($markers as $m)
                                    {
                                        if($i ==0)
                                            echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'
                                                 .'<ul>';

                                        echo "<li><b>Marker</b>: ".$m['snpLab']['LabName']."<br>";
                                        $traitsByMarker = common\models\TraitsByMarkersByProject::getTraitsByMarkersByProjectAsArray($m['MarkersByProjectId']);
                                        echo "<b>Traits: </b>".$traitsByMarker;
                                        echo "</li>";
                                        $i++;
                                        $total++;

                                        if($i == 10 || $total == $lenght)
                                        {   
                                            echo '</ul>'
                                               . '</div>';
                                            $i = 0;
                                        }
                                    }
                                ?>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
                            </div>
                            
                       <?php endif; ?>
                </div>
            </div><!-- content markers -->  
            <div class="row wer">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Preview Grid Definition </div> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action">  
                    <?php if($model->StepProjectId >= 3): ?>
                        <span class='glyphicon glyphicon-ok pull-right'></span>
                        <?php if($model->StepProjectId < \common\models\StepProject::SENT): ?>
                            <button id="<?= Yii::$app->homeUrl;?>project/update-grid-definition?idProject=<?= $model->ProjectId;?>" class='btn-edit-project pull-right'>
                              <span class='glyphicon glyphicon-pencil'></span>
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <input type='button' id="<?= common\models\StepProject::MARKERS_SELECTION ?>?id=<?= $model->ProjectId;?>" value='To Do' class='buton-action gray-888-text pull-right' />
                    <?php endif; ?>
                </div>
            </div>
            <div id="GridVisualizator">
                <?php if($model->StepProjectId >= 3):?>
                    <?= $this->render('_gridPreview',['model' => $model, 
                                                  'samplesByProject'=>$samplesByProject,
                                                  'numLastSampleByPlate'=>$numLastSampleByPlate,
                                                  'parents'=>$parents,
                                                ])?>
                <?php endif; ?>
            </div>
            <div class="row wer">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Sample Dispatch </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action"> 
                    <?php if($model->StepProjectId >= 5): ?>
                        <span class='glyphicon glyphicon-ok pull-right'></span>
                        <?php if($model->StepProjectId < \common\models\StepProject::SAMPLE_RECEPTION): ?>
                            <button id='<?=Yii::$app->homeUrl; ?>project/update-sample-dispatch?idProject=<?= $model->ProjectId; ?>' class='btn-edit-project pull-right'>
                              <span class='glyphicon glyphicon-pencil'></span>
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <input type='button' id='<?= $model->StepProjectId;?>?id=<?= $model->ProjectId;?>' value='To Do' class='buton-action gray-888-text pull-right' />
                    <?php endif; ?>
                </div>
            </div>
            <div style="height:auto !important;">
                 <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <?php
                            if($dispatch)
                            {
                                echo DetailView::widget([
                                    'model' => $dispatch,
                                    'attributes' => [
                                        //'Marker_Id',
                                        'Carrier',
                                        'TrackingNumber',
                                        'Date',
                                    ],
                                ]); 
                            }
                        ?>
                    </div>
                 </div>
            </div>
            <div class="row wer"> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Sample Reception </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action"> <?=$model->StepProjectId >= 6? "<span class='glyphicon glyphicon-ok pull-right'></span>":"<input type='button' id='".$model->StepProjectId."?id=".$model->ProjectId."' value='To Do' class='buton-action gray-888-text pull-right' />" ?></div>
            </div>
            <div style="height:auto !important;">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <?php
                            if($reception)
                            {
                                echo DetailView::widget([
                                    'model' => $reception,
                                    'attributes' => [
                                        //'Marker_Id',
                                        ['label'=>'Lab Reception', 'value'=>$reception->LabReception],

                                    ],
                                ]); 
                            }
                        ?>
                    </div>
                 </div>
            </div>
            <div class="row wer"> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Shipment & Plates </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-action"> <?=$model->StepProjectId >= 5? "<span class='glyphicon glyphicon-ok pull-right'></span>" :"" ?> 
                </div>
            </div> <!-- header samples & shipment -->
            <div style="height:auto !important;">
                <?php if($shipment != null): ?>
                    <div style='margin:10px auto 10px 100px'>
                        <table id="detail-project" >
                            <tr>
                                <th style="text-align:left;">Date Shipment:</th><td><?= $shipment['CreationDate'] ?></td>
                            </tr>
                            <tr>
                                <th>Jobs Grouped: </th><td> <?= $shipment['Names'] ?></td>
                            </tr>
                        </table>
                    </div>   
                <?php endif; ?>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 "></div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 ">
                    <?php
                        if($plates = $model->getPlates())
                        {
                            //$rows = $model->getPlatesToDetailView();
                            $modelPlate = new \common\models\PlateSearch();
                            echo GridView::widget([
                                'dataProvider' => $modelPlate->search(['PlateSearch'=> ["ProjectId" =>$model->ProjectId]]),
                                'filterModel' => $modelPlate,
                                'summary'=>"",
                                'columns' => [
                                        'PlateId',
                                        [
                                            'attribute' => 'Plate Status',
                                            'format' => 'raw',
                                            'value' => function($data)
                                                       {
                                                           return  $data->getStatusName();
                                                       },
                                        ],
                                        [
                                            'attribute' => '',
                                            'format' => 'raw',
                                            'value' => function ($data) {
                                                            return Html::checkbox('vCheck[]', false, ['value'=> $data->PlateId, 'disabled' => $data->StatusPlateId < \common\models\StatusPlate::ADN_EXTRACTED]);
                                                        },
                                        ],
                                    ],
                                ]);   
                        }
                    ?>           
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" ></div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" align="center" >
                        
                    <?php if($model->StepProjectId >= common\models\StepProject::SAMPLE_RECEPTION): ?>
                    <?= Html::a("Download PDF", ["download-plate-pdf" , "projectIds"=>$model->ProjectId],['class' => 'btn btn-success btn-nuevo-reclamo pull-right btn-nuevo-create font-white'] ); ?>
                          <?=  Html::a(Yii::t('app', 'View Grids'),['view-shipment', 'idProject' => $model->ProjectId],[ 'class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create font-white', 'style' => 'margin-top:0px !important;', 'target' => '_blank']); ?>  
                    <?php endif; ?>
                    
                    <?php if($plates = $model->getPlatesDnaExtracted()): ?>
                        <?=  Html::a(Yii::t('app', 'Download Samples Sheets'),"#",["onclick"=>"return assaySamples()", 'class' => 'btn btn-warning btn-nuevo-reclamo pull-right btn-nuevo-create font-white', "style" => "margin-top:0px !important;"]); ?>  
                   
                        <?php /*  Html::a(Yii::t('app', 'Upload Assay Markers'),"#",["onclick"=>"return assaySamples()", 'class' => 'btn btn-info btn-nuevo-reclamo pull-right btn-nuevo-create font-white', "style" => "margin-top:0px !important;"]); */?>  
                        <?php if($model->markersByProjects != null): ?>
                            <?=  Html::button(Yii::t('app', 'Upload Assay Sheet'),[
                                'class' => 'btn btn-info btn-nuevo-reclamo pull-right btn-nuevo-create font-white',
                                "style" => "margin-top:0px !important; font-family:arial",
                                'onclick' => 'return verifyMarkers()',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal',
                                'data-url' => Url::to(['assaybyproject/upload-assay', 'ProjectId' => $model->ProjectId]),
                            ]) ?>
                        <?php else: ?>
                            <div class="alert alert-default">
                                Can not load assay sheets.<br> You must select markers.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($model->getAssayByProjects()): ?>
                        <?php
                        foreach($model->assayByProjects as $assay)
                        {
                            $time = strtotime($assay->Date);
                            $myFormatForView = date("d-m-Y", $time);
                            echo Html::a(Yii::t('app', "<span class='glyphicon glyphicon-link'></span> " . $assay->BarcodeAssay ." (".$myFormatForView.")"), "../".$assay->Path,[ "class" => "new-link", "onclick" =>"return downloadAssay(this);"]) ."<br>";  
                        }
                        ?>
                    <?php  endif; ?>
                    
                     <!-- <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a> -->
                </div>
            </div> <!-- content samples & shipment -->
            <div class="row wer"> 
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 head-accordion"> Protocols & Reports </div>   
            </div>
            <div style="height:auto !important;">
                <?php if($model->StepProjectId >= common\models\StepProject::GENOTYPED): ?>
                    <h1>Protocols</h1>
                    <div id="gridProtocols" style="margin-bottom:15px; margin-top:15px"></div>
                    <h1>Reports</h1>
                    <div id="gridReport" style="margin-top:15px"></div>
                    <div id="modal-ajax"></div>
                <?php else: ?>
                    <h1> The project does not fulfill the requirements to start using this section<br> Assay sheets needs to be uploaded.</h1>
                     
                <?php endif; ?>
            </div>
        </div>     
        <!-- <a href="#" id="colapsefalse">Expand All </a> -->
</div>
<script>
   
   
    //init12 = function(){
    //    example(); 
    //};
        
    $("#colapsefalse").click(function(){    
        $('#accordion .ui-accordion-content').show();
        return false;
    });
    
    assaySamples = function()
    {
        <?php if($model->markersByProjects != null): ?>
            if ($("input[name='vCheck[]']:checked").length == 0){
                alert("You must select at least one Plate");
                return false;             
            }else{
                var pageHeight = $( document ).height();
                //var pageWidth = $(window).width();
                $('#divBlack').css({"height": pageHeight});

                $('#divBlack').show();
               $.post(
                    "<?= Yii::$app->homeUrl; ?>plate/download-assay-samples-by-project?", $("input[name='vCheck[]']").serialize(), function(e){
                                                                                                                                                //console.log(e); return false;
                                                                                                                                                $('#divBlack').hide();
                                                                                                                                                window.location = "<?= Yii::$app->homeUrl; ?>plate/zip-save?pat="+e;  
                                                                                                                                            }
                );
            return false;
            };
        <?php else:?>
            alert('You must select the markers to use this section.');
            return false;
        <?php endif;?>
    }
    
    
    function addProtocol()
    {
        $.ajax({
            url:"../protocol/add-protocol?ProjectId=<?= $model->ProjectId?>",  //Server script to process data
            cache: false,
            type:"GET",
            // Form data
            //data:{ReportId:dataItem.ReportTypeId},
            beforeSend: function(){ 
                //$("#FormField").hide('500');
                $("#modal-ajax").html("<div class='modal fade' id='myModal' role='dialog'>\n\
                                        <div class='modal-dialog'>\n\
                                            <div class='modal-content'>\n\
                                                <div class='modal-header'>\n\
                                                    <button type='button' class='close' data-dismiss='modal'>&times;</button>\n\
                                                    <div id='result-ajax' style='min-height:400px !important;'></div>\n\
                                                    <div id='loading_modal' style='text-align:center; top:150px;display: inline'>\n\
                                                        <img src='<?= Yii::$app->request->baseUrl ?>/images/loading.gif' width='60'/>\n\
                                                        <br>\n\
                                                        <span style='color:#333'>Wait please..</span>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>");
                            
                            $("#myModal").modal();
            }, 
            success: function(response) {
                $("#loading_modal").hide();
                $("#result-ajax").html(response);
                return false;
            },
            error: function(error){
                console.log(error);
                //$(".modal-body").html(error);
            },

        });
    }
            
</script>
<style>
    /********  PARA EL ACORDION ***************/
      /*Basic Styles*/
    html {
            height: 100%;
            background: #e3e3e0;
    }
    
    :focus, :active {
            outline: 0;
    }
    * { 
            -moz-box-sizing: border-box; 
            -webkit-box-sizing: border-box; 
            box-sizing: border-box;
    }
    #accordion {
            width: 90%;
            margin: 50px auto;
    }
    #accordion .wer 
    {
            background-color: none !important;
            margin: 0px;
            padding:0;
            width: 100%;
    }

    #accordion .ui-accordion-header .head-accordion 
    {
            color: #fff;
            line-height: 42px;
            display: block;
            font-size: 12pt;
            //width: 50%;
            text-indent: 10px;
            text-shadow: 1px 1px 0px rgba(0,0,0,0.2);
            //background-color: #ff0000;
            //overflow-y: auto;
            //height: 50px;
    }
    #accordion .ui-accordion-header .head-action 
    {
        padding: 10px;
        color: #fff;
        font-size:20px;
    }
    #accordion .ui-accordion-content > * {
            margin: 0;
            padding: 1px;
    }
    #accordion .ui-accordion-content a {
            color: #777;
    }
    #accordion .ui-accordion-header {
            background-color: #389abe;
            background-image: -moz-linear-gradient(top,  #389abe 0%, #2a7b99 100%);
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#389abe), color-stop(100%,#2a7b99));
            background-image: -webkit-linear-gradient(top,  #389abe 0%,#2a7b99 100%);
            background-image: -o-linear-gradient(top,  #389abe 0%,#2a7b99 100%);
            background-image: -ms-linear-gradient(top,  #389abe 0%,#2a7b99 100%);
            background-image: linear-gradient(to bottom,  #389abe 0%,#2a7b99 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#389abe', endColorstr='#2a7b99',GradientType=0 );

    }

    .ui-state-default
    {
        background-color: #ff00ff;
    }

    .ui-state-active
    {
        background-color: #a8b700 !important;
            background-image: -moz-linear-gradient(top,  #a8b700 0%, #82922a 100%) !important;
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#a8b700), color-stop(100%,#82922a)) !important;
            background-image: -webkit-linear-gradient(top,  #a8b700 0%,#82922a 100%) !important; 
            background-image: -o-linear-gradient(top,  #a8b700 0%,#82922a 100%) !important;
            background-image: -ms-linear-gradient(top,  #a8b700 0%,#82922a 100%) !important;
            background-image: linear-gradient(to bottom,  #a8b700 0%,#82922a 100%) !important;
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a8b700', endColorstr='#82922a',GradientType=0 ) !important;
    }
    .ui-widget input
    {
        font-size: 14px !important;
        text-transform: capitalize;
    }

    .font-white
    {
        color: #FFF !important;
        width: 100%;
    }

    table
    {
        border:4px !important;
        border-spacing: 10px !important;
    }
    
</style>