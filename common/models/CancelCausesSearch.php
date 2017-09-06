<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CancelCauses;

/**
 * CancelCausesSearch represents the model behind the search form about `common\models\CancelCauses`.
 */
class CancelCausesSearch extends CancelCauses
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CancelCausesId'], 'integer'],
            [['Description', 'Name'], 'safe'],
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
        $query = CancelCauses::find();

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
            'CancelCausesId' => $this->CancelCausesId,
            'IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'Name', $this->Name]);

        return $dataProvider;
    }
}
