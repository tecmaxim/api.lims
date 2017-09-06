<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "plate_history_by_project".
 *
 * @property integer $PlateHistoryByProjectId
 * @property integer $PlateId
 * @property integer $ProjectId
 * @property string $Date
 * @property integer $IsActive
 *
 * @property Project $project
 * @property Plate $plate
 */
class PlateHistoryByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plate_history_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PlateId', 'ProjectId', 'Date', 'IsActive'], 'required'],
            [['PlateId', 'ProjectId', 'IsActive'], 'integer'],
            [['Date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PlateHistoryByProjectId' => 'Plate History By Job',
            'PlateId' => 'Plate ID',
            'ProjectId' => 'Job',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
    {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }
    
    static function getProjectByPlateId($plateId)
    {
        $result = PlatesByProject::findAll(["PlateId" => $plateId, "IsActive" => 1]);
        if($result && $result->count == 1)
        {
            return $result;
        }else
            return null;
    }
}
