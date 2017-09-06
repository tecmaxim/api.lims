<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogProjectClosed;

/**
 * LogProjectClosedSearch represents the model behind the search form about `common\models\LogProjectClosed`.
 */
class LogProjectClosedSearch extends LogProjectClosed
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LogProjectClosedId', 'ProjectId'], 'integer'],
            [['Description'], 'safe'],
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
        $query = LogProjectClosed::find();

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
            'LogProjectClosedId' => $this->LogProjectClosedId,
            'ProjectId' => $this->ProjectId,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
