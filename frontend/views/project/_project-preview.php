<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->registerJs('init_project_preview(' . $project->ProjectId . ',"' . Yii::$app->request->baseUrl . '");', $this::POS_READY);
?>

<div class="container">
    <div class="row">
        <?= $this->render('_header-steps'); ?>
    </div>

    <div class="row marker-blue-container" >
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 col-md-offset-1 col-lg-offset-1">
                <h1>Summary</h1>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">    
                <?php if ($project->StepProjectId == \common\models\StepProject::ON_HOLD) : ?>
                    <?=
                    Html::button(Yii::t('app', 'Resume'), [
                        //'class' => 'btn btn-success btn-nuevo-reclamo btn-nuevo-create',
                        'class' => 'btn btn-success btn-nuevo-reclamo pull-right btn-nuevo-create',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['resume', 'id' => $project->ProjectId]),
                            // 'data' => [
                            //    'method' => 'post',
                            // ],
                    ])
                    ?>
<?php endif; ?>
            </div>

        </div>

        <div class="row">           
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"> </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">

                <?php if ($statusByProject != null): ?>

                        <?php $classAlert = $statusByProject->StepProjectId == common\models\StepProject::FINISHED ? "alert-brown" : "alert-warning" ?>
                    <div class=" alert <?= $classAlert ?>">
                        <h4> <?= $project->stepProject->Name ?></h4>
                        <?php if ($project->StepProjectId == \common\models\StepProject::ON_HOLD): ?>
                            <h4>Last Status: <?= $statusByProject->stepProject->Name ?> </h4>
    <?php endif; ?>
    <?= $statusByProject->Comments ?>
                        <br>
                        Date: <?= date('d-m-Y H:i:s', strtotime($statusByProject->Date)) ?>
                    </div>
                <?php endif; ?>
                <?=
                DetailView::widget([
                    'model' => $project,
                    'attributes' => [
                        //'Marker_Id',
                        'Name',
                        'ProjectCode',
                        ['attribute' => 'Crop', 'value' => $project->crop->Name],
                        //'campaign.Year',
                        'user.Username',
                        ['attribute' => 'Project Type', 'value' => $project->projectType->Name],
                        ['attribute' => 'Priority', 'format' => 'raw', 'value' => $project->getPriority()],
                        ['attribute' => 'Last Step Project', 'format' => 'raw', 'value' => $project->stepProject->Name],
                        //'priority',
                        'NumberSamples',
                        ['attribute' => 'Research Station', 'value' => $project->researchStation->Short],
                        'DeadLine',
                        'FloweringExpectedDate',
                        'SowingDate',
                        [
                            'attribute' => $parents != null ? 'Parents' : 'Materials',
                            'format' => 'raw',
                            'value' => $project->getParentsAsArray(),
                            //'visible' => $parents != null ? true : false,
                        ]
                    ],
                ])
                ?>
            </div>
            
        </div>    
        <div class="row">

            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                <h1>SnpLabs</h1>
            </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
            <?php if (isset($markers)): ?>
                <div id="grid-marker-render"></div>
            <?php endif; ?>
             </div>
        </div>
        <div class="row" >
<?php if ($statusByProject != null): ?>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                    <h1>Shipments & Plates</h1>
                </div>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
    <?php if ($shipment != null): ?>
                        <div style='margin:10px auto 10px 10px'>
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
                </div>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                    <?php
                    if ($plates = $project->getPlates()) {
                        //$rows = $model->getPlatesToDetailView();
                        $modelPlate = new \common\models\PlateSearch();
                        echo GridView::widget([
                            'dataProvider' => $modelPlate->search(['PlateSearch' => ["ProjectId" => $project->ProjectId]]),
                            'filterModel' => $modelPlate,
                            'summary' => "",
                            'columns' => [
                                'PlateId',
                                [
                                    'attribute' => 'Plate Status',
                                    'format' => 'raw',
                                    'value' => function($data) {
                                        return $data->getStatusName();
                                    },
                                ],
                            ],
                        ]);
                    }
                    ?>           
                </div>

                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                    <h1>Reports</h1>
                </div>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                    <div id="gridReport_2"></div>
                </div>
            <?php else: ?>

                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                    <h1>Grid Preview</h1>
                </div>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">
                <?=
                $this->render('_gridPreview', ['model' => $project,
                    'samplesByProject' => $samplesByProject,
                    'numLastSampleByPlate' => $numLastSampleByPlate,
                    'parents' => $parents,
                    'preview' => true,
                ])
                ?>
                </div>
<?php endif; ?>
        </div>
        <!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <button type="button" class="btn btn-primary in-nuevos-reclamos" id="sendTo"><?= Yii::t('app', 'Send To Breeder'); ?></button>
        </div>
        -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-right" style="margin-top:30px;">
            <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= $statusByProject == null ? Yii::t('app', 'Back To Job View') : Yii::t('app', 'Back To Job List'); ?></button>
        </div>
    </div>
</div>
<style>
    .customClass1 {
        background: #222;
        color: #eee;
    }

    .customClass2 {
        background: #555;
        color: #eee;
    }

    .grid-markers{
        text-align: center;
        font-size: 14px;

        border-bottom: 1px solid #aaa !important;
    }
</style>
<script>
    $("#step1").removeClass("selected");
    $("#step5").addClass("selected");
    $("#sendTo").click(function () {
        if (confirm("Are you sure you want to send the mail to the breeder ?"))
            window.location = "<?= Yii::$app->homeUrl; ?>project/view?id=<?= $project->ProjectId ?>";

                    window.location = "<?= Yii::$app->homeUrl; ?>project/view?id=<?= $project->ProjectId ?>";
                            return false;
                        });

                        $("#cancel").click(function ()
                        {
<?php if ($statusByProject != null): ?>
                                window.location = "<?= Yii::$app->homeUrl ?>project/";
<?php else: ?>
                                window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $project->ProjectId ?>";
<?php endif; ?>
                                });

                                var headerAttributes = {'style': 'height: 25px;text-align: center;white-space: pre-line; font-size:20px'};



</script>


