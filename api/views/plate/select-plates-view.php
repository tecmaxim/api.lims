<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Plate */

?>

<div id="select-plates">
    <div class="row">    
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
            <h1>Select Plates</h1>
        </div>
    </div>
    <div id="loading_modal" style="text-align:center; display: none" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        <span style="color:#333">Creating Genspin plates...</span>
    </div>
    <div class="row">
        <?php $form = ActiveForm::begin(['id' => 'itemForm', 'enableAjaxValidation' => true, 'enableClientScript' => true]); ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= $form->field($model, 'plateSelected')->textInput(['readonly' => true]) ?>  
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?= $form->field($model, 'Plates')->widget(Select2::classname(), [
                            'data' =>  ArrayHelper::map($platesControlled, "PlateId", "Name"),
                            //'type' => 2,
                            'options' => ['placeholder' => 'Loading..', 'multiple' => true],
                            'pluginOptions' => [
                                    'allowClear' => true,
                                    //'depends' => ['plateSelected'],
                                    //'placeholder' => false,
                                    //'Loading' => true,
                                    'tags' => true,
                                    'maximumInputLength' => 3,
                                    //'url' => Url::to(['/plate/get-plates-controlleds'])
                                ],
                        ]); 
                    ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
            <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"> <?=  Yii::t('app', 'Cancel');  ?> </button>
        </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    $('#itemForm').submit(function(e){
        if($(".has-error")[0])
        {
            $('#itemForm').slideDown(500);
            $('.modal-content').css('height','600px');
            $("#loading_modal").hide(200);
            
        }else
        {
        var cant = $('.select2 li').length;
            if(cant > 4)
            {
                e.preventDefault();
                alert('You can select up to 3 items.');
                return false;
            }
            
            $('#itemForm').slideUp(500);
            $('.modal-content').css('height','600px');
            $("#loading_modal").fadeIn(200);
        } 
    });
</script>