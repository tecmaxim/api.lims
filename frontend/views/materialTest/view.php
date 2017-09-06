<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MaterialTest */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view material-test-view">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <?php if(Yii::$app->user->getIdentity()->itemName != "lab"): ?>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <p style="float: right;">
                <?=  Html::button(Yii::t('app', 'Update'),[
                    'class' => 'btn btn-primary',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['update', 'id' => $model->Material_Test_Id])
                ]); ?>
                <?=  Html::button(Yii::t('app', 'Delete'),[
                    'class' => 'btn btn-danger',
                    'data-reload' => '#modal',
                    'data-url' => Url::to(['delete', 'id' => $model->Material_Test_Id]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
                ]) ?>
            </p>
        </div>
        <?php endif;  ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'Material_Test_Id',
            'Name',
            ['label'=>'Crop', 'value'=>$model->crop->Name],
            'CodeType',
            'Owner',
            //'Generation',
            'HeteroticGroup',
            'Pedigree',
            //'Origin',
            //'Country',
            'Type',
            //'IsActive',
        ],
    ]) ?>

</div>
