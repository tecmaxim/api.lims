<?php

namespace common\models;
use yii\data\SqlDataProvider;

use Yii;

/**
 * This is the model class for table "markers_by_project".
 *
 * @property integer $MarkersByProjectId
 * @property integer $MarkerId
 * @property integer $ProjectId
 * @property integer $IsActive
 *
 * @property Project $project
 * @property Marker $marker
 */
class MarkersByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public $MarkersCopy;
    public $MarkersByQuerys;
    public $Crop_Id;
    public $errorMarker;
    
    public static function tableName()
    {
        return 'markers_by_project';
    }

    /**
     * @inheritdoc
     */
    
    
    public function rules()
    {
        return [
            [['ProjectId', 'IsActive','Snp_lab_Id'], 'integer'],
            [['MarkerId'], 'safe'],
            [['MarkersByQuerys'], 'safe'],
            ['ProjectId', 'required'],
            ['MarkersCopy', 'pasteError'],
            //['MarkersCopy', 'controlTraits'],
            ['MarkersCopy', 'required', 'when' => function($model) {
                            return $model->MarkerId == "";
                            }, "message" => "Complete at least one item"],   
        ];
    }

    /**
     * @inheritdoc
     */
   
    public function attributeLabels()
    {
        return [
            'MarkersByProjectId' => Yii::t('app', 'Markers By Job'),
            'MarkerId' => Yii::t('app', 'Marker/s'),
            'Snp_lab_Id' => Yii::t('app', 'SnpLab/s'),
            'MarkersCopy' => Yii::t('app', 'Copy Markers (format suport: "m1, m2, mn" , "m1; m2; m3" or copy and paste col by excel )'),
            'ProjectId' => Yii::t('app', 'Job'),
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
    public function getMarker()
    {
        return $this->hasOne(Marker::className(), ['Marker_Id' => 'MarkerId']);
    }
    
    public function getMarkersInArray($array_markers)
    {
        $conection = \Yii::$app->dbGdbms;
      
        $new_array=implode("','", $array_markers);
        $query = str_replace(chr(13),"", $new_array);
      
        $sql = "SELECT Marker_Id FROM marker m WHERE m.Name in ('".$query."')";
        
       
        $result = $conection->createCommand($sql)->queryAll();
        
        return ($result);
        
    }
    
    public function getMarkersNameByProject($idProject) 
    {      
        $markersCopy = "";
        
        $markers = $this->find()
                    ->with('marker')
                    ->where(['ProjectId' => $idProject])
                    ->all();
        if($markers)
        {
            foreach($markers as $m)
            {
                $markersCopy .= $m->marker->Name; 
                $markersCopy .= "\n"; 
            }
            return $markersCopy;
        }else
            return false;
        
    }
    
    public function delteMarkersPrevios($idProject)
    {
        $sql = "DELETE FROM markers_by_project WHERE ProjectId=".$idProject;
        
        \Yii::$app->db->createCommand($sql)->execute();
        
    }
    
    public function pasteError($attribute, $params)
    {
        if($this->errorMarker)
        {
            $this->addError('MarkersCopy', "some entries are not loaded into the database as Markers.");
        }
    }
    
    public function controlTraits($attribute, $params)
    {
        $arrayMarkers = $this->NormalizeInArray($this->$attribute);
        //print_r($arrayMarkers); exit;
        $traitsByProject = TraitsByMaterials::getTraitsByMaterialByProjectByProjectId($this->ProjectId);
        if(count($traitsByProject) > count($arrayMarkers))
        {
            $this->addError('MarkersCopy', "The amount of traits is greater than the number of markers");
        }
        
    }
    
    /*function to normalize string markersCopy and convert it to string
     * 
     */
    private function NormalizeInArray($new_string) {
        
        if (strpos($new_string, "\n") !== false)
            $array_markers = explode("\n", $new_string);
        elseif (strpos($new_string, ", ") !== false)
            $array_markers = explode(", ", $new_string);
        elseif (strpos($new_string, "; ") !== false)
            $array_markers = explode("; ", $new_string);
        else
            $array_markers[] = $new_string;
        
        foreach($array_markers as $k => $v)
        {
            $str = str_replace(PHP_EOL, '', $v);
       
        }
         //print_r($str); exit;
        return $array_markers;
    }
    
    /******************************************************************
    *                                                                 *
    *                   functions with SnpLabs                        *
    *                                                                 *
    *******************************************************************/
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSnpLab()
    {
        return $this->hasOne(SnpLab::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }
    
    public function getSnpLabInArray($array_markers)
    {
        $conection = \Yii::$app->dbGdbms;
      
        $new_array=implode("','", $array_markers);
        $query = str_replace(chr(13),"", $new_array);
      
        $sql = "SELECT Snp_lab_Id FROM snp_lab  s WHERE s.LabName in ('".$query."')";
       
        $result = $conection->createCommand($sql)->queryAll();
        
        return ($result);
        
    }
    
    public function getSnpLabNameByProject($idProject) 
    {      
        $markersCopy = "";

        $markers = $this->find()
                    ->with('snpLab')
                    ->where(['ProjectId' => $idProject])
                    ->all();
        if($markers)
        {
            foreach($markers as $m)
            {
                $markersCopy .= $m->snpLab->LabName; 
                $markersCopy .= "\n"; 
            }
            
            return $markersCopy;
        }else
            return false;
        
    }

 
}
