<?php

$plateModel = new \common\models\Plate();
$row = 1;
$col = 1;
$cont = 1;
$class = "position";
$class_parent = "parents";
$round = 1;
$grid_count = 0;
$var = "";
$refs = []; // this is used to save references when 2 or mar projects are combined
foreach ($samplesByPlate as $sample) {
    /*
     * Begin of grid
     */
    //echo $sample['ProjectId'];
    $projectId = $sample['ProjectId'] != null ? $sample['ProjectId'] : $projectId;
    if ($cont == 1) {
        //echo "<div style='clear:both'></div>";
        if ($round > 1 && $round % 2 != 0) {
            if($grid_count == 4)
            {
                $grid_count = 0;
                $var .= '<pagebreak />';
            }
                
            echo '<div style="clear:both; width:100%; height:5px; border:0px solid #000;"></div>';
        }
        $round++;
        $grid_count++;
        echo "<div id='cutter'>";
        echo "<div id='plate'>";
        $plate = common\models\Plate::find()->where(["PlateId" => $sample['PlateId']])->asArray()->one();
        $name = common\models\Plate::getNamesProjectByPlateId($sample['PlateId']);
        
        if(count($name)> 1)
        {
            $refs[] =[ "name" => $name, 'plateId' => $plate['PlateId']];
            $name = "More than one project";
        }else
            $name = $name[0];
        $code = $plate['PlateId'];
        
        echo '<div style="width:60px; font-size: 0.7em; border:0px solid #000; margin-left:15px;">' . 'TP'.sprintf('%06d', $code) . '</div>';
        echo '<div style="clear:both; width:100%; height:1px;"></div>';

        echo "<div class='columns' >";
    }

    /*
     * End actual Row and open new row
     */
    if ($row >= 9) {
        echo "</div>"; // close Row
        $row = 1; // reset
        echo "<div class='columns' >"; // open Row
    }
    /*
     * New cell
     */
    if ($sample['Type'] == "SAMPLE") {
        
        echo "<a href='#' onclick='return actions(event);'>"
        . "<div class='" . $class . "' id=''>" . str_replace('_', '<br>_', str_replace('-','<br>-',$sample['SampleName'])) . "<input type='hidden' name='' size='1'></input></div>"
        . "</a>";
    } elseif ($sample['Type'] == "CN" || $sample['Type'] == "F1") {
        $type = $sample['Type'] == "CN" ? "N" : $sample['Type'];
        echo "<a href='#' onclick='return actions(event);'>"
        . "<div class='parents' id='" . $sample['SamplesByPlateId'] . "'>" . $type . "<input type='hidden' name='' size='1'></input></div>"
        . "</a>";
    } elseif ($sample['Type'] == "PARENT") {
        if (array_key_exists($projectId, $array_parents)) {
            foreach($array_parents[$projectId] as $parent)
            {
                echo "<a href='#' onclick='return actions(event);'>"
                . "<div class='" . $class_parent . "' id=''>" . (string)$parent['Name'] . "<input type='hidden' name='' size='1'></input></div>"
                . "</a>";

                $key = array_search($parent, $array_parents[$projectId]);
                unset($array_parents[$projectId][$key]);

                array_push( $array_parents[$projectId], $parent);
                break;
                            
            }
        }
    } elseif ($sample['Type'] == NULL || $sample['Type'] == 'NS') {
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
        echo '<div style="float:left; width:400px; font-size: 0.7em;margin:-5px 0px 3px 15px; border:0px solid #00ff55">' . $name . '</div> ';
        echo "</div>";
        //echo "</div>"; //col-9
        //echo '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">';
        //echo    $this->render('../project/resources/__gridActions-shipment', [ "plate" => $plate]);
        //echo '</div>';
    } else {
        $cont++;
    }
}

/*
 * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
 */

//El anterior Me da el próximo valor a insertar
if ($cont <= 96 && $cont > 1) {
    $plateModel->prepareCompleteGridDiv($cont, $row, $var);
    echo "</div>"; //plate ID
    echo "</div>"; //col-8;
    echo "</div>";
    //echo "</div>"; //col-8;
}

if(count($refs)> 0)
    {
        echo '<div style="clear:both"> ';
        echo '<h1>References</h1>';    
        echo '</div>';
        echo '<div> ';
            foreach($refs as $ref)
            {
                $name_plate = "<strong>TP".sprintf('%06d',$ref['plateId']).":</strong><br>";
                foreach($ref['name'] as $values)
                { 
                    $name_plate .= $values."<br>";
                }
                echo $name_plate;
            }   
        echo '</div>';
    }
            