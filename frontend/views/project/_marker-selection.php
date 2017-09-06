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
<div id="divBlack" style="display:none;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<div class="project-form">
   
    <div class="container">
        <div class="row">
            
        <?=  $this->render('_header-steps');  ?>
        <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
        </div>
        <div class="row marker-blue-container" >
         
                <?php $form3 = ActiveForm::begin(["id"=>"form_step2", "action"=>isset($update)? "update-select-markers?idProject=".$model->ProjectId:"select-markers?id=".$model->ProjectId]); 
                   
                ?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>
             
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= Html::a('Markers Selection', ['query/query1' , 'method' =>1 , 'projectId'=>$model->ProjectId, "update" => isset($update)? true:false, "projectName"=> $projectName], ['class' => 'btn btn-success in-project padding-10', "id"=>"button2"]) ?>
                     <?= Html::a('Polymorphism Search', ['query/query2', 'method' =>2, 'projectId'=>$model->ProjectId, "update" => isset($update)? true:false, "projectName" => $projectName], ['class' => 'btn btn-success in-project padding-10', "id"=>"button3"]) ?>
                <?php //= Html::Button('Edit', ['type'=>'submit', 'class' => 'btn btn-warning padding-10']) ?>                   
                </div>
                <div style="display: none"><?= $form3->field($model, 'ProjectId')->hiddenInput(); ?> </div>
              
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?php // $form3->field($model, 'MarkerId')->textInput(); ?>
                    <?= $form3->field($model, 'Snp_lab_Id')->textInput(); ?>
                </div>
               
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?php //= $form3->field($model, 'MarkersCopy')->dropDownList(ArrayHelper::map(Marker::find()->where(["IsActive"=>1, "Crop_Id"=>$project->Crop_Id])->limit(30)->all(), 'Marker_Id', 'Name'), ['prompt'=>'-- Select Campaign --']) ?>
                    <?= $form3->field($model, 'MarkersCopy')->textarea(["rows"=>6]) ?>
                </div>   
                 
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <!--<button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc" id="BackToStep1"><?= Yii::t('app', 'Back To Previous Step'); ?></button>-->
                    <?= Html::a('Back To Previous', ['update?id='.$model->ProjectId], ['class' => 'btn btn-primary in-nuevos-reclamos grey-ccc']) ?>
                </div>
                 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    
                    <?= Html::button( isset($update)?'Save':'Save & Next',['type'=>'submit','class' => 'btn btn-primary in-nuevos-reclamos',]) ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Cancel Marker Selection'); ?></button>
                </div>
            </form>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
        </div> 
    </div>
</div> 

<script>
    // $("#button3").click(function(){alert('Disable in Testing Mode.'); return false;})
    $("#step1").removeClass("selected");
    $("#step2").addClass("selected");
    $("#cancel").click(function()
                         {
                            window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $model->ProjectId ?>";
                         });
    init = function()
    {         
        var pageHeight = $( document ).height();
        //var pageWidth = $(window).width();
        $('#divBlack').css({"height": pageHeight});   
        
        $('#markersbyproject-snp_lab_id').kendoDropDownList({
                        filter: "startswith",
                        optionLabel: "Find SnpLabs",
                        dataTextField: "name",
                        select:function(e){ 
                                           var dataItem = this.dataSource.view()[e.item.index()];
                           
                                            var target = $("#markersbyproject-markerscopy").val();
                                            if (target !== "")
                                                $("#markersbyproject-markerscopy").val(target+"\n"+dataItem.name);
                                            else
                                                $("#markersbyproject-markerscopy").val(dataItem.name);
                                         },
                        dataValueField: "id",
                        dataSource: {
                           serverFiltering: true,
                            transport: {
                                read: {
                                    /*Old */
                                    //url: "<?= Yii::$app->homeUrl ?>marker/get-markers-by-kendo?crop=<?php // $model->Crop_Id ?>",
                                    url: "<?= Yii::$app->homeUrl ?>snplab/get-snplabs-by-kendo?crop=<?= $model->Crop_Id ?>",
                                }
                            }
                        }
                        //serverFiltering: true,
                    });
        
    };
        
    $("#form_step2").submit(function()
    {
        $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');});
    });
</script>

            
            