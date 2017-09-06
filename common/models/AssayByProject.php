<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

/**
 * This is the model class for table "assay_by_project".
 *
 * @property integer $AssayByProjectId
 * @property integer $ProjectId
 * @property integer $BarcodeAssay
 * @property string $Path
 * @property boolean $IsActive
 * @property boolean $Date
 *
 * @property Project $project
 */
class AssayByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'assay_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', ], 'integer'],
            [['IsActive'], 'boolean'],
            [['Date'], 'safe'],
            [['BarcodeAssay'], 'safe'],
            [['Path'], 'string', 'max' => 250],
            [['Comments',], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'AssayByProjectId' => 'Assay By Job',
            'ProjectId' => 'Job',
            'BarcodeAssay' => 'Barcode Assay',
            'Path' => 'Path',
            'Comments' => 'Comments',
            'Date' => 'Date',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /*
     * parameter: string
     * @return mixed
     */
    public function checkFile($file)
    {
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);

        $Reader -> setReadDataOnly (true);
        $ObjXLS = $Reader ->load( $file );
        $ObjXLS->setActiveSheetIndex(0);

        $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
        //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
        $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);
        $error = [];
        if($highestColumnIndex != 3 || $lastRow != 100 )
        {
            $error['dimensions'] = "File dimensions is wrong (3 columns / 100 rows).";
        }
        
        if(($result = $this->controlHeaders($ObjXLS)) != null)
        {
            $error['headers'] = "The header is wrong. (Ref: <i> Barcode: number, Plate Type: type, Well Layout: xxx</i>)";
        }
        
        if(($result = $this->controlHeadersAssay($ObjXLS)) != null)
        {
            $error['headersAssay'] = "The header assay is wrong. (Ref: <i> Well Location, Liquid Name, Liquid Type</i>)";
        }
        
        if($error != null)
        {
            $ObjXLS->disconnectWorksheets();
            return $error;
        }
        
        if(($result = $this->controlMarkers($ObjXLS, $lastRow)) != null)
        {
            $error['markers'] = "Some of the markers are in the file, are not in the database:".$result;
        }
        
        
        $ObjXLS->disconnectWorksheets();
	unset($ObjXLS, $Reader);
        
        return $error;
    }
    
    
    private function controlHeaders($ObjXLS)
    {
        for($col=0; $col < 3; $col++ )
        {
            if($col == 0)
            {
                $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 1)->getValue())) != 'BARCODE:' ? true : false;
                $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 2)->getValue())) != 'PLATETYPE:' ? true : false;
                $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 3)->getValue())) != 'WELLLAYOUT:' ? true : false;
            }elseif($col == 1)
            {
                $error[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 1)->getValue() == '' ? true : false;
                $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 2)->getValue())) != 'ASSAY' ? true : false;
                $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 3)->getValue())) != 'SBS-96' ? true : false;
            }else
            {
                $error[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 1)->getValue() != '' ? true : false;
                $error[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 2)->getValue() != '' ? true : false;
                $error[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 3)->getValue() != '' ? true : false;
            }
            
        }
        if (in_array(true, $error))
            return true;
        else
            return null;
    }
    
    private function controlHeadersAssay($ObjXLS)
    {
        $col = 0;
        //$col++;
        //$col++;
        
        $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col++, 4)->getValue())) != 'WELLLOCATION' ? true : false;
        $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col++, 4)->getValue())) != 'LIQUIDNAME' ? true : false;
        $error[] = ($new_string = $this->normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 4)->getValue())) != 'LIQUIDTYPE' ? true : false;   
        
        if (in_array(true, $error))
            return true;
        else
            return null;
    }
    
    
    private function controlMarkers($ObjXLS, $highestRow)
    {
        $snpLab_fail = null;
        for($row=5; $row <= $highestRow; $row++ )
        {
            $val = $ObjXLS->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue();
            if($val != "")
            {
                $marker = $this->normalizeString($val);
                
                /* Temporary bheavior with markers*/
                $snpLab = SnpLab::find()
                                    ->where(['UPPER(LabName)' => $marker, 'IsActive'=>1])
                                    //->where(['UPPER(Name)' => $marker, 'IsActive'=>1])
                                    ->one();
                if(!$snpLab)
                {
                    $snpLab_fail .= $snpLab_fail == null ? '"'.$val.'"' : ', "'.$val.'"'; 
                }
            }
        }
        
        return $snpLab_fail;
    }
    
    
    public function getBarcodeFromAssay($file)
    {
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);

        $Reader -> setReadDataOnly (true);
        $ObjXLS = $Reader ->load( $file );
        $ObjXLS->setActiveSheetIndex(0);
        
        $barcode = $ObjXLS->getActiveSheet()->getCellByColumnAndRow(1, 1)->getValue();
        
        $ObjXLS->disconnectWorksheets();
	unset($ObjXLS, $Reader);
        
        return $barcode;
    }
    
    public function saveAssay($uploadModel ,$file, $barcode )
    {
        $this->BarcodeAssay = $barcode;
        $this->ProjectId = $uploadModel->ProjectId;
        $this->Path = $file;
        $this->Comments = $uploadModel->Comments;
        $this->Date = date('Y-m-d');
        $this->IsActive = 1;
        if(!$this->save())
        {
            print_r($this->getErrors()); exit;
        }
    }
            
    private function normalizeString($string)
    {
        $new_string = iconv( "ASCII", "UTF-8//IGNORE",  $string);
        $new_string = trim($new_string);
        $new_string = str_replace(" ", "", $new_string);
        $new_string = strtoupper($new_string);
        
        return $new_string;
    }
}
