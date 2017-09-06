<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResearchStation;

/**
 * ResearchStationSearch represents the model behind the search form about `common\models\ResearchStation`.
 */
class ResearchStationSearch extends ResearchStation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ResearchStationId', 'CountryId', 'CityId'], 'integer'],
            [['Short'], 'safe'],
            [['IsActive'], 'boolean'],
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
        $query = ResearchStation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->andFilterWhere([
            'IsActive' => 1,
        ]);
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ResearchStationId' => $this->ResearchStationId,
            'CountryId' => $this->CountryId,
            'CityId' => $this->CityId,
            'IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Short', $this->Short]);

        return $dataProvider;
    }
}
