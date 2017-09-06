<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Fingerprint */

$this->title = $model->Name == NULL ? 'Fingerprint sin Nombre':$model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fingerprints'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fingerprint-view">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
             <p class="pull-right">
        <?= Html::a(Yii::t('app', 'Index'), ['index', 'id' => $model->Fingerprint_Id], ['class' => 'btn btn-gray']) ?>
       

       
        <?= Html::button(Yii::t('app', 'Delete'),
                    [ 'class' => 'btn btn-danger',
                      'data-reload' => '#modal',
                      'data-url' => Url::to(['delete', 'id' => $model->Fingerprint_Id]),
                       ]); ?>
            
             </p>
        </div>
    </div>
    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'Fingerprint_Id',
            'Name',
            'DateCreated',
            [
                'attribute'=>'Project_id',
                'format'=>'raw', 
                'value'=>'<kbd>'.$model->Project_Id != NULL ? "Sin Proyecto" : $model->Project_Id.'</kbd>', 
                'displayOnly'=>true
            ],
            //'IsActive',
        ],
    ]) ?>
    
  

</div>
