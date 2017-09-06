<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "materials_by_project".
 *
 * @property integer $MaterialsByProject
 * @property integer $Material_Test_Id
 * @property integer $ProjectId
 *
 * @property Project $project
 * * @property Project $IsActive
 * @property MaterialTest $materialTest
 */
class MaterialsByProject extends \yii\db\ActiveRecord
{
    
    const POLLEN_DONOR = 1;
    const POLLEN_RECEPTOR = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materials_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Material_Test_Id', 'ProjectId', 'ParentTypeId', 'IsActive'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'MaterialsByProject' => Yii::t('app', 'Materials By Job'),
            'Material_Test_Id' => Yii::t('app', 'Material  Test  ID'),
            'ProjectId' => Yii::t('app', 'Job'),
            'ParentTypeId' => Yii::t('app', 'Parent Type ID'), 
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialTest()
    {
        return $this->hasOne(MaterialTest::className(), ['Material_Test_Id' => 'Material_Test_Id']);
    }
    
    public function getParentType() 
   { 
       return $this->hasOne(ParentType::className(), ['ParentTypeId' => 'ParentTypeId']); 
   } 
    
   static function getMaterialsNameByIdProjectGroups($idProject)
   {
       $projects = is_array($idProject) ? implode(',',$idProject) : $idProject;
       $sql ="SELECT mp.ProjectId, m.Name, mp.ParentTypeId FROM materials_by_project mp 
	INNER JOIN `advanta.gdbms`.material_test m ON m.Material_Test_Id = mp.Material_Test_Id
	WHERE mp.ProjectId in (
		(SELECT ProjectId FROM project_groups_by_project pbp2
		 WHERE pbp2.ProjectGroupsId = (		
				SELECT pbp.ProjectGroupsId FROM project_groups_by_project pbp
				WHERE pbp.ProjectId in (".$projects.")
                                GROUP BY pbp.ProjectGroupsId )
                ORDER BY pbp2.ProjectId ASC
		)
	) 
	ORDER BY mp.ProjectId ASC, mp.ParentTypeId ASC";
       
       $idMaterials = Yii::$app->db->createCommand($sql)->queryAll();
       $parents = [];
       foreach($idMaterials as $materials)
       {
            $parents[$materials['ProjectId']][] = ["Name"=>$materials['Name'], "type" => $materials['ParentTypeId']];    
       }
       
       return $parents;
   }
   
   public function getTraitsByMaterialsByProjects()
   {
       return $this->hasMany(TraitsByMaterials::className(), ['MaterialsByProjectId' => 'MaterialsByProject']);
   }
        
}
