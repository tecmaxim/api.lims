<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "protocol".
 *
 * @property string $ProtocolId
 * @property string $Code
 * @property integer $ProjectId
 * @property integer $ProtocolResultId
 * @property string $Comments
 * @property boolean $IsActive
 *
 * @property ProtocolResult $protocolResult
 * @property Project $project
 */
class Protocol extends \yii\db\ActiveRecord
    {
    public $ProjectId;
    public $ProtocolFile;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'protocol';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Code'], 'required'],
            [['ProtocolResultId'], 'integer'],
            [['Comments'], 'string'],
            [['IsActive'], 'boolean'],
            [['Code'], 'string', 'max' => 100],
            [['Code'], 'uniqueCode'],
            [['ProtocolFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['pdf'], 'checkExtensionByMimeType'=>false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProtocolId' => 'Protocol ID',
            'Code' => 'Code',
            
            'ProtocolResultId' => 'Result',
            'Comments' => 'Comments',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProtocolResult()
    {
        return $this->hasOne(ProtocolResult::className(), ['ProtocolResultId' => 'ProtocolResultId']);
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRawDatas()
    {
       return $this->hasMany(RawData::className(), ['ProtocolId' => 'ProtocolId']);
   }
      
    static function getProtocolsByProjectId($projectId)
    {
        $kendoArray = [];
        $protocols = Protocol::find()
                            ->innerJoin('protocol_by_project', 'protocol_by_project.ProtocolId = protocol.ProtocolId')
                            ->where(["protocol_by_project.ProjectId" => $projectId, 'IsActive'=>1 ])
                            ->all();
        if($protocols)
        {
            foreach ($protocols as $protocol)
            {
                $kendoArray[] = ["ProtocolId" => $protocol->ProtocolId,
                                 "Code" => $protocol->Code,
                                 "ProtocolResult" => $protocol->protocolResult != "" ? $protocol->protocolResult->Name : 'Empty',
                                 "Comments" => $protocol->Comments,
                                ];
            }
        }
        //in_array('REPORT TABLE', array_map(function($reports) { return $reports['ReportName']; }, $kendoArray));
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $kendoArray;
    }
    
    static function getProtocolByReportId($reportId)
    {
        $protocolId = RawData::find()
                        ->where(["ReportId" => $reportId, "IsActive" => 1])
                        ->column('ProtocolId')
                        ->one();
        
        return $protocolId;
    }
    
    static function deleteById($id)
    {
        $protocol  = Protocol::findOne($id);
        $protocol->deleteLogic();
    }
    
    function deleteLogic()
    {
        $this->IsActive = 0;
        $this->save();
    }
    
    public function uniqueCode($attribute, $params)
    {
        $have = $this->find()->where(["Code" => $this->$attribute, "IsActive" => 1 ])->one();
        if($have)
        {
            $this->addError('Code', "The project must be unique.");
        }
    }
    
}
