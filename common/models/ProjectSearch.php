<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;

/**
 * ProjectSearch represents the model behind the search form about `common\models\Project`.
 */
class ProjectSearch extends Project
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectId', 'Crop_Id', 'UserId', 'IsActive','NumberSamples'], 'integer'],
            [['Name', 'ProjectCode', 'Priority', 'DeadLine', 'FloweringExpectedDate', 'SowingDate', 'Comments', 'ResearchStationId'], 'safe'],
            [['Parent1_donnor', 'Parent2_receptor','GenerationId','StepProjectId'], 'safe'],
            //[['Date'], 'required'],
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
    public function search($params = null, $select_prject = null)
    {
        if($select_prject != null)
        {
            $query = Project::find();
            $attributes = ['attributes' => [
                                'ProjectCode' => [
                                    'asc' =>[ 'ProjectCode' => SORT_ASC ],
                                    'desc' =>[ 'ProjectCode' => SORT_DESC ],
                                    'default' => SORT_DESC,
                                ],
                                'Pollen Donnor' => [ 
                                    'asc' =>[ 'm.Material_Test_Id' => SORT_ASC ],
                                    'desc' =>[ 'm.Material_Test_Id' => SORT_DESC ],
                                    //'default' => SORT_DESC,
                                ],
                                'Pollen Receptor' => [ 
                                    'asc' =>[ 'm2.Material_Test_Id' => SORT_ASC ],
                                    'desc' =>[ 'm2.Material_Test_Id' => SORT_DESC ],
                                    //'default' => SORT_DESC,
                                ],
                                'Generation' => [ 
                                    'asc' =>[ 'generation.Description' => SORT_ASC ],
                                    'desc' =>[ 'generation.Description' => SORT_DESC ],
                                    //'default' => SORT_DESC,
                                ],
                                'Step Project' => [ 
                                    'asc' =>[ 'step_project.Name' => SORT_ASC ],
                                    'desc' =>[ 'step_project.Name' => SORT_DESC ],
                                    //'default' => SORT_DESC,
                                ],
                            ],
                        ];
            $relations = ['generation', 'stepProject'];
        }
        else
        {
            $query = Project::find()->asArray();
            $attributes = ['attributes' => ['ProjectId' => [
                                                'asc' =>[ 'ProjectId' => SORT_ASC ],
                                                'desc' =>[ 'ProjectId' => SORT_DESC ],
                                                'default' => SORT_DESC,
                                            ]
                                    ]
                            ];
            $relations = ['user', 'crop'];
        }
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $select_prject == null ? array('pageSize' => false): array('pageSize' => 20),
            'sort' =>  $attributes ,
        ]);
        
        if($select_prject != null)
            $query->joinWith($relations);
        else
            $query->with($relations);
        
        $this->load($params);
        
        if (!$this->validate()) {
            $query->andFilterWhere([
                'UserId' => $this->UserId,
                'project.IsActive' => 1,
                
                ]);
            $query->orderBy(["ProjectId" => SORT_DESC]);
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        //Only for selectProject method.
        if($select_prject != null)
        {
            $query->leftJoin('materials_by_project m', '`m`.ProjectId = project.ProjectId' );
            $query->leftJoin('materials_by_project m2', 'm2.ProjectId = project.ProjectId'); 
            $query->innerJoin('`advanta.gdbms`.material_test m3', 'm3.Material_Test_Id = m.Material_Test_Id');
            $query->innerJoin('`advanta.gdbms`.material_test as m4', 'm4.Material_Test_Id = m2.Material_Test_Id');
            $query->andFilterWhere(['m.Material_Test_Id' => $this->Parent1_donnor, 
                                    'm2.Material_Test_Id' => $this->Parent2_receptor,
                                    'm.ParentTypeId' => 1,
                                    'm2.ParentTypeId' => 2]);
        }
        
        $query->andFilterWhere([
            'ProjectId' => $this->ProjectId,
            'Crop_Id' => $this->Crop_Id,
            'Date' => $this->Date,
            'UserId' => $this->UserId,
            'ProjectTypeId' => $this->ProjectTypeId, 
            'project.StepProjectId' => $this->StepProjectId, 
            'ResearchStationId' => $this->ResearchStationId, 
            'project.GenerationId' => $this->GenerationId, 
            'Priority' => $this->Priority, 
            'NumberSamples' => $this->NumberSamples, 
            'DeadLine' => $this->DeadLine,
            'FloweringExpectedDate' => $this->FloweringExpectedDate,
            'SowingDate' => $this->SowingDate,
            'NumberSamples' => $this->NumberSamples,
            
            'project.IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'ProjectCode', $this->ProjectCode])
            ->andFilterWhere(['like', 'Priority', $this->Priority])
            ->andFilterWhere(['like', 'Comments', $this->Comments])
            ->andFilterWhere(['like', 'ResearchStationId', $this->ResearchStationId]);
        
        //$query->orderBy(["ProjectId" => SORT_DESC]);
        //$query->with(['crop']);
        //$query->with(['user']);
        
        //$query->with(['campaign']);

        return $dataProvider;
    }
    
}
