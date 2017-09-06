<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Plate;

/**
 * PlateSearch represents the model behind the search form about `common\models\Plate`.
 */
class PlateSearch extends Plate
{
    public $ProjectName;
    public $ProjectId;
    public $Parent;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['StatusPlateId', 'IsActive','ProjectId'], 'integer'],
            [['PlateId'], 'validateFormat'],
            [['Date', 'ProjectName', 'Parent'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Plate::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 100),
        ]);
        
        $query->andFilterWhere([
            'plate.IsActive' => 1,
        ]);
        
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->joinWith("platesByProjects");
        $query->innerJoin("project", "plates_by_project.ProjectId = project.ProjectId");
            
        if($this->ProjectName != null or $this->ProjectId != null)
        {   
            //$query->join("INNER", "plates_by_project", ["PlateId"=>$this->PlateId]);
            //$query->join("INNER", "project", ["ProjectId"=>$this->ProjectId]);
            $query->where ("project.Name like '%".$this->ProjectName."%'");
            if($this->ProjectId != null)
            {
                $query->where ("project.ProjectId = ".$this->ProjectId);
            }
        }
        
        if($this->Parent != null)
        {
           $query->innerJoin("materials_by_project", "materials_by_project.ProjectId = plates_by_project.ProjectId");
           $query->andWhere("materials_by_project.MaterialsByProject = ".$this->Parent);
        }

        $query->andFilterWhere([
            'plate.PlateId' => $this->PlateId,
            'Date' => $this->Date,
            'StatusPlateId' => $this->StatusPlateId,
        ]);
        if($this->Parent != null)
            $query->groupBy('plate.PlateId');
            
        $query->orderBy('PlateId DESC');
        
        return $dataProvider;
    }
    
    /*
     * 
     */
    public function validateFormat($attr, $param)
    {
        if(strlen($this->PlateId) != 8 && (strpos( $this->PlateId, 'TP' ) === false || strpos( $this->PlateId, 'DP' === false ) || strpos( $this->PlateId, 'DD' ) === false ))
        {
            
           $this->addError($attr, 'Wrong Format: Plate Code must be "TP000012" / "DP000012" / "DD000012"'); 
        }else
        {
            $this->PlateId = (int)substr($this->PlateId, 2);
        }
    }
    
    public function searchByQuery($params)
    {
        $query = Plate::find();
         
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 100),
        ]);
        
        $this->load($params);
        
        $query->innerJoin('plates_by_project', 'plates_by_project.PlateId = plate.PlateId' );
        
        $subQuery1 = ProjectGroupsByProject::find()->select('ProjectGroupsId')->where(['ProjectId' => $this->ProjectId]);
        
        $subQuery2 = ProjectGroupsByProject::find()->select('ProjectId')->where(['ProjectGroupsId' => $subQuery1]);
        
        //$query->where(['project_groups_by_project.ProjectGroupsId' => $subQuery]);
        
        $query->where(['IN', 'plates_by_project.ProjectId', $subQuery2]);
        
        return $dataProvider;
       
    }
}