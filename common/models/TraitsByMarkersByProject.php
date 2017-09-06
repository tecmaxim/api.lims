<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "traits_by_markers_by_project".
 *
 * @property integer $TraitsByMarkersByProjectId
 * @property integer $MarkersByProjectId
 * @property integer $TraitsByMaterialId
 *
 * @property TraitsByMaterial $traitsByMaterial
 * @property MarkersByProject $markersByProject
 */
class TraitsByMarkersByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traits_by_markers_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MarkersByProjectId', 'TraitsByMaterialId'], 'required'],
            [['MarkersByProjectId', 'TraitsByMaterialId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TraitsByMarkersByProjectId' => 'Traits By Markers By Project ID',
            'MarkersByProjectId' => 'Markers By Project ID',
            'TraitsByMaterialId' => 'Traits By Material ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTraitsByMaterials()
    {
        return $this->hasOne(TraitsByMaterials::className(), ['TraitsByMaterialId' => 'TraitsByMaterialId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarkersByProject()
    {
        return $this->hasOne(MarkersByProject::className(), ['MarkersByProjectId' => 'MarkersByProjectId']);
    }
    
    public static function getTraitsBybProjectByKendo($idProject)
    {
            $traits = \common\models\TraitsByMaterials::find()
                   ->join('inner join','materials_by_project', 'MaterialsByProject = MaterialsByProjectId')
                   ->where(["materials_by_project.ProjectId" => $idProject])
                   //->asArray()
                   ->all();
           if($traits)
           {
                $i = 0;
                foreach($traits as $t)
                {
                    $data[$i]['Name'] = $t->traits->Name;
                    $data[$i]['TraitsId'] = $t->TraitsId;
                    $i++;
                }
           }
           
           return $data;
    }
    
    public static function deleteTraitsByMarkersByProject($idProject)
    {
        $sql = "DELETE FROM traits_by_markers_by_project WHERE MarkersByProjectId in 
                (
                    SELECT MarkersByProjectId FROM markers_by_project where ProjectId=".$idProject."
                )";
        Yii::$app->db->createCommand($sql)->execute();
    }
    
    static function getTraitsBybProjectId($idProject)
    {
            $data = "";
            $traits = \common\models\TraitsByMaterials::find()
                   ->join('inner join','materials_by_project', 'materials_by_project.MaterialsByProject = traits_by_materials.MaterialsByProjectId')
                   ->where(["materials_by_project.ProjectId" => $idProject])
                   //->asArray()
                   ->all();
           if($traits)
           {
                $i = 0;
                foreach($traits as $t)
                {
                    $data[$i]['Name'] = $t->traits->Name;
                    $data[$i]['TraitsId'] = $t->TraitsId;
                    $i++;
                }
           }
           
           return $data;
    }
    
    static function getTraitsByMarkersByProjectAsArray($markerByProjectId)
    {
        $traits = TraitsByMarkersByProject::find()
                                ->with('traitsByMaterials')
                                ->where(["MarkersByProjectId" => $markerByProjectId])
                                //->asArray()
                                ->all();
        $string = "";
        foreach($traits as $trait)
        {
            
            $string .= $string == "" ? $trait['traitsByMaterials']['traits']['Name'] : " - ".$trait['traitsByMaterials']['traits']['Name'];
        }
        
        return $string;
        
    }
    
    static function getWithProjectId($projectId)
    {
        $traitsByMarkers = TraitsByMarkersByProject::find()
                                ->innerJoin('traits_by_materials', 'traits_by_materials.TraitsByMaterialId=traits_by_markers_by_project.TraitsByMaterialId')
                                ->innerJoin('materials_by_project', 'materials_by_project.MaterialsByProject = traits_by_materials.MaterialsByProjectId')
                                ->where(['materials_by_project.ProjectId'=>$projectId])
                                ->all();
        
        return $traitsByMarkers;
    }
}
