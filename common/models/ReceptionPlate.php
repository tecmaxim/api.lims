<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reception_plate".
 *
 * @property integer $ReceptionPlateId
 * @property string $LabReception
 * @property integer $ProjectId
 * @property integer $IsActive
 *
 * @property Project $project
 */
class ReceptionPlate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reception_plate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LabReception', 'ProjectId'], 'required'],
            [['LabReception'], 'date', 'format' => 'yyyy-M-d'],
            [['LabReception'], 'safe'],
            [['ProjectId', 'IsActive'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ReceptionPlateId' => Yii::t('app', 'Reception Plate ID'),
            'LabReception' => Yii::t('app', 'Lab Reception'),
            'ProjectId' => Yii::t('app', 'Project ID'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
}
