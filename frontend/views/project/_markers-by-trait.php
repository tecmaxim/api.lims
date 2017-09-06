<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Crop;
use common\models\Campaign;
use common\models\User;
use common\models\Marker;
use common\models\ProjectType;
use yii\jui\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use miloschuman\highcharts\HighchartsAsset;


/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('init();', $this::POS_READY);
?>
<div class="project-form">
   
    <div class="container">
        <div class="row">
            
        <?=  $this->render('_header-steps');  ?>
        </div>
        <div class="row marker-blue-container" >
                <?php  $form3 = ActiveForm::begin(["id"=>"form_step2"]); 
                   
                ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="alert alert-info"> <b>PLF:</b> 180 Markers</div>
                 </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= Html::a('Markers Search', ['query/query1' , 'method' =>1], ['class' => 'btn btn-warning padding-10', "id"=>"button2"]) ?>
                     <?= Html::a('Polymorphism Search', ['query/query2', 'method' =>2], ['class' => 'btn btn-warning padding-10', "id"=>"button2"]) ?>
                <?php //= Html::Button('Edit', ['type'=>'submit', 'class' => 'btn btn-warning padding-10']) ?>                   
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?= $form3->field($model, 'Markers')->widget(Select2::className(),[
                                'name' => 'Markers',
                                'data' => ArrayHelper::map(Marker::find()->where(["IsActive"=>1])->limit(30)->all()  , "Marker_Id", "Name" ),
                                'options' => ['placeholder' => '',
                                                 'multiple' => true,
                                                ],
                                ]); 
                            ?>
                </div>
               
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <!--<button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc" id="BackToStep1"><?= Yii::t('app', 'Back To Previous Step'); ?></button>-->
                    <?= Html::a('Back To Previous', ['create'], ['class' => 'btn btn-primary in-nuevos-reclamos grey-ccc']) ?>
                </div>
                 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= Html::a('Next', ['create', 'step' => 3], ['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset"><?= Yii::t('app', 'Cancel'); ?></button>
                </div>
            </form>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
        </div>
    </div>
           
            
            
<script>
     $("#step1").removeClass("selected");
    $("#step2").addClass("selected");
    init = function()
    {
       
        $("#plate_samples").submit(function(e){
           e.preventDefault();
           $("#step6").slideDown(600, function()
                    {
                        $("#step5").slideUp("600");
                        $('#h3s5 h3').addClass("grey");
                        $('#h3s6 h3').removeClass("grey");
                    });
         });
       
       /*********************   ONLY STEP 2   ************************/
      
         
     
        /*********************   //ONLY STEP 2   ************************/

        $(".reset").click(function(e)
        {
           e.preventDefault();
           window.location=  "<?= Yii::$app->homeUrl; ?>project/create";
        });
        $("#select_markers").click(function(e)
        {
           e.preventDefault();
           $("#step3").slideUp(600, function()
            {
                $('#h3s3 h3').addClass("grey");
                $('#h3s2 h3').removeClass("grey");
                $("#step2").slideDown("slow");
            });     
        });
       
       $("#saveStep2").click(function(e)
       {
           e.preventDefault();
           $("#step2").slideUp(600, function()
            {
                $('#h3s2 h3').addClass("grey");
                $("#step4").slideDown("slow");
                $('#h3s4 h3').removeClass("grey");
            });
       })
       
       
    
    /*******************    BACK TO  *************************/
    
        $("#BackToStep1").click(function(e)
           {
               e.preventDefault();
               $("#step2").slideUp(600, function()
                {
                    $('#h3s2 h3').addClass("grey");
                    $("#step1").slideDown("slow");
                    $('#h3s1 h3').removeClass("grey");
                });
           });
        $("#BackToStep2").click(function(e)
        {
            e.preventDefault();
            $("#step4").slideUp(600, function()
             {
                 $('#h3s4 h3').addClass("grey");
                 $("#step2").slideDown("slow");
                 $('#h3s2 h3').removeClass("grey");
             });
        });
        $("#BackToStep4").click(function(e)
        {
            e.preventDefault();
            $("#step5").slideUp(600, function()
             {
                 $('#h3s5 h3').addClass("grey");
                 $("#step4").slideDown("slow");
                 $('#h3s4 h3').removeClass("grey");
             });
        });
        
       
    };
    
    selectAll = function()
    {   //console.log($("input[name='vCheck[]']:checked").length);
        if ( $("input[name='vCheck[]']:checked").length == 0){
		$("input[name='vCheck[]']").prop("checked",true);	
	}else{
		$("input[name='vCheck[]']").prop("checked", false);	
	}
        //return false;
    };
    
    retry = function()
    {
        
        if($("#project-projecttype").val() == 1)
        {
            $("#traits").hide();
            $("#parent2").hide();
            $("#parent1").hide();
            $("#project-numbersamples").hide();
            
            alert("Agregar opción para agregar muchas lineas");
            $.ajax({
            url: "<?= Url::toRoute('material-test/get-materials-by-crops-by-ajax');?>",
            data: {cropId: $('#Crop_Id').val()},
            success: function(response)
            {
                $("#materials").html(response);
                
             
            }
            
        });
        
        } 
       return false;   
    };
       
   
</script>
