<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Reader_Excel5;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Worksheet;
use PHPExcel_Settings;
use cinghie\tcpdf\TCPDF;
use kartik\mpdf\Pdf;


//use PHPExcel_Writer_PDF;
//use PHPExcel_Writer_PDF_DomPDF;

/**
 * This is the model class for table "samples_by_project".
 *
 * @property integer $SamplesByProjectId
 * @property integer $ProjectId
 * @property string $SampleName
 *
 * @property Project $project
 */
class SamplesByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const HEIGHT = 8; // number Rows per plate
    const LAST_CHARCOL = 77; //M
    const INIT_CHARCOL = 66; //B
    
    const CHARCOL_CODE = 76; //M
    const CHARCOL_CODE_2 = 89; //B
    
    const LAST_CHARCOL_2 = 91; //Z
    const INIT_CHARCOL_2 = 80; //P
    
    const SPACE_PARENTS_and_NEGATIVE= 3; //B
    const SEPARATOR = 2;
    const REST_ROW_INITIAL = 7;
    const COMBINATED = 1;
    
    // for Grid Definition
    public $Genotypes;
    public $PlateIdList; 
    public $IsTemplate;
    public $ColumnNumbers;
    public $CharSeparator = "-";
    public $ColumnPivote;
    
    // samples container
    public $samplesContainer; 
    
    // Only for draw grid
    private $plateId; 
    private $samplesByProject_insertBach = array();
    private $styleCell = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true,
            
        ),
        'font' => array(
            'bold' => false,
            'color' => array('rgb' => '000000'),
            'size' => 6,
            'name' => 'Arial'
        ),
    );
    
    public static function tableName()
    {
        return 'samples_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'SampleName'], 'required'],
            [['ProjectId'], 'integer'],
            [['SampleName'], 'string'],
            [['PlateIdList','IsTemplate','CharSeparator','ColumnNumbers','ColumnPivote'], 'safe'],
            [['SampleName'], 'control1'],
            ['SampleName', 'required', 'on' => 'unload', 'message' => 'Elements inserted do not comply the rule defined'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'SamplesByProjectId' => Yii::t('app', 'Samples By Project ID'),
            'ProjectId' => Yii::t('app', 'Job'),
            'PlateIdList' => Yii::t('app', 'Asociate old plates'),
            'SampleName' => Yii::t('app', 'Sample Name: maximum 15 characters per sample'),
            'IsTemplate' => Yii::t('app', 'Check this if you are going to copy an excel template. Remember to paste template headers!'),
            'ColumnPivote' => Yii::t('app', 'Header of the pivot column'),
            'ColumnNumbers' => Yii::t('app', 'Column Amount'),
        ];
    }
    
    /* vailda if any sample is exceeded 10 char
     * paramters string, mixed
     * return bolean
     */
    public function control1($attribute, $params)
    {       
        if ($this->IsTemplate != 1){
            $samples = $this->normalizeAndControlSamples($this->$attribute);
            if ($this->IsTemplate != 1 && $samples != false){
                $this->addError($attribute, 'Some samples exceed 15 characters');
            }
        }else
        {
            if($this->CharSeparator == "" || $this->ColumnPivote == "" || $this->ColumnNumbers == "")
            {
                $this->addErrors(['CharSeparator' => 'This field is required',
                                  'ColumnNumbers' => 'This field is required', 
                                  'ColumnPivote' => 'This field is required']);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    public function generateGrid($idProjects, $method = null, $f1= null, $action) 
    {
        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $oSheet = $oExcel->getActiveSheet();
        //$oSheet->getStyle()->applyFromArray();
        /* Set Widht Column A */
        //$oSheet->getColumnDimension(chr( SamplesByProject::INIT_CHARCOL - 1 ))->setWidth(1.95);
        /* Set Widht Column N */
        $oSheet->getColumnDimension(chr( SamplesByProject::LAST_CHARCOL + 1 ))->setWidth(3.15);
        /* Set Widht Column O */
        $oSheet->getColumnDimension(chr( SamplesByProject::LAST_CHARCOL + 2 ))->setWidth(3.15);
        
        $oSheet->getDefaultRowDimension()->setRowHeight(15);
        $oSheet->getDefaultColumnDimension()->setWidth(2.60);
        
        $row=1;        
        if($method == null)
        {
            $model = $this->generateGridSeparated($oSheet, $idProjects, $row, $f1, $action);
        }else
        {
            $positions = $this->generateGridCombinated($oSheet, $idProjects, $row, $f1, $action);
            //print_r($positions); exit;
            $generated = $this->completeGridCombinated($oSheet, $positions['row'], $positions['charCol'], $positions['limitsToReturn'], $action);
            $model = $positions['model'];
            
        }
        
        if($this->samplesByProject_insertBach != null)
        {
            SamplesByPlate::saveByBatchInsert($this->samplesByProject_insertBach, $method);
        }
        
        $this->applyBordersToCut($oExcel);
                         
        $data = $this->downloadMethod($action, $oExcel, $model, $idProjects);
        return $data;
        
    }
    
    private function generateGridSeparated($oSheet, $idProjects, $row, $f1 = null, $action = null )
    {        
        $f1_parameter = null;
        $round = SamplesByProject::INIT_CHARCOL;
        $plates = ""; //Variable used to download grids but with apply code
        foreach($idProjects as $key => $id)
        {
            //$array_samples_by_project = Plate::find()->where(['ProjectId'=>$id])->all();
            $model = Project::findOne($id);
            $samples = $this->findAll(["ProjectId" => $id]);
            
            if($f1 != null)
            {
               if($model->generation->IsF1 == 1)
               {
                    $f1_parameter = 1;
                    
               }else
                    $f1_parameter  = null;
            }
            
            $parents = $model->getParentsInArrayPublic();
            $array_samples_by_project = $this->separeSamplesByGrid96($samples, $f1_parameter, $parents);
            
            foreach($array_samples_by_project as $p)
            {
                // --- $genotypesByPlate = \common\models\GenotypeByPlate::findAll(["PlateId"=>$p->PlateId]); ----
                //$this->applyProjectNameInGrid($oSheet, $model->Name, $row, $round);
                if($action == Project::SEND)            
                {
                    $plate = new Plate();
                    $plateId = $plate->createNewPlate();
                    $plate->plateByProject($model->ProjectId, $plateId);
                    $this->applyCodePlateInGrid($oSheet, $plateId, $row, $round);
                    $this->samplesByProject_insertBach[] = ["PlateId" => $plateId, "SamplesByProjectId" =>$p, "parents" => $parents, "f1" => $f1_parameter ] ;
                }elseif($model->StepProjectId >= StepProject::SENT)
                {
                    if($plates == null)
                        $plates = PlatesByProject::getPlatesByProjectId($model->ProjectId);
                    
                    $this->applyCodePlateInGrid($oSheet, $plates[0]['PlateId'], $row, $round);
                    if(count($plates) > 1)
                    {
                        array_shift($plates);
                        $plates = array_values($plates);
                    }
                }
                
                $row++;                
                //$this->grid($oSheet, $genotypesByPlate, $parents, $row );
                $round = $this->grid($oSheet, $p, $parents, $row, null, $f1_parameter, $round, $model->Name );
                //print_r($round); exit;
                
                if($round == SamplesByProject::INIT_CHARCOL)
                {
                    $row +=9 ;   
                }else
                {
                    $row--;
                }
            }
        }
        return $model;
    }
    
    private function generateGridCombinated($oSheet, $idProjects, $row, $f1 = null, $action )
    {       
        $method = 1;
        $positions= "";
         $f1_parameter  = null;
         
        foreach($idProjects as $key => $id)
        {
            $model = Project::findOne($id);
            $data_model['Name'] = $model->Name;
            $data_model['ProjectId'] = $id;
            $samples = $this->findAll(["ProjectId" => $id]);
            $maxNumBySamplesByPlte = 1;
            if(($parents = $model->getParentsInArrayPublic()) != null)
            {   
                //Add 2 for parents;
                $maxNumBySamplesByPlte = $maxNumBySamplesByPlte +2;
            }
            if($f1 != null)
            {
               if($model->generation->IsF1 == 1)
               {
                    $f1_parameter = 1;
                    $maxNumBySamplesByPlte++;
               }else
                    $f1_parameter  = null;
            }
            
            $array_samples_by_project = $this->orderSamples($samples);
            
            foreach($array_samples_by_project as $order_samples)
            {
                if($positions == '')
                {
                    $this->applyProjectNameInGrid($oSheet, $model->Name, $row, SamplesByProject::INIT_CHARCOL);
                    $positions = $this->gridContinued($oSheet, $order_samples, $parents, 2, SamplesByProject::INIT_CHARCOL, 9, $f1_parameter, $maxNumBySamplesByPlte, $data_model, null, $action);
                }
                else
                {  
                    $positions = $this->gridContinued($oSheet, $order_samples, $parents, $positions['row'], $positions['charCol'], $positions['limitsToReturn'], $f1_parameter, $maxNumBySamplesByPlte, $data_model, $positions['flag_name'], $action);                    
                }
            }
        }
        $array_names = Yii::$app->session->get('names_grids');
        if($array_names != null)
        {
            //print_r($array_names); exit;
            $this->addReferences($oSheet, $positions['limitsToReturn'], $array_names );
            Yii::$app->session->remove('names_grids');
        }
        
        $positions["model"] = $model;
        return $positions;
    }
    
    function grid($oSheet, $genotypes, $parents = null, $rowInitial, $method = null, $f1 = null, $round = null, $projectName = null) 
    {
        $charCol = $round; //== SamplesByProject::INIT_CHARCOL ?SamplesByProject::INIT_CHARCOL :SamplesByProject::INIT_CHARCOL_2; // char B
        $row = $rowInitial;
        $lastRowToreturn = $rowInitial + SamplesByProject::REST_ROW_INITIAL;
        
        foreach ($genotypes as $sample) // Only max is 94 per plate
        {   
            $char = ($charCol == 91 || $charCol == 'AA')  ? 'AA':chr($charCol);
                    
            $oSheet->getRowDimension($row)->setRowHeight(27);
            
            $oSheet->getColumnDimension($char )->setWidth(4.60);

            $this->setBordersInCell($oSheet, $charCol, $row);

            $oSheet->getStyle($char . $row)
                    ->applyFromArray($this->styleCell);

            $oSheet->SetCellValue( $char. $row, $sample->SampleName);
            
            if ($row == $lastRowToreturn ) 
            {
                $row = $rowInitial;
                $charCol++;
            }else
                $row++;
        }
        //if ($charCol <= 77 && $row < ($lastRowToreturn - 1))
        //{
            // posible de agregar solo padres hasta q..
            if($method != null)
            {
                if($f1 != null)
                {
                    $this->addF1 ( $oSheet, $charCol, $row);
                    $row++;
                }
                
                $positions = $this->addParentsAndNegative ($charCol, $oSheet, $parents, $row, $lastRowToreturn);

                return $positions;
            }
            else{
                $round = $round == SamplesByProject::INIT_CHARCOL ?SamplesByProject::INIT_CHARCOL_2 :SamplesByProject::INIT_CHARCOL; // char B
                
                $this->completeGrid($charCol, $oSheet, $parents, $row, $lastRowToreturn, $f1, $projectName, $round);
                
                return $round;
            }
    }
    
    function gridContinued($oSheet, $genotypes, $parents = null, $rowInitial, $charColActual = null, $limitActual = null, $f1=null, $maxNumOfSamplesByPlate = null, $data_model, $flag_name = null, $action) 
    {
        $charCol = $charColActual;
        $row = $rowInitial;

        if(($rowInitial == $limitActual) && $charCol == SamplesByProject::LAST_CHARCOL)
        {
            $lastRowToreturn =   $rowInitial + SamplesByProject::SEPARATOR ;
        }
        else
            $lastRowToreturn = $limitActual;
        
        foreach ($genotypes as $sample) // Only max is 94 per plate
        {
            $char = ($charCol == 91 || $charCol == 'AA')  ? 'AA':chr($charCol);
            /* Si es la 1era inserción al cominezo de la grilla, insertar nombre*/
            if($row == ($limitActual - SamplesByProject::REST_ROW_INITIAL) || $flag_name == 1) 
            {
                if(($charCol === SamplesByProject::INIT_CHARCOL) || ($charCol === SamplesByProject::INIT_CHARCOL_2 || $flag_name == 1))
                {   
                    $charColToName = $this->getCharColToName($charCol);
                    $this->addProjectNameInActualGrid($oSheet, $data_model['Name'], $lastRowToreturn, $charColToName);
                    
                    if($action == Project::SEND)            
                    {  
                        if(($charCol === SamplesByProject::INIT_CHARCOL || $charCol === SamplesByProject::INIT_CHARCOL_2) && $flag_name != 1 )
                        {
                            $plate = new Plate();
                            $this->plateId = $plate->createNewPlate();
                            $plate->plateByProject($data_model['ProjectId'], $this->plateId);
                            $this->applyCodePlateInGrid($oSheet, $this->plateId, $row, $charColToName, $lastRowToreturn);
                        }elseif($flag_name == 1){
                            Plate::plateByProject($data_model['ProjectId'], $this->plateId);
                            $this->plateId = "";   
                        }
                    }
                    $flag_name = 0;
                }
            }
            
            if($action == Project::SEND )
            {
                $this->samplesByProject_insertBach[] = ["PlateId" => $this->plateId, "SamplesByProjectId" =>$sample->SamplesByProjectId, 'Type'=>'SAMPLE' ] ;
            }
            /* Cell actions*/
            $oSheet->getRowDimension($row)->setRowHeight(27);
            $oSheet->getColumnDimension($char)->setWidth(4.60);
            $this->setBordersInCell($oSheet, $charCol, $row);
            $oSheet->getStyle($char . $row)
                    ->applyFromArray($this->styleCell);
            $oSheet->SetCellValue($char . $row, $sample->SampleName);
            
            if($charCol == SamplesByProject::LAST_CHARCOL && $row == ($limitActual - $maxNumOfSamplesByPlate))
            {/* Limite 1 */
                if($f1 != null)
                {
                    $row++;
                    $this->addF1 ( $oSheet, $charCol, $row, $action);
                }
                $row++;
                $this->addParentsAndNegative2 ($charCol, $oSheet, $parents, $row, $lastRowToreturn, $action);
                /*row debe volver al 1er lugar donde empezó la grilla anterior*/   
                $row = $lastRowToreturn - SamplesByProject::REST_ROW_INITIAL;
                /*Charcol debe comenzar en la siguiente grilla, Char "O"*/
                $charCol = SamplesByProject::INIT_CHARCOL_2;
               
                $limitActual = $lastRowToreturn; 
            }
            elseif($charCol == SamplesByProject::LAST_CHARCOL_2 && $row == ($limitActual - $maxNumOfSamplesByPlate))
            {/* Limite 2 */
                if($f1 != null)
                {
                    $row++;
                    $this->addF1 ( $oSheet, $charCol, $row, $action);   
                }
              
                $row++;
                $this->addParentsAndNegative2 ($charCol, $oSheet, $parents, $row, $lastRowToreturn, $action);
                   
                $row = $limitActual + SamplesByProject::SEPARATOR;
                $charCol = SamplesByProject::INIT_CHARCOL;
                $lastRowToreturn = $row + SamplesByProject::REST_ROW_INITIAL;
                $limitActual = $lastRowToreturn; 
                
            }
            elseif ($row == $lastRowToreturn) 
            {
                $row = ($lastRowToreturn - SamplesByProject::REST_ROW_INITIAL);
                $charCol++;
            }else
                $row++;
        }//end main foreach
        
        if($f1 != null)
        {
            $this->addF1 ( $oSheet, $charCol, $row, $action);
           
            $row = $row == $lastRowToreturn ?  ($lastRowToreturn - SamplesByProject::REST_ROW_INITIAL): ++$row;
        }

        $positions = $this->addParentsAndNegative2 ($charCol, $oSheet, $parents, $row, $lastRowToreturn, $action);
        $positions['flag_name'] = 1;
        return $positions;
    }
    
    private function completeGrid($charColActual, $oSheet, $parents, $rowActual, $limitToReturn, $f1 = null, $projectName, $round) 
    {
        $charCol = $charColActual;
        $cont = 0;
        $char = ($charCol == 91 or $charCol == 'AA') ? 'AA':chr($charCol);
        
        for ($col = $rowActual; $col <= $limitToReturn; $col++) {

            // Add Parents if is first at 3th rounds
            if($f1 != null)
            {
                $this->addF1 ( $oSheet, $charCol, $col);
                $f1 = null;
            }
            elseif ( $cont <= 1) 
            {
                if($parents != null)
                {
                    $this->addParent($oSheet, $charCol, $col, $parents[$cont]);
                    $cont++;
                }else{
                    $cont = 2;
                    $col--;
                }
            } elseif ($cont == 2) {
                $this->addNegativeControl($oSheet, $charCol, $col);
                $cont++;
                //break;
            } 
            else {

                $oSheet->getRowDimension($col)->setRowHeight(27);

                $oSheet->getColumnDimension($char)->setWidth(4.60);

                $this->setBordersInCell($oSheet, $charCol, $col);

                $oSheet->getStyle($char . $col)
                        ->applyFromArray($this->styleCell);

                $oSheet->SetCellValue($char . $col, '');
            }
            
            if (($col == $limitToReturn && $charCol < SamplesByProject::LAST_CHARCOL) || ($charCol >= SamplesByProject::INIT_CHARCOL_2 && $charCol < SamplesByProject::LAST_CHARCOL_2)) 
            {
                $col = $limitToReturn - 8;
                $charCol++;
                $char++;
            }
            $this->applyProjectNameInGrid($oSheet, $projectName, $col, $round);
//            if ($cont == -1) 
//            {
//                $cont = 0;
//                $f1 = null;
//            }
        } //for principal
    }
    
    private function completeGridCombinated($oSheet, $rowActual ,$charColActual,$limitToReturn)
    {
        /*If the rowActual > limit height of plate($limitToReturn) and $charCol = "J", create new plate*/
//        if($rowActual >= $limitToReturn && $charColActual == Plate::LAST_CHARCOL)
//        {
//            $limitToReturn = ($limitToReturn * 2) + 2; // + 2 for de blank space
//            $rowActual = $limitToReturn + 2;
//            
//        } 
        if((($limitToReturn - $rowActual) == SamplesByProject::REST_ROW_INITIAL) && $charColActual == SamplesByProject::INIT_CHARCOL)
        {
            return 0;
        }else{
            $charCol = $charColActual;
            for ($col = $rowActual; $col <= $limitToReturn; $col++) {
                $char = ($charCol == 91 || $charCol == 'AA')  ? 'AA':chr($charCol);
                // Add Parents if is first at 3th rounds
                $oSheet->getRowDimension($col)->setRowHeight(27);

                $oSheet->getColumnDimension($char)->setWidth(4.60);

                $this->setBordersInCell($oSheet, $charCol, $col);

                $oSheet->getStyle($char . $col)
                        ->applyFromArray($this->styleCell);

                $oSheet->SetCellValue($char . $col, '');

                if ($col == $limitToReturn && $charCol < SamplesByProject::LAST_CHARCOL) 
                { 
                    $col = $limitToReturn - SamplesByProject::HEIGHT;
                    $charCol++;
                }elseif($col == $limitToReturn && $charCol >= SamplesByProject::INIT_CHARCOL_2 && $charCol < SamplesByProject::LAST_CHARCOL_2)
                {
                    $col = $limitToReturn - SamplesByProject::HEIGHT;
                    $charCol++;
                }
            }//for principal
            return 1;
        }
    }
    
    private function addParentsAndNegative($charColActual, $oSheet, $parents, $rowActual, $limitToReturn) 
    {   
        $charCol = $charColActual;
        $cont = 0;
        //print_r($charCol);
        //print_r($rowActual); exit;
        
        for ($col = $rowActual; $col <= $limitToReturn; $col++) {
            // Add Parents if is first at 3th rounds
            if ( $cont <= 1) 
            {
                if($parents != null)
                {
                    $this->addParent($oSheet, $charCol, $col, $parents[$cont]);
                    $cont++;
                }else 
                {    
                    $cont = 2;
                    $col--;
                }
            } elseif ($cont == 2) {
                $this->addNegativeControl($oSheet, $charCol, $col);
                $cont++;
                
                //break;
            } 
            
            if ($col == $limitToReturn)
            {
                if($charCol >= SamplesByProject::LAST_CHARCOL)
                {
                    $col = $limitToReturn + SamplesByProject::SEPARATOR;
                    $charCol = SamplesByProject::INIT_CHARCOL;
                    $new_limitToReturn = $col + SamplesByProject::REST_ROW_INITIAL;
                }else
                {
                    $col = $limitToReturn - SamplesByProject::HEIGHT;
                    $charCol++;
                }
            }
        }//for principal
        $positions = $this->calculateReturnPoint($col, $charCol, $limitToReturn);
       
        return $positions;
        
        
    }
    
    private function addParentsAndNegative2($charColActual, $oSheet, $parents, $rowActual, $limitToReturn, $action = null) 
    {   
        $charCol = $charColActual;
        $cont = 0;
        
        for ($col = $rowActual; $col <= $limitToReturn; $col++) {
            // Add Parents if is first at 3th rounds
            if ( $cont <= 1) 
            {
                if($parents != null)
                {
                    $this->addParent($oSheet, $charCol, $col, $parents[$cont]);
                    $cont++;
                    
                    if($action == Project::SEND )
                    {
                        $this->samplesByProject_insertBach[] = ["PlateId" => $this->plateId, "SamplesByProjectId" => "", "Type"=>"PARENT" ] ;
                    }
                }else 
                {    
                    $cont = 2;
                    $col--;
                }
            } elseif ($cont == 2) {
                $this->addNegativeControl($oSheet, $charCol, $col);
                $cont++;
                if($action == Project::SEND )
                {
                    $this->samplesByProject_insertBach[] = ["PlateId" => $this->plateId, "SamplesByProjectId" => "", "Type"=>"CN" ] ;
                }
                break;
            } 
            
            if ($col == $limitToReturn)
            {
                if($charCol == SamplesByProject::LAST_CHARCOL)
                {
                    $col = $limitToReturn - SamplesByProject::HEIGHT;
                    $charCol = SamplesByProject::INIT_CHARCOL_2;
                }elseif($charCol >= SamplesByProject::LAST_CHARCOL_2)
                {
                    $col = $limitToReturn + SamplesByProject::SEPARATOR;
                    $charCol = SamplesByProject::INIT_CHARCOL;
                    $new_limitToReturn = $col + SamplesByProject::REST_ROW_INITIAL;
                }else
                {
                    /**Reset $col*/
                    $col = $limitToReturn - SamplesByProject::HEIGHT;
                    $charCol++;
                }
            }
        }//for principal
        
        $positions = $this->calculateReturnPoint($col, $charCol, $limitToReturn);
        return $positions;
         
    }
    
    /* This method define the borders styles in each cells */
    private function setBordersInCell($oSheet, $charCol, $col) {
        $char = ($charCol == 91 || $charCol == 'AA') ? 'AA':chr($charCol);
        
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getTop()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getLeft()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $oSheet->getStyle( $char . $col)
                ->getBorders()
                ->getRight()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }
    
    private function addF1($oSheet, $charCol, $col, $action = null) {
        $char = ($charCol == 91 or $charCol == 'AA') ? 'AA':chr($charCol);
        $oSheet->SetCellValue($char . $col, 'F-1');

        $oSheet->getRowDimension($col)->setRowHeight(27);

        $oSheet->getColumnDimension($char)->setWidth(4.60);

        $this->setBordersInCell($oSheet, $charCol, $col);

        $oSheet->getStyle($char . $col)
                ->applyFromArray($this->styleCell);

        $oSheet->getStyle($char . $col)->applyFromArray(
                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'CCCCCC')
                    )
        ));
        if($action == Project::SEND )
        {
            $this->samplesByProject_insertBach[] = ["PlateId" => $this->plateId, "F1" => true, "Type"=>'F1' ] ;
        }
    }

    private function addParent($oSheet, $charCol, $col, $name) {
        //print_r($name); exit;
        $char = ($charCol == 91 or $charCol == 'AA')? 'AA':chr($charCol);
        
        $oSheet->SetCellValue( $char . $col, $name);

        $oSheet->getRowDimension($col)->setRowHeight(27);

        $oSheet->getColumnDimension($char)->setWidth(4.60);

        $this->setBordersInCell($oSheet, $charCol, $col);

        $oSheet->getStyle( $char . $col)
                ->applyFromArray($this->styleCell);

        $oSheet->getStyle( $char . $col)->applyFromArray(
                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc')
                    )
        ));
    }

    private function addNegativeControl($oSheet, $charCol, $col) {
        $char = ($charCol == 91 || $charCol == 'AA') ? 'AA':chr($charCol);
        
        $oSheet->SetCellValue( $char . $col, 'N');

        $oSheet->getRowDimension($col)->setRowHeight(27);

        $oSheet->getColumnDimension( $char)->setWidth(4.60);

        $this->setBordersInCell($oSheet, $charCol, $col);

        $oSheet->getStyle( $char . $col)
                ->applyFromArray($this->styleCell);

        $oSheet->getStyle( $char . $col)->applyFromArray(
                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'CCCCCC')
                    )
        ));
    }
    
    private function applyProjectNameInGrid($oSheet, $nameProject, $row, $round) {
        $charCol = $round;
        //$oSheet->mergeCells("B1:E1");
        $oSheet->SetCellValue(chr($charCol) . $row, $nameProject);

        $oSheet->getStyle(chr($charCol) . $row)->applyFromArray(
                array(
//                    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'FFFF00')
//                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '000000'),
                        'size' => 8,
                        'name' => 'Arial'
                    ),
        ));
    }

    private function applyCodePlateInGrid($oSheet, $code, $row, $round, $limitRow = null) 
    {
        $charCol = $round == SamplesByProject::INIT_CHARCOL ? SamplesByProject::CHARCOL_CODE : SamplesByProject::CHARCOL_CODE_2;
        
        $new_code = 'TP'.sprintf('%06d', $code);
        $row = $limitRow != null ? ($limitRow - SamplesByProject::HEIGHT):$row;
        
        //$oSheet->mergeCells("J1:L1");
        $oSheet->mergeCells(chr($charCol).$row.":".chr($charCol+1).$row);
        $oSheet->SetCellValue(chr($charCol) . $row, $new_code);

//        $oSheet->getStyle(chr($charCol) . $row)->applyFromArray(
//                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'FFAA00')
//                    )
//        ));
    }
    
    private function addProjectNameInActualGrid($oSheet, $nameProject, $limitRow, $charColActual) 
    {
        $names_grids = Yii::$app->session->get("names_grids");
        $row = $limitRow - SamplesByProject::HEIGHT;
        $charCol = $charColActual;
        $lastName = $oSheet->getCell(chr($charCol).$row)->getValue();
        //$oSheet->mergeCells("B1:E1");
        if($lastName != "" && $lastName !== $nameProject)
        {
           /* print_r("in".$lastName."-".chr($charCol).$row); exit;*/
            $array_name = [chr($charCol).$row => $lastName." - ".$nameProject ];
            
            if($names_grids != null)   
                Yii::$app->session->set("names_grids", array_merge($names_grids, $array_name ));
            else
                Yii::$app->session->set("names_grids", ($array_name ));
            
            $oSheet->SetCellValue(chr($charCol) . $row, chr($charCol).$row);
        }else  
        {
            $oSheet->SetCellValue(chr($charCol) . $row, $nameProject);
        }

        $oSheet->getStyle(chr($charCol) . $row)->applyFromArray(
                array(
//                    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'FFFF00')
//                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '000000'),
                        'size' => 8,
                        'name' => 'Arial'
                    ),
                    
        ));
        
    }

    
    /*
     * looks for all samples of a project
     * paramter: id Project (integer)
     * return: string
     */
    static function getSamples($idProject)
    {
        $samples = SamplesByProject::find()->where(["ProjectId"=>$idProject])->asArray()->all();
        
        $stringToReturn = "";
        
        
        $last = end($samples);
                
        foreach($samples as $sp)
        {
            if($sp == $last)
                $stringToReturn .= $sp['SampleName'];
            else
                $stringToReturn .= $sp['SampleName']."\n";
        }
        
        return $stringToReturn;
    }
    
    /*
     * Delete all samples of a proejct
     *return: integer 
     */
    public function deleteSamples()
    {
        $sql = "DELETE FROM samples_by_project WHERE ProjectId=".$this->ProjectId;
        $rows = Yii::$app->db->createCommand($sql)->execute();
        
        return $rows;
    }
    
    /*
     * Save sent samples
     * parameter: array
     * return integer
     */
    public function saveSamples($samples, $idProject = null)
    {
        $sql = "INSERT INTO samples_by_project (ProjectId, SampleName) VALUES";
        $id = $idProject != null ? $idProject : $this->ProjectId;
        $last = end($samples);
        $limit = count($samples);
        $it=1;
        foreach($samples as $key => $name)
        {            
            if($it == $limit)
                $sql .= "(".$id.",'".$name."');";
            else
                $sql .= "(".$id.",'".$name."'),";
            $it++;
        } 
        $rows = Yii::$app->db->createCommand($sql)->execute();
        
        return $rows;
    }
    
    /*
     * return arrays with 93 or 92 samples.
     * paramter: array dataProvider
     * return: mixed
     */
    private function separeSamplesByGrid96($samples, $f1 = null, $parents = null)
    {
        $cont = 0;
        $i = 0;
        if($parents != null)
            $limit = $f1 != null ? 92:93;
        else
            $limit = $f1 != null ? 94:95;
        
        foreach ($samples as $s)
        {
            if( $i == $limit)
            {
                $cont ++; $i=0;
            }

            $array_samples_by_project[$cont][] = $s;
            $i++;

        }
        return $array_samples_by_project;
    }
    
    private function orderSamples($samples)
    {
        $cont = 0;
        $i = 0;
       
        foreach ($samples as $s)
        {
            $array_samples_by_project[$cont][] = $s;
            $i++;
        }
        
        return $array_samples_by_project;
    }
    
    /*
     * return de Max number for each plate
     * params integer Id Project
     * return integer
     */
    static function getLastNumberByPlate($projectId, $parents)
    {
        $project = Project::findOne($projectId);
        if($parents != null)
        {
            $max = $project->generation->IsF1 == 1? 92 : 93;
        }else
            $max = $project->generation->IsF1 == 1? 94 : 95;
        
        return $max;
    }
    
    /*
     * Return de next cell to complete grid
     * parameters: integer actual Cell, integer CharColumn, integer limit To return
     * return mixed;
     */
    private function calculateReturnPoint($col, $charCol, $limitToReturn)
    {
        if ($col >= $limitToReturn ) 
        {
            if($charCol == SamplesByProject::LAST_CHARCOL)
            {
                $col = $limitToReturn - SamplesByProject::HEIGHT;
                $charCol = SamplesByProject::INIT_CHARCOL_2;
                
            }elseif($charCol >= SamplesByProject::LAST_CHARCOL_2)
            {
                $col = $limitToReturn + SamplesByProject::SEPARATOR;
                $charCol = SamplesByProject::INIT_CHARCOL;
                $new_limitToReturn = $col + SamplesByProject::REST_ROW_INITIAL;
            }
            else
            {
                $charCol++;
                $col = $limitToReturn - SamplesByProject::REST_ROW_INITIAL;
            }
        }else
            $col++;
        
        $positions['row'] = $col;
        $positions['charCol'] = $charCol;
        $positions['limitsToReturn'] = isset($new_limitToReturn) ? $new_limitToReturn:$limitToReturn;
       
        return $positions;
    }
    
    private function downloadMethod($action, $oExcel, $model, $idProjects )
    {
        
        $oExcel->getActiveSheet()
                ->getPageSetup()
                ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $oExcel->getActiveSheet()
                ->getPageSetup()
                ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $oExcel->getActiveSheet()
                ->getPageMargins()->setTop(0.5);
        $oExcel->getActiveSheet()
                ->getPageMargins()->setRight(0.6);
        $oExcel->getActiveSheet()
                ->getPageMargins()->setLeft(0.7);
        $oExcel->getActiveSheet()
                ->getPageMargins()->setBottom(0.5);
         
        $oExcel->getActiveSheet()->getPageSetup()->setScale(107);
        
        $name = $model->Name.'.xls';
              
        if($action == Project::DOWNLOAD)
        {
            
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=".$name);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
            $objWriter->save('php://output');
            
            /*
            $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
            $rendererLibrary = 'dompdf-0.6.0-b3';
    
            $rendererLibraryPath = dirname(__FILE__) .'/../../vendor/' . $rendererLibrary;
            //----------------------
            //print_r($rendererLibraryPath); exit;
            $oExcel->getActiveSheet()->setTitle('Orari');

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="01simple.pdf"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'PDF');

            $objWriter->setSheetIndex(0);
            $objWriter->save('php://output');
            exit;
            */
        }else{
                    
            
            $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
            $objWriter->save($name);

            $data['file'] = $name;
            $data['userId'] = $model->UserId;
            $data['projectsId'] = $idProjects;
        
            return $data;
        }
    }
    
    private function getCharColToName($charCol)
    {
        if($charCol >= SamplesByProject::INIT_CHARCOL && $charCol <= SamplesByProject::LAST_CHARCOL)
            $char_col_to_name = SamplesByProject::INIT_CHARCOL;
        elseif($charCol >= SamplesByProject::INIT_CHARCOL_2 && $charCol <= SamplesByProject::LAST_CHARCOL_2)
            $char_col_to_name = SamplesByProject::INIT_CHARCOL_2;
        
        return $char_col_to_name;
    }
    
    private function addReferences($oSheet, $limitsToReturn, $array_names )
    {
        $row = $limitsToReturn + SamplesByProject::SEPARATOR;
        $oSheet->SetCellValue(chr(SamplesByProject::INIT_CHARCOL) . $row, "References:");
        $oSheet->getStyle(chr(SamplesByProject::INIT_CHARCOL) . $row++)->applyFromArray(
                array(
//                    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'FFFF00')
//                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '000000'),
                        'size' => 12,
                        'name' => 'Arial'
                    ),
                    
        ));
        $row++;
        foreach($array_names as $key => $value)
        {   
            $oSheet->SetCellValue(chr(SamplesByProject::INIT_CHARCOL) . $row, $key." => ".$value);
            $oSheet->getStyle(chr(SamplesByProject::INIT_CHARCOL) . $row++)->applyFromArray(
                array(
//                    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'FFFF00')
//                    ),
                    'font' => array(
                        'bold' => false,
                        'color' => array('rgb' => '000000'),
                        'size' => 10,
                        'name' => 'Arial'
                    ),
                    
        ));
        }
    }
    
    private function normalizeAndControlSamples($genotypes) {
        
        $exceded = false;
                
        if (strpos($genotypes, "\n") !== false)
            $array_genotype = explode("\n", $genotypes);
        elseif (strpos($genotypes, " ") !== false)
            $array_genotype = explode(" ", $genotypes);
        elseif (strpos($genotypes, ", ") !== false)
            $array_genotype = explode(", ", $genotypes);
        elseif (strpos($genotypes, "; ") !== false)
            $array_genotype = explode("; ", $genotypes);
        else
            $array_genotype[] = $genotypes;

        $string_clean = implode(",", $array_genotype);
        $newString = str_replace(chr(13), "", $string_clean);
        $new_array_clean = explode(",", $newString);
        $length = count($new_array_clean);
        if ($new_array_clean[$length - 1] == "")
                unset($new_array_clean[$length - 1]);
        
        foreach($new_array_clean as $key => $val)
        {
            if(strlen($val)> 15)
                $exceded = true;
        }
        return $exceded;
    }
    
    /*
    private function addCodePlateInActualGrid($oSheet, $code, $limitRow) 
    { 

    }
    */
    
    private function applyBordersToCut($oExcel)
    {
        $oSheet = $oExcel->getActiveSheet();
        $highestRow = $oExcel->getActiveSheet()->getHighestRow();
        $highestCol = $oExcel->getActiveSheet()->getHighestColumn();
        $LeftStyle = array(
                        'borders' => array(
                          'left' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK
                          )
                        )
                      );
        $BottomStyle = array(
                        'borders' => array(
                          'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK
                          )
                        )
                      );
        $TopStyle = array(
                        'borders' => array(
                          'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK
                          )
                        )
                      );
        
        $oSheet->getStyle( chr( SamplesByProject::LAST_CHARCOL + 2 ). 1 .":".chr( SamplesByProject::LAST_CHARCOL + 2 ).($highestRow+1))->applyFromArray($LeftStyle);
        $oSheet->getStyle( "AC1:AC".($highestRow+1))->applyFromArray($LeftStyle);
        $oSheet->getStyle( "A1:A".($highestRow+1))->applyFromArray($LeftStyle);
        
        $row = 0;
        for($i = 1; $i <= $highestRow; $i++)
        {
            $oSheet->getStyle( "A".$i.":AB".$i )->applyFromArray($TopStyle);
            $i += 9;
            $oSheet->getStyle( "A".$i.":AB".$i )->applyFromArray($BottomStyle);
            
        }
    }
    
    static function generatePDF($tbl, $css = null, $resource = null)
    {
        if($resource == null)
        {
            $destination = Pdf::DEST_DOWNLOAD;

            $filename = "advanta_grids_".date('Y-m-d').'.pdf';
        }else
        {
             $destination = Pdf::DEST_FILE;
             $filename = "C:/php/grid_".date('Y-m-d').'.pdf';
        }
        
         $pdf = new Pdf([
        // set to use core fonts only
        'mode' => Pdf::MODE_UTF8, 
        // A4 paper format
        'format' => Pdf::FORMAT_A4, 
        // portrait orientation
        'orientation' => Pdf::ORIENT_LANDSCAPE,
        //name of file
        'filename' => $filename,
        // stream to browser inline
        'destination' => $destination, 
        // your html content input
        'content' => $tbl,
        // format content from your own css file if needed or use the
        // enhanced bootstrap css built by Krajee for mPDF formatting 
        //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'cssFile' => '@frontend/web/css/site2.css',
        // any css to be embedded if required
        //'cssInline' => $css, 
         // set mPDF properties on the fly
        'options' => ['title' => 'Advanta Seeds'],
        // call mPDF methods on the fly
//        'methods' => [ 
//            'SetHeader'=>['Krajee Report Header'], 
//            'SetFooter'=>['{PAGENO}'],
//        ]
        'marginTop' => 7.50,
    ]);
    
    // return the pdf output as per the destination setting
    
    $pdf->render(); 
    
    return $filename;
        
    exit;    
   
    }
    
    static function createSamplesByTemplate($model)
    {
        $samples_formated = htmlspecialchars($model->SampleName);
        //formating string on new lines formates to <br />
        $samples_formated = nl2br($samples_formated);
        //crate array cutting string on new lines formates to <br />
        $samples_formated = explode('<br />',$samples_formated);
        // remove white spaces, and replace with ~
        //$samples_formated = preg_replace('/\s+/', '~', $samples_formated); 
        //crate array cutting string on new lines formates to <br~/>~
        //$samples_formated = explode('<br~/>~',$samples_formated);
        echo "<pre>";
        
        foreach ($samples_formated as $row)
        {
            $separated_rows[] = preg_replace('/\s+/', '~', trim($row), ($model->ColumnNumbers - 1)); 
        }
        
        if(is_array($separated_rows))
        {
            foreach($separated_rows as $wholeRows)
            {
                $array_rows_by_columns[] = explode('~', preg_replace('/\s+/', '', $wholeRows));
            }
        }
        
        //In the first index, should be the headers     
        if($model->ColumnNumbers == count($array_rows_by_columns[0]))
        {
            if(($result = array_search($model->ColumnPivote, $array_rows_by_columns[0])) == false)
            {
                $model->addError('SampleName','Pivote Column was not found');
                return ['error' => true];
            }
        }else{
            $model->addError('SampleName','The number of columns defined does not match the template');
            return ['error' => true];
        }
        //pop headers row;
        array_shift($array_rows_by_columns);
        //prepare samples names 
       
        $result = $model->prepareSamples($array_rows_by_columns);
        
        return $result;
    }
    /* Generate an array with the samples named by template
     * @prarms mixed, object
     * @return array
     */
    private function prepareSamples($array_samples)
    {
        $samples = [];
        
       if(is_array($array_samples))
       {
           foreach($array_samples as $sample)
           {
               if(count($sample) == $this->ColumnNumbers)
               {
                   //get the last element of array
                   $pivot = array_pop($sample);
                   
                   $sample_name = implode($this->CharSeparator, $sample);
                   if(strlen($sample_name) >= 12)
                   {
                       $this->addError('SampleName','The length of the sample names can not exceed 15 charcateres');
                       return ['error' => true];
                   }
                   
                    // remove some white space left
                   if(strpos($replace_pivot = preg_replace('/\s+/', '~', $pivot ), '~'))
                   {
                       $pivot = str_replace('~', ',', $replace_pivot );
                   }
                   
                   if(strpos($pivot, ',') !== false)
                   {
                       $samples = array_merge($samples, $this->alternatedPivot($pivot, $sample_name));
                   }elseif(strpos($pivot,'-') !== false)
                   {
                       
                       $samples =  array_merge($samples, $this->secuentialPivot($pivot, $sample_name));
                       
                   }else
                   {
                       $samples = array_merge($samples, $this->singlePivot($pivot, $sample_name));
                   }
               }
           }
           return $samples;
       }else
       return false;
    }
    
    /*
     * Pivot samples in secuential orden from a-b
     * @params $pivot string, $samples_name array
     * @return array
     */
    private function secuentialPivot($pivot, $sample_name)
    {
        ini_set('memory_limit', '-1');
        
        $array_pivots = explode('-', $pivot);
        
        for($i = $array_pivots[0]; $i <= $array_pivots[1]; $i++)
        {
            $samples[] = $sample_name.$this->CharSeparator.$i;
        }
        
        return $samples;       
    }
    
    /*
     * Pivot samples in alternated orden a, c, e
     * @params $pivot string, $samples_name array
     * @return array
     */
    private function alternatedPivot($pivot, $sample_name)
    {
        $array_pivots = explode(',', $pivot);
        
        foreach($array_pivots as $key => $pivots)
        {
            if(strpos($pivots, '-') !== false)
            {
                $array_pivot = explode('-', $pivots);
        
                for($i = $array_pivot[0]; $i <= $array_pivot[1]; $i++)
                {
                    $samples[] = $sample_name.$this->CharSeparator.trim($i);
                }
                
            }else
                $samples[] = $sample_name.$this->CharSeparator.trim($pivots);
        }
        
        return $samples;     
        
    }
    
    private function singlePivot($pivot, $sample_name)
    {
        for($i = 1; $i <= $pivot; $i++)
        {
            $samples[] = $sample_name.$this->CharSeparator.$i;
        }
        return $samples;
    }
    
    /*** New Metods ***/
    public function prepareSamplesToCreateGrid($idProjects, $method, $f1, $action)
    {
        $last_index = null;
        
        foreach($idProjects as $id)
        {
            $project = Project::findOne($id);
            
            if($project)
            {
                $parents = MaterialsByProject::find()
                                    ->where("ProjectId = " . $project->ProjectId . " and ParentTypeId<>3 ")
                                    ->orderBy([ 'ParentTypeId' => SORT_ASC])                   
                                    ->all();
                
                $samples = \yii\helpers\ArrayHelper::toArray($project->samplesByProjects);
                
                $size = $parents == null ? 95 : 93; 
                
                if($method == SamplesByProject::COMBINATED && $this->samplesContainer != null)
                {
                    //NECESITO OBTENER EL ULTIMO INDICE DEL PLATE DE 96 Q QUEDO SIN TERMINAR.
                    $last_index = count($this->samplesContainer) % 96;
                }else
                    $last_index = null;
                $lastIndex = $this->buildSamplesByPlateArray($project->ProjectId, $size - $f1, $samples, $parents, $f1, $last_index, $method); 
            }
        }
        //print_r($this->samplesContainer); exit;
        $rows = $this->saveByBatchSamples($this->samplesContainer);
        
        return $rows > 1;
        
    }
    
    protected function buildSamplesByPlateArray($projectId, $size, $samples, $parents, $f1, $lastIndex = null, $method = null)
    {           
        /*if($parents)
            $array_parentes = \yii\helpers\ArrayHelper::map($parents,"Material_Test_Id","materialTest.Name");  
        */      
        $count = $lastIndex == 0 ? 1 : $lastIndex;
        
        //Inconsistencia de muestras
        if($count >= 93 && $count < 96)
        {
            for($rest = $count; $rest < 96; $rest++)
            {
                $this->samplesContainer[] = ["Type" => "NS"];
            }
            $count = 1;
        }
        
        if($count > 1 )
        {
            $plates_array = \yii\helpers\ArrayHelper::getColumn($this->samplesContainer, "PlateId");
                // remove last element, because it might be null
            array_pop($plates_array);
            
            $plate_current = end(array_filter($plates_array));
            $count++;
        }
        $plate = null;
        $iterator = 0;
                
        foreach($samples as &$sample)
        {
            if($count == 1)
            {
                $plate = New Plate();
                $plate->StatusPlateId = StatusPlate::SENT;
                $plate->Date = date('Y-m-d');
                $plate->IsActive = 1;
                $plate->save();
                
                $plate->plateByProject($projectId, $plate->PlateId);
            }
            if($plate)
            {
                $sample['PlateId'] = $plate->PlateId;
            }else
            {
                $sample['PlateId'] = $plate_current;
                if($iterator == 0)
                {
                    
                    Plate::plateByProject($projectId, $plate_current);
                }
            }
            
            $sample['Type'] = 'SAMPLE';
            
            $this->samplesContainer[] = $sample;
                   
            if($count == $size)
            {   
                //$currenIndex = count($this->samplesContainer);
                
                if($parents)
                {
                    $this->samplesContainer[] = ["Type" => "PARENT"];
                    $this->samplesContainer[] = ["Type" => "PARENT"];
                }
                
                if($f1)
                {
                    $this->samplesContainer[] = ["Type" => "F1"];   
                }
                                
                $this->samplesContainer[] = ["Type" => "CN"];
                
                $count = 1;
                
            }else           
                $count++;
            
            //
            $iterator++;
        }
        //print_r($count . "<br>");
        // if the las sample
        if($count > 1 && $count <= $size)
        {
            
            if($parents)
            {
                $this->samplesContainer[] = ["Type" => "PARENT"];
                $this->samplesContainer[] = ["Type" => "PARENT"];
            }

            if($f1)
            {
                $this->samplesContainer[] = ["Type" => "F1"];   
            }
            
            $this->samplesContainer[] = ["Type" => "CN"];
            
            if($method != SamplesByProject::COMBINATED )
            {
                //sum 3 places for p1,p2 and cn
                $count += 3;
                
                for($rest = $count; $rest <= 96; $rest++)
                {
                    $this->samplesContainer[] = ["Type" => "NS"];
                }

                $count = 1;
            }
        }
        
        return $count;
    }
    
    protected function saveByBatchSamples($samples_array) {
        
        $plateId = null;
        $sql = "INSERT INTO samples_by_plate (PlateId, SamplesByProjectId, Type,IsActive) VALUES";
            $i = 1;
            foreach ($samples_array as $samples_by_plate) {
                
                if(key_exists('PlateId', $samples_by_plate))
                {
                    if ($plateId == null || ($plateId != $samples_by_plate['PlateId'] && $samples_by_plate['PlateId'] != '' )) {
                        $plateId = $samples_by_plate['PlateId'];
                    }
                }
                switch ($samples_by_plate['Type']) {
                    case 'SAMPLE':
                        $sql .="(" . $plateId . "," . $samples_by_plate['SamplesByProjectId'] . ",'" . $samples_by_plate['Type'] . "', 1),";
                        break;
                    case 'PARENT':
                        $sql .="(" . $plateId . ",NULL,'" . $samples_by_plate['Type'] . "', 1),";
                        break;
                    case 'CN':
                        $sql .="(" . $plateId . ",NULL,'" . $samples_by_plate['Type'] . "', 1),";
                        break;
                    case 'F1':
                        $sql .="(" . $plateId . ",NULL,'" . $samples_by_plate['Type'] . "', 1),";
                        break;
                    default :
                        //No Sample
                        $sql .="(" . $plateId . ",NULL,'NS', 1),";
                        break;
                }
                
                if($i == 96)
                    $i = 1;
                else
                    $i++;
            }
            
            if($i > 1)
            {
                for($i2 = $i; $i2 <= 96; $i2++)
                {
                    $sql .="(" . $plateId . ",NULL,NULL, 1),";
                }
            }
        
        $replace_semicolon = substr_replace($sql, ";", -1);

        return Yii::$app->db->createCommand($replace_semicolon)->execute();
    }
     
    static function createPdfBarcode($plates_array)
    {
        $string .= $this->render("plate/get-barcode", [
                        'plate' => $plates_array,
        ]);
        
        $filename = 'C:/php/sheet_barcode_'.date('Y-m-d');
        
        $pdf = new Pdf([
        
            'mode' => Pdf::MODE_UTF8, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'filename' => $filename,
            'destination' => Pdf::DEST_FILE,
            'content' => $string,
            'cssFile' => '@frontend/web/css/site2.css',
            'options' => ['title' => 'Barcode Sheet'],
            'marginTop' => 7.50,
        ]);

        $pdf->render(); 

        return $filename;
                
    }
}