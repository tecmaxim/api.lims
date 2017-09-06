<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Allele */

$this->title = $model->Allele_Id;
$this->params['breadcrumbs'][] = ['label' => 'Alleles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="allele-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p style="float: right;">
        <?= Html::a('Index', ['index', 'id' => $model->Allele_Id], ['class' => 'btn btn-gray']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->Allele_Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Allele_Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Allele_Id',
            'IsActive',
            'LongDescription',
        ],
    ]) ?>

</div>
