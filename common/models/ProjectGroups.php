<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_groups".
 *
 * @property integer $ProjectGroupsId
 * @property string $CreationDate
 * @property string $ShipingDate
 * @property string $ReceptionDate
 * @property string $IsActive
 *
 * @property ProjectGroupsByProject[] $projectGroupsByProjects
 */
class ProjectGroups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CreationDate', 'IsActive'], 'required'],
            [['CreationDate', 'ShipingDate', 'ReceptionDate', 'IsActive'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectGroupsId' => 'Project Groups ID',
            'CreationDate' => 'Creation Date',
            'ShipingDate' => 'Shiping Date',
            'ReceptionDate' => 'Reception Date',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectGroupsByProjects()
    {
        return $this->hasMany(ProjectGroupsByProject::className(), ['ProjectGroupsId' => 'ProjectGroupsId']);
    }
    
    static function getDataProjectGroupsByProjectId($idProject)
    {
        $sql = "SELECT p.CreationDate, p.ShipingDate, p.ReceptionDate, group_concat(p1.Name separator '<br>') as Names FROM project_groups p
                inner join project_groups_by_project p2 on p2.ProjectGroupsId=p.ProjectGroupsId
                inner join project_groups_by_project p3 on p2.ProjectGroupsId=p3.ProjectGroupsId
                inner join project p1 on p1.ProjectId=p3.ProjectId
                where p2.ProjectId=".$idProject;
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        return $result;
    }
}
