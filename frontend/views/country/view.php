<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-view">

    <div class="row">
       
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->CountryId])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->CountryId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
                ]) ?>
            </p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'CountryId',
            'Name',
            //'IsActive:boolean',
        ],
    ]) ?>

</div>
