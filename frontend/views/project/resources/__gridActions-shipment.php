<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default margin-top-30">
    <div class="panel-heading">Grid Actions</div>
    <div class="panel-body">
        <div class="row">
            <div class="hidden-md hidden-lg col-xs-12 col-sm-12"><h1><?= 'TP'.sprintf('%06d', $plate['PlateId']) ?></h1></div>
        </div>
        <div>
            
            <?php if ($plate['StatusPlateId'] < \common\models\StatusPlate::CONTROLLED): ?>
                <?php if($model->StepProjectId >= \common\models\StepProject::SAMPLE_RECEPTION): ?>
                    <?= Html::button(Yii::t('app', 'Samples Control')
                        , ['class' => 'btn btn-primary btn-block margin-bottom-10 font-white'
                        , 'data-toggle' => 'modal'
                        , 'data-target' => '#modal'
                        , 'data-url' => Url::to('../plate/samples-control?id=' . $plate['PlateId'])])
                    ?>
                <?php else: ?>
                    <div class=" col-lg-12 col-sm-12col-md-12 alert alert-warning ">
                        <strong>Attention:</strong> You must recive the samples first.
                    </div>
                <?php endif; ?>
            <?php else: ?>
             <?php
                $steps = \common\models\DateByPlateStatus::findAll(["PlateId" => $plate['PlateId']]);
                foreach ($steps as $step) {
                    echo '<div class="alert alert-info">';
                    echo "<p> Status: " . $step->statusPlate->Name . '</p>';
                    $date = explode('-', $step->Date);
                    $date_formated = implode("/", array_reverse($date));
                    echo " Date: " . $date_formated;
                    echo '</div>';
                }
                ?>
            
            
                <?php if ($plate['StatusPlateId'] == \common\models\StatusPlate::CONTROLLED): ?>
                    <?=  Html::a(Yii::t('app', 'Ready to extract DNA')
                            , '../plate'
                            , ['class' => 'btn btn-success btn-block margin-bottom-10', "target" => "_blank"])
                    ?>
                <?php endif; ?>
               
            <?php endif; ?>
        </div>

        <div>
<?=
Html::button(Yii::t('app', 'Request Again')
        , ['class' => 'btn btn-danger btn-block font-white'
    , 'data-toggle' => 'modal'
    , 'data-target' => '#modal'
    , 'data-url' => Url::to(['plate/order-again', 'id' => $plate['PlateId']])
])
?>
            <?php // Html::a('Order Again', ['#'], ['id'=>'reorder','class' => 'btn btn-info btn-grid-actions font-white']) ?>    

        </div>
        <?php if (($platesDiscarted = common\models\Plate::getDiscartedById($plate['PlateId'])) != null): ?>
            <div id="discarted_info">
                <div class=" col-lg-10 col-sm-10 col-md-10 info_success_plate alert alert-warning ">
                    <strong>Reorder Plate</strong>
                    <p>Last Reason : <?= $platesDiscarted['discarted']['causeByDescartedPlates']['Name'] ?></p>
                    <p>Last Date : <?= $platesDiscarted['discarted']['Date'] ?></p>
                    <p>Number of times : <?= $platesDiscarted['count'] ?></p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
