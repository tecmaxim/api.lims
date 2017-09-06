<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "traits_by_materials".
 *
 * @property integer $TraitsByMaterialId
 * @property integer $MaterialsByProjectId
 * @property integer $TraitsId
 * @property boolean $IsActive
 *
 * @property MaterialsByProject $materialsByProject
 * @property Traits $traits
 */
class TraitsByMaterials extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traits_by_materials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MaterialsByProjectId', 'TraitsId'], 'required'],
            [['MaterialsByProjectId', 'TraitsId'], 'integer'],
            [['IsActive'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TraitsByMaterialId' => 'Traits By Material ID',
            'MaterialsByProjectId' => 'Materials By Project ID',
            'TraitsId' => 'Traits ID',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialsByProject()
    {
        return $this->hasOne(MaterialsByProject::className(), ['MaterialsByProject' => 'MaterialsByProjectId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTraits()
    {
        return $this->hasOne(Traits::className(), ['TraitsId' => 'TraitsId']);
    }
    
    static function getTraitsByParent($projectId, $type)
    {
        $sql = "SELECT t.TraitsId, t.Name FROM traits t
        inner join traits_by_materials tm on tm.TraitsId=t.TraitsId
        inner join materials_by_project mp on mp.MaterialsByProject=tm.MaterialsByProjectId
        where t.IsActive=1 and mp.ProjectId=".$projectId." and mp.ParentTypeId=".$type;
        
        $traits = Yii::$app->db->createCommand($sql)->queryAll();
        
        if ($traits) 
        {
            foreach ($traits as $t) {
                $array_t[] = $t['TraitsId'];
            }
            return $array_t;
        }else
            return [];
    }
    
    static function getTraitsByMaterialByProject($materialByPtojectId)
    {
        $traitsIds = TraitsByMaterials::find()
                            ->with('traits')
                            ->where(["MaterialsByProjectId" => $materialByPtojectId , "IsActive" => 1])
                            ->asArray()
                            ->all();
        
        return  $traitsIds;
                
    }
    
    static function getTraitsByMaterialByProjectByProjectId($projectId)
    {
        $traits = TraitsByMaterials::find()
                            //->with('traits')
                            ->innerJoin('materials_by_project', 'materials_by_project.MaterialsByProject = traits_by_materials.MaterialsByProjectId')
                            ->where(["materials_by_project.ProjectId" => $projectId ])
                            ->asArray()
                            ->all();
        
        return  $traits;
                
    }
    
    static function getTraitsByMaterialByProjectAsString($materialByPtojectId)
    {
        $traitsIds = TraitsByMaterials::getTraitsByMaterialByProject($materialByPtojectId);
        
        $string = "";
        foreach($traitsIds as $trait)
        {
            $string .= $string == "" ? $trait['traits']['Name'] : " - ".$trait['traits']['Name'];
        }
        
        return  $string;
                
    }
}
