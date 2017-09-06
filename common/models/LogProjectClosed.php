<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log_project_closed".
 *
 * @property integer $LogProjectClosedId
 * @property integer $ProjectId
 * @property string $Description
 *
 * @property Project $project
 */
class LogProjectClosed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_project_closed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'Description'], 'required'],
            [['ProjectId'], 'integer'],
            [['Description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'LogProjectClosedId' => 'Log Project Closed ID',
            'ProjectId' => 'Project ID',
            'Description' => 'Description',
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
