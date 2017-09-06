<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Protocol;

/**
 * ProtocolSearch represents the model behind the search form about `common\models\Protocol`.
 */
class ProtocolSearch extends Protocol
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProtocolId', 'ProjectId', 'ProtocolResultId'], 'integer'],
            [['Code', 'Comments'], 'safe'],
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
        $query = Protocol::find();

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
            'ProtocolId' => $this->ProtocolId,
            'ProjectId' => $this->ProjectId,
            'ProtocolResultId' => $this->ProtocolResultId,
            'IsActive' => $this->IsActive,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Comments', $this->Comments]);

        return $dataProvider;
    }
}
