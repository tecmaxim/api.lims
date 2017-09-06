<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Crop;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use miloschuman\highcharts\HighchartsAsset;

HighchartsAsset::register($this)->withScripts([]);
HighchartsAsset::register($this)->withScripts(['highstock', 'modules/exporting']);


?>

<?php $this->registerJs('init_dashboard_view("'.Yii::$app->request->baseUrl.'");', $this::POS_READY);?>

<div class="snp-index">
    
        <h1>Dashboard</h1>
        <div id="filters_query" style="background-color:#f5f5f5;">
            <div class="row">
               
                 <?php $form =  \yii\widgets\ActiveForm::begin(["id" => "dashboard-form",'action' =>'#']); ?>
                <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12" style="margin-top: 10px">  
                  <?= $form->field($model, 'cropId')->textInput(); ?>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <?= $form->field($model, 'TypeDate')->textInput(); ?>
                </div>
                <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <?= $form->field($model, 'DateFrom')->textInput(); ?>            
                </div>
                <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12" style="margin-top: 10px">
                  <?= $form->field($model, 'DateTo')->textInput(); ?>            
                </div>
                <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12" style="margin-top: -18px">
                    <?= Html::submitButton( '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Graphic ', ['class' => 'btn btn-primary  btn-dashboard']); ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div id="dashboard" style="margin-top: 25px;"></div>
    
</div>
<div id="divBlack" style="display:none;margin-left: -30px;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<script>
    $("#dashboard-form").submit(function(e)
    {
        e.preventDefault();
        
        $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');});
        
        $.ajax({
            url: "<?= Yii::$app->request->baseUrl?>/site/dashboard",
            data: {
                    cropId: $('#crop-cropid').val(),
                    DateType: $("#crop-typedate").val(),
                    DateFrom: $("#crop-datefrom").val(),
                    DateTo: $("#crop-dateto").val()
                  },
                          
            before: function()
            {
                $("#divBlack").fadeIn(function(){$('body').css('overflow','auto');});
            },
            success: function(response)
            {
                //console.log(response); 
                $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});
                $('#dashboard').html(response);
            }
        });
    })
</script>