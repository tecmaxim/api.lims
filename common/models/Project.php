<?php

namespace common\models;

use common\models\Marker;
use Yii;

/**
 * This is the model class for table "project".
 * @property integer $ProjectId
 * @property string $Name
 * @property string $Crop_Id
 * @property integer $UserId
 * @property integer $ProjectTypeId 
 * @property integer $TissueOrigin
 * @property integer $StepProjectId 
 * @property integer $ResearchStationId 
 * @property integer $GenerationId 
 * @property string $ProjectCode
 * @property integer $Priority
 * @property integer $NumberSamples 
 * @property string $DeadLine
 * @property string $FloweringExpectedDate
 * @property string $SowingDate
 * @property string $StepProjectId
 * @property string $Date
 * @property string $Comments
 * @property boolean $IsActive
 * @property boolean $IsSent

 * @property ProjectByProjectType[] $projectByProjectTypes
 */
class Project extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    const PollenDonnor_Receptor = 1;
    const N_Parents = 2;
    
    const FINGERPRINT = 1;
    const BACKCROSS = 2;
    const BACKCROSS_B = 3;
    const BACKCROSS_F = 4;
    const SML = 5;
    const PROGENY_TEST = 6;
    const PRURITY_TEST = 7;
    
    const SEND=1;
    const DOWNLOAD=2;

    //public $ProjectType;
    public $Trait;
    public $MaterialMods;
    public $Samples;
    public $Markers;
    public $vCheck = array();
    public $Parent2_receptor;
    public $Parent1_donnor;
    public $HasParents;
    public $traits_by_parent1;
    public $traits_by_parent2;
    public $UserByProject;
    //to new functionality FP materials selection
    public $FpMaterials;
    public $MaterialsContainer;
    public $MaterialsContainerHidden;
    /* thats variables are to cancel, finisg, hold method*/
    public $CommentToChangeStatus;
    public $Plates;
    public $CancelCauses;
    // To FP projects
    // $TissueOrigin;

    public static function tableName() {
        return 'project';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['Crop_Id', 'UserId', 'GenerationId', 'Priority', 'DeadLine', 'ProjectTypeId'], 'required'],
            [['Crop_Id', 'UserId', 'ProjectTypeId', 'StepProjectId', 'ResearchStationId', 'GenerationId', 'Priority', 'NumberSamples', 'HasParents'], 'integer'],
            [['DeadLine', 'FloweringExpectedDate', 'Parent1_donnor', 'Parent2_receptor','SowingDate', 'Date', 'vCheck', 'Priority', 'Trait','Name', 'IsSent', 'traits_by_parent2','traits_by_parent1','UserByProject','UpdateAt','StepUpdateAt'], 'safe'],
            [['Comments'], 'string'],
            [['Name','TissueOrigin'], 'string', 'max' => 150],
            [['ProjectCode'], 'string', 'max' => 50],
            //[['HasParents'], 'required', 'on' => 'ProjectDefinition'],
            [['ResearchStationId'], 'required', 'on' => 'ProjectDefinition'],
            [['IsActive'], 'boolean'],
            [['CommentToChangeStatus'], 'string'],
            [['CommentToChangeStatus'], 'required', 'on' => 'ChangeStatus'],
            [['SowingDate', 'FloweringExpectedDate', 'DeadLine'], 'orderControl', 'on' => 'ProjectDefinition'],
            /* Flag OnHold*/
            //[['IsOnHold'], 'integer'],
            /* Parameters to Cancel projects*/
            [['Plates', 'MaterialsContainerHidden', 'FpMaterials','MaterialsContainer'], 'safe' ],
            [['CancelCauses'], 'required', 'on' => 'CancelProject'],
            [['MaterialsContainer'], 'validationMaterialsExists'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() 
    {
        return [
            'ProjectId' => Yii::t('app', 'Job'),
            'Name' => Yii::t('app', 'Name'),
            'Crop_Id' => Yii::t('app', 'Crop'),
            'CampaignId' => Yii::t('app', 'Campaign'),
            'UserId' => Yii::t('app', 'Requested By'),
            'Priority' => Yii::t('app', 'Priority'),
            'DeadLine' => Yii::t('app', 'Dead Line'),
            'FloweringExpectedDate' => Yii::t('app', 'Flowering Expected Date'),
            'SowingDate' => Yii::t('app', 'Sowing Date'),
            'Comments' => Yii::t('app', 'Comments'),
            'ProjectTypeId' => Yii::t('app', 'Job Type'),
            'ResearchStationId' => Yii::t('app', 'Research Station'),
            'IsActive' => Yii::t('app', 'Is Active'),
            'NumberSamples' => Yii::t('app', 'Number Of Samples'),
            'MaterialMods' => Yii::t('app', 'Material Mods'),
            'Samples' => Yii::t('app', 'Samples (Ctrl+C and Ctrl+V)'),
            'StepProjectId' => Yii::t('app', 'Step Job'),
            'Parent1_donnor' => Yii::t('app', 'Pollen Donor'),
            'Parent2_receptor' => Yii::t('app', 'Pollen Receptor'),
            'GenerationId' => Yii::t('app', 'Generation'),
            'HasParents' => Yii::t('app', 'Materials Type'),
            'UserByProject' => Yii::t('app', 'Add Others Users'),
            'CommentToChangeStatus' => Yii::t('app', 'Description'),
            //'IsOnHold' => Yii::t('app', 'On Hold'),
            'Plates' => Yii::t('app', 'Select plates to cancel'),
            'FpMaterials' => Yii::t('app', 'Find Materials'),
            'MaterialsContainer' => Yii::t('app','Option to copy and paste materials'),
            'TissueOrigin' => Yii::t('app','Tissue Origin'),
        ];
    }
    
    public function validationMaterialsExists($attr, $param)
    {        
        $arrayMaterial = array_filter(array_unique($this->NormalizeInArrayMaterial($this->MaterialsContainer)));
        // Filter duplicated values in array and convert to string
        $values = implode("','", $arrayMaterial);
        $result = $this->getMaterialNameExists($values);
        
        if(count($result) != count($arrayMaterial))
            $this->addError($attr, 'Some of these materials does not found. Please check this field');
        else
        {
            $this->MaterialsContainerHidden = [];
            foreach($result as$materialId)
            {
                $this->MaterialsContainerHidden[] = $materialId['Material_Test_Id'];
            }
        }
    }
    
    public function orderControl($attr, $param)
    {
        if(!((strtotime($this->SowingDate) < strtotime($this->DeadLine)) && (strtotime($this->SowingDate) <= strtotime($this->FloweringExpectedDate))))
        {
           $this->addError($attr, 'Wrong Order: Sowing Date must be lower than Dead Line and Flowering Date');
           
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign() 
    {
        return $this->hasOne(Campaign::className(), ['CampaingId' => 'CampaignId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrop() {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['UserId' => 'UserId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialsByProjects() {
        return $this->hasMany(MaterialsByProject::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSamplesByProjects() {
        return $this->hasMany(SamplesByProject::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlatesByProjects() {
        return $this->hasMany(PlatesByProject::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */
   public function getAssayByProjects()
   {
       return $this->hasMany(AssayByProject::className(), ['ProjectId' => 'ProjectId']);
   }
   
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getUserByProjects()
   {
       return $this->hasMany(UserByProject::className(), ['ProjectId' => 'ProjectId']);
   }
   
   /**
     * @return \yii\db\ActiveQuery
     */
    public function getDispatchPlates() {
        return $this->hasOne(DispatchPlate::className(), ['ProjectId' => 'ProjectId']);
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getReceptionPlates()
    {
        return $this->hasOne(ReceptionPlate::className(), ['ProjectId' => 'ProjectId']);
    }
   
    public function getMarkersByProjects() {
        return $this->hasMany(MarkersByProject::className(), ['ProjectId' => 'ProjectId']);
    }
    
    public function getStepProject() {
        return $this->hasOne(StepProject::className(), ['StepProjectId' => 'StepProjectId']);
    }

    public function getProjectType() {
        return $this->hasOne(ProjectType::className(), ['ProjectTypeId' => 'ProjectTypeId']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTraitsByProjects() {
        return $this->hasMany(TraitsByProject::className(), ['ProjectId' => 'ProjectId']);
    }
    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getParent2_receptor() {
        return $this->hasOne(MaterialTest::className(), ['Material_Test_Id' => 'Parent2_receptor']);
    }
    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getParent1_donnor() {
        return $this->hasOne(MaterialTest::className(), ['Material_Test_Id' => 'Parent1_donnor']);
    }
    
    public  function getPriority($project = null) {
        
            switch ($this->Priority) {
                case 1:
                    $priority = "<span class='labels priority-h'>High</span>";
                    break;
                case 2:
                    $priority = "<span class='labels priority-m'>Medium</span>";
                    break;
                default:
                    $priority = "<span class='labels priority-l'>Low</span>";
                    break;
            }
        return $priority;
    
    }
    
    public static function getPriorityStatic($project = null) {
        if($project != null)
        {
            switch ($project['Priority']) {
                case 1:
                    $priority['type'] = "High";
                    //$priority['color'] = "labels priority-h";
                    $priority['color'] = "danger";
                    break;
                case 2:
                    $priority['type'] = "Medium";
                    //$priority['color'] = "labels priority-m";
                    $priority['color'] = "warning";
                    break;
                default:
                    $priority['type'] = "Low";
                    $priority['color'] = "success";
                    break;
                
            };
        }
        return $priority;
    }
    
    public function traitsByProjectView() {
        $stringtrait = "";
        foreach ($this->traitsByProjects as $trait) {
            $stringtrait .= "<kbd>" . $trait->traits->Name . "</kbd> ";
        }
        return $stringtrait;
    }

    public function getResearchStation() 
    {
        return $this->hasOne(ResearchStation::className(), ['ResearchStationId' => 'ResearchStationId']);
    }
    
    /**
     * @return \yii\db\ActiveQuery 
     */
    public function getGeneration() {
        return $this->hasOne(Generation::className(), ['GenerationId' => 'GenerationId']);
    }
    
   
   /*
    * return project's parents when that ones are pollen donor and receptor
    * @params integer
    * return mixed
    */
   static function getParentsByProject($projectId)
    {   
        $sql = "SELECT mp.Material_Test_Id  FROM materials_by_project mp "
                . "INNER JOIN `advanta.gdbms`.material_test m ON m.Material_Test_Id = mp.Material_Test_Id "
                ." WHERE mp.ParentTypeId <> 3 and mp.ProjectId=".$projectId
                ." ORDER BY mp.ParentTypeId ASC";

        $result = Yii::$app->db->createCommand($sql)->queryAll();
        
        return $result;
       
   }
   
   public function getParentsInArrayPublic()
   {
       $parents = $this->getParentsInArray($this->ProjectId);
       return $parents;
   }
   
   static function getParentsInArray($projectId)
    {
       if(is_array($projectId))
       {
            $parent= null;
            foreach($projectId as $key => $val)
            {
                $sql = "SELECT Material_Test_Id FROM materials_by_project mp"
                        ." WHERE mp.ParentTypeId <> 3 and mp.ProjectId=".$val
                        ." ORDER BY mp.ParentTypeId ASC";

                if(($result = Yii::$app->db->createCommand($sql)->queryAll()) != null)
                {
                    //print_r($result); exit;
                    foreach($result as $p)
                    {
                        $parent[$val][] = MaterialTest::findOne($p['Material_Test_Id'])->Name;
                    }

                }
            }
            return $parent;
            
       }else
       {
           $parent= null;
           
           $projectId = $projectId;
            $sql = "SELECT Material_Test_Id FROM materials_by_project mp "
                    . "WHERE mp.ParentTypeId <> 3 and mp.ProjectId=".$projectId;
            $sql .= " ORDER BY mp.ParentTypeId ASC";
            
            if(($result = Yii::$app->db->createCommand($sql)->queryAll()) != null)
            {
                foreach($result as $p)
                {
                     $parent[] = MaterialTest::find()->where(['Material_Test_Id' => $p['Material_Test_Id'] ])->one()->Name;
                }

            }
            return $parent;
       }
   }
   
   
   static function getStepProjectById($projectId)
   {
       $project = Project::findOne($projectId);
       
       return $project->stepProject->Name;
   }
   
   /*
    * Delete Materials, traits_bY_materials
    * if there are markers relationated by traits_by_materials delete markers
    */
   public function deleteMaterialsByProject()
   {
       if( TraitsByMarkersByProject::getWithProjectId($this->ProjectId))
       {
           $sql = "DELETE traits_by_markers_by_project FROM traits_by_markers_by_project 
                   INNER JOIN traits_by_materials ON traits_by_materials.TraitsByMaterialId=traits_by_markers_by_project.TraitsByMaterialId 
                   INNER JOIN materials_by_project ON materials_by_project.MaterialsByProject = traits_by_materials.MaterialsByProjectId 
                   WHERE materials_by_project.ProjectId =".$this->ProjectId;
           $result = Yii::$app->db->createCommand($sql)->execute();
       }
       $sql =   "DELETE  traits_by_materials FROM traits_by_materials"
               ." INNER JOIN materials_by_project ON traits_by_materials.MaterialsByProjectId = materials_by_project.MaterialsByProject"
               ." WHERE materials_by_project.ProjectId=".$this->ProjectId.";"
               ." DELETE FROM materials_by_project WHERE ProjectId=".$this->ProjectId;
       $result = Yii::$app->db->createCommand($sql)->execute();
   }
   
   public function deleteTraitsByProject()
   {
       $sql = "DELETE FROM traits_by_project WHERE ProjectId=".$this->ProjectId;
       $res = Yii::$app->db->createCommand($sql)->execute();
   }
   
   public function deleteSamplesByProject()
   {
       $sql = "DELETE FROM samples_by_project WHERE ProjectId=".$this->ProjectId;
       
       $res = Yii::$app->db->createCommand($sql)->execute();
   }
   
    static function setProjectNoSent($projectsId)
    {
        $model = new Project();
        $arrayProjects = is_array($projectsId) == true ? $projectsId: array([0 => $projectsId]);
        foreach($arrayProjects as $key => $id)
        {
            $project = $model->findOne($id);            
            $project->IsSent = 0;
            $project->update();
        }
    }
    
    /*
     * Search if exist any project with generations F1 
     * parameter  array
     * return integer
     */
    static function anyF1($projects)
    {
        foreach ($projects as $key => $id)
        {
            $model = Project::findOne($id);
            if($model->generation->IsF1 == 1)
            {
              return true;
            }
        }
        return false;
    }
    
    public function getParentsAsArray()
    {
        
        if($this->ProjectTypeId != Project::FINGERPRINT)
        {
            $parents = \common\models\MaterialsByProject::find()
                            ->where("ProjectId = ".$this->ProjectId." and ParentTypeId<>3 ")
                            ->orderBy([ 'ParentTypeId' => SORT_ASC])
                            ->all();
            $string = "";

            foreach($parents as $parent)
            {
                $string .= "<kbd>".$parent->materialTest->Name."(".$parent->parentType->Type.") </kbd>&nbsp;";
            }
        }else
        {
            $string = \common\models\MaterialsByProject::find()
                            ->select('GROUP_CONCAT(m2.Name SEPARATOR "</kbd> <kbd>") as Names')
                            ->innerJoin('`advanta.gdbms`.material_test as m2', 'm2.Material_Test_Id=materials_by_project.Material_Test_Id')
                            ->where("ProjectId = ".$this->ProjectId." and materials_by_project.ParentTypeId=3")
                            ->orderBy([ 'm2.Name' => SORT_ASC])
                            ->scalar();
            $string = '<kbd>'.$string;
            
        }
            return $string;
    }
    
    static function updatesStatusByPlateId($plateId, $step)
    {
        $model = Project::findBySql("SELECT project.ProjectId, StepProjectId FROM project
                                    INNER JOIN project_groups_by_project p on p.ProjectId = project.ProjectId
                                    WHERE p.ProjectGroupsId IN (
                                            SELECT p2.ProjectGroupsId FROM plates_by_project p
                                            INNER JOIN project_groups_by_project p2 ON p.ProjectId = p2.ProjectId
                                            WHERE p.PlateId = ".$plateId." group by p2.ProjectGroupsId
                                    ) AND project.StepProjectId < ".$step )->all();
        
        if($model)
        {
            if(count($model) > 1)
            {
                foreach($model as $m)
                { 
                    //print_r($m);exit;
                    $mod = Project::findOne($m);
                    $mod->StepProjectId = $step;
                    if(!$mod->save())
                    {
                        print_r($mod->getErrors());
                        exit;
                    } 
                }
            }  else {
                $model->StepProjectId = $step;
                if(!$model->save())
                {
                    print_r($model->getErrors());
                    exit;
                }
            }
        }  
        return true;
    }   
    
    public function getPlatesDnaExtracted()
    {
        $countPlates = PlatesByProject::find()
                        ->innerJoin("plate", "plates_by_project.PlateId = plate.PlateId")
                        ->where(["plates_by_project.ProjectId" => $this->ProjectId ])
                        ->andWhere("plate.StatusPlateId >=".StatusPlate::ADN_EXTRACTED)
                        ->count();          
                                
        return $countPlates;
    }
    
    public function getPlates()
    {
        $sql = "SELECT ProjectId from project_groups_by_project 
                WHERE ProjectGroupsId = (
                        SELECT ProjectGroupsId from project_groups_by_project pgbp
                        WHERE pgbp.ProjectId = (".$this->ProjectId."))";
        $combined_projects = Yii::$app->db->createCommand($sql)->queryAll();
        $projects_mapeds = \yii\helpers\ArrayHelper::map($combined_projects, 'ProjectId', 'ProjectId');
        
        $plates = PlatesByProject::find()
                        ->innerJoin("plate", "plates_by_project.PlateId = plate.PlateId")
                        ->where(["in","plates_by_project.ProjectId", $projects_mapeds])
                        ->asArray()
                        ->all();
        
        return $plates;
    }
    
    public function getPlatesToDetailView()
    {
        $plates = PlatesByProject::find()
                
                        ->join("inner join","plate", "plates_by_project.PlateId = plate.PlateId")
                        ->join("inner join","status_plate", "status_plate.StatusPlateId = plate.StatusPlateId")
                        ->select(['*','status_plate.Name','plate.PlateId','plate.StatusPlateId'])
                        ->where(["plates_by_project.ProjectId" => $this->ProjectId])
                        ->asArray()
                        ->all();
        
        foreach($plates as $p)
        {
            switch ($p['StatusPlateId'])
            {
                case StatusPlate::CONTROLLED:
                    $string = "<span class='label label-info'>".$p['Name']."</span>";
                    break;
                case StatusPlate::SAVED_METHOD:
                    $string = "<span class='label label-warning'>".$p['Name']."</span>";
                    break;
                case StatusPlate::ADN_EXTRACTED:
                    $string = "<span class='label label-success'>".$p['Name']."</span>";
                    break;
                case StatusPlate::CUANTIFICATION:
                    $string = "<span class='label label-primary'>".$p['Name']."</span>";
                    break;
                case StatusPlate::CANCELED:
                    $string = "<span class='label label-danger'>".$p['Name']."</span>";
                    break;
                default:
                    $string = "<span class='label label-default'>".$p['Name']."</span>";
                    break;
            }
            $rows[] =   [
                            "label" => $p['PlateId'],
                            'format' => 'raw', 
                            "value" =>$string ,
                        ];            
        }
        return $rows;
    }
    
    static function setStepProject($projectId)
    {
        $model = Project::findOne($projectId);
        $model->StepProjectId = StepProject::GENOTYPED;
        if(!$model->save())
        {
            print_r($model->getErrors()); exit;
        }
    }
    
    public function getStatusProject()
    {
        $status = StatusByProject::find()
                        ->where(["ProjectId" => $this->ProjectId /*,"StepProjectId" => StepProject::ON_HOLD*/])
                        ->orderBy(["StatusByProjectId" =>  SORT_DESC])
                        ->select("StepProjectId")
                        ->scalar();
                        //->one();
        return $status;
    }
    
    /*
     * Return list of project like same parents
     * @params parents mixed
     * @return mixed
     */
    static function getProjectsByParents($parents, $projectId)
    {
        $arrayParents = \yii\helpers\ArrayHelper::getColumn($parents, "Material_Test_Id"); //toArray($parents);
                
        $projects = \common\models\Project::find()->innerJoin('materials_by_project', "materials_by_project.ProjectId = project.ProjectId")
                                                   ->where(["in", "Material_Test_Id", $parents])
                                                   ->andWhere(["<>","project.ProjectId", $projectId])
                                                   ->andWhere(["project.IsActive" => 1])
                                                   ->groupBy("project.ProjectId")
                                                   ->all();
         
        return $projects;
    }
    
    public function getQueryStringPlatesByProjectsGruoup($idProject) {
        // In this line i change the sbyp.ProjectId for p1.ProjectId 
        $projects = is_array($idProject) ? implode(',', $idProject) : $idProject;
        $sql = "SELECT sp.SamplesByPlateId, sp.PlateId, p1.ProjectId, sp.SamplesByProjectId, sbyp.SampleName, sp.`Type`, sp.StatusSampleId, p1.Name  FROM samples_by_plate sp 
                left join samples_by_project sbyp ON sbyp.SamplesByProjectId=sp.SamplesByProjectId
                left join plates_by_project pbyp ON pbyp.PlateId=sp.PlateId
                left join project p1 ON p1.ProjectId=pbyp.ProjectId
                WHERE sp.PlateId in (
                                        SELECT PlateId FROM plates_by_project pbp
                                        INNER JOIN project_groups_by_project pgbp ON pgbp.ProjectId=pbp.ProjectId
                                        where pgbp.ProjectId in (". $projects.")                                                     
                                    GROUP BY PlateId)
                GROUP  BY sp.SamplesByPlateId";

        return $sql;
    }

    // Modified Matias
    public function getMaterialNameExists($materials)
    {
        $Query = "SELECT m.Name, m.Material_Test_Id FROM material_test m  WHERE m.IsActive=1 and m.Crop_Id=".$this->Crop_Id." and m.Name IN ('".$materials."') ORDER BY m.Name ASC";
        return  Yii::$app->dbGdbms->createCommand($Query)->queryAll();
    }

    private function NormalizeInArrayMaterial($array, $flagProjectDefinition = null) {
        if ($flagProjectDefinition != null)
            $array = preg_replace('/\s+/', '', $array);
        

        if (strpos($array, "\n") !== false)
            $array_markers = explode("\n", $array);
        elseif (strpos($array, ",") !== false)
            $array_markers = explode(",", $array);
        elseif (strpos($array, "; ") !== false)
            $array_markers = explode(";", $array);
        else
            $array_markers[] = $array;
        
        return preg_replace('/\s+/', '', $array_markers);
    }
    
    public function getParentByType($type = null)
    {
        $parent = MaterialsByProject::find()
                                //->innerJoin('`advanta.gdbms`.material_test', 'material_test.Material_Test_Id = materials_by_project.Material_Test_Id')
                                ->where(['ProjectId' => $this->ProjectId, 'ParentTypeId' => $type, 'materials_by_project.IsActive' => 1])
                                //->asArray()
                                ->one();
        
        if($parent)
            return $parent->materialTest->Name;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsLinkage() 
    {
        return $this->hasOne(ProjectsLinkage::className(), ['ProjectId' => 'ProjectId']);
    }
    
    public function getTraitsByMaterials()
    {
        $array_traits = array();
        if($this->ProjectTypeId != Project::FINGERPRINT)
        {
            foreach($this->materialsByProjects as $materials)
            {
                if(count($materials->traitsByMaterialsByProjects) > 0)
                {
                    foreach($materials->traitsByMaterialsByProjects as $trait)
                    {
                        $array_traits[] = $trait->traits->Name;
                    }
                }
            }
        }
        return $array_traits;
    }
    
    public function getTraitsInString()
    {
        $string_trait = "";
        if(count($this->getTraitsByMaterials() > 0))
        {
            foreach($this->getTraitsByMaterials() as $key => $traitName)
            {
                $string_trait .= "<kbd> $traitName </kbd>&nbsp;";
            }
        }
        
        return $string_trait;
    }
}
