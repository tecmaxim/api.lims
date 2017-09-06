<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_type".
 *
 * @property integer $ReportTypeId
 * @property string $Name
 * @property boolean $IsActive
 *
 * @property Report[] $reports
 */
class ReportType extends \yii\db\ActiveRecord
{
    const RAW_DATA = 1;
    const DOUBLE_ENTRY_TABLE = 2;
    const DOUBLE_ENTRY_TABLE_PROCESSED = 3;
    const REPORTS = 4;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ReportTypeId' => 'Report Type ID',
            'Name' => 'Name',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['ReportTypeId' => 'ReportTypeId']);
    }
}
