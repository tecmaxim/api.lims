<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Marker;
use yii\data\SqlDataProvider;  

/**
 * MarkerSearch represents the model behind the search form about `common\models\Marker`.
 */
class MarkerSearch extends Marker
{
    /**
     * @inheritdoc
     */
    public $search;
    public $Pagination;
    public $CropName;
    
    public function rules()
    {
        return [
            [['Marker_Id', 'IsActive'], 'integer'],
            [['Name', 'ShortSequence', 'LongSequence', 'Crop_Id'], 'safe'],
           // [['PublicCm', 'AdvCm'], 'number'],
            [['search'], 'string'],
            [['CropName','Marker_Type_Id'], 'safe'],
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
        if (($this->load($params) && $this->validate()))
        {
           //ini_set("memory_limit", - 1);
           $sql = "";
            if($this->Marker_Type_Id == 2)
                $queryFilterBySearch = " from marker m
                                         left join microsatellite_data ms on ms.Marker_Id=m.Marker_Id
                                         where m.IsActive=1 ";
            else
                $queryFilterBySearch = " from marker m
                                         where m.IsActive=1 ";                                     
                    
                    
                    if ($this->search !=null  )    
                        $queryFilterBySearch .= " and m.Name like '%". $this->search."%'";
                    elseif ($this->Name !=null  )    
                         $queryFilterBySearch .= " and m.Name like '%". $this->Name."'";
                    
                  
                    if($this->Crop_Id != null)
                         $queryFilterBySearch .= "  and m.Crop_Id = ".$this->Crop_Id;
                    if($this->Marker_Type_Id != 3)
                         $queryFilterBySearch .= "  and m.Marker_Type_Id = ".$this->Marker_Type_Id;
                    
            //$sql =   "SELECT COUNT(m.Name) ".$queryFilterBySearch;
            //print_r($sql); exit; 
            
               /**********      SQLDAtaProvider     **************/
            $sql =   "select * ".$queryFilterBySearch." ORDER BY Name";
            $count = Yii::$app->dbGdbms->createCommand($sql)->execute();
           
            //$sql .=   " ORDER BY s.AdvLinkageGroup DESC, s.AdvCm DESC";
            
            $dataprov = new SqlDataProvider([
               'sql' => $sql,
               'db' => Yii::$app->dbGdbms,
               'totalCount' => intval($count),
               'sort' => [
                        'attributes' => [
                                        'Name',

                                        ],
                        ],
                'pagination' => [
                        'pageSize' => $this->Pagination == "" ? false : $this->Pagination,
                 ]
               ]);
                
       }
      else 
       {
            $sql =   "select COUNT(m.Name) from marker m
                            where m.IsActive=1 ";
            
            $sql .= " and Crop_Id=".$this->Crop;

            $count = Yii::$app->dbGdbms->createCommand($sql)->queryScalar();
            
            $dataprov = new SqlDataProvider([
           'sql' => "select *
                    from marker m
                    where m.IsActive=1 ORDER BY Name",
           'totalCount' => intval($count),
           'db' => Yii::$app->dbGdbms,
           'sort' => [
                    'attributes' => [
                                    'Name',
                                    'PIC',
                                    'CropName' => [
                                        'asc' => ['crop.Name' => SORT_ASC],
                                        'desc' => ['crop.Name' => SORT_DESC],
                                        'label' => 'Cultivo',
                                        'default' => SORT_ASC
                                            ],
                                 
                                    ],
                     ],
            'pagination' => [
                       'pageSize' => $this->Pagination,
                ]
           ]);
       }
      // print_r($dataprov); exit;
      return $dataprov;

    }
    
    public function searchByIds($vChecks)
    {
        $query = Marker::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       
        $query->andWhere(['in', ["marker.Marker_Id"], $vChecks]);
       
        return $dataProvider;
    }
}
