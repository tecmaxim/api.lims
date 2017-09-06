<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->Username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                 <p class="pull-right">
            <?= Html::a(Yii::t('app', 'Index'), ['index', 'id' => $model->UserId], ['class' => 'btn btn-gray']) ?>


            <?= Html::button(Yii::t('app', 'Update'),
                                                      ['class' => 'btn btn-primary', 
                                                       'data-reload' => '#modal',
                                                       'data-url' => Url::to(['update', 'id' => $model->UserId]),
                                                      ]); ?>
            <?= Html::button(Yii::t('app', 'Delete'),
                        [ 'class' => 'btn btn-danger',
                          'data-reload' => '#modal',
                          'data-url' => Url::to(['delete', 'id' => $model->UserId]),
                           ]); ?>

                 </p>
            </div>
    </div>
   

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'UserId',
            'Username',
            //'AuthKey',
            [
                'attribute'=>'Role',
                'format'=>'raw', 
                'value'=>'<kbd>'.$model->ItemName. '</kbd>', 
                'displayOnly'=>true
            ],
            [
           	'attribute'=>'Crops',
           	'format' => 'raw',
                'value'=>$model->getCropbyusersArray(),
                'displayOnly'=>true
            ],
            //'PasswordResetToken',
            'Email:email',
            'CreatedAt',
            //'UpdatedAt',
            
            //'IsActive:boolean',
        ],
    ]) ?>                          
 
</div>
