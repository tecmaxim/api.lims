<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    
    #row
    {
        margin: 5px;
        //background-color: #fff;
        overflow: auto;
        
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
        width: 830px !important;       
    }
    <?php if(isset($adnExtraction)): ?>
       .modal-content
        {
            min-height:650px !important;
            height: auto;
        }
    <?php else: ?>
        .modal-content
        {
            height: 600px !important;
        }
    <?php endif; ?>
    
</style>
<div class="pleate-create">
    
    <div id="loading_modal" style="text-align:center; display: none" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        <span style="color:#333">Wait please..</span>
    </div>
    <?php  if(!isset($ok)): ?>
    
    <div class="row"> 
        <?php
            $row = 1;
            $col = 1;
            $cont = 1;
            $class = "sample_ok";
            $value = 1;
            $form = ActiveForm::begin(['id' => 'itemForm']); 
            //$form->field($model, 'PlateId')->hiddenInput()
            if(isset($adnExtraction))
            {
              $value = 4;  
             
            }
            foreach ($samplesByPlate as $sample) 
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
                if ($cont == 1) {
                    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "> ';
                    $plateId = $sample['PlateId'];
                     $name = common\models\Plate::getNamesProjectByPlateId($sample['PlateId']);
                    if(count($name)> 1)
                    {
                        $refs[] =[ "name" => $name, 'plateId' => $sample['PlateId']];
                        $name = "More than one project";
                    }else
                        $name = $name[0];
                    echo $this->render('../project/resources/__plate-name-code',['code' => $plateId, 'name' => $name]);
                    echo "<div style='clear:both'>  </div>";

                    echo $this->render('../project/resources/__plate-headers');
                    echo "<div id='plate'>";
                    echo "<div id='row' >";
                }
                /*
                 * End actual Row and open new row
                 */
                if ($row >= 9) {
                    echo "</div>"; // close Row
                    $row = 1; // reset
                    echo "<div id='row' >"; // open Row
                }
                /*
                 * New cell
                 */
                if($sample['Type'] == "SAMPLE")
                {
                    if($sample['StatusSampleId'] == common\models\StatusSample::RECIVED_OK || $sample['StatusSampleId'] == null)
                    {
                        echo "<a href='#' onclick='return actions(event, ".$value.");'>"
                        . "<div class='" . $class . "' id='" . $sample['SamplesByPlateId'] . "'>" . str_replace('_', '<br>_', str_replace('-','<br>-',$sample['SampleName']))
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
                    echo "<a href='#'>"
                    . "<div class='parents' id='" . $sample['SamplesByPlateId'] . "'>" . $type
                        ."<input type='hidden' name='samples[".$sample['SamplesByPlateId']."]' id='".$sample['SamplesByPlateId']."' size='1'></input>"
                    ."</div>"        
                    . "</a>";
                }elseif($sample['Type'] == "PARENT")
                {
                    foreach($parents as $parent)
                    {
                        //print_r($parent); exit;
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
                        
                        array_shift($parents);
                       break;
                    }
                }
                elseif($sample['Type'] == NULL || $sample['Type'] == 'NS')
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

                if ($cont == 96) 
                {
                    $cont = 1;
                    $row = 1; //$parents != null ? 1 : $row++;
                    echo "</div>";
                    echo "</div>";
                    echo "</div>"; //col-9
                    
                } else {
                    $cont++;
                }
            }
            /*
             * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
             */
            echo "<input type='hidden' name='PlateId' id='' size='1' value='".$plateId."' ></input>";
            //El anterior Me da el próximo valor a insertar
            if ($cont <= 96 && $cont > 1) {

                echo $this->render('../project/resources/__completeGrid', ['cont' => $cont, 'rowActual' => $row]);
                echo "</div>"; //plate ID
                echo "</div>"; //col-8;
                echo "</div>"; //col-8;
            }
            
            if(isset($adnExtraction))
            {
              echo '<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1 "> ';
                 echo $form->field( $adnModel, 'Comments')->textarea();
              echo '</div>';
            }
        ?>
            
        
        
        <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
            <?= Html::submitButton ('Save ',['type'=>'submit','class' => 'btn btn-primary in-nuevos-reclamos']) ?>
        </div>
        
        
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
        //console.log(realValue);
        //console.log(ide);
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
    
    /*
    saved = function(e)
    {
        var data = { plateId:<?=$plateId; ?> , lola:$("plate_samples").serialize() };
        $.ajax({
                    //type: "post",
                    url: "../plate/samples-control?id=<?= $plateId ?>",
                    data:  data,
                    beforeSend: function () 
                    {
                        $("#plate_samples").slideUp('500');
                        $("#loading").show();
                    },
                    success: function ()
                    {
                       $("#loading").hide();
                       $("#success").delay('600').show('500');
                       
                    }
                });
    };
        
    selectAll = function()
    {   //console.log($("input[name='vCheck[]']:checked").length);
               
               
            $( ".position" ).each(function( index ) {
             // console.log( index + ": " + $( this ).text() );
             $( this ).addClass("verde");
             
            });
            
           
        //$("#"+ide).removeClass('position');
       // $("#"+ide).addClass("verde");
        //return false;
    }*/
    
    $(function(){
        $("#itemForm").submit(function()
        {
            if($(".has-error").length > 0)
            {
                alert("Please correct the errors and try again.");
                return false;
            }
            $("#itemForm").slideUp('500');
            $("#loading_modal").fadeIn('200');
        });
    });
   
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
  
          setTimeout(function () {
                     $('#modal').modal('toggle');
                 }, 1500);
                 
    });
    </script>
   
    <?php  endif; ?>

</div>

