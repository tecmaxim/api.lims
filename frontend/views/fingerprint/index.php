<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\FingerprintSearch;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FingerprintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Fingerprints');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fingerprint-index">

<div class="row">
         <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
      
   </div>
    <?php echo $this->render('_search', ['model' => $searchModel, 'fromDashboard' => $fromDashboard]);  ?>
     <?php  Pjax::begin(['id' => 'itemList']) ; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => [
             ['class' => 'frontend\widgets\RowLinkColumn'],
            //'Fingerprint_Id',
            'Name',
            'DateCreated',
            [
           	'label'=>'SNP LABs',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                        
					 return FingerprintSearch::getCantSnps($data->Fingerprint_Id);
					},
            ],
            [
           	'label'=>'Materials',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                        
					 return FingerprintSearch::getCantMaterials($data->Fingerprint_Id);
					},
            ],
            
            ["label" => "Crop", "value" => "crop.Name"],
            //''
            //'Project_Id',
            //'IsActive',

           
        ],
    ]); ?>
<?php  Pjax::end(); ?>
         
</div>
