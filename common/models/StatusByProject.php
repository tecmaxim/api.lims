<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "status_by_project".
 *
 * @property integer $StatusByProjectId
 * @property integer $ProjectId
 * @property integer $StepProjectId
 * @property string $Comments
 * @property string $Date
 *
 * @property Project $project
 * @property StepProject $stepProject
 */
class StatusByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'StepProjectId', 'Comments', 'Date'], 'required'],
            [['ProjectId', 'StepProjectId'], 'integer'],
            [['Date'], 'safe'],
            [['Comments'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'StatusByProjectId' => 'Status By Project ID',
            'ProjectId' => 'Project ID',
            'StepProjectId' => 'Step Project ID',
            'Comments' => 'Comments',
            'Date' => 'Date',
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
    public function getStepProject()
    {
        return $this->hasOne(StepProject::className(), ['StepProjectId' => 'StepProjectId']);
    }
    
    static function saveStatusByProject($project, $status)
    {
        
        $statusProject = new \common\models\StatusByProject();
        $statusProject->ProjectId = $project->ProjectId;
        $statusProject->StepProjectId = $status;
        $statusProject->Date = date("Y-m-d H:i:s");
        $statusProject->Comments = $project->CommentToChangeStatus;
        $statusProject->save();
    }
}
