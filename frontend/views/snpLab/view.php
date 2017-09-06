<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SnpLab */

$this->title = $model->LabName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Snp Labs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="snp-lab-view">

<div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
             <p class="pull-right">
                <?= Html::a(Yii::t('app', 'Index'), ['index', 'id' => $model->Snp_lab_Id], ['class' => 'btn btn-gray']) ?>
                <?= Html::button(Yii::t('app', 'Update') 
                                       , ['class' => 'btn btn-primary'
                                       , 'data-reload' => '#modal'
                                       , 'data-url' => Url::to(['update', 'id' => $model->Snp_lab_Id])])
                ?>
                <?= Html::button(Yii::t('app', 'Delete'),
                            [ 'class' => 'btn btn-danger',
                              'data-reload' => '#modal',
                              'data-url' => Url::to(['delete', 'id' => $model->Snp_lab_Id]),
                ]); ?>
            </p>
        </div>
</div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'Snp_lab_Id',
            //'Snp_Id',
            'LabName',
            ["label" => "Original Name", "value" => $model->marker->Name],
            [
                'attribute'=>'PurchaseSequence', 
                'format'=>'raw', 
                'value'=>'<kbd>'.$model->PurchaseSequence != NULL ? substr($model->PurchaseSequence, 0 , 30).'...' : "".' </kbd>', 
                'displayOnly'=>true
            ],
            'AlleleFam',
            'AlleleVicHex',
            'ValidatedStatus',
            'Quality',
            'Box',
            'PositionInBox',
            'PIC',
            //'IsActive',
            //'Observation',
        ],
    ]) ?>
 </div>
