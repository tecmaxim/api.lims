<?php

use yii\widgets\DetailView;
use common\models\StepProject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;


?>
<div id="divBlack" style="display:none; overflow-y: hidden ">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>

<!-- Headers Actions-->
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7" style="padding-left:80px !important;">
        <h1><?= $model->Name?></h1>       
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left:80px !important;">
        <?=
        Html::button(Yii::t('app', 'On Hold'), [
            'class' => 'btn btn-warning btn-action-project',
            //'id' => 'onHold_button',
            'data-toggle' => 'modal',
            'data-target' => '#modal',
            'data-url' => Url::to(['on-hold', 'id' => $model->ProjectId]),
        ])
        ?>
        <?php if ($model->StepProjectId >= 1 && $model->StepProjectId < \common\models\StepProject::GENOTYPED) : ?>

            <?=
            Html::button(Yii::t('app', 'Cancel'), [
                'class' => 'btn btn-default btn-action-project grey-ccc reset',
                //'id' => 'onHold_button',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-url' => Url::to(['cancel', 'id' => $model->ProjectId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
            ])
            ?>

        <?php endif; ?>

        <?php if ($model->StepProjectId >= \common\models\StepProject::PROJECT_DEFINITION && $model->StepProjectId < \common\models\StepProject::SENT): ?>

            <?=
            Html::button(Yii::t('app', 'Delete Project'), [
                'class' => 'btn btn-danger btn-action-project',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-url' => Url::to(['delete', 'id' => $model->ProjectId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
            ])
            ?>

        <?php elseif ($model->StepProjectId >= \common\models\StepProject::GENOTYPED && $model->StepProjectId < \common\models\StepProject::ON_HOLD): ?>
            <?=
            Html::button(Yii::t('app', 'Finish Project'), [
                'class' => 'btn btn-action-project brown',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-url' => Url::to(['finish', 'id' => $model->ProjectId]),
                    // 'data' => [
                    //    'method' => 'post',
                    // ],
            ])
            ?>
        <?php endif; ?>
    </div>
</div>

<div class="new-container">
    <!-- Size Screen helpers-->
    <!--
    <div class="hidden-lg hidden-md hidden-sm float"> <h2>XS screen</h2></div>
    <div class="hidden-lg hidden-md hidden-xs float"> <h1>SM screen</h1></div>
    <div class="hidden-lg hidden-xs hidden-sm float"> <h1>MD screen</h1></div>
    <div class="hidden-xs hidden-md hidden-sm float"> <h2>LG screen</h2></div>
    -->
    <!-- Progress bar -->
    <section id="progress-bar">
        <div class="row">
            <div class="col-md-12">
                <div class="progress">
                    <?php $left = 5; ?>
                    <?php foreach ($steps as $key => $step):?>
                        <?php $step['StepProjectId'] < $progress['avilableStep'] ? $class = "primary-color" : $class = "no-color";?>
                        <!-- modify numbers step for breeder case.-->
                        <?php if(Yii::$app->user->getIdentity()->itemName == 'breeder'):?>
                        <div class="item-step <?= $class;?>" style="left:<?= $left?>%;"><?= StepProject::getNumberBreeder($step['StepProjectId']) ;?></div>
                        <?php else: ?>
                            <div class="item-step <?= $class;?>" style="left:<?= $left?>%;"><?= $step['StepProjectId'] == StepProject::REPORT ? '8':$step['StepProjectId'];?></div>
                        <?php endif; ?>
                            <div class="item-detail" style="left:<?= $left;?>%;"><?= $step['Name'] === 'Sent' ? 'Shipment':$step['Name']?></div>

                        <?php $left += 12; ?>
                    <?php endforeach; ?>
                    
                    <div class="progress-bar" style="width: <?= $progress['percent'] ?>%;"></div>
                </div>
                
            </div>
        </div>
    </section>
    <!-- Buttons -->
    <section id="button-steps">
        <div class="row">
           
            <?php foreach ($steps as $key => $step):?>
                <!-- Define the button class-->
                <?php $step['StepProjectId'] <= $progress['avilableStep'] ? $class = $step['Color'] : $class = "disabled";?>
                
                <?php if($step['StepProjectId'] <= $progress['avilableStep'] || ($progress['avilableStep'] == StepProject::SENT && $step['StepProjectId'] <= StepProject::SENT)): ?>
                    <?= $step['url']; ?>
                <?php else: ?>
                    <a href="#" onclick="return false;" />
                <?php endif; ?>
                
                        
                <div class="col-lg-3 col-sm-12 col-xs-12 col-md-4">
                    <div class="new-button-steps <?= $class ?>">
                        <span class="glyphicon glyphicon-<?= $step['Icon']?> icon-button"></span>
                        <div class="data">
                            <?= $step['info']; ?>
                        </div>
                        <div class="label-step">
                           <?= $step['Name'] === 'Sent' ?  Yii::$app->user->getIdentity()->itemName == "breeder" ? "Create Plates " : 'Shipment & Plates Status' : $step['Name']?>
                        </div>
                    </div>
                </div>
                </a>
            <?php endforeach; ?>
        
        </div> 
    </section>
    
    <section id="content-render">
        
    </section>
</div>

<script>
    var pageHeight = $( document ).height();
    var pageWidth = $(window).width();
     //alert(pageHeight); exit;
    $('#divBlack').css({"height": pageWidth});
    
    
</script>