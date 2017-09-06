<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;


/**
 * This is the model class for table "raw_data".
 *
 * @property integer $RawData
 * @property integer $ReportId
 * @property string $AssayName
 * @property string $SamplePlateBarcode
 * @property string $SamplePlateWellLocation
 * @property string $ScanDate
 * @property string $ProtocolId
 * @property string $DateImport
 * @property boolean $IsActive
 *
 * @property Protocol $protocol
 */
class RawData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'raw_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['AssayName', 'SamplePlateBarcode', 'SamplePlateWellLocation', 'ScanDate', 'ProtocolId'], 'required'],
            [['ScanDate',], 'safe'],
            [['ProtocolId', 'ReportId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['AssayName'], 'string', 'max' => 100],
            [['SamplePlateBarcode'], 'string', 'max' => 10],
            [['SamplePlateWellLocation'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RawData' => 'Raw Data',
            'AssayName' => 'Assay Name',
            'SamplePlateBarcode' => 'Sample Plate Barcode',
            'SamplePlateWellLocation' => 'Sample Plate Well Location',
            'ScanDate' => 'Scan Date',
            'ProtocolId' => 'Protocol ID',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProtocol()
    {
        return $this->hasOne(Protocol::className(), ['ProtocolId' => 'ProtocolId']);
    }
    
    /*
     *Save Data from file RawData
     *@parameter string
     *@return array 
     */
    public function saveRawData($fileName, $modelReport)
    {
        $protocolName = "";
        
        $Reader = PHPExcel_IOFactory::createReaderForFile($fileName);
        $Reader->setReadDataOnly(true);
        $ObjXLS = $Reader->load( $fileName );
        
        $ObjXLS->setActiveSheetIndex(0);
        
        $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
        //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
        $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();
        
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);
           
        
        if($headerError = $this->controlHeaders($ObjXLS, $highestColumnIndex))
        {
            return $headerError;
        }
        
        if($protocolError = $this->controlProtocol($ObjXLS, $lastRow))
        {
            return $protocolError;
        }
        
        /* start to get data of file*/
        for($row = 2; $row <= $lastRow; $row++)
        {
            $dataFile[$row]['AssayName'] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( PHPExcel_Cell::columnIndexFromString('H') - 1, $row)->getValue();
            $dataFile[$row]['SamplePlateBarcode'] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( PHPExcel_Cell::columnIndexFromString('L') - 1, $row)->getValue();
            $dataFile[$row]['SamplePlateWellLocation'] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( PHPExcel_Cell::columnIndexFromString('M') - 1, $row)->getValue();
            $date = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( PHPExcel_Cell::columnIndexFromString('Y') - 1, $row)->getValue();
            $string = strtotime($date);
            $dataFile[$row]['ScanDate'] = date('Y-m-d H:i:s', $string);
            if($protocolName == "")
                $protocolName = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( PHPExcel_Cell::columnIndexFromString('AC') - 1, $row)->getValue();
        }
        
        $result = $this->saveDataFile($dataFile, $modelReport, $protocolName);
        
        return $result;
    }
    
    private function controlHeaders($ObjXLS, $length)
    {
        $headerControl = array('AssayName',
                                'SamplePlateBarcode',
                                'SamplePlateWellLocation',
                                'ScanDate',
                                'ProtocolName');
        $row = 1; 
        
        for($col = 0; $col <= $length; $col++)
        {
            $headers[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
        }
        
        $cont = 0;
        
        foreach($headerControl as $key => $headerName)
        {
            if(($headerKey = array_search($headerName, $headers)) !== false)
            {
                unset($headers[$headerKey]);
                $cont++;
            }
            
        }
        
        if($cont != count($headerControl))
            return "File headers do not match";
        else
            return null;
    }
    
    private function controlProtocol($ObjXLS, $lastRow)
    {
        $protocol = "";
        $columnIndex = PHPExcel_Cell::columnIndexFromString('AB'); //protocol;
                
        for($row = 2; $row <= $lastRow; $row++)
        {
            if($protocol == "")
            {
                $protocol = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($columnIndex, $row)->getValue();
                
            }else
            {
                if($protocol != $ObjXLS->getActiveSheet()->getCellByColumnAndRow($columnIndex, $row)->getValue())
                        
                    return "More than one protocols were found in the file.";        
            }
        }

        if($protocol == "") return "No protocols in the file";
    }
    
    private function saveDataFile($dataFile, $modelReport, $protocolName)
    {
        $sql = "INSERT INTO raw_data (AssayName,SamplePlateBarcode,SamplePlateWellLocation,ScanDate,ProtocolId, ReportId, IsActive) VALUES ";
        
        $protocol = Protocol::find()->where(["Code" => $protocolName, "IsActive" => 1 ])->one();
        
        if($protocol != null)
        {
            $protocolId = $protocol->ProtocolId;
        }
        else
        {
            $protocol = new Protocol();
            //$protocol->ProjectId = $modelReport->ProjectId;
            $protocol->Code = $protocolName;
            $protocol->IsActive = 1;
            
            if(!$protocol->save())
            {
                print_r($protocol->getErrors());
                exit;
            };
            $protocolId = $protocol->ProtocolId;
        }
        
        //Save relation protocl_by_project
        ProtocolByProject::saveNew($protocolId, $modelReport->ProjectId);
            
        foreach($dataFile as $data)
        {
            $sql .= "('".$data['AssayName']."',"
                    . "'".$data['SamplePlateBarcode']."',"
                    . "'".$data['SamplePlateWellLocation']."',"
                    . "'".$data['ScanDate']."',"
                    . $protocolId.","
                    . $modelReport->ReportId.","
                    . " 1),";
        }
        
        $sql = substr_replace($sql,';',-1);
        
        Yii::$app->db->createCommand($sql)->execute();
    }
        
    static function getRawDatapoint($cropId, $dateFrom, $dateTo, $dateType, $categories)
    {
        $serie = [];
         $sql = "SELECT count(*) as total, DATE_FORMAT(r2.ScanDate, '%Y') as Year, 
                                          DATE_FORMAT(r2.ScanDate, '%m') as Month,
                                          DATE_FORMAT(r2.ScanDate, '%d') as Day,
                                          DATE_FORMAT(r2.ScanDate, '%Y-%m-%d') as ScanDate";
                                          if($dateType == 2)
                                                $sql .= ", DATE_FORMAT(r2.ScanDate,'%Y-%m') as auxMonth";
                                          $sql .= " FROM raw_data r2
                INNER JOIN
                            (
                                SELECT concat(AssayName, SamplePlateBarcode, SamplePlateWellLocation) as 'string', RawDataId, r.ScanDate
                                FROM raw_data r
                                inner join protocol p on p.ProtocolId = r.ProtocolId
                                inner join protocol_by_project pbp1 on p.ProtocolId = pbp1.ProtocolId
                                inner join project p1 on p1.ProjectId = pbp1.ProjectId
                                where  p.IsActive = 1 and p1.IsActive = 1";
                                $sql .= $cropId > 0 ? " and p1.Crop_Id =".$cropId : "";
                                $sql .= " group by string
                            ) as r3 on r3.RawDataId = r2.RawDataId WHERE ";
        
        if($dateType == 1)
        {
             $columnToSearch = "Year";
            $sql.= " r2.ScanDate >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and r2.ScanDate <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Year";
        }
        elseif($dateType == 2)
        {
            $columnToSearch = "auxMonth";
            $sql.= " r2.ScanDate >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and r2.ScanDate <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Month";
        }        
        else
        {
            $columnToSearch = 'ScanDate';
            $sql.= " r2.ScanDate >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and r2.ScanDate <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Day";
            
        }
        
        $results = Yii::$app->db->createCommand($sql)->queryAll();
        if($results)
        {
            foreach($categories as $key => $val)
            {
                $dateCat = \common\components\Operations::dateFromCategories($dateType, $val);                      
               
                //print_r($dateCat); exit;
                    if(is_int($restKey = array_search($dateCat, \common\components\Operations::array_column($results, $columnToSearch))))
                    {
                        $serie[] = (int)$results[$restKey]['total'];
                    }else
                        $serie[] = 0; 
            }
        }
        //print_r($serie); exit;
        return $serie;
    }
    
    static function deleteByReportId($reportId)
    {
        $sql = "DELETE FROM raw_data WHERE ReportId=".$reportId;
        
        return Yii::$app->db->createCommand($sql)->execute();
    }
    /*
     * 
     *
    function recursiveDelete($str)
    {
       if (is_file($str))
       {
           return @unlink($str);
       }
       elseif (is_dir($str))
       {
           $scan = glob(rtrim($str,'/').'/*');
           foreach($scan as $index=>$path)
           {
               recursiveDelete($path);
           }
           return @rmdir($str);
       }
    }
     * 
     */
    
    
    static function saveProtocolByProject($reportId, $projectId)
    {
        $protocolByProject = new ProtocolByProject();
        $protocolByProject->ProtocolId = Protocol::getProtocolByReportId($reportId);
        $protocolByProject->ProjectId = $projectId;
        if(!$protocolByProject->save())
        {
            print_r($protocolByProject->getErrors()); exit;
        }
            
    }
    
}
