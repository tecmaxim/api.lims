<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use common\components\Operations;

/**
 * This is the model class for table "double_entry_table".
 *
 * @property integer $DoubleEntryTableId
 * @property integer $ProjectId
 * @property string $Url
 * @property string $Date
 * @property boolean $IsActive
 *
 * @property DoubleEntryMarker[] $doubleEntryMarkers
 * @property DoubleEntryResult[] $doubleEntryResults
 * @property DoubleEntrySample[] $doubleEntrySamples
 * @property Project $project
 */
class DoubleEntryTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'double_entry_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ReportId', 'Date'], 'required'],
            [['ReportId'], 'integer'],
            [['Date'], 'safe'],
            [['IsActive'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DoubleEntryTableId' => 'Double Entry Table ID',
            'ProjectId' => 'Job',
            'Url' => 'Url',
            'Date' => 'Date',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntryMarkers()
    {
        return $this->hasMany(DoubleEntryMarker::className(), ['DoubleEntryTableId' => 'DoubleEntryTableId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntryResults()
    {
        return $this->hasMany(DoubleEntryResult::className(), ['DoubleEntryTableId' => 'DoubleEntryTableId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntrySamples()
    {
        return $this->hasMany(DoubleEntrySample::className(), ['DoubleEntryTableId' => 'DoubleEntryTableId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['ReportId' => 'ReportId']);
    }
    
    static function saveDoubleEntryTable($fileName, $report)
    {
        $Reader = PHPExcel_IOFactory::createReaderForFile($fileName);
        $model = new DoubleEntryTable();
        
        $Reader->setReadDataOnly(true);
        $ObjXLS = $Reader->load( $fileName );
        $ObjXLS->setActiveSheetIndex(0);

        $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
        //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
        $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();
        $lastRow = $model->controlEmptyRow($lastRow,$ObjXLS );
        $highestColumnIndex1 = PHPExcel_Cell::columnIndexFromString($lastCol);
        $highestColumnIndex = $model->controlEmptyCol($highestColumnIndex1, $ObjXLS);
        //$colSamples = 4; //begin on 4 row of file
        //$colMarker = 2;
        //
        // $result have a value, if exist any report with the same url
        $model->ReportId = $report->ReportId;
        $model->Date = date('Y-m-d H:i:s');
        $model->IsActive = 1;
        $model->save();
        
        $marker = [];
        $sql = "";
        for($rowSamples = 2; $rowSamples <= $lastRow; $rowSamples++)
        {
            $sample = Operations::normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow( 0, $rowSamples)->getValue());
            $sql .= "INSERT INTO double_entry_sample (DoubleEntryTableId, Name) VALUES"
                 . " (".$model->DoubleEntryTableId.", '".$sample."');"
                . " SET  @sampleId".$rowSamples." = LAST_INSERT_ID();";
            
            for($colMarkers = 2; $colMarkers < $highestColumnIndex; $colMarkers++)
            {
                if(count($marker) != ($highestColumnIndex - 2))
                {
                    $marker[$colMarkers] = Operations::normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow( $colMarkers, 1)->getValue());
                    $sql .= "INSERT INTO double_entry_marker (DoubleEntryTableId, Name) VALUES"
                     . " (".$model->DoubleEntryTableId.", '".$marker[$colMarkers]."');"
                    . " SET  @markerId".$colMarkers." = LAST_INSERT_ID();";
                }
                //for($colValue = 2; $colValue < $highestColumnIndex; $colValue)
                //{
                    $value = Operations::normalizeString($ObjXLS->getActiveSheet()->getCellByColumnAndRow( $colMarkers, $rowSamples)->getValue());
                     $sql .= "INSERT INTO double_entry_result (DoubleEntryMarkerId, DoubleEntrySampleId, DoubleEntryTableId, Value) VALUES"
                     . " (@markerId".$colMarkers.", @sampleId".$rowSamples.",".$model->DoubleEntryTableId.", '".$value."');";
                    // " SET  @markerId".$rowSamples." = LAST_INSERT_ID();";
                //}
            }
        }
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

            try {
                $connection->createCommand($sql)->execute();
                $transaction->commit();

            } catch (\Exception $e) {
                $transaction->rollBack();
                echo ($e);
                
            }
       
        
    }
    
    public function deleteAllData()
    {
        $sql = "DELETE FROM double_entry_result WHERE DoubleEntryTableId =".$this->DoubleEntryTableId.";";
        $sql .= " DELETE FROM double_entry_marker WHERE DoubleEntryTableId =".$this->DoubleEntryTableId.";";
        $sql .= " DELETE FROM double_entry_sample WHERE DoubleEntryTableId =".$this->DoubleEntryTableId.";";
        
        if(Yii::$app->db->createCommand($sql)->execute())
        {
            $this->IsActive = 0;
            $this->save();
        };
    }
    
    private function controlEmptyRow($lastRow, $ObjXLS)
    {
        for($i = 1; $i <= $lastRow; $i++)
        {
            $value = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( 0, $i)->getValue();
            
            if($value == "")
                return $cont;
            else
                $cont =$i;
        }
        
        return $cont;
    }
    
    private function controlEmptyCol($lastCol, $ObjXLS)
    {
        for($i = 0; $i <= $lastCol; $i++)
        {
            $value = $ObjXLS->getActiveSheet()->getCellByColumnAndRow( $i, 1)->getValue();
            
            if($value == "")
                return $i;
            else
                $cont = $i;
        }
       return $cont;
    }
    
    static function getDatapoint($cropId, $dateFrom, $dateTo, $dateType, $categories, $fails = null)
    {
        $serie = [];
        $sql = "SELECT 
                count(db.DoubleEntryResultId) as total,
                DATE_FORMAT(dt.Date, '%Y') as Year, 
                DATE_FORMAT(dt.Date, '%m') as Month, 
                DATE_FORMAT(dt.Date, '%d') as Day";
                if($dateType == 2)
                    $sql .= ", DATE_FORMAT(dt.Date,'%Y-%m') as auxMonth";
        $sql .= ",DATE_FORMAT(dt.Date,'%Y-%m-%d') as Date FROM  double_entry_result db
                INNER JOIN double_entry_table dt ON dt.DoubleEntryTableId = db.DoubleEntryTableId
                INNER JOIN report r ON r.ReportId = dt.ReportId
                INNER JOIN project p ON p.ProjectId = r.ProjectId";
                if($fails == null)
                    $sql .= " WHERE db.Value <> 'DROPOUT' and db.Value <> 'INDETERMINATE' and db.Value <> 'NOCALL' and db.Value <> 'LOWSIGNAL' and r.IsActive=1";
                else
                    $sql .= " WHERE db.Value in('DROPOUT', 'INDETERMINATE', 'NOCALL', 'LOWSIGNAL')  and r.IsActive=1";
                
                $sql .= $cropId > 0 ? " and p.Crop_Id =".$cropId : ""; 
                
        if($dateType == 1)
        {
             $columnToSearch = "Year";
            $sql.= " and dt.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and dt.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Year";
        }
        elseif($dateType == 2)
        {
            $columnToSearch = "auxMonth";
            $sql.= " and dt.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and dt.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Month";
        }        
        else
        {
            $columnToSearch = 'Date';
            $sql.= " and dt.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and dt.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
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
}
