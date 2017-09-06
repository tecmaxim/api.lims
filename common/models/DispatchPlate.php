<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dispatch_plate".
 *
 * @property integer $DispatchPlateId
 * @property integer $ProjectId
 * @property string $Date
 * @property string $Carrier
 * @property string $TrackingNumber
 * @property integer $IsActive
 *
 * @property Project $project
 */
class DispatchPlate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dispatch_plate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'Date', 'Carrier', 'TrackingNumber'], 'required'],
            [['ProjectId', 'IsActive'], 'integer'],
            [['Date'], 'date', 'format' => 'yyyy-M-dd'],
            [['Carrier', 'TrackingNumber'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DispatchPlateId' => Yii::t('app', 'Dispatch Plate ID'),
            'ProjectId' => Yii::t('app', 'Job'),
            'Date' => Yii::t('app', 'Date'),
            'Carrier' => Yii::t('app', 'Carrier'),
            'TrackingNumber' => Yii::t('app', 'Tracking Number'),
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
