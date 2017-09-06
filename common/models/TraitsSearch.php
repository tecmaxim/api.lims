<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Traits;

/**
 * TraitsSearch represents the model behind the search form about `common\models\Traits`.
 */
class TraitsSearch extends Traits
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TraitsId', 'Crop_Id'], 'integer'],
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
        $query = Traits::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            $query->andFilterWhere([
                'IsActive' => 1,
            ]);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'TraitsId' => $this->TraitsId,
            'Crop_Id' => $this->Crop_Id,
            'IsActive' => 1,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        $query->orderBy(["Name" => SORT_ASC ]);
        
        return $dataProvider;
    }
}
