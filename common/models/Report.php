<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report".
 *
 * @property integer $ReportId
 * @property integer $ProjectId
 * @property integer $ReportTypeId
 * @property string $Url
 * @property boolean $IsActive
 *
 * @property ReportType $reportType
 * @property Project $project
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $file;
    
    const ROW_DATA = 1;
    CONST DOUBLE_ENTRY_TABLE = 2;
    const DOUBLE_ENTRY_TABLE_PROCESED = 3;
    const REPORT = 4;
    
     
    public static function tableName()
    {
        return 'report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['xlsx','xls','csv'], 'checkExtensionByMimeType'=>false],
            [['ProjectId', 'ReportTypeId'], 'required'],
            [['ProjectId', 'ReportTypeId'], 'integer'],
            [['Date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['IsActive'], 'boolean'],
            [['Url'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ReportId' => 'Report ID',
            'ProjectId' => 'Job',
            'ReportTypeId' => 'Report Type',
            'Url' => 'Url',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportType()
    {
        return $this->hasOne(ReportType::className(), ['ReportTypeId' => 'ReportTypeId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /*
     * 
     */
    static function getReportByProjectId($projectId)
    {
        $kendoArray = [];
        $reports = Report::find()
                            ->where(["ProjectId" => $projectId, 'IsActive'=>1 ])
                            ->all();
        $typeReports = ReportType::find()->where(["IsActive" => 1])->asArray()->all();
        if($reports)
        {
            foreach ($reports as $report)
            {
                $kendoArray[] = ["ReportTypeId" => $report->ReportTypeId,
                                 "ReportName" => $report->reportType->Name,
                                 "Url" => $report->Url == "" ? 'Empty' : "/".$report->Url,
                                 "Date" => $report->Date == "" ? 'Empty' : date('d-m-Y H:i:s', strtotime($report->Date)),
                                 "File" => substr($report->Url, 18),
                                 "ReportId" => $report->ReportId
                                ];
            }
        }
        //in_array('REPORT TABLE', array_map(function($reports) { return $reports['ReportName']; }, $kendoArray));
        foreach($typeReports as $type)
        {
            if(!in_array($type['Name'], array_map(function($reports) { return $reports['ReportName']; }, $kendoArray)))
            {
                $kendoArray[] = [
                                    "ReportTypeId" => $type['ReportTypeId'],
                                    "ReportName" => $type['Name'], 
                                    "Url" => null, 
                                    "Date" => null,
                                    "File" => null
                                ];
            }
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $kendoArray;
    }
    
    /*
     * Delete reports into BD
     * @parameter integer
     * @return null
     */
    static function deleteById($id, $projectId, $file)
    {
        $model = Report::findOne($id);
        
        // if is RawData, it must delete only de relation
        if($model->ReportTypeId == ReportType::RAW_DATA)
        {

            $rawData = RawData::find()->where(["ReportId" => $id])->asArray()->one();
            $cantProtocolByProject = ProtocolByProject::find()->where(["ProtocolId" => $rawData['ProtocolId']])->count();
            $protocolByProject = ProtocolByProject::find()
                                                        ->where(["ProtocolId" => $rawData['ProtocolId'], "ProjectId" => $projectId])
                                                        ->one();
            if($cantProtocolByProject == 1)
            {
                $protocolByProject->delete();
                Protocol::deleteById($rawData['ProtocolId']);
                RawData::deleteByReportId($id);
                unlink($file);
                return $model->deleteLogic();
            }else
            {
                if($protocolByProject)
                    $protocolByProject->delete();
                return $model->deleteLogic();
            }
                  
        }elseif($model->ReportTypeId == ReportType::DOUBLE_ENTRY_TABLE)
        {
            $doubleEntryTable = DoubleEntryTable::find()
                                                ->where(["ReportId" => $model->ReportId, "IsActive"=>1])
                                                ->one();
            if($doubleEntryTable)
            {
                $doubleEntryTable->deleteAllData();
            }
            unlink($file);
            return $model->deleteLogic();
        }else
        {
            unlink($file);
            return $model->deleteLogic();
        }
    }
    
    public function deleteLogic()
    {
        $sqlUpdate = "UPDATE report SET IsActive = 0 WHERE ReportId=".$this->ReportId;
        
        Yii::$app->db->createCommand($sqlUpdate)->execute();
        
        echo 'The File has been deleted <br>';            
    }
        
    
}
