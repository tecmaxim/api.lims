<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Map;

/**
 * MapSearch represents the model behind the search form about `common\models\Map`.
 */
class MapSearch extends Map
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Map_Id', 'Crop_Id', 'IsActive'], 'integer'],
            [['Name', 'Type', 'Date'], 'safe'],
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
        $query = Map::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Map_Id' => $this->Map_Id,
            'Crop_Id' => $this->Crop_Id,
            'Date' => $this->Date,
            'IsActive' => $this->IsActive,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Type', $this->Type]);

        return $dataProvider;
    }
}
