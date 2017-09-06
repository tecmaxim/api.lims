<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public $Role;
    public $search;
    public function rules()
    {
        return [
            [['UserId'], 'integer'],
            [['Username', 'AuthKey', 'PasswordHash', 'PasswordResetToken', 'Email', 'CreatedAt', 'UpdatedAt'], 'safe'],
            [['IsActive'], 'boolean'],
            ['Role', 'string'],
            [['search'], 'string'],
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
        
        $query = User::find();
       
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->from($this->tableName(). ' u');
        
        $query->andFilterWhere([
                     'u.IsActive' => 1,
         ]);
        if($this->Role != NULL)
        {
            $query->innerJoinWith(['authAssignments']);
            $query->andFilterWhere(['item_name'=> $this->Role]);
        }
         if($this->search != NULL)
            $query->andFilterWhere(['like', 'Username', $this->search]);
         if($this->Email != NULL)
            $query->andFilterWhere(['like', 'Email', $this->Email]);
       
            //$query->andFilterWhere(['like', 'Email', $this->Email]);
          
          
//          if (!($this->load($params) && $this->validate())) {
 //           $query->joinWith(['assaybrands', 'snp']);  
            $query->andFilterWhere(['like', 'Username', $this->Username])
                ->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
                ->andFilterWhere(['like', 'PasswordHash', $this->PasswordHash])
                ->andFilterWhere(['like', 'PasswordResetToken', $this->PasswordResetToken])
                ->andFilterWhere(['like', 'Email', $this->Email]);
        
            
       /* if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        $query->andFilterWhere([
            'UserId' => $this->UserId,
            'CreatedAt' => $this->CreatedAt,
            'UpdatedAt' => $this->UpdatedAt,
        ]);
      
       
        //print_r($query); exit;
        
       return $dataProvider;
    }
}
