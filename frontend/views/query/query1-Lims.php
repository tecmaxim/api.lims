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

//$this->registerJs('init2();', $this::POS_READY);
//$this->registerJs('init2();', $this::POS_LOAD);
?>
<h1>Markers Selection</h1>
<div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>

<?= $this->render('resources/_filtersQuery1', ['searchModel' => $searchModel, 'itemsValidated' => $itemsValidated, "isLims" => isset($isLims)? $isLims : null]); ?>
<?php //$action = isset($update) ? ['query2?update=1'] : ['query2']; ?>

<?php if (!$hasMap && isset(Yii::$app->session['cropId'])) { ?>
    <div id="divBlack2">
        <h1> There are no Maps loaded</h1>
    </div>
    <script>
        setTimeout(function(e){
            window.location = "<?= Yii::$app->homeUrl; ?>project/<?= $update == true ? "update-select-markers?idProject=" : "select-markers?id=" ?><?= Yii::$app->session->get('projectId') ?>"; return false; 
        }, 2500);
    </script>
<?php } ?>

<?php if ($dataProvider != "") {
    $this->registerJs('init2();', $this::POS_READY);
    ?>
    <div id="container"></div>
    <div class="row">
        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 pull-right">

            <div id="menu_float2">
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
               <!-- <a href="#" onclick="return downloadTempalte();" class ="user export template" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Template </a> -->
                <a href="javascript: selectMarkers();" class ="user export oranges"> <span class="glyphicon glyphicon-share size12" aria-hidden="true"> </span> Select Markers </a>
               <!--  <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a> -->
                </ul>
            </div>
        </div>
    </div>
    <?php
    if (isset($dataProvider->models[0]['Marker_Id'])) {
        $markerOnly = true;
    } else
        $markerOnly = "";

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id' => 'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => $isSearchConsensus == true ? \common\models\SnpLab::getColumnsByConsensus() : [
            [
                'label' => '',
                'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
                //'visible' => $markerOnly == null ? false : true,
                'value' => function ($data) {
                if (Yii::$app->session['sd'])   
                {
                    if (isset($data['Marker_Id'])) 
                    {
                        if (in_array($data['Snp_lab_Id'], Yii::$app->session['sd']))
                            return '<input type="checkbox" name="vCheck[]" value="' . $data['Snp_lab_Id'] . '" checked>';
                        else
                            return '<input type="checkbox" name="vCheck[]" value="' . $data['Snp_lab_Id'] . '" >';
                    }
                }else
                    return '<input type="checkbox" name="vCheck[]" value="' . $data['Snp_lab_Id'] . '" >';

            },
            ],
            // 'Snp_lab_Id',
            //'Name',
            ['label' => 'Name', 'value' => 'Name', 'visible' => $markerOnly == null ? false : true],
            [
                'label' => 'Snp Labs',
                'format' => 'raw',
                'value' => function ($data) {

                    return (isset($data['SnplabConcat'])) ? $data['SnplabConcat'] : "<kbd>None</kbd>";
                },
                'visible' => $markerOnly == true ? true : false,
            ],
            [
                'label' => 'LabName',
                'format' => 'raw',
                'value' => function ($data) {

                    return $data['LabName'] == null ? "<kbd>None</kbd>" : $data['LabName'];
                },
                'visible' => $markerOnly == true ? false : true,
            ],
            [
                'label' => 'Barcode',
                'format' => 'raw',
                'value' => function($data) {
                    return \common\models\SnpLab::applyMaskBarcodes($data['Number']);
                },
                'visible' => $markerOnly == true ? false : true,
            ],
            [
                'label' => 'LG',
                'value' => function ($data) {
                    return $data['LinkageGroup'];
                },
            ],
            [
                'label' => 'cM',
                'value' => function ($data) {
                    return $data['Position'];
                },
            ],
            [
                'label' => 'Box',
                'value' => function ($data) {
                    return $data['Box'];
                },
                'visible' => $markerOnly == true ? false : true,
            ],
            [
                'label' => 'PositionInBox',
                'value' => function ($data) {
                    return $data['PositionInBox'];
                },
                'visible' => $markerOnly == true ? false : true,
            ],
            //'Crop_Id',
            [
                'label' => 'Quality',
                'format' => 'raw',
                'value' => function ($data) {

                    return $data['Quality'] == null ? "<kbd>None</kbd>" : $data['Quality'];
                },
                'visible' => $markerOnly == true ? false : true,
            ],
            ['class' => 'frontend\widgets\RowLinkColumn'],
                ],
    ]);
    ?>



    <script>
        selectMarkers = function()
        {
            if ($("input[name='vCheck[]']:checked").length == 0)
            {
                alert("You must select at least one item");
                return false;
            } else{
            if (confirm("Are you sure to select this items?"))
                    window.location = "<?= Yii::$app->homeUrl; ?>project/<?= $update == true ? "update-select-markers?idProject=" : "select-markers?id=" ?><?= Yii::$app->session->get('projectId') ?>&" + $("input[name='vCheck[]']").serialize(); return false; 
                    return false;
            }
        }

        selectAll = function()
        {   //console.log($("input[name='vCheck[]']:checked").length);
        if ($("input[name='vCheck[]']:checked").length == 0){
        $("input[name='vCheck[]']").prop("checked", true);
        } else{
        $("input[name='vCheck[]']").prop("checked", false);
        }
        //return false;
        };
                init2 = function()
                {

                $("#step2").click(function(e)
                {
                window.location = "<?= Yii::$app->homeUrl; ?>project/create?back=1";
                        return false;
                });
                
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
                        max: <?= $lastChromosme ?>,
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
                                if(isset($marker['Name']))
                                    echo '{name: "'.$marker['Name'].'", x: ' .$ll2 .', y: ' .$ll .'},';
                                else
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
                                    if(isset($marker['Name']))
                                        echo '{name: "'.$marker['Name'].'", x: ' .$cero2 .', y: ' .$cero .'},';
                                    else
                                        echo '{name: "'.$marker['LabName'].'", x: ' .$cero2 .', y: ' .$cero .'},';
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

    </script>

    <?php
} else {
    echo "<h1><p>No Markers  found</p></h1>";
    ?>


<?php } ?>

<?php if (Yii::$app->session->get('cropId') != null): ?>
    <?php if (!$dataProvider): ?>
        <script>
            setTimeout(function(){
            $('#snplabsearch-crop').trigger('change');
            }, 2000);
        </script>
    <?php endif; ?>
<?php endif; ?>
<style>
    /* styles harcoded only by higcharts behavior*/
    #container{height:400px !important; clear:both;}
    #snplabsearch-validatedstatus label{float:left; margin-right: 15px;}

    .btn.btn-gray-clear 
    {
        margin-right: 10px !important;
        margin-left: 10px !important;
    }

</style>
<script>
    reset = function()
    {
        //$('#Polymorfism').find("select").val("");
        //document.reload();
        window.location=  "<?= Yii::$app->homeUrl; ?>query/query1?method=1&projectId=<?=Yii::$app->session->get('projectId')?><?= isset($update)?"&update=1":''?>";
    };
    back = function()
    {
    //document.reload();
        window.location = "<?= Yii::$app->homeUrl ?>project/<?= $update == true ? "update-select-markers?idProject=" : "select-markers?id=" ?><?= Yii::$app->session->get('projectId') ?>";
    }
    
    $('#searchSnpLab').submit(function(e){

    $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
            //  console.log(e); return false;
            // alert("de afuera"); 
            var has_error = $("#filters_query").find(".has-error").text();
            if (!has_error)
            $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
    });
</script>