<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\StatusPlate;
?>

<style>
       .modal-content
        {
            //min-height:650px !important;
            height: auto;
        }

</style>
<?php
/* @var $this yii\web\View */
/* @var $model common\models\Plate */

$this->title = 'TP'.sprintf("%06d",$model->PlateId);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Generation'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(isset($method))
{
    $url = $method == 'GenSpin' ? 'create-genspin-method' : 'adn-extraction';
}

$column = $model->StatusPlateId >= StatusPlate::ADN_EXTRACTED ? [
            //'GenerationId',
            //'Description',
            [
             'attribute' => 'Job',
             'format'=>'raw', 
             'value' =>  $model->getProjectName(),
                        
            ],
            [
             'attribute' => 'Shipment Date',
             'format'=>'raw', 
             'value' =>  $model->Date,
                        
            ],
            [
             'attribute' => 'Status',
             'format'=>'raw', 
             'value' =>  $model->statusPlate->Name,
                        
            ],
            [
             'attribute' => 'Date Last Status',
             'format'=>'raw', 
             'value' => $model->getLastDateByPlateStatus(),
            ],
            [
                'attribute' => 'DNA Method',
                'format'=>'raw', 
                'visible' => $model->StatusPlateId >= StatusPlate::ADN_EXTRACTED ? true: false,
                'value' => $model->adnExtractions != null ? ($model->adnExtractions->Method ==  'GenSpin' ? $model->getMethod(): $model->adnExtractions->Method): $model->getMethod(),
            ],
            [
                'attribute' => 'Dilution',
                'format'=>'raw', 
                'visible' => $model->StatusPlateId >= StatusPlate::ADN_EXTRACTED ? true: false,
                'value' => $model->adnExtractions != null ? $model->adnExtractions->Dilution: $model->getGenSpinData()->Dilution,
            ],
            [
                'attribute' => 'Cuantification',
                'format'=>'raw', 
                'visible' => $model->StatusPlateId >= StatusPlate::ADN_EXTRACTED ? true: false,
                'value' => $model->adnExtractions != null ? $model->adnExtractions->Cuantification: $model->getGenSpinData()->Cuantification,
            ],
            [
                'attribute' => 'Comments',
                'format'=>'raw', 
                'visible' => $model->StatusPlateId >= StatusPlate::ADN_EXTRACTED ? true: false,
                'value' => $model->adnExtractions != null ? $model->adnExtractions->Comments: $model->getGenSpinData()->Comments,
            ],
            [
                'attribute' => 'View Ptoject Plates',
                'format'=>'raw', 
                'value' => $model->linkToPlates(),
            ]
            //'IsActive:boolean',
        ] : [
            //'GenerationId',
            //'Description',
            [
             'attribute' => 'Job',
             'format'=>'raw', 
             'value' =>  $model->getProjectName(),
                        
            ],
            [
             'attribute' => 'Shipment Date',
             'format'=>'raw', 
             'value' =>  $model->Date,
                        
            ],
            [
             'attribute' => 'Status',
             'format'=>'raw', 
             'value' =>  $model->statusPlate->Name,
                        
            ],
            [
             'attribute' => 'Date Last Status',
             'format'=>'raw', 
             'value' => $model->getLastDateByPlateStatus(),
            ],
            [
                'attribute' => 'DNA Method',
                'format'=>'raw', 
                'visible' => $model->StatusPlateId == StatusPlate::ADN_EXTRACTED ? true: false,
                'value' => $model->adnExtractions != null ? $model->adnExtractions->Method: $model->getMethod(),
            ],
            [
                'attribute' => 'View Plates Group',
                'format'=>'raw', 
                'value' => $model->linkToPlates(),
            ]];
?>
<div class="generation-view">
    <div class="row">    
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p style="float: right;">
                <?php if($model->StatusPlateId == \common\models\StatusPlate::CONTROLLED): ?>
                    <?=  Html::button(Yii::t('app', 'Select DNA Method'),[
                        'class' => 'btn btn-success',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['select-method', 'id' => $model->PlateId])
                    ]); ?>
                <?=  Html::button(Yii::t('app', 'Order Again'),[
                        'class' => 'btn btn-danger',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['order-again', 'id' => $model->PlateId]),

                    ]) ?>

                <?php endif; ?>
                <?php if($model->StatusPlateId == \common\models\StatusPlate::SAVED_METHOD): ?>
                    <?=  Html::button(Yii::t('app', 'DNA Extraction'),[
                        'class' => 'btn btn-success',
                        'data-reload' => '#modal',
                        'data-url' => Url::to([$url, 'id' => $model->PlateId])
                    ]); ?>
                <?=  Html::button(Yii::t('app', 'Cancel DNA Method'),[
                        'class' => 'btn btn-success',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['cancel-method', 'id' => $model->PlateId])
                    ]); ?>

                <?php endif; ?>
                <?php if($model->StatusPlateId >= \common\models\StatusPlate::ADN_EXTRACTED): ?>
                    <?php if($model->StatusPlateId < \common\models\StatusPlate::CUANTIFICATION): ?>
                        <?=  Html::button(Yii::t('app', 'Cuantification'),[
                                'class' => 'btn btn-success',
                                'data-reload' => '#modal',
                                'data-url' => Url::to(['create-cuantification', 'id' => $model->PlateId]),
                            ]) ?>
                    <?php endif; ?>
                    
                    <?=  Html::a(Yii::t('app', 'Assay Samples'),
                        '#',
                        ['id' => 'genotype-samples',
                        'class' => 'btn btn-warning']
                    ) ?>
                    <?=  Html::button(Yii::t('app', 'Change Job'),[
                        'class' => 'btn btn-primary',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['get-projects-availables-to-change', 'id' => $model->PlateId]),

                    ]) ?>
                    <?php /*  Html::button(Yii::t('app', 'Assay Markers'),[
                        'class' => 'btn btn-info',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['export-genotype-markers', 'id' => $model->PlateId]),

                    ]) */?>
                    <div id="divLoad" class="modal-loading-2" style="display:none;">
                        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="30"/>
                    </div>
                <?php endif; ?>
                    
                <?php /* Html::button(Yii::t('app', '<b>Cancel Plate</b>'),[
                        'class' => 'btn btn-danger',
                        'data-reload' => '#modal',
                        'data-url' => Url::to(['cancel-plate', 'id' => $model->PlateId]),

                    ]) */ ?>
            </p>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $column,
    ]) ?>

</div>

<script>
    $(function(){
       $("#genotype-samples").click(function(){
            $(this).hide('500');
            $("#divLoad").show('500');
                $.post(
                    "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/export-assay-samples",
                    {id: <?= $model->PlateId; ?> }, 
                    function(path){
                                console.log(path);
                                
                                window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel-saved?pat="+path;  
                                $("#divLoad").hide('500');
                                $("#genotype-samples").show('500');
                            }
                    );
       });
    });
</script>