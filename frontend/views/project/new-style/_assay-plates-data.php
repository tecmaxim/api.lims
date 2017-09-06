<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h1> Plates Status & Actions</h1>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <?php if ($shipment != null): ?>
            
                <table id="detail-project" >
                    <tr>
                        <th width="200">Jobs Grouped: </th><td width="400"> <?= $shipment['Names'] ?></td>
                    </tr>
                </table>
            
        <?php endif; ?>
    </div>
    <?php if($model->StepProjectId < common\models\StepProject::GENOTYPED):?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="alert alert-warning">
           <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>  You must upload at least one <strong>Assay Sheet</strong> to continue with Reports step.
        </div>
        <?php if($model->StepProjectId < common\models\StepProject::DNA_EXTRACTION):?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>  You must extract DNA from at least <strong>one Plate</strong> in order to continue with Assay Sheets Uploads.
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 ">
        <?php
        if ($plates = $model->getPlates()) {
            //$rows = $model->getPlatesToDetailView();
            $modelPlate = new \common\models\PlateSearch();
            echo GridView::widget([
                'dataProvider' => $modelPlate->searchByQuery(['PlateSearch' => ["ProjectId" => $model->ProjectId]]),
                'filterModel' => $modelPlate,
                'summary' => "",
                'columns' => [
                    [
                       'attribute' => 'PlateId',
                        'format'=>'raw', 
                        'value' => function($data){ return 'TP'.sprintf("%'.06d\n",$data->PlateId); },
                    ],
                    [
                        'attribute' => 'Plate Status',
                        'format' => 'raw',
                        'value' => function($data) {
                            return $data->getStatusName();
                        },
                    ],
                    [
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::checkbox('vCheck[]', false, ['value' => $data->PlateId, 'disabled' => $data->StatusPlateId < \common\models\StatusPlate::ADN_EXTRACTED]);
                        },
                            ],
                        ],
                    ]);
                }
                ?>           
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" align="center" >

                <?php if ($model->StepProjectId >= common\models\StepProject::SAMPLE_RECEPTION): ?>
                    <?= Html::a("Download PDF", ["download-plate-pdf", "projectIds" => $model->ProjectId], ['class' => 'btn btn-success btn-block btn-margin-5 font-white']); ?>
                    <?= Html::a(Yii::t('app', 'View Grids'), ['view-shipment', 'idProject' => $model->ProjectId], [ 'class' => 'btn btn-primary btn-block btn-margin-5 font-white']); ?>  
                <?php endif; ?>

                <?php if ($plates = $model->getPlatesDnaExtracted()): ?>
                    <?= Html::a(Yii::t('app', 'Download Samples Sheets'), "#", ["onclick" => "return assaySamples()", 'class' => 'btn btn-warning btn-block btn-margin-5 font-white']); ?>  

                    <?php /*  Html::a(Yii::t('app', 'Upload Assay Markers'),"#",["onclick"=>"return assaySamples()", 'class' => 'btn btn-info btn-nuevo-reclamo pull-right btn-nuevo-create font-white', "style" => "margin-top:0px !important;"]); */ ?>  
                    <?php if ($model->markersByProjects != null): ?>
                        <?=
                        Html::button(Yii::t('app', 'Upload Assay Sheet'), [
                            'class' => 'btn btn-info btn-block btn-margin-5 font-white',
                            "style" => "margin-top:0px !important; font-family:arial",
                            'onclick' => 'return verifyMarkers()',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal',
                            'data-url' => Url::to(['assaybyproject/upload-assay', 'ProjectId' => $model->ProjectId]),
                        ])
                        ?>
                    <?php else: ?>
                        <div class="alert alert-default">
                            Can not load assay sheets.<br> You must select markers.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($model->getAssayByProjects()): ?>
                    <?php
                    foreach ($model->assayByProjects as $assay) {
                        $time = strtotime($assay->Date);
                        $myFormatForView = date("d-m-Y", $time);
                        echo Html::a(Yii::t('app', "<span class='glyphicon glyphicon-link'></span> " . $assay->BarcodeAssay . " (" . $myFormatForView . ")"), "../" . $assay->Path, [ "class" => "new-link", "onclick" => "return downloadAssay(this);"]) . "<br>";
                    }
                    ?>
                <?php endif; ?>

 <!-- <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a> -->
    </div>

</div>
<script>
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
</script>