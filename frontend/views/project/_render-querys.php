<?php
use yii\helpers\Url;
use miloschuman\highcharts\HighchartsAsset;

HighchartsAsset::register($this)->withScripts([]);
HighchartsAsset::register($this)->withScripts(['highstock', 'modules/exporting']);

$this->registerJs('init();', $this::POS_READY);

?>
    <div class="container">
        <div class="row">
            <?=  $this->render('_header-steps');  ?>
            <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
        </div>
        <div class="row marker-blue-container" >
           
            <?php
            if($query_selected == 1)
            {
               echo ' <form id="searchSnpLab" action="'.Yii::$app->homeUrl.'query/query1" method="get">';
                
                echo   $this->render('../query/query1-Lims',['searchModel' => $searchModel, 
                                                        'dataProvider' => $dataProvider,
                                                        //'selectedDataProvider' => $snps_checked,
                                                        'limitsXcM' => $limitsXcM,
                                                        'lastChromosme' => $lastChromosme,
                                                        'hasMap' => $hasMap,
                                                        'mapTypes' => $mapTypes,
                                                        'itemsValidated' => $itemsValidated,
                                                        'isSearchConsensus' =>$isSearchConsensus,
                                                        'isLims'    => 1,
                                                        'update' => $update,
                                                        ]);
            }else{
                echo ' <form id="FingerprintSearch" action="'.Yii::$app->homeUrl.'query/query2" method="get">';
                echo   $this->render('../query/query2-lims',[
                                                        'dataProvider'         => $dataProvider,
                                                        'fp_material1'         => $fp_material1, // radiobuton FP_material_id's calculated in the call ajax
                                                        'fp_material2'         => $fp_material2, // radiobuton FP_material_id's calculated in the call ajax
                                                        'Fingerprint'          => $Fingerprint, // model
                                                        'IsLims'               => 1,
                                                        'totalCompared'        => $Fingerprint->getTotalCompared(Yii::$app->request->queryParams),
                                                        'limitsXcM'            => $limitsXcM, // limits to grapfhic
                                                        'both'                 => $Fingerprint->Method == 3 ? true : false,
                                                        'hasFP'                => $hasFP,
                                                        'mapsType'             => $mapsType,
                                                        'update'               => $update,
                                                        'dataMaterials'        => $dataMaterials,
                                                        'hasMap'               => $hasMap,

                                                        ]);
            } ?>
           </form>
        </div>
        
    </div>


<script>
    var pageHeight = $( document ).height();
    var pageWidth = $(window).width();
     //alert(pageHeight); exit;
    $('#divBlack').css({"height": pageWidth});
    
    init = function()
    {   
        $("#step1").removeClass("selected");
        $("#step2").addClass("selected");
        
        $("#step2").click(function(e)
        {        
            window.location = "<?= Yii::$app->homeUrl; ?>project/create?back=1";
            return false;
        });
        
        $(".reset").click(function(e)
        {
           e.preventDefault();
           window.location=  "<?= Yii::$app->homeUrl; ?>project/create";
        });
       
       
        
        $.each($('#reset'), function(index, value) { 
            $(this).click(function(e)
            {
                e.preventDefault();
                window.location=  "<?= Yii::$app->homeUrl; ?>project/create";
            });
        });
    
    // Trigger change event on crop if cropId is in session
     <?php /*
     <?php if(Yii::$app->session->get('cropId') != null ): ?>
      <?php if($fp_material1 == ""): ?>
        setTimeout(function(){
        $('#fingerprintsearch-crop').trigger('change');
      }, 100);
    
      <?php endif;?>
    <?php endif;?>
    */?>
    };
    
       
</script>