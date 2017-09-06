<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Maps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="map-index">

     <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?=  Html::button(Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Map'),]), [
                'class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-url' => Url::to('create')
            ]);?>
         </div>
    </div>
 <?php  Pjax::begin(['id' => 'itemList']) ; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => [
            [
           	'label'=>'',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                        
					 return '<input type="checkbox" name="vCheck[]" value="'.$data->Map_Id.'" >';
					},
            ],
          //'Map_Id',
            //['label'=>'Crop','value'=> 'crop.Name'],
            'Name',
            ['label'=>'Map Category','value'=> 'mapType.Name'],
            [
               'label'=>'Mapping Team',
               'format' => 'raw',
               'value'=>function ($data) {
                                            //return '<input type="checkbox" name="vCheck[]" value="'.$data->Map_Id.'" >';
                                            foreach($data->mapResults as $d)
                                                return $d->MappingTeam; 
                                            
                                       },
           ],
            [
               'label'=>'Mapped Population',
               'format' => 'raw',
               'value'=>function ($data) {
                                            //return '<input type="checkbox" name="vCheck[]" value="'.$data->Map_Id.'" >';
                                            foreach($data->mapResults as $d)
                                                return $d->MappedPopulation; 
                                            
                                       },
           ],
           'Date',
            [
               'label'=>'Current',
               'format' => 'raw',
               'value'=>function ($data) {
                                            return $data->IsCurrent == 1 ? '<span class="glyphicon glyphicon-ok size12"  aria-hidden="true"> </span>' : "";
                                            
                                            
                                       },
           ],

            ['class' => 'frontend\widgets\RowLinkColumn'],
        ],
    ]); ?>
     <?php  Pjax::end(); ?>

</div>

<script>
    downloadMap = function(id)
                            {
                                if(confirm("This action can take several minutes. Are you sure you want continue?  "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?+&id="+id; return false;
        return false;
                            }
</script>