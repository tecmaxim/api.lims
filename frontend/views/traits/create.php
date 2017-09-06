<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Traits */

$this->title = Yii::t('app', 'Create Traits');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Traits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
