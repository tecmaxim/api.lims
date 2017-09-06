<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "snp_lab_by_project".
 *
 * @property integer $SnpLabByProjectId
 * @property integer $SnpLabId
 * @property integer $ProjectId
 * @property boolean $IsActive
 *
 * @property Project $project
 */
class SnpLabByProject extends \yii\db\ActiveRecord
{
    
    public $MarkersCopy;
    public $MarkersByQuerys;
    public $Crop_Id;
    public $errorMarker;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'snp_lab_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['SnpLabId', 'ProjectId'], 'required'],
            [['SnpLabId', 'ProjectId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['MarkersByQuerys'], 'safe'],
            ['MarkersCopy', 'pasteError'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'SnpLabByProjectId' => 'Snp Lab By Project ID',
            'SnpLabId' => 'Snp Lab ID',
            'ProjectId' => 'Project ID',
            'IsActive' => 'Is Active',
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
    public function getSnpLab()
    {
        return $this->hasOne(SnpLab::className(), ['Snp_Lab_Id' => 'Snp_Lab_Id']);
    }
    
    public function getMarkersInArray($array_markers)
    {
        $conection = \Yii::$app->dbGdbms;
      
        $new_array=implode("','", $array_markers);
        $query = str_replace(chr(13),"", $new_array);
      
        $sql = "SELECT Snp_Lab_Id FROM snp_lab s WHERE s.LabName in ('".$query."')";
        
       
        $result = $conection->createCommand($sql)->queryAll();
        
        return ($result);
        
    }
    
    public function pasteError($attribute, $params)
    {
        if($this->errorMarker)
        {
            $this->addError('MarkersCopy', "some entries are not loaded into the database as Markers.");
        }
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
                $markersCopy .= $m->marker->Name; 
                $markersCopy .= "\n"; 
            }
            return $markersCopy;
        }else
            return false;
        
    }
    
    public function delteSnpLabPrevios($idProject)
    {
        $sql = "DELETE FROM snp_lab_by_project WHERE ProjectId=".$idProject;
        
        \Yii::$app->db->createCommand($sql)->execute();
        
    }
    
}
