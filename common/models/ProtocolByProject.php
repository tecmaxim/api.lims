<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "protocol_by_project".
 *
 * @property integer $ProtocolByProjectId
 * @property string $ProtocolId
 * @property integer $ProjectId
 *
 * @property Project $project
 * @property Protocol $protocol
 */
class ProtocolByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'protocol_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProtocolId', 'ProjectId'], 'required'],
            [['ProtocolId', 'ProjectId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProtocolByProjectId' => 'Protocol By Job ID',
            'ProtocolId' => 'Protocol ID',
            'ProjectId' => 'Job ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProtocol()
    {
        return $this->hasOne(Protocol::className(), ['ProtocolId' => 'ProtocolId']);
    }
    
    static function saveNew($protocolId, $projectId)
    {
        $model = new ProtocolByProject();
        $model->ProjectId = $projectId;
        $model->ProtocolId = $protocolId;
        $model->save();
    }
    
    
}
