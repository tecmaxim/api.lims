<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "samples_by_plate".
 *
 * @property integer $SamplesByPlateId
 * @property integer $PlateId
 * @property integer $SamplesByProjectId
 * @property integer $StatusSampleId
 * @property varchar $Type
 * @property integer $IsActive
 *
 * @property SamplesByProject $samplesByProject
 * @property Plate $plate
 * @property StatusSample $statusSample 
 */
class SamplesByPlate extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'samples_by_plate';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['PlateId', 'IsActive'], 'required'],
            [['PlateId', 'SamplesByProjectId', 'SamplesByProjectId', 'IsActive'], 'integer'],
            [['StatusSampleId', 'Type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'SamplesByPlateId' => 'Samples By Plate ID',
            'PlateId' => 'Plate ID',
            'SamplesByProjectId' => 'Samples By Project ID',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getStatusSample() {
        return $this->hasOne(StatusSample::className(), ['StatusSampleId' => 'StatusSampleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSamplesByProject() {
        return $this->hasOne(SamplesByProject::className(), ['SamplesByProjectId' => 'SamplesByProjectId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
            {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }

    /*
     * Save by sql batch string the samples and plates
     * parameter mixed
     * return null
     */

    static function saveByBatchInsert($samples_array, $method) {
        $plateId = null;
        $sql = "INSERT INTO samples_by_plate (PlateId, SamplesByProjectId, Type,IsActive) VALUES";

        if ($method === null) {
            foreach ($samples_array as $plate_samples) {
                $iterator = 0;
                foreach ($plate_samples['SamplesByProjectId'] as $sample) {
                    $sql .="(" . $plate_samples['PlateId'] . "," . $sample->SamplesByProjectId . ",'SAMPLE', 1),";
                    $iterator++;
                }
                if ($plate_samples['parents'] != null) {
                    foreach ($plate_samples['parents'] as $key => $val) {
                        $sql .="(" . $plate_samples['PlateId'] . ",NULL,'PARENT', 1),";
                        $iterator++;
                    }
                }

                if ($plate_samples['f1'] != null) {
                    $sql .="(" . $plate_samples['PlateId'] . ",NULL,'F1', 1),";
                    $iterator++;
                }

                $sql .="(" . $plate_samples['PlateId'] . ",NULL,'CN', 1),";
                $iterator++;

                for ($i = $iterator; $i <= 95; $i++) {
                    $sql .="(" . $plate_samples['PlateId'] . ",NULL,NULL, 1),";
                }
            }
        } else {

            $i = 1;
            foreach ($samples_array as $samples_by_plate) {

                if ($plateId == null || ($plateId != $samples_by_plate['PlateId'] && $samples_by_plate['PlateId'] != '' )) {
                    $plateId = $samples_by_plate['PlateId'];
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
                }
                
                if($i == 96)
                    $i = 1;
                else
                    $i++;
            }
            
            for($i2 = $i; $i2 <= 96; $i2++)
            {
                $sql .="(" . $plateId . ",NULL,NULL, 1),";
            }
        }
        $replace_semicolon = substr_replace($sql, ";", -1);
        //print_r($replace_semicolon); exit;
        Yii::$app->db->createCommand($replace_semicolon)->execute();
    }

    /*
     * Save status of each sample.
     * paramter: array
     * return null
     */

    static function saveControlSamples($control_samples, $fromAdn = null) {
        $sql = "";
        
        if($fromAdn != null)
        {
            foreach ($control_samples as $id => $val) 
            {
                if($val != "")
                {
                    $sql .= "UPDATE samples_by_plate s SET s.StatusSampleId=" . $val . " WHERE s.SamplesByPlateId=" . $id . ";";   
                }
            }
        }else
        {
            foreach ($control_samples as $id => $val) 
            {
                $val = $val == "" ? 3 : $val;
                $sql .= "UPDATE samples_by_plate s SET s.StatusSampleId=" . $val . " WHERE s.SamplesByPlateId=" . $id . ";";   
            }
        }

        Yii::$app->db->createCommand($sql)->execute();

        return null;
    }
    
    static function orderSamplesByGenSpin($samples)
    {
        foreach($samples as $arraySample)
        {
            $cont = 1;
            for($i = 0; $i < 96 ; $i++)
            {
                $matrizSamples[] = $arraySample[$i];
                $matrizSamples[] = isset($arraySample[$i+8]) ? $arraySample[$i+8]: "";
                
                //unset($arraySample[$cont]);
                //unset($arraySample[$cont+8]);
                
                if($cont == 8)
                {
                    //$arraySample = array_values($arraySample);
                    $i += 8;
                    $cont = 1;
                }else
                    $cont++;
                
            }
            
        }
        return $matrizSamples;
    }
    
    static function findSamplesExtracted($cropId, $dateFrom, $dateTo, $dateType, $categories)
    {
        $serie = "";
        
        $sql = "Select count(DISTINCT sp.SamplesByPlateId) as total, DATE_FORMAT(p.Date,'%Y') as Year, DATE_FORMAT(p.Date,'%m') as Month, DATE_FORMAT(p.Date,'%d') as Day, ";
                if($dateType == 2)
                    $sql .= " DATE_FORMAT(p.Date,'%Y-%m') as auxMonth,";
                $sql .= " p.Date  FROM samples_by_plate sp"
                ." INNER JOIN plate p ON p.PlateId = sp.PlateId
                INNER JOIN plates_by_project pb ON pb.PlateId = p.PlateId
                INNER JOIN project p1 ON p1.ProjectId = pb.ProjectId 
                WHERE  sp.StatusSampleId = 4 and p1.IsActive= 1 ";
        $sql .= $cropId > 0 ? "and p1.Crop_Id =".$cropId : "";
        if($dateType == 1)
        {
            $sql.= " and p.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and p.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $columnToSearch = "Year";
            $sql.= " Group By Year"; 
            
        }
        elseif($dateType == 2)
        {
            //$date = \common\components\Operations::getNumberOfMonth($date);
            $sql.= " and p.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and p.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $columnToSearch = "auxMonth";
            $sql.= " Group By Month";
        }        
        else
        {
            $sql.= " and p.Date >= '".date('Y-m-d 01:00:00',strtotime($dateFrom))."' and p.Date <= '".date('Y-m-d H:i:s',strtotime($dateTo))."'";
            $sql.= " Group By Day";
            $columnToSearch = "Date";
           
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
        
        return $serie;
    }

}
