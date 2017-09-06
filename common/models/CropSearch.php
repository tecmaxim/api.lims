<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Crop;

/**
 * CropSearch represents the model behind the search form about `common\models\Crop`.
 */
class CropSearch extends Crop
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Crop_Id', 'IsActive'], 'integer'],
            [['Name', 'ShortName', 'LatinName'], 'safe'],
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
        $query = Crop::find();

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
            'Crop_Id' => $this->Crop_Id,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'ShortName', $this->ShortName])
            ->andFilterWhere(['like', 'LatinName', $this->LatinName]);

        return $dataProvider;
    }
}
