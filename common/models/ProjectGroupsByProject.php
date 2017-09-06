<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_groups_by_project".
 *
 * @property integer $ProjectGroupsByProjectId
 * @property integer $ProjectGroupsId
 * @property integer $ProjectId
 *
 * @property ProjectGroups $projectGroups
 * @property Project $project
 */
class ProjectGroupsByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_groups_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectGroupsId', 'ProjectId'], 'required'],
            [['ProjectGroupsId', 'ProjectId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectGroupsByProjectId' => 'Project Groups By Project ID',
            'ProjectGroupsId' => 'Project Groups ID',
            'ProjectId' => 'Project ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectGroups()
    {
        return $this->hasOne(ProjectGroups::className(), ['ProjectGroupsId' => 'ProjectGroupsId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    static function findProjectsGrouped($projectId)
    {
        $sql = "SELECT ProjectId FROM project_groups_by_project p0
                WHERE p0.ProjectGroupsId = 
                                (SELECT ProjectGroupsId FROM  project_groups_by_project p
                                where p.ProjectId=".$projectId.") "
               . "and ProjectId <> ".$projectId;
        
        $projects = Yii::$app->db->createCommand($sql)->queryAll();
        
        return $projects;
    }
}
