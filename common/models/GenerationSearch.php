<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Generation;

/**
 * GenerationSearch represents the model behind the search form about `common\models\Generation`.
 */
class GenerationSearch extends Generation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['GenerationId'], 'integer'],
            [['Description'], 'safe'],
            [['IsF1', 'IsActive'], 'boolean'],
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
        $query = Generation::find();

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
            'GenerationId' => $this->GenerationId,
            'IsF1' => $this->IsF1,
            'IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
