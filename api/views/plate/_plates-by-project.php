<?php
use yii\helpers\Html;
?>

<?php if (Yii::$app->controller->action->id === "get-shipment-data"): ?>
    <div class="col-xs-12 col-sm-12 col-ms-10 col-lg-10">
        <h1>Shippment and Plates</h1>
    </div>
<div class="col-xs-12 col-sm-12 col-ms-2 col-lg-2">
        <?= Html::a("<span class='glyphicon glyphicon-save-file'>&nbsp;</span>Download Grids", ["download-plate-pdf", "projectIds" => $model->ProjectId], ['class' => 'btn btn-success btn-block btn-margin-5 font-white']); ?>
    </div>

<?php endif; ?>
<div class="row"> 
    <?php
    $row = 1;
    $col = 1;
    $cont = 1;
    $class = "position";
    $class_parent = "parents";
    //new
    $refs = [];
    foreach ($samplesByPlate as $sample) {
        /*
         * switch to define class
         */
        switch ($sample['StatusSampleId']) {
            case common\models\StatusSample::RECIVED_OK:
                $class = "sample_ok";
                break;
            case common\models\StatusSample::DEAD:
                $class = "rojo";
                //$class_parent = "rojo";
                break;
            case \common\models\StatusSample::_EMPTY:
                $class = "emptyness";
                //$class_parent = "emptyness";
                break;
            case \common\models\StatusSample::BAD_EXTRACTION:
                $class = "bad_adn";
                //$class_parent = "bad_adn";
                break;
            case \common\models\StatusSample::DNA_EXCTRACTED_OK:
                $class = "green_success";
                //$class_parent = "green_success";
                break;
            default:
                $class = "emptyness";
            //$class_parent = "parents"; 
        }

        /*
         * Begin of grid
         */
        //echo $sample['ProjectId'];
        $projectId = $sample['ProjectId'] != null ? $sample['ProjectId'] : $projectId;
        if ($cont == 1) {
            echo '<div class="hidden-xs hidden-sm col-md-7 col-lg-7 col-lg-offset-1 col-md-offset-1"> ';
            $plate = common\models\Plate::find()->where(["PlateId" => $sample['PlateId']])->asArray()->one();
            $name = common\models\Plate::getNamesProjectByPlateId($sample['PlateId']);
            if (count($name) > 1) {
                $refs[] = [ "name" => $name, 'plateId' => $plate['PlateId']];
                $name = "More than one project";
            } else
                $name = $name[0];
            echo $this->render('../project/resources/__plate-name-code', ['code' => $plate['PlateId']]);
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
        if ($sample['Type'] == "SAMPLE") {

            echo "<a href='#' onclick='return actions(event);'>"
            . "<div class='" . $class . "' id=''>" . $sample['SampleName'] . "<input type='hidden' name='' size='1'></input></div>"
            . "</a>";
        } elseif ($sample['Type'] == "CN" || $sample['Type'] == "F1") {
            $type = $sample['Type'] == "CN" ? "N" : $sample['Type'];
            echo "<a href='#' onclick='return actions(event);'>"
            . "<div class='parents' id='" . $sample['SamplesByPlateId'] . "'>" . $type . "<input type='hidden' name='' size='1'></input></div>"
            . "</a>";
        } elseif ($sample['Type'] == "PARENT") {
            if (array_key_exists($projectId, $array_parents)) {
                
                foreach ($array_parents[$projectId] as $parent) {
                    switch ($sample['StatusSampleId']) {
                        case common\models\StatusSample::RECIVED_OK:
                            $class_parent = "sample_ok";
                            break;
                        case common\models\StatusSample::DEAD:
                            $class_parent = "rojo";
                            break;
                        case \common\models\StatusSample::_EMPTY:
                            $class_parent = "emptyness";
                            break;
                        case \common\models\StatusSample::BAD_EXTRACTION:
                            $class_parent = "bad_adn";
                            break;
                        case \common\models\StatusSample::DNA_EXCTRACTED_OK:
                            $class_parent = "green_success";
                            break;
                        default:
                            $class = "position";
                            $class_parent = "parents";
                    }

                    echo "<a href='#' onclick='return actions(event);'>"
                    . "<div class='" . $class_parent . "' id=''>" . $parent['Name'] . "<input type='hidden' name='' size='1'></input></div>"
                    . "</a>";
                    $key = array_search($parent, $array_parents[$projectId]);
                    unset($array_parents[$projectId][$key]);

                    array_push($array_parents[$projectId], $parent);
                    break;
                }
                /*
                  if($parent['ProjectId'] == $projectId)
                  {
                  echo "<a href='#' onclick='return actions(event);'>"
                  . "<div class='".$class_parent."' id=''>" . $parent['Name'] . "<input type='hidden' name='' size='1'></input></div>"
                  . "</a>";

                  $key = array_search($parent, $array_parents);
                  unset($array_parents[$key]);

                  array_push( $array_parents, $parent);
                  //array_shift($array_parents);

                  break;
                  }else{

                  }
                 * 
                 */
            } 
        } elseif ($sample['Type'] == NULL || $sample['Type'] == 'NS') {

            //print_r($parent); exit;
            echo "<a href='#' onclick='return actions(event);'>"
            . "<div class='position-null' id='" . $sample['SamplesByPlateId'] . "'><input type='hidden' name='' size='1'></input></div>"
            . "</a>";
        }
        $row++;
        /*
         * Control of parents
         */

        if ($cont == 96) {
            $cont = 1;
            $row = 1; //$parents != null ? 1 : $row++;
            echo "</div>";
            echo "</div>";
            echo $this->render('../project/resources/__add-name-plate', ['name' => $name]);
            echo "</div>"; //col-9
            
            if(Yii::$app->user->getIdentity()->itemName != "breeder" && !isset($hiddenActions))
            {
                echo '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">';
                echo $this->render('../project/resources/__gridActions-shipment', [ "plate" => $plate, "model"=> $model]);
                echo '</div>';
            }
        } else {
            $cont++;
        }
    }

    /*
     * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
     */

    //El anterior Me da el próximo valor a insertar
    if ($cont <= 96 && $cont > 1) {

        echo $this->render('../project/resources/__completeGrid', ['cont' => $cont, 'rowActual' => $row]);
        echo "</div>"; //plate ID
        echo "</div>"; //col-8;
        echo "</div>"; //col-8;
        
        if(Yii::$app->user->getIdentity()->itemName != "breeder" && !isset($hiddenActions))
        {
            echo '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">';
            echo $this->render('../project/resources/__gridActions-shipment', ["plate" => $plate, "model"=> $model]);
            echo '</div>';
        }
    }

    if (count($refs) > 0) {
        echo '<div class="col-xs-12 hidden-sm-12 col-md-12 col-lg-12 "> ';
        echo '<h1>References</h1>';
        echo '</div>';
        echo '<div class="col-xs-12 hidden-sm-12 col-md-7 col-lg-7 col-lg-offset-1 col-md-offset-1"> ';
        foreach ($refs as $ref) {
            $name_plate = "<strong>" . 'TP'. sprintf('%06d',$ref['plateId']) . ":</strong><br>";
            foreach ($ref['name'] as $values) {
                $name_plate .= $values . "<br>";
            }
            echo $name_plate;
        }
        echo '</div>';
    }
    ?>


</div>

<script>
   $('body').on('hidden.bs.modal','#modal', function (e) {
            var pageHeight = $( document ).height();
            $('#divBlack').css({"height": pageHeight});
            
            $("#divBlack").show();
            var url = window.location.href;
            if(url.indexOf("sample-reception?") >= 1)
            {
                window.location = "<?= Yii::$app->urlManager->baseUrl?>/project/view?id=<?= $projectId?>";
            }else
                window.location.reload();
        });
</script>