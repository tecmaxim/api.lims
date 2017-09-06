<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "plates_by_project".
 *
 * @property integer $PlatesByProjectId
 * @property integer $ProjectId
 * @property integer $PlateId
 * @property string $Date
 * @property boolean $IsActive
 *
 * @property Plate $plate
 * @property Project $project
 */
class PlatesByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plates_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'PlateId', ], 'required'],
            [['ProjectId', 'PlateId'], 'integer'],
            [['IsActive'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PlatesByProjectId' => Yii::t('app', 'Plates By Job'),
            'ProjectId' => Yii::t('app', 'Job'),
            'PlateId' => Yii::t('app', 'Plate ID'),
            'Date' => Yii::t('app', 'Date'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
    {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    static function getParentsByPlateId($plateId)
    {
        $parents = "";
        $sql = "SELECT mp.Material_Test_Id FROM materials_by_project mp 
                INNER JOIN plates_by_project pbp ON  pbp.ProjectId=mp.ProjectId
                WHERE pbp.PlateId=".$plateId." ORDER BY mp.ProjectId ASC, mp.ParentTypeId ASC ";
        
        $idParents = Yii::$app->db->createCommand($sql)->queryAll();
        
        $sql2 = "SELECT m.Name FROM material_test m where m.Material_Test_Id=";
        foreach($idParents as $parent)
        {
            $parents[] = Yii::$app->dbGdbms->createCommand($sql2 . $parent['Material_Test_Id'])->queryOne();    
        }    
        
        return $parents;
    }
    
    static function getProjectByPlateId($plateId, $byProjectList = null)
    {
        $projects = PlatesByProject::find()->where(["PlateId" => $plateId])->all();
        
        $name = "";
        if($byProjectList != null)
        {
            if(count($projects) == 1)
            {
                return $projects[0];     
            }else{
                return false;
            }
            
        }else{
            foreach($projects as $project)
            {
                $name .= $name == "" ? $project->project->Name : "__".$project->project->Name;
            }
            return $name;
        }
    }
    
    static function getPlatesByProjectId($projectId)
    {
        $plates = PlatesByProject::find()
                         //->select("PlateId")
                         ->select(["CONCAT('TP',LPAD(PlateId, 6, 0)) as Formated", "PlateId"])
                         ->where(["ProjectId" => $projectId, "IsActive" => 1])
                         ->orderBy("PlatesByProjectId", SORT_ASC)
                         ->asArray()
                         ->all();
        return $plates;    
    }
    
    /*
     * Save new relations between old plates and the current project on definiton grid
     * @params array plates, int projectId
     * @return void
     */
    static function savePlatesByProjectId($plates, $projectId, $isUpdate = null)
    {
        if ($isUpdate != null)
        {
            $sql = "DELETE from samples_by_project WHERE ProjectId =".$projectId;
            Yii::$app->db->createCommand($sql)->execute();
            
        }
        if($plates)
        {
            foreach($plates as $key => $plateId)
            {
                $model = new PlatesByProject();
                $model->PlateId = $plateId;
                $model->ProjectId = $projectId;
                $model->IsActive = 1;
                $model->save();
            }
        }
    }
    
    
}
