<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Traits */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Traits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traits-view">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->TraitsId])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->TraitsId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
                ]) ?>
            </p>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'TraitsId',
            ['label'=>'Crop','value'=>$model->crop->Name],
            'Name',
            'Description',
            //'IsActive:boolean',
        ],
    ]) ?>

</div>
