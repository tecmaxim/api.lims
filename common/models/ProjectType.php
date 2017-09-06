<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "analysis".
 *
 * @property integer $AnalysisId
 * @property string $Name
 * @property string $Type
 * @property integer $IsActive
 *
 * @property ProjectByAnalysis[] $projectByAnalyses
 */
class ProjectType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IsActive'], 'integer'],
            [['Name', 'Type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectTypeId' => Yii::t('app', 'Job Type'),
            'Name' => Yii::t('app', 'Name'),
            'Type' => Yii::t('app', 'Type'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectByAnalyses()
    {
        return $this->hasMany(ProjectByProjectType::className(), ['ProjectTypeId' => 'ProjectTypeId']);
    }
}
