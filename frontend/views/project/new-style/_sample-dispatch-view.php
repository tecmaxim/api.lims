<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
?>
<div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
        <h1> Samples Dispatch</h1>
    </div>
<div class="row">
    
    <?php if($model->StepProjectId < \common\models\StepProject::SAMPLE_RECEPTION): ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">
            <?= Html::a("Edit", ["update-sample-dispatch" , "idProject"=>$model->ProjectId],['class' => 'btn btn-primary btn-render-content pull-right'] ); ?>
        </div>
    <?php endif; ?>
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