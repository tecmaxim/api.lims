<?php

use yii\helpers\Html;

?>
<div class="panel panel-default margin-top-30">
    <div class="panel-heading">Grid Actions</div>
    <div class="panel-body">

        <?php if ($isSent == 1): ?>
            <div class="alert alert-info" >
                <b>This grid has been sent.</b>
            </div>
            
            <div>
                <?php //= Html::a('Order Samples Again', ['update?id=klxnsand'], ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create font-white'])  ?>    
                <?php /* Html::button(Yii::t('app', 'Order Samples Again')
                  , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create font-white'
                  , 'data-toggle' => 'modal'
                  , 'data-target' => '#modal'
                  , 'data-url' => Url::to('../reasonbyproject/create?projectId='.$projectId)])
                 */ ?>
            </div>
        <?php endif; ?>
        <?= Html::a('Download Grid', ['#'], ['id' => 'download-grid', 'class' => 'btn btn-success btn-lg btn-block font-white' , 'onclick' => 'return downloadGrid('.$projectId.')']); ?>    
            <div style="margin-left: 30px; display: block ">
                <?php if ($isF1): ?>
                    Add the F1 to the grid: <input type="checkbox" name="f1" id="f1" checked />

                <?php endif; ?>
            </div>
    </div>
</div>


