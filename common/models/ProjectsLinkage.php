<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "projects_linkage".
 *
 * @property integer $ProjectsLinkageId
 * @property integer $ProjectId
 * @property integer $LinkedTo
 *
 * @property Project $project
 * @property Project $linkedTo
 */
class ProjectsLinkage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_linkage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'LinkedTo'], 'required'],
            [['ProjectId', 'LinkedTo'], 'integer'],
            [['ProjectId'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectsLinkageId' => 'Projects Linkage ID',
            'ProjectId' => 'Project ID',
            'LinkedTo' => 'Linked To',
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
    public function getLinkedTo()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'LinkedTo']);
    }
    
    public function getLinkageTree()
    {
        $sql =  "SELECT T2.LinkedTo
                FROM (
                    SELECT
                        @r AS _id,
                        (SELECT @r := LinkedTo FROM projects_linkage WHERE ProjectId = _id) AS parent_id,
                        @l := @l + 1 AS lvl
                    FROM
                        (SELECT @r := 48, @l := 0) vars,
                        projects_linkage m
                    WHERE @r <> 0) T1
                JOIN projects_linkage T2
                ON T1._id = T2.ProjectId
                ORDER BY T1.lvl DESC;";
        
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        
        return $result;
    }
}
