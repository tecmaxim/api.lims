<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "plate".
 *
 * @property integer $PlateId
 * @property integer $ProjectId

 * @property integer $IsActive
 * @property integer $StatusPlateId
 * @property GenotypeByPlate[] $genotypeByPlates
 * @property Project $project
 */
class Plate extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    const COMBINED = 1;

    public static function tableName() {
        return 'plate';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['StatusPlateId', 'IsActive'], 'integer'],
            [['Date'], 'required'],
            [['IsActive', 'StatusPlateId'], 'safe'],
                //['Genotypes', 'required', 'on' => 'unload', 'message' => 'Elements inserted do not comply the rule defined'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'PlateId' => Yii::t('app', 'Plate'),
            'Date' => Yii::t('app', 'Date'),
            'StatusPlateId' => Yii::t('app', 'Plate Status'),
            //'Genotypes' => Yii::t('app', 'Genotypes (format suport: "m1, m2, mn" , "m1; m2; m3" or copy and paste col by excel )'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getPlatesByProjects() {
        return $this->hasMany(PlatesByProject::className(), ['PlateId' => 'PlateId']);
    }

    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getSamplesByPlates() {
        return $this->hasMany(SamplesByPlate::className(), ['PlateId' => 'PlateId']);
    }

    public function getStatusPlate() {
        return $this->hasOne(StatusPlate::className(), ['StatusPlateId' => 'StatusPlateId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdnExtractions() {
        return $this->hasOne(AdnExtraction::className(), ['PlateId' => 'PlateId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDateByPlateStatuses() {
        return $this->hasMany(DateByPlateStatus::className(), ['PlateId' => 'PlateId']);
    }

    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getDiscartedPlates() {
        return $this->hasMany(DiscartedPlates::className(), ['PlateId' => 'PlateId']);
    }

    public function verExample() {

        $objPHPExcel1 = PHPExcel_IOFactory :: load('C:/TemplateGrid20-00-09.xls');
        $objPHPExcel2 = PHPExcel_IOFactory :: load('C:/TemplateGrid20-00-08.xls');

        $objPHPExcel1->getActiveSheet()->fromArray(
                $objPHPExcel2->getActiveSheet()->toArray(), null, 'A' . ( $objPHPExcel1->getActiveSheet()->getHighestRow() + 1 )
        );


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel2007');
        $objWriter->save('C:/animals.xlsx');
    }

    static function createNewPlate() {
        $plate = new Plate();

        $plate->Date = date("Y-m-d");
        $plate->StatusPlateId = StatusPlate::SENT;
        $plate->IsActive = 1;
        if (!$plate->save()) {
            print_r($plate->getErrors());
            exit;
        };

        return $plate->PlateId;
        //print_r()
    }

    /*
     * Create a new relation beetwen project and plate
     * paramteres int, int;
     * return null;
     * 
     */

    static function plateByProject($projectId, $plateId) {
        $plate_by_project = new PlatesByProject();
        $plate_by_project->ProjectId = $projectId;
        $plate_by_project->PlateId = $plateId;
        $plate_by_project->IsActive = 1;
        $plate_by_project->save();
    }

    /*
     * Get the name of all projects grouped
     * @return String
     */
    static function getNamesProjectByPlateId($plateId) {
        $sql = "SELECT p.Name from plates_by_project p2
        INNER JOIN project p ON p.ProjectId=p2.ProjectId
        where PlateId=" . $plateId . "
        GROUP BY p.ProjectId";

        $name = Yii::$app->db->createCommand($sql)->queryColumn();

//        $new_name= explode('<->', $name);
//        str_replace(,'<br>',$name, $sql)
//        for
        
        return $name;
    }

    static function saveStatus($plateId, $statusId) {
        $plate = Plate::findOne($plateId);
        $plate->StatusPlateId = $statusId;
        $plate->save();
    }

    public function getProjectName() {
        $name = "";
        $platesByProject = PlatesByProject::findOne(["PlateId" => $this->PlateId]);
        if ($platesByProject != null) {
            $name = $platesByProject->project != null ? $platesByProject->project->Name : "";
        }

        return $name;
    }

    public function getLastDateByPlateStatus() {
        if ($this->dateByPlateStatuses) {
            $last = count($this->dateByPlateStatuses) - 1;
            return $this->dateByPlateStatuses[$last]->Date;
        } else
            return null;
    }

    /*
     * change StatusSampleId=null all samples from plate
     * @return void
     * @parameter null
     */

    public function resetSamples() {
        foreach ($this->samplesByPlates as $sample) {
            //print_r($sample->StatusSampleId."<br>");
            $sample->StatusSampleId = "";
            if (!$sample->save())
                print_r($sample->getErrors());
        }
    }

    public function deleteDateStatus() {
        $sql = "DELETE FROM date_by_plate_status WHERE PlateId=" . $this->PlateId;

        Yii::$app->db->createCommand($sql)->execute();
    }

    static function getDiscartedById($id) {
        $data = array();
        $discarted = DiscartedPlates::find()
                ->with('causeByDescartedPlates')
                ->where(["PlateId" => $id])
                ->orderBy('Date')
                ->asArray()
                ->all();

        if ($discarted) {
            $count = count($discarted);

            $data['discarted'] = $discarted[$count - 1];
            $data['count'] = $count;
        }

        return $data;
    }

    public function getStatusName() {

        switch ($this->StatusPlateId) {
            case StatusPlate::CONTROLLED:
                $string = "<span class='label label-info'>" . $this->statusPlate->Name . "</span>";
                break;
            case StatusPlate::SAVED_METHOD:
                $method = $this->adnExtractions != null ? $this->adnExtractions->Method : 'GenSpin';
                $string = "<span class='label label-warning'>" . $this->statusPlate->Name . ": <b>" . $method . "</b></span>";
                break;
            case StatusPlate::ADN_EXTRACTED:
                $string = "<span class='label label-success'>" . $this->statusPlate->Name . "</span>";
                break;
            case StatusPlate::CUANTIFICATION:
                $string = "<span class='label label-primary'>" . $this->statusPlate->Name . "</span>";
                break;
            case StatusPlate::CANCELED:
                $string = "<span class='label label-danger'>" . $this->statusPlate->Name . "</span>";
                break;
            case StatusPlate::RECIEVED:
                $string = "<span class='label label-success'>" . $this->statusPlate->Name . "</span>";
                break;
            default:
                $string = "<span class='label label-default'>" . $this->statusPlate->Name . "</span>";
                break;
        }

        return $string;
    }

    /* If not method and Stauts Plate is ADN EXCTRACTED, the method is genspin
     * @parameter this;
     * @return string 
     */

    public function getMethod() {
        $examples = (string) $this->PlateId;
        $array_json = json_encode(["PlateId" => $examples]);

        $sql = "SELECT * FROM genspin g WHERE g.Plates like '%" . $array_json . "%'";
        $genspin = Yii::$app->db->createCommand($sql)->queryOne();
        $genspin_plates = "<b>GenSpin: </b>";
        if ($genspin) {
            $decode = json_decode($genspin['Plates']);
            foreach ($decode as $plates) {
                $genspin_plates .= $genspin_plates == "<b>GenSpin: </b>" ? $plates->PlateId : ', ' . $plates->PlateId;
            }

            return $genspin_plates;
        } else
            return null;
    }

    public function getGenSpinData() {
        $examples = (string) $this->PlateId;
        $array_json = json_encode(["PlateId" => $examples]);

        $sql = "SELECT * FROM genspin g WHERE g.Plates like '%" . $array_json . "%'";
        $genspin = Yii::$app->db->createCommand($sql)->queryOne();

        $adnExtraction = AdnExtraction::findOne($genspin['AdnExtractionId']);

        return $adnExtraction;
    }

    public function linkToPlates() {
        $projectModel = PlatesByProject::findOne(["PlateId" => $this->PlateId]);
        if ($projectModel->project->StepProjectId >= StepProject::SAMPLE_RECEPTION)
            return Html::a(Yii::t('app', 'View Project Grids'), ['project/view-shipment', 'idProject' => $projectModel->ProjectId], [ 'class' => 'btn btn-info  btn-sm']);
        elseif ($projectModel->plate->StatusPlateId == StatusPlate::CANCELED) {
            return "-Canceled-";
        } else
            return "The plate has not been received";
    }

    public function getProjectsByPlates() {
        $projectsByPlate = $this->getPlatesByProjects();

        foreach ($projectsByPlate->orderBy(["ProjectId" =>SORT_ASC]) ->all() as $project) {
            $projectsId[] = $project->ProjectId;
        }

        return $projectsId;
    }

    static function getParentsInPlates() {
        $parents = MaterialsByProject::find()
                ->innerjoin('`advanta.gdbms`.`material_test`', '`advanta.gdbms`.`material_test`.Material_Test_Id = materials_by_project.Material_Test_Id')
                ->where(['in', 'ParentTypeId', [1, 2]])
                ->orderBy('material_test.Name', SORT_ASC)
                ->groupBy('material_test.Name')
                ->all();

        return $parents;
    }

    /*
     * Search plates with the same project parents
     * @params array
     * @return mixed
     */

    static function getPlatesByParents($parents) {
        $arrayParents = \yii\helpers\ArrayHelper::getColumn($parents, "Material_Test_Id");
        $plates = Plate::find()->asArray()
                ->innerJoin("plates_by_project", "plates_by_project.PlateId = plate.PlateId")
                ->innerJoin("materials_by_project", "materials_by_project.ProjectId = plates_by_project.ProjectId")
                ->where(["in", "materials_by_project.Material_Test_Id", $arrayParents])
                ->andWhere([">=", "plate.StatusPlateId", StatusPlate::ADN_EXTRACTED])
                ->all();
        return $plates;
    }
    
    /*
     * Iterator, create string with grids preview to render PDF
     * @params int, array, array, mixed
     * @return string
     */
    static function createStringView($numLastSampleByPlate, $parents, $samplesByProject, $model, $method = null, $grid_continued = null) {
     
        $plate = new Plate();
        
        $row = $grid_continued != null ? $grid_continued['row'] : 1;
        $cont = $grid_continued != null ? $grid_continued['cont'] : 1;
        $class = "position";
        $round = $grid_continued != null ? $grid_continued['round'] : 1;
        $grid_count = $grid_continued != null ? $grid_continued['grid_count'] : 0;
        $var = $grid_continued != null ? $grid_continued['var'] : ""; //<table>";
        $refs = []; // this is used to save references when 2 or mar projects are combined
        
        foreach ($samplesByProject as $sample) {
        /*
         * Begin of grid
         */
        if ($cont == 1) 
        {
            //$var .= "<div style='clear:both'></div>";
            if ($round > 1 && $round % 2 != 0)
            {
                if($grid_count == 4)
                {
                    $grid_count = 0;
                    $var .= '<pagebreak />';
                }
                
                $var .= '<div style="clear:both; width:100%; height:5px; border:0px solid #000;"></div>';
            }
            $round++;
            $grid_count++;
            
            //$var .= $this->render('../project/resources/__plate-headers');
            $var .= "<div id='cutter'>";
            $var .= "<div id='plate' >";
            if( Project::findOne($sample['ProjectId'])->StepProjectId >= StepProject::SENT )
            {
                $plateByProject = self::getPlateByProjectAndSampleByProject($sample['ProjectId'], $sample['SamplesByProjectId']);
                $name = self::getNamesProjectByPlateId($plateByProject['PlateId']);

                if(count($name)> 1)
                {
                    $refs[] =[ "name" => $name, 'plateId' => $plateByProject['PlateId']];
                    $name = "More than one project";
                }else
                    $name = $name[0];

                $code = $plateByProject['PlateId'];
                $var .= '<div style="width:60px; font-size: 0.7em; border:0px solid #000; margin-left:15px;">' . 'TP'.sprintf('%06d', $code) . '</div>';
            }
            
            $var .= '<div style="clear:both; width:100%; height:1px;"></div>';
            $var .= "<div class='columns' >";
        }
        /*
         * End actual Row and open new row
         */
        if ($row >= 9) {
            $var .= "</div>"; // close Row
            $row = 1; // reset
            $var .= "<div class='columns' >"; // open Row
        }
        /*
         * New cell
         */
        $var .= "<div class='".$class."'>" . $sample['SampleName'] . "</div>";
        $row++;
        /*
         * Control of parents
         */
        
        if ($cont == $numLastSampleByPlate) 
        {
            $cont = 1;
            if ($model->generation->IsF1 == "F1") {
                $var = $plate->prepareF1Div($parents,$row, $var);
                $row++;
            }
            $var = $plate->prepareParentsDiv($parents,$row, $var );
            $row = 1; //$parents != null ? 1 : $row++;
            $var .= "</div>";
            $var .= "</div>";
            if(isset($name))
                $var .= '<div style="float:left; width:300px; font-size: 0.7em;margin:-8px 0px 2px 15px; border:0px solid #00ff55">' . $name . '</div> ';
            $var .= "</div>";
            
            //$var .= "</div>";
        } else {
            $cont++;
        }
    }
    /*
     * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
     */
    
    //El anterior Me da el próximo valor a insertar
        if ($cont <= $numLastSampleByPlate && $cont > 1) 
        {
            if ($model->generation->IsF1 == 1) {
                // me tiene que devolver el proximo
                $var = $plate->prepareF1Div($parents, $row, $var);
                $row = $row >= 9 ? 2 : ++$row;
                $cont++;

            }
                //print_r($row); exit;
            $var = $plate->prepareParentsDiv( $parents,  $row, $var);

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
            
            if($method == self::COMBINED)
            {
                return ["cont" => $cont, 
                        "grid_count" => $grid_count,
                        "var" => $var,
                        "round" => $round,
                        "row" => $row];
            }
            //print_r($row); exit;
            $var = $plate->prepareCompleteGridDiv ($cont, $row, $var);
            /*
             * ON $plate->prepareCompleteGridDiv
             * 
            $var .= "</div>"; //plate ID
            $var .= "</div>"; //col-8;
            $var .= "</div>";
            */
        }
        //$var .= "</td></tr></table>";
        if(count($refs)> 0)
        {
            $var .= '<div style="clear:both"> ';
            $var .= '<h1>References</h1>';    
            $var .= '</div>';
            $var .= '<div> ';
                foreach($refs as $ref)
                {
                    $name_plate = "<strong>".$ref['plateId'].":</strong><br>";
                    foreach($ref['name'] as $values)
                    { 
                        $name_plate .= $values."<br>";
                    }
                    $var .= $name_plate;
                }   
            $var .= '</div>';
        }
        //$var .= "plateH:87mm | columnH:81.5mm columnW:9.840 | positionH:8.48mm | cutter:472";
        return $var;
    }
    
    static function createPlatePdf($samplesByPlate, $array_parents, $model)
    {
        //return 
    }
    
    /*
     * Generate F1 cell and add it to string grid
     * @params int, string
     * @return string
     */
    private function prepareF1Div($row, $var)
    {
        $class = "parents";
        if ($row >= 9) 
        {
            $var .= "</div>";
            $row = 1; // reset
            $var .= "<div class='columns' >";
        }
        $var .= "<div class='".$class."'> F1 </div>";
        
        return $var;
    }
    
    /*
     * Generate Parents cell and add it to string grid
     * @params $array, int, global string
     * @return string
     */
    private function prepareParentsDiv($parents, $row, &$var)
    {
        $class = "parents";
    
        if($parents != null)
        {
            foreach($parents as $p)
            {
                if ($row >= 9 ) 
                    {
                        $var .= "</div>";
                        $row = 1; // reset
                        $var .= "<div class='columns' >";
                    }

                $var .= "<div class='".$class."'>" . $p->materialTest->Name ."</div>"
               ;
                $row++;
            }
        }

        // me devuelve el q sigue
        if (isset($last))
        {
            //print_r($row); exit;
            if ($row >= 9) 
            {
                $var .= "</div>";
                $row = 1; // reset
                $var .= "<div class='columns' >";
            }
        }

        $var .= "<div class='".$class."'> N </div>";
        
        return $var;
    }
    
    /*
     * Generate empty cell and add it to string grid
     * @params int, int, string
     * @return string
     */
    public static function prepareCompleteGridDiv($cont, $rowActual, $var)
    {
        
        $row = $rowActual;
        $limitTotal = isset($genSpin) ? 384:96;
        $limit = isset($genSpin) ? 17:9;
        
        for($i = $cont; $i <= $limitTotal;$i++)
        {
            if ($row >= $limit) 
            {
                $var .= "</div>";
                $row = 1; // reset
               $var .="<div class='columns' >";
            }
            
            /* CONTROL END COL */
           
            $var .=  "<div class='position-null'></div>";
            
            $row++;
             
        }
        
        $var .= "</div>"; //plate ID
        $var .= "</div>"; //col-8;
        $var .= "</div>";
        return $var;           
    }
    
    private function getPlateByProjectAndSampleByProject($projectId, $sampleByProjectId)
    {
        $sql = "SELECT pbp.PlateId from plates_by_project pbp
                INNER JOIN samples_by_plate sbp ON  sbp.PlateId = pbp.PlateId
                INNER JOIN samples_by_project sbp2 ON sbp2.SamplesByProjectId = sbp.SamplesByProjectId
                WHERE sbp.SamplesByProjectId = ".$sampleByProjectId." AND pbp.ProjectId =".$projectId;
        
        return Yii::$app->db->createCommand($sql)->queryOne();
    }
    
}