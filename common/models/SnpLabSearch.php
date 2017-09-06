<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SnpLab;
use yii\data\SqlDataProvider;

/**
 * SnpLabSearch represents the model behind the search form about `common\models\SnpLab`.
 */
class SnpLabSearch extends SnpLab
{
    public $assayBrandName;
    public $LinkageGroupFrom;
    public $LinkageGroupTo;
    public $PositionFrom;
    public $PositionTo;
    public $search;
    public $batch;
    public $Crop;
    public $Pagination;
    public $Map;
    public $MapTypeId;
    public $MarkerType;
    public $Name;
    public $hiddenField;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Snp_lab_Id', 'Marker_Id', 'IsActive'], 'integer'],
            [['LabName', 'PurchaseSequence', 'AlleleFam', 'AlleleVicHex', 'ValidatedStatus', 'Quality', 'Box', 'PositionInBox', 'Observation', 'assayBrandName', 'LinkageGroupFrom','LinkageGroupTo', 'PositionFrom','PositionTo', 'Crop', 'MapTypeId', 'Map'], 'safe'],
            [['PIC'], 'number'],
            //[['Map'], 'required','on' => 'query1'],
            [['MapTypeId','Map'], 'required', 'on' => 'query1'],
            
            [['Crop'], 'required'],
            [['search'], 'string'],
            [['batch'], 'string', 'max' => 5000],
            [['Pagination'], 'safe'],
            
            [['MarkerType'], 'safe'],
            [['Name'], 'safe'],
            [['hiddenField'], 'safe'],
            
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'PositionFrom' => Yii::t('app', 'cM From'),
            'PositionTo' => Yii::t('app', 'cM To'),
            'MapTypeId' => Yii::t('app', 'Map Category'),
            'Map' => Yii::t('app', 'Mapped Population'),
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
    public function search($params, $orderBy = null)
    {
        $sql = "";
        $queryFilterBySearch = " from snp_lab s1
                left join map_result mr ON mr.Snp_lab_Id=s1.Snp_lab_Id and mr.IsActive=1
                left join map ON mr.Map_Id= map.Map_Id
                left join assaybrand a ON a.Snp_lab_Id=s1.Snp_lab_Id 
                left join barcode b on b.Snp_lab_Id = s1.Snp_lab_Id 
                where  s1.IsActive=1";     

        if (($this->load($params) && $this->validate()))
        {
            $queryFilterBySearch .= " and map.Map_Id = ";
                if($this->Map == null)
                    $queryFilterBySearch.= ' (SELECT Map_Id FROM map 
                    where Crop_Id='.$this->Crop.' and IsActive=1 and Type="CONSENSUS" and MapTypeId='.$this->MapTypeId.'
                    ORDER BY Date asc LIMIT 1)';
                else
                {
                    $queryFilterBySearch.= $this->Map;
                }

            if($this->batch !=null  )
            {
               //$ar = '"'.str_replace(' ', '", "', $this->batch).'"';
              //print_r($ar); exit;
              $ar = null;
              if(strpos($this->batch, " ")!== false)
                $ar = explode(" ", $this->batch);
              elseif(strpos($this->batch, ",")!== false)
                $ar = explode(",", $this->batch);
              elseif(strpos($this->batch, ";")!== false)
                $ar = explode(";", $this->batch);

              if($ar!= null)
                $ar=implode("', '", $ar);
              else
                $ar = $this->batch;
              //print_r("'".$ar."'"); exit;
                $queryFilterBySearch .= " and s1.LabName in ('".$ar."')";    
            }
            elseif ($this->search !=null  )
                $queryFilterBySearch .= " and s1.LabName like '%". $this->search."%'";
               
            elseif ($this->LabName !=null  )
                $queryFilterBySearch .= " and s1.LabName like '%". $this->Name."%'";

            if($this->PurchaseSequence != null)
                $queryFilterBySearch .= " and s1.PurchaseSequence LIKE '".$this->PurchaseSequence."'";
            if($this->AlleleFam != null)    
                $queryFilterBySearch .= " and s1.AlleleFam LIKE '".$this->AlleleFam."'";
            if($this->AlleleVicHex != null)    
                $queryFilterBySearch .= "  and s1.AlleleVicHex LIKE '".$this->AlleleVicHex."'";
            if($this->ValidatedStatus != null)
            {
                $concat_validation = "";
                foreach($this->ValidatedStatus as $key => $value)
                {
                    if($concat_validation == "")
                        $concat_validation .= $value == "" ? " and (s1.ValidatedStatus is NULL":" and (s1.ValidatedStatus LIKE '%".$value."%'";
                    else
                        $concat_validation .= $value == "" ? " or s1.ValidatedStatus is NULL":" or s1.ValidatedStatus LIKE '%".$value."%'";
                        
                }
                $queryFilterBySearch .= $concat_validation.")";
                //exit;
                //$queryFilterBySearch .= "  and s1.ValidatedStatus LIKE '%".$this->ValidatedStatus."%'";
            }
                
            if($this->Quality != null)   
                $queryFilterBySearch .= "  and s1.Quality LIKE '%".$this->Quality."%'";
            if($this->Box != null)   
                $queryFilterBySearch .= "  and s1.Box LIKE '".$this->Quality."'";
            if($this->PositionInBox != null)   
                $queryFilterBySearch .= "  and s1.PositionInBox LIKE '".$this->PositionInBox."'";
            if($this->PIC != null)
                $queryFilterBySearch .= "  and s1.PIC LIKE '".$this->PositionInBox."'";
            if($this->assayBrandName != null)
                $queryFilterBySearch .= "  and a.Name LIKE '%".$this->assayBrandName."%'";
            if($this->LinkageGroupTo != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup <= ".$this->LinkageGroupTo;
            if($this->LinkageGroupFrom != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup >= ".$this->LinkageGroupFrom;
            if($this->PositionTo != null)
                 $queryFilterBySearch .= "  and mr.Position <= ".$this->PositionTo;
            if($this->PositionFrom != null)
                 $queryFilterBySearch .= "  and mr.Position >= ".$this->PositionFrom;
//                    if($this->Crop != null)
//                         $queryFilterBySearch .= "  and m.Crop_Id = ".$this->Crop;
            }

                    
            //$sql2 =   "SELECT COUNT(*) ".$queryFilterBySearch;
            
            
               /**********      SQLDAtaProvider     **************/
            
            $sql =   "select mr.Position, mr.LinkageGroup, s1.Snp_lab_Id, s1.LabName, s1.PurchaseSequence, s1.AlleleFam, s1.AlleleVicHex, s1.ValidatedStatus, s1.Quality, s1.Box, s1.PositionInBox, s1.PIC
                    , map.Crop_Id, a.Name as AssayName,  GROUP_CONCAT(b.Number SEPARATOR ',') as Number ".$queryFilterBySearch;
            
            if($orderBy != null)
                $sql .=   " GROUP BY s1.Snp_lab_Id ORDER BY s1.LabName ASC";
            else
                $sql .=   " GROUP BY s1.Snp_lab_Id ORDER BY mr.LinkageGroup DESC, mr.Position DESC";
            
            //print_r($sql); exit; 
            $count = Yii::$app->dbGdbms->createCommand($sql)->execute();
            
            if(isset($params['all']))
                $pagination = "";
            else
                $pagination = $this->Pagination;
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
                        'pageSize' => $pagination,
                 ]
               ]);
       
        $dataprov->key = "Snp_lab_Id";
       
       return $dataprov;
       
    }
    
    public function search2($params)
    {
        //$connection = \Yii::$app->db;
        //print_r($params); exit;
        $sql = "";
        $queryFilterBySearch = " FROM snp_lab s1
                    inner join marker m ON m.Marker_Id=s1.Marker_Id
                    left join assaybrand a ON a.Snp_lab_Id=s1.Snp_lab_Id 
                    left join barcode b on b.Snp_lab_Id = s1.Snp_lab_Id 
                    where  s1.IsActive=1";
        
        if (($this->load($params) && $this->validate()))
        {
            if($this->batch !=null  )
            {
               //$ar = '"'.str_replace(' ', '", "', $this->batch).'"';
              //print_r($ar); exit;
              $ar = null;
              if(strpos($this->batch, " ")!== false)
                $ar = explode(" ", $this->batch);
              elseif(strpos($this->batch, ",")!== false)
                $ar = explode(",", $this->batch);
              elseif(strpos($this->batch, ";")!== false)
                $ar = explode(";", $this->batch);

              if($ar!= null)
                $ar=implode("', '", $ar);
              else
                $ar = $this->batch;
              //print_r("'".$ar."'"); exit;
                $queryFilterBySearch .= " and s1.LabName in ('".$ar."')";    
            }elseif ($this->search !=null  )    
                $queryFilterBySearch .= " and s1.LabName like '%". $this->search."%'";
            elseif ($this->Name !=null  )    
                 $queryFilterBySearch .= " and s1.LabName like '%". $this->Name."%'";

            if($this->PurchaseSequence != null)
                $queryFilterBySearch .= " and s1.PurchaseSequence LIKE '".$this->PurchaseSequence."'";
            if($this->AlleleFam != null)    
                $queryFilterBySearch .= " and s1.AlleleFam LIKE '".$this->AlleleFam."'";
            if($this->AlleleVicHex != null)    
                $queryFilterBySearch .= "  and s1.AlleleVicHex LIKE '".$this->AlleleVicHex."'";
            if($this->ValidatedStatus != null)    
                $queryFilterBySearch .= "  and s1.ValidatedStatus LIKE '%".$this->ValidatedStatus."%'";
            if($this->Quality != null)   
                $queryFilterBySearch .= "  and s1.Quality LIKE '%".$this->Quality."%'";
            if($this->Box != null)   
                $queryFilterBySearch .= "  and s1.Box LIKE '".$this->Box."'";
            if($this->PositionInBox != null)   
                $queryFilterBySearch .= "  and s1.PositionInBox LIKE '".$this->PositionInBox."'";
            if($this->PIC != null)
                $queryFilterBySearch .= "  and s1.PIC LIKE '".$this->PositionInBox."'";
            if($this->assayBrandName != null)
                $queryFilterBySearch .= "  and a.Name LIKE '%".$this->assayBrandName."%'";
            if($this->Crop != null)   
                $queryFilterBySearch .= "  and m.Crop_Id = ".$this->Crop;
        }
        else
        {
            if($this->Crop != null)   
                $queryFilterBySearch .= "  and m.Crop_Id = ".$this->Crop;
            
            $this->Pagination = false;
        }
            
        $sql =   "SELECT COUNT(s1.LabName) ".$queryFilterBySearch;
        //print_r($sql); exit; 
        //$count = Yii::$app->dbGdbms->createCommand($sql)->queryScalar();
           /**********      SQLDAtaProvider     **************/
        $sql =   "select s1.Snp_lab_Id, s1.Marker_Id, s1.LabName, s1.PurchaseSequence, s1.AlleleFam, s1.AlleleVicHex, s1.ValidatedStatus, s1.Quality, s1.Box, s1.PositionInBox, s1.PIC
                    , m.Crop_Id, a.Name as AssayName, GROUP_CONCAT(b.Number SEPARATOR ', ') as Number ".$queryFilterBySearch;

        $sql .=   " GROUP BY s1.Snp_lab_Id ORDER BY s1.LabName ASC";
        $count = Yii::$app->dbGdbms->createCommand($sql)->execute();
           
        //print_r($sql); exit; 
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
                    'pageSize' => $this->Pagination,
             ]
           ]);
               
         
       //else return false;
       
       return $dataprov;
    
    }
    
    public function searchMarker($params, $orderBy = null)
    {
        if (($this->load($params) && $this->validate()))
        {
            $queryFilterBySearch = " from map "
                ."inner join map_result mr ON mr.Map_Id= map.Map_Id "
                ."left join marker m ON  mr.Marker_Id= m.Marker_Id  and m.IsActive=1 "
                ."left join snp_lab s1 ON m.Marker_Id=s1.Marker_Id and s1.IsActive=1 "
                ." where map.IsActive=1 ";

            if($this->MarkerType != 3 ) $queryFilterBySearch .=" and m.Marker_Type_Id=".$this->MarkerType;

            $queryFilterBySearch .= " and map.Map_Id = ";
                if($this->Map == null)
                {
                    $queryFilterBySearch.= ' (SELECT Map_Id FROM map'
                            . ' WHERE Crop_Id='.$this->Crop.' and IsActive=1 and Type="CONSENSUS" and MapTypeId='.$this->MapTypeId.'
                    ORDER BY Date asc LIMIT 1)';
                }
                else
                    $queryFilterBySearch.= $this->Map." and map.Crop_Id=".$this->Crop;

            if($this->batch !=null  )
            {
                $ar = null;
                if(strpos($this->batch, " ")!== false)
                  $ar = explode(" ", $this->batch);
                elseif(strpos($this->batch, ",")!== false)
                  $ar = explode(",", $this->batch);
                elseif(strpos($this->batch, ";")!== false)
                  $ar = explode(";", $this->batch);
                
                if($ar!= null)
                    $ar=implode("', '", $ar);
                else
                    $ar = $this->batch;
                
               $queryFilterBySearch .= " and m.Name in ('".$ar."')";    
              
            }
            elseif ($this->search !=null  )
            {
                $queryFilterBySearch .= " and m.Name like '%". $this->search."%'";
            }
            elseif ($this->Name !=null  )
            {
                $queryFilterBySearch .= " and m.Name like '%". $this->Name."%'";
            }
           
            if($this->PurchaseSequence != null)
                $queryFilterBySearch .= " and s1.PurchaseSequence LIKE '".$this->PurchaseSequence."'";
//            if($this->AlleleFam != null)    
//                $queryFilterBySearch .= " and s1.AlleleFam LIKE '".$this->AlleleFam."'";
//            if($this->AlleleVicHex != null)    
//                $queryFilterBySearch .= "  and s1.AlleleVicHex LIKE '".$this->AlleleVicHex."'";
            if($this->ValidatedStatus != null)
            {
                $concat_validation = "";
                foreach($this->ValidatedStatus as $key => $value)
                {
                    if($concat_validation == "")
                        $concat_validation .= $value == "" ? " and (s1.ValidatedStatus is NULL":" and (s1.ValidatedStatus LIKE '%".$value."%'";
                    else
                        $concat_validation .= $value == "" ? " or s1.ValidatedStatus is NULL":" or s1.ValidatedStatus LIKE '%".$value."%'";
                        
                }
                $queryFilterBySearch .= $concat_validation.")";
            }
            
            if($this->Quality != null)   
                $queryFilterBySearch .= "  and s1.Quality LIKE '%".$this->Quality."%'";
            if($this->Box != null)   
                $queryFilterBySearch .= "  and s1.Box LIKE '".$this->Quality."'";
            if($this->PositionInBox != null)   
                $queryFilterBySearch .= "  and s1.PositionInBox LIKE '".$this->PositionInBox."'";
            if($this->PIC != null)
                $queryFilterBySearch .= "  and s1.PIC LIKE '".$this->PositionInBox."'";
            if($this->assayBrandName != null)
                $queryFilterBySearch .= "  and a.Name LIKE '%".$this->assayBrandName."%'";
            if($this->LinkageGroupTo != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup <= ".$this->LinkageGroupTo;
            if($this->LinkageGroupFrom != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup >= ".$this->LinkageGroupFrom;
            if($this->PositionTo != null)
                 $queryFilterBySearch .= "  and mr.Position <= ".$this->PositionTo;
            if($this->PositionFrom != null)
                 $queryFilterBySearch .= "  and mr.Position >= ".$this->PositionFrom;
//            if($this->Crop != null)
//                 $queryFilterBySearch .= "  and m.Crop_Id = ".$this->Crop;
                    
            //$sql =   "SELECT COUNT(*) ".$queryFilterBySearch;
            //print_r($sql); exit; 
            
               /**********      SQLDAtaProvider     **************/
            
            $sql =   "select m.Marker_Id, m.Name as Name, mr.Position, mr.LinkageGroup, m.ShortSequence, m.LongSequence, s1.Snp_lab_Id, GROUP_CONCAT(s1.LabName separator ', ') as SnplabConcat, 
                       m.Crop_Id ".$queryFilterBySearch;
            $sql .=   " GROUP BY m.Marker_Id";
            
            if($orderBy != null)
                $sql .=   " ORDER BY s1.LabName ASC";
            else
                $sql .= " ORDER BY LinkageGroup DESC, mr.Position DESC";
            
            $count = Yii::$app->dbGdbms->createCommand($sql)->execute();
            //print_r($sql); exit;    
            
            if(isset($params['all']))
                $pagination = "";
            else
                $pagination = $this->Pagination;
            
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
                        'pageSize' => $pagination,
                 ]
               ]);
       }else 
           return false;
       
        $dataprov->key = "Marker_Id";
        return $dataprov;
    }
    
    public function searchWithOutSelection($params, $selection)
    {
      $query = SnpLab::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $SnpLabSearch = array("SnpLabSearch"=>array());
        $SnpLabSearch["SnpLabSearch"] = $params;
        
        $query->andFilterWhere([
                     'snp_lab.IsActive' => 1,
        			 ]);
        if (!($this->load($SnpLabSearch) && $this->validate())) {   
           
            $query->joinWith(['assaybrands', 'snp']);
            $query->andWhere(['snp.IsActive' =>1]);
             $dataProvider->setPagination(false);
            return $dataProvider;
        }
        $query->andFilterWhere([
            'Snp_lab_Id' => $this->Snp_lab_Id,
            'Snp_Id' => $this->Snp_Id,
            'PIC' => $this->PIC,
            'snp_lab.IsActive' =>1,
        ]);
        if ($this->search !=null  )    
            $query->andFilterWhere(['like', 'LabName', $this->search]);
        else
            $query->andFilterWhere(['like', 'LabName', $this->LabName]);
        
         
        
        $query ->andFilterWhere(['like', 'PurchaseSequence', $this->PurchaseSequence])
            ->andFilterWhere(['like', 'AlleleFam', $this->AlleleFam])
            ->andFilterWhere(['like', 'AlleleVicHex', $this->AlleleVicHex])
            ->andFilterWhere(['like', 'ValidatedStatus', $this->ValidatedStatus])
            ->andFilterWhere(['like', 'Quality', $this->Quality])
            ->andFilterWhere(['like', 'Box', $this->Box])
            ->andFilterWhere(['like', 'PositionInBox', $this->PositionInBox])
            ->andFilterWhere(['like', 'Observation', $this->Observation]);

        // filter by assayBrand name
        $query->joinWith(['assaybrands' => function ($q) {
            $q->where('assaybrand.Name LIKE "%' . $this->assayBrandName . '%"');
        }]);
        
        // filter by snp name
        $query->joinWith(['snp' => function ($q) {
            $q->andFilterWhere(['>=', 'snp.LinkageGroup',  $this->LinkageGroupFrom]);
            $q->andFilterWhere(['<=', 'snp.LinkageGroup',  $this->LinkageGroupTo]);
            $q->andFilterWhere(['=', 'snp.IsActive',  1]);
        }]);

       $query->andWhere(['not in', ["snp_lab.Snp_lab_Id"], $selection]);
        // echo $query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;
        // exit();

        //$dataProvider->setPagination(false);
        //foreach($dataProvider->getModels() as $asd)
          //  print_r( $asd->LabName);
        
        return ($dataProvider);  
        //print_r($selection); exit;
    }
    
    public function searchByIds($vChecks)
    {
        $query = Marker::find()->with("snpLabs","snpLabs.barcodes");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       
        $query->andWhere(['in', ["marker.Marker_Id"], $vChecks]);
        
        $query->orderBy('Name');
        $dataProvider->pagination = false;
        return $dataProvider;
    }
    
    public function getLimitsXcM($params)
    {
       $max ="";
       
        if($this->load($params))
        {
            $connection = \Yii::$app->dbGdbms;
            $sql = 'SELECT LinkageGroup, MAX(Position) FROM map_result mr
                    INNER JOIN map m ON m.Map_Id=mr.Map_Id
                    WHERE m.Crop_Id='.$this->Crop.'. and m.Map_Id='.$this->Map
                    .' GROUP BY LinkageGroup';
//
            $maxCm =  $connection->createCommand($sql)->queryAll();
          
           return($maxCm);
        }
//        else 
//        {
//            $connection = \Yii::$app->db;
//            $sql = 'SELECT LinkageGroup, MAX(Position) FROM map_result mr
//                    INNER JOIN map m ON m.Map_Id=mr.Map_Id
//                    WHERE m.Crop_Id='.$params.'.
//                    GROUP BY LinkageGroup';
//            $maxCm =  $connection->createCommand($sql)->queryAll();
//          
//           return($maxCm); exit;
//        }
    }
       
    public static function getCropById($id)
    {
        return Yii::$app->dbGdbms->createCommand("Select Name from crop where Crop_Id=:id",  [':id' => $id])->queryOne();
    }
    
    public function searchByConsensus($params, $orderBy = null)
    {
        $sql = "";
        $queryFilterBySearch = " from snp_lab s1
                inner join marker m ON m.Marker_Id=s1.Marker_Id
                inner join map_result mr ON mr.Marker_Id=s1.Marker_Id and mr.IsActive=1
                inner join map ON mr.Map_Id= map.Map_Id"
                //inner join assaybrand a ON a.Snp_lab_Id=s1.Snp_lab_Id 
                //left join barcode b on b.Snp_lab_Id = s1.Snp_lab_Id 
                ." where  s1.IsActive=1 ";     
        
        if (($this->load($params) && $this->validate()))
        {
            
            $queryFilterBySearch.="and map.Map_Id=".$this->Map;
            
            if($this->batch !=null  )
            {
              $ar = null;
              if(strpos($this->batch, " ")!== false)
                $ar = explode(" ", $this->batch);
              elseif(strpos($this->batch, ",")!== false)
                $ar = explode(",", $this->batch);
              elseif(strpos($this->batch, ";")!== false)
                $ar = explode(";", $this->batch);

              if($ar!= null)
                $ar=implode("', '", $ar);
              else
                $ar = $this->batch;
              //print_r("'".$ar."'"); exit;
                $queryFilterBySearch .= " and s1.LabName in ('".$ar."')";    
            }
            elseif ($this->search !=null  )
                $queryFilterBySearch .= " and s1.LabName like '%". $this->search."%'";
               
            elseif ($this->LabName !=null  )
                $queryFilterBySearch .= " and s1.LabName like '%". $this->Name."%'";

            if($this->PurchaseSequence != null)
                $queryFilterBySearch .= " and s1.PurchaseSequence LIKE '".$this->PurchaseSequence."'";
            if($this->AlleleFam != null)    
                $queryFilterBySearch .= " and s1.AlleleFam LIKE '".$this->AlleleFam."'";
            if($this->AlleleVicHex != null)    
                $queryFilterBySearch .= "  and s1.AlleleVicHex LIKE '".$this->AlleleVicHex."'";
            if($this->ValidatedStatus != null)
            {
                $concat_validation = "";
                foreach($this->ValidatedStatus as $key => $value)
                {
                    if($concat_validation == "")
                        $concat_validation .= $value == "" ? " and (s1.ValidatedStatus is NULL":" and (s1.ValidatedStatus LIKE '%".$value."%'";
                    else
                        $concat_validation .= $value == "" ? " or s1.ValidatedStatus is NULL":" or s1.ValidatedStatus LIKE '%".$value."%'";
                        
                }
                $queryFilterBySearch .= $concat_validation.")";
                //exit;
                //$queryFilterBySearch .= "  and s1.ValidatedStatus LIKE '%".$this->ValidatedStatus."%'";
            }
                
            if($this->Quality != null)   
                $queryFilterBySearch .= "  and s1.Quality LIKE '%".$this->Quality."%'";
            if($this->Box != null)   
                $queryFilterBySearch .= "  and s1.Box LIKE '".$this->Quality."'";
            if($this->PositionInBox != null)   
                $queryFilterBySearch .= "  and s1.PositionInBox LIKE '".$this->PositionInBox."'";
            if($this->PIC != null)
                $queryFilterBySearch .= "  and s1.PIC LIKE '".$this->PositionInBox."'";
            if($this->assayBrandName != null)
                $queryFilterBySearch .= "  and a.Name LIKE '%".$this->assayBrandName."%'";
            if($this->LinkageGroupTo != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup <= ".$this->LinkageGroupTo;
            if($this->LinkageGroupFrom != null)
                 $queryFilterBySearch .= "  and mr.LinkageGroup >= ".$this->LinkageGroupFrom;
            if($this->PositionTo != null)
                 $queryFilterBySearch .= "  and mr.Position <= ".$this->PositionTo;
            if($this->PositionFrom != null)
                 $queryFilterBySearch .= "  and mr.Position >= ".$this->PositionFrom;

        }
            
        /**********      SQLDAtaProvider     **************/
        $sql =   "SELECT m.Name, mr.Position, mr.LinkageGroup, s1.Snp_lab_Id, s1.LabName, s1.PurchaseSequence, s1.AlleleFam, s1.AlleleVicHex, s1.ValidatedStatus, s1.Quality, s1.Box, s1.PositionInBox, s1.PIC
                , map.Crop_Id ".$queryFilterBySearch;
        
        if($orderBy != null)
            $sql .=   "ORDER BY s1.LabName ASC";
        else
        $sql .=   " ORDER BY mr.LinkageGroup DESC, mr.Position DESC";
        //print_r($sql); exit;
        $count = Yii::$app->dbGdbms->createCommand($sql)->execute();

        if(isset($params['all']))
            $pagination = "";
        else
            $pagination = $this->Pagination;
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
                    'pageSize' => $pagination,
             ]
           ]);
       
        $dataprov->key = "Snp_lab_Id";
       
       return $dataprov;
        
    }
}
