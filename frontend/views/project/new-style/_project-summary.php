<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
?>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
        <h1> Project Definition</h1>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">
    <?php if($model->StepProjectId < \common\models\StepProject::SENT ): ?>
        <?= Html::a("Edit", ["update" , "id"=>$model->ProjectId],['class' => 'btn btn-primary btn-render-content pull-right'] ); ?>
    <?php endif; ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'Marker_Id',
                'Name',
                'ProjectCode',
                ['attribute' => 'Crop', 'value' => $model->crop->Name],
                //'campaign.Year',
                'user.Username',
                ['attribute' => 'Project Type', 'value' => $model->projectType->Name],
                ['attribute' => 'Priority', 'format' => 'raw', 'value' => $model->getPriority()],
                ['attribute' => 'Last Step Project', 'format' => 'raw', 'value' => $model->stepProject->Name],
                //'priority',
                //'NumberSamples',
                ['attribute' => 'Research Station', 'value' => $model->researchStation->Short],
                'DeadLine',
                'FloweringExpectedDate',
                'SowingDate',
                [
                    'attribute' => 'TissueOrigin',
                    'format' => 'raw',
                    'value' => $model->TissueOrigin,
                    'visible' => $model->ProjectTypeId == \common\models\Project::FINGERPRINT ? true : false,
                ],
                [
                    'attribute' => $parents != null ? 'Parents' : 'Materials',
                    'format' => 'raw',
                    'value' => $model->getParentsAsArray(),
                    //'visible' => $parents != null ? true : false,
                ],
                [
                    'attribute' => 'Parent Project',
                    'format' => 'raw',
                    'value' => $model->projectsLinkage != null ? $model->projectsLinkage->project->ProjectCode : null,
                    'visible' => $model->projectsLinkage != null ? true : false,
                ],
                [
                    'attribute' => 'Traits',
                    'format' => 'raw',
                    'value' => $model->getTraitsInString(),
                    'visible' => $model->getTraitsInString() != null ? true : false,
                ]
            ],
        ])
        ?>
    </div>
<style>
table.detail-view th {
        width: 35% !important;
}
</style>