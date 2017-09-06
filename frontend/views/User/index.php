<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\AuthAssignment;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="user-index">

   <div class="row">
         <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
       
       <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <?= Html::button("Create User"          
               , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
               , 'data-toggle' => 'modal'
               , 'data-target' => '#modal'
               , 'data-url' => Url::to('../site/signup')
                  ]);
           ?>
       </div>
   </div>
    <?php echo $this->render('_search', ['searchModel' => $searchModel, 'role' => $role]);  ?>
  
    <?php //= Html::a('Create User', ['site/signup'], ['class' => 'btn btn-primary btn-nuevo-reclamo']) ?>
    
    <?php if(isset($success)): ?>	
	 <div class="alert alert-success" role="alert"> Se creó el usuario satisfactoriamente</div>
    <?php endif;?>
    
   <?php  Pjax::begin(['id' => 'itemList']) ; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => [
            ['class' => 'frontend\widgets\RowLinkColumn'],
            //'UserId',
            'Username',
            [
           	'label'=>'Rol',
           	'format' => 'raw',
       		'value'=>function ($data) {
					return $data->itemName;
					},
            ],
            [
           	'label'=>'Crops',
           	'format' => 'raw',
       		'value'=>function ($data) {
                                            $cult ="";
					foreach($data->cropbyusers as $cb)
                                            $cult .= $cb->crop->Name.", ";
                                        return substr($cult, 0, -2);// para sacar el punto y coma del final
					},
            ],
            'Email',
            'CreatedAt',
        ],
    ]); ?>
         
<?php  Pjax::end(); ?>
