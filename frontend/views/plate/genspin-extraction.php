<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="pleate-create ">
    
    <div id="loading_modal" style="text-align:center; display: none"  >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        <span style="color:#333">Wait please..</span>
    </div>
    <div class="row" id="delete_msj" style="display:none">
        <div id="success" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-warning" >
            <b>The planet has been removed</b>.
        </div>
    </div> 
    <?php  if(!isset($ok)): ?>
    <?php  $form = ActiveForm::begin(['id' => 'itemForm']); ?>
    <div class="row"  id="content_form">
        
        <?php
            $row = 1;
            $col = 1;
            $cont = 1;
            $class = "sample_ok";
            $value = 4;
            
            echo $form->field( $adnModel, 'AdnExtractionId')->hiddenInput()->label(false);                            
            //foreach ($genspinSamples as $samplesByPlate)
            //{
                $end = 1;
				// $b = $genspinSamples[1];
				// $a = $genspinSamples[2];
				// $genspinSamples=[];
				// $genspinSamples[0] = $b;
				// $genspinSamples[1] = $a;
				// echo count($genspinSamples);exit();
				// $genspinSamples = array_slice($genspinSamples, 0, 385);
                foreach ($genspinSamples as $sample) 
                {
                    /*
                     * switch to define class
                     */
                    switch ($sample['StatusSampleId'])
                    {
                        case common\models\StatusSample::RECIVED_OK:
                            $class = "green_success";
                            break;
                        case common\models\StatusSample::DEAD:
                            $class = "emptyness";
                            break;

                        case \common\models\StatusSample::_EMPTY:
                            $class = "emptyness";

                    }

                    /*
                     * Begin of grid
                     */
                    if($end == 1)
                    {
                        $plateId = $sample['PlateId'];
                        $end = 0;
                        //print_r($parents[$plateId]); exit;
                    }
                    if ($cont == 1) {
                        /*
                         * echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "> ';*/
                        //$plateId = $sample['PlateId'];
                        /*echo $this->render('../project/resources/__plate-name-code',['code' => $plateId, 'name' => common\models\Plate::getNamesProjectByPlateId($sample['PlateId'])]);
                        echo "<div style='clear:both'>  </div>";
                        */
                        echo '<div class="col-xs-12"> <!-- COL-12-->';
                        echo $this->render('../project/resources/__plate-headers',['genspin' => true]);
                        echo "<div id='plate'> <!-- PLATE-->";
                        echo "<div id='row' > <!-- ROW-->";
                    }
                    /*
                     * End actual Row and open new row
                     */
                    if ($row >= 17) {
                        echo "</div> <!-- ROW-->"; // close Row
                        $row = 1; // reset
                        echo "<div id='row' > <!-- ROW-->"; // open Row
                    }
                    /*
                     * New cell
                     */
                    if($sample['Type'] == "SAMPLE")
                    {
                        if($sample['StatusSampleId'] == common\models\StatusSample::RECIVED_OK || $sample['StatusSampleId'] == null)
                        {
                            echo "<a href='#' onclick='return actions(event, ".$value.");'>"
                            . "<div class='" . $class . "' id='" . $sample['SamplesByPlateId'] . "'>" . $sample['samplesByProject']['SampleName']
                                ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='samples-".$sample['SamplesByPlateId']."' size='1' value='".$value."' ></input>"
                            ."</div>"
                            . "</a>";
                        }else
                            echo "<a href='#' onclick=''>"
                            . "<div class='" . $class . "' id='" . $sample['SamplesByPlateId'] . "'>" . $sample['samplesByProject']['SampleName']
                                ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='samples-".$sample['SamplesByPlateId']."' size='1' value='' ></input>"
                            ."</div>"
                            . "</a>";
                    }elseif($sample['Type'] == "CN" || $sample['Type'] == "F1" ){
                        $type =  $sample['Type'] == "CN" ? "N":$sample['Type'];
                        echo "<a href='#' onclick='return actions(event);'>"
                        . "<div class='parents' id='" . $sample['SamplesByPlateId'] . "'>" . $type 
                            ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='".$sample['SamplesByPlateId']."' size='1'></input>"
                        ."</div>"        
                        . "</a>";
                    }elseif($sample['Type'] == "PARENT")
                    {
                        foreach($parents[$plateId] as $parent)
                        {
                            /*
                            * switch to define class
                            */
                            switch ($sample['StatusSampleId'])
                            {
                                case common\models\StatusSample::RECIVED_OK:
                                    $class_parent = "green_success";
                                    break;
                                case common\models\StatusSample::DEAD:
                                    $class_parent = "emptyness";
                                    break;

                                case \common\models\StatusSample::_EMPTY:
                                    $class_parent = "emptyness";
                                    break;
                                default:
                                    $class_parent = 'parents';

                            }
                            if($sample['StatusSampleId'] == common\models\StatusSample::RECIVED_OK || $sample['StatusSampleId'] == null)
                            {
                                echo "<a href='#'onclick='return actions(event, ".$value.", true);'>"
                                    . "<div class='".$class_parent."' id='" . $sample['SamplesByPlateId'] . "'>" . $parent['Name'] 
                                    ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='samples-".$sample['SamplesByPlateId']."' size='1' value='".$value."'></input>"   
                                    . "</div>"
                                    . "</a>";
                            }else
                            {   
                                echo "<a href='#'onclick=''>"
                                    . "<div class='".$class_parent."' id='" . $sample['SamplesByPlateId'] . "'>" . $parent['Name'] 
                                    ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='samples-".$sample['SamplesByPlateId']."' size='1' value=''></input>"   
                                    . "</div>"
                                    . "</a>";
                            }    
                            array_shift($parents[$plateId]);
                           break;
                        }
                    }
                    elseif($sample['Type'] == NULL)
                    {
                        //print_r($parent); exit;
                        echo "<a href='#' onclick='return actions(event);'>"
                                ."<div class='position-null' id='" . $sample['SamplesByPlateId'] . "'>"
                                    ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='".$sample['SamplesByPlateId']."' size='1'></input>"
                                ."</div>"
                            ."</a>";

                    }
                    $row++;
                    /*
                     * Control of parents
                     */

                    if ($cont == 384) 
                    {
                        $cont = 1;
                        $row = 1; //$parents != null ? 1 : $row++;
                        echo "</div> <!--  -->";
                        echo "</div> <!--  -->";
                        echo "</div> <!-- col-9 -->"; //col-9

                    } else {
                        $cont++;
                    }
                    
                    /*
                     * Control to new plate. when it reaches 96 
                     */
                    if($end == 96)
                    {
                        $end = 1;
                    }
                        $end++;
                }
            //}
            /*
             * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
             */
            echo "<input type='hidden' name='PlateId' id='' size='1' value='".$plateId."' ></input>";
            //El anterior Me da el próximo valor a insertar
            if ($cont <= 384 && $cont > 1) {

                echo $this->render('../project/resources/__completeGrid', ['cont' => $cont, 'rowActual' => $row, 'genSpin'=> true]);
                echo "</div> <!-- Plate ID --> "; //plate ID
                echo "</div> <!-- col-8 -->"; //col-8;
                echo "</div> <!-- col-8 -->"; //col-8;
            }
            echo '<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1 "> ';
                echo $form->field( $adnModel, 'Comments')->textarea();
            echo '</div>';            
        ?>
        
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"></div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc" id="cancel" data-dismiss="modal" > <?=  Yii::t('app', 'Cancel Plate');  ?> </button>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <?= Html::submitButton ('Save ',['class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"></div>
       <?php  ActiveForm::end(); ?>
    </div>
    
    <script>
        actions = function(e, adn, parents = null)
        {   
            var ide= e.srcElement.id;
            var realValue = adn;
            var errorValue = adn === 4 ? 5:2;
            var class_seted = adn === 4 ? 'green_success':parents === null ?'sample_ok':'parents';
            var class_recived = 'position';
            console.log(realValue);
            console.log(ide);
            if($("#samples-"+ide).val() === "")
            {
                $("#samples-"+ide).val(realValue);
                $("#"+ide).removeClass(class_recived);
                $("#"+ide).addClass(class_seted);
            }
            else if($("#samples-"+ide).val() == realValue)
            {
                //alert(class_seted);
                $("#samples-"+ide).val(errorValue);
                //$("#"+ide).text(2);
                $("#"+ide).removeClass(class_seted);
                $("#"+ide).addClass("rojo");
            }
            else
            {
                $("#samples-"+ide).val("");
                //$("#"+ide).text("");
                $("#"+ide).removeClass('rojo');
                $("#"+ide).addClass(class_recived);
            }
            return false;
        };
        
        
        $("#itemForm").submit(function()
        {
            if($(".has-error").length > 0)
            {
                alert("Please correct the errors and try again.");
                event.preventDefault();
                return false;
            }
            $("#itemForm").slideUp('500');
            $("#loading_modal").fadeIn('200');
       /*
             $.ajax({
                type: "POST",
                url: "<?= Yii::$app->homeUrl; ?>plate/save-gen-spin?adnExtractionId=<?= $adnModel->AdnExtractionId;?>",
                data: $("#itemForm").serialize(),
                success: function (e) {
                        //alert(e);
                        //$("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});

                        $("#loading_modal").fadeOut('200');
                        $('.modal-content').attr('style', 'min-height: 500px !important');
                        $('#pleate-create').html('<div class="alert alert-success">The plate has been saved succesfully</div>'); 
                        //event.preventDefault();

                    }
                //dataType: dataType
              });
            */
        });
       
        
        /*$("#cancel").click(function(e){
            $.ajax({
                    url: "<?= Url::toRoute('plate/cancel-plate-genspin'); ?>",
                    data: {adnExtractionId: <?= $adnModel->AdnExtractionId ?>},
                    beforeSend: function () {
                        //$(".pleate-create").slideUp('500');
                        $("#itemForm").slideUp('500');
                        $("#content_form").slideUp('500');
                        $("#loading_modal").fadeIn('200');
                    },
                    success: function (response)
                    {
                        if(response == 'ok')
                        {
                            $("#loading_modal").fadeOut('200');
                            $("#delete_msj").fadeIn('500');    
                        }    
                    }
                });
        });
    
    */
    </script>
    <?php  else: ?>
    <div class="row">
        <div id="success" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-success" >
            Counting samples successfully saved!
        </div>
    </div> 
    <script>
    $(function(){
        $("#loading_modal").fadeOut('200');
    });
    </script>
   
    <?php  endif; ?>

</div>
<style>    
    #row
    {
        margin: 4px;
        width: auto;
        /*background-color: #fff;*/
        overflow: auto;
    }
    #container-index-left
    {
        margin-top:25px;
    }
    #index_up {
        margin-left: 4.4px;
        width: 35px;
        font-weight: bold;
    }
    .position:hover
    {
        background-color: #eee;
        cursor: pointer;
        
    }
    
    #menu_float2
    {
        right: 340px;
    }
    
    .modal-dialog
    {
        width: 1300px !important;       
    }
   
    .modal-content
     {
         min-height:940px !important;
         height: auto;
         
     }
    .position ,.verde ,.rojo ,.parents, .position-null, .emptyness, .green_success, .sample_ok, .bad_adn
    {
        width: 30px !important;
        height: 30px !important;
        font: 7px Arial;
    }
    
</style>
