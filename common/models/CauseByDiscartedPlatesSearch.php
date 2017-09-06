<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CauseByDiscartedPlates;

/**
 * CauseByDiscartedPlatesSearch represents the model behind the search form about `common\models\CauseByDiscartedPlates`.
 */
class CauseByDiscartedPlatesSearch extends CauseByDiscartedPlates
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CauseByDiscartedPlatesId'], 'integer'],
            [['Name', 'Description'], 'safe'],
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
        $query = CauseByDiscartedPlates::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('IsAvtive = 1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'CauseByDiscartedPlatesId' => $this->CauseByDiscartedPlatesId,
            'IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
