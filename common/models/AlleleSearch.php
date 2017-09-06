<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Allele;

/**
 * AlleleSearch represents the model behind the search form about `common\models\Allele`.
 */
class AlleleSearch extends Allele
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Allele_Id', 'IsActive'], 'integer'],
            [['LongDescription'], 'safe'],
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
        $query = Allele::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            'IsActive' => $this->IsActive
        ]);
                
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Allele_Id' => $this->Allele_Id,
        ]);

        $query->andFilterWhere(['like', 'LongDescription', $this->LongDescription]);

        return $dataProvider;
    }
}
