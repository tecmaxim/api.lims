<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "double_entry_result".
 *
 * @property integer $DoubleEntryResultId
 * @property integer $DoubleEntryMarkerId
 * @property integer $DoubleEntrySampleId
 * @property integer $DoubleEntryTableId
 * @property string $Value
 *
 * @property DoubleEntrySample $doubleEntrySample
 * @property DoubleEntryMarker $doubleEntryMarker
 * @property DoubleEntryTable $doubleEntryTable
 */
class DoubleEntryResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'double_entry_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DoubleEntryMarkerId', 'DoubleEntrySampleId', 'DoubleEntryTableId', 'Value'], 'required'],
            [['DoubleEntryMarkerId', 'DoubleEntrySampleId', 'DoubleEntryTableId'], 'integer'],
            [['Value'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DoubleEntryResultId' => 'Double Entry Result ID',
            'DoubleEntryMarkerId' => 'Double Entry Marker ID',
            'DoubleEntrySampleId' => 'Double Entry Sample ID',
            'DoubleEntryTableId' => 'Double Entry Table ID',
            'Value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntrySample()
    {
        return $this->hasOne(DoubleEntrySample::className(), ['DoubleEntrySampleId' => 'DoubleEntrySampleId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntryMarker()
    {
        return $this->hasOne(DoubleEntryMarker::className(), ['DoubleEntryMarkerId' => 'DoubleEntryMarkerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleEntryTable()
    {
        return $this->hasOne(DoubleEntryTable::className(), ['DoubleEntryTableId' => 'DoubleEntryTableId']);
    }
}
