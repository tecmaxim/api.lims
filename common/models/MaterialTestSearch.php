<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MaterialTest;


/**
 * MaterialTestSearch represents the model behind the search form about `common\models\MaterialTest`.
 */
class MaterialTestSearch extends MaterialTest
{
    /**
     * @inheritdoc
     */
    
    public function rules()
    {
       return [
            [['Material_Test_Id', 'Crop_Id', 'IsActive'], 'integer'],
           [['Name', 'Crop_Id', 'CodeType', 'PreviousCode', 'Owner', 'Generation', 'HeteroticGroup', 'Pedigree', 'Type'], 'safe'],
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
        $query = MaterialTest::find();
        //print_r($params); exit;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $query->andFilterWhere([
            'material_test.IsActive' => 1
        ]);
                
        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['crop']);
            $query->andWhere(['crop.IsActive' =>1]);
            $dataProvider->setPagination(false);
            return $dataProvider;
        }
        //print_r($this); exit;
        $query->andFilterWhere([
            'Material_Test_Id' => $this->Material_Test_Id,
        ]);

       $query->andFilterWhere(['like', 'Name', $this->Name])
           ->andFilterWhere(['like', 'CodeType', $this->CodeType])
           ->andFilterWhere(['like', 'PreviousCode', $this->PreviousCode])
           ->andFilterWhere(['like', 'Owner', $this->Owner])
           ->andFilterWhere(['like', 'Generation', $this->Generation])
           ->andFilterWhere(['like', 'HeteroticGroup', $this->HeteroticGroup])
           ->andFilterWhere(['like', 'Pedigree', $this->Pedigree])
           ->andFilterWhere(['like', 'Type', $this->Type]);
               
        $query->orderBy(["Crop_Id" => SORT_ASC]);

        return $dataProvider;
    }
    
    public function searchByIds($vChecks)
    {
        $query = MaterialTest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       
        $query->andWhere(['in', ["material_test.Material_Test_Id"], $vChecks]);
       
        return $dataProvider;
    }
}
