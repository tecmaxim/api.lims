<?php
use yii\helpers\Html;

?>

<?php if(Yii::$app->controller->action->id != "project-preview"): ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
        <h1>Grid Definition</h1>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">
    <?php if($model->StepProjectId < \common\models\StepProject::SENT): ?>
                <?= Html::a("Edit", ["update-grid-definition" , "idProject"=>$model->ProjectId],['class' => 'btn btn-primary btn-render-content pull-right'] ); ?>
    <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<div class="hidden-xs hidden-sm col-md-8 col-lg-8">
    <?php
    $row = 1;
    $col = 1;
    $cont = 1;
    $class = "position";
    foreach ($samplesByProject as $sample) {
        /*
         * Begin of grid
         */
        if ($cont == 1) {
            echo "<div style='clear:both'></div>";
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
        echo "<a href='#' onclick='return actions(event);'>"
        . "<div class='" . $class . "' id='" . $sample['SamplesByProjectId'] . "'>" .  str_replace('_', '<br>_', str_replace('-','<br>-',$sample['SampleName'])) .  "<input type='hidden' name='' size='1'></input></div>"
        . "</a>";
        $row++;
        /*
         * Control of parents
         */
        
        if ($cont == $numLastSampleByPlate) 
        {
            $cont = 1;
            if ($model->generation->IsF1 == "F1") {
                echo $this->render('resources/__addF1', ['parents' => $parents, 'row' => $row]);
                $row++;
            }
            echo $this->render('resources/__addParents', ['parents' => $parents, 'row' => $row] );
            $row = 1; //$parents != null ? 1 : $row++;
            echo "</div>";
            echo "</div>";
        } else {
            $cont++;
        }
    }
    /*
     * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
     */
    
    //El anterior Me da el próximo valor a insertar
    if ($cont <= $numLastSampleByPlate && $cont > 1) {
        if ($model->generation->IsF1 == 1) {
            // me tiene que devolver el proximo
            echo $this->render('resources/__addF1', ['parents' => $parents, 'row' => $row]);
            $row = $row >= 9 ? 2 : ++$row;
            $cont++;
            
        }
            //print_r($row); exit;
        echo $this->render('resources/__addParents', ['parents' => $parents, 'row' => $row, "last" => "ok"]);
       
        $num_to_add = $parents != null ? 3 : 1;
        for($i = 0; $i < $num_to_add; $i++)
        {
            if($row < 9)
                $row++;
            else
                $row = 2;
        }
        // Me da el proximo
        $cont += $num_to_add;
        //print_r($row); exit;
        echo $this->render('resources/__completeGrid', ['cont' => $cont, 'rowActual' => $row]);
        echo "</div>"; //plate ID
        echo "</div>"; //col-8;
    }
    ?>

</div>
    <?php if (!isset($preview)): ?>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <?= $this->render('resources/__gridActions', ["isF1" => $model->generation->IsF1, "projectId" => $model->ProjectId, "isSent" => $model->IsSent]); ?>
    </div>
<?php endif; ?>

