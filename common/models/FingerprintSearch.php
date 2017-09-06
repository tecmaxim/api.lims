<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use common\models\Fingerprint;


/**
 * FingerprintSearch represents the model behind the search form about `common\models\Fingerprint`.
 */
class FingerprintSearch extends Fingerprint
{
    public $Fingerprint_Material_Id2;
    public $Fingerprint_Material_Id;
    public $Fingerprint_Id2;
    public $Fingerprint_Id;
    public $radio1;
    public $radio2;
    public $Crop;
    public $Method;
    public $Map;
    public $MapTypeId;
    public $search;
    public $MarkerType;
    public $hiddenField;
    
    protected $searchConsensus = null;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Fingerprint_Id', 'Project_Id', 'IsActive'], 'integer'],
            [['Name', 'DateCreated','radio1', 'radio2','Crop', 'Crop_Id', 'Method', 'Fingerprint_Id','Fingerprint_Id2','Map', 'MapTypeId',], 'safe'],
            [['Fingerprint_Material_Id2', 'Fingerprint_Material_Id','radio1', 'radio2', 'MapTypeId', 'Crop'], 'required', 'on' => 'query2'],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }
    
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
	
    public function attributeLabels()
    {
        return [
           
            'Fingerprint_Material_Id2' => Yii::t('app', '2nd Material'),
            'Fingerprint_Material_Id'  => Yii::t('app', 'Material'),
            'MapTypeId' => Yii::t('app', 'Map Category'),
            'Map' => Yii::t('app', 'Mapped Population'),
        ];
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
        //print_r($params); exit;
        $query = Fingerprint::find();
		

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere([
	
            'IsActive' => 1,
        ]);
        $this->load($params);

        if (!$this->validate()) {
            
            return $dataProvider;
            
        }
        $query->andFilterWhere([
            
            'Fingerprint_Id' => $this->Fingerprint_Id,
            //'DateCreated' => $this->DateCreated,
            'Project_Id' => $this->Project_Id,
            'Crop_Id' => $this->Crop_Id,
            'IsActive' => 1,
        ]);
        
          $query ->andFilterWhere(['like', 'DateCreated', $this->DateCreated]);

        $query->andFilterWhere(['like', 'Name', $this->Name]);

        return $dataProvider;
    }
	
    public function get_polymorfic($params)
    {
        $this->load($params);
        if(Map::isConsensus($this->Map))
            $this->searchConsensus = true;
        
        if(($this->radio2 != "" ) or ($this->radio1 != ""))
        {
            switch ($this->Method)
            {
                case 1: //Polymorfic
                    if($this->searchConsensus != true)
                        $dataProvider = $this->polymorfic($this->Crop,$this->Map, $this->MapTypeId ,  $this->radio1,  $this->radio2);
                    else
                        $dataProvider = $this->polymorficByConsensus($this->Map, $this->radio1,  $this->radio2);
                    break;
                case 2:// Monomorfic
                  $dataProvider = $this->monomorfic($this->Crop,$this->Map, $this->MapTypeId ,  $this->radio1,  $this->radio2 );              
                    break;
                default: // Both
                    if($this->searchConsensus != true)
                        $dataProvider = $this->polyAndMonomorfic($this->Crop,$this->Map, $this->MapTypeId ,  $this->radio1,  $this->radio2 );
                    else
                        $dataProvider = $this->polyAndMonomorficByConsensus($this->Map,  $this->radio1,  $this->radio2 );
                    break;
            }           
        }else
            $dataProvider = false;
    
        return ( $dataProvider);
            //$model = $dataProvider;
    }
    
    public function getTotalCompared($params)
    {
        $this->load($params);
        if(($this->radio2 != "" ) or ($this->radio1 != ""))
        {        
            $connection = \Yii::$app->dbGdbms;
            $sql ='Select count(*) from fingerprint_snplab fs
                    inner join fingerprint_material fm ON fs.Fingerprint_Id= fm.Fingerprint_Id
                    where fm.Fingerprint_Material_Id ='.$this->radio1.'
                     and fs.Snp_lab_Id in 
                    (
                    Select fs2.Snp_lab_Id from fingerprint_snplab fs2
                    inner join fingerprint_material fm2 ON fs2.Fingerprint_Id= fm2.Fingerprint_Id
                    where fm2.Fingerprint_Material_Id ='.$this->radio2.'
                                        )';
            $max = $connection->createCommand($sql)->queryScalar();
            
        }
       
        if(isset($max))
            return $max;
        else 
            return false;
        exit;
            //$model = $dataProvider;
    }
    
    public function getMaterialByFpMaterialId($id)
    {
         $connection = \Yii::$app->dbGdbms;
            $sql ='SELECT m1.Name, fm.TissueOrigin From material_test m1
                    INNER JOIN fingerprint_material fm ON fm.Material_Test_Id = m1.Material_Test_Id and fm.IsActive=1
                    WHERE fm.Fingerprint_Material_Id = '.$id.' and m1.IsActive=1';
                   
           
           return $connection->createCommand($sql)->queryOne();
    }
    
    public static function getMaterialStringByFpMaterialId($id)
    {
         $connection = \Yii::$app->dbGdbms;
            $sql ='SELECT m1.Name, fm.TissueOrigin From material_test m1
                    INNER JOIN fingerprint_material fm ON fm.Material_Test_Id = m1.Material_Test_Id and fm.IsActive=1
                    WHERE fm.Fingerprint_Material_Id = '.$id.' and m1.IsActive=1';
                   
           
           $material = $connection->createCommand($sql)->queryOne();
           return $material['Name']." ( ".$material['TissueOrigin']." )";
    }
    
    public static function getCantSnps($id)
    {
         $connection = \Yii::$app->dbGdbms;
         $sql ='Select COUNT(*) from fingerprint_snplab fs 
               where fs.Fingerprint_Id='.$id;
         
        return $connection->createCommand($sql)->queryScalar();
    }
    
    public static function getCantMaterials($id)
    {
         $connection = \Yii::$app->dbGdbms;
         $sql ='Select COUNT(*) from fingerprint_material fs 
                where fs.Fingerprint_Id='.$id;
         
        return $connection->createCommand($sql)->queryScalar();
    }
    
    public function polymorfic($crop, $Map, $MapType, $material1, $material2)
    {   
        $sql = 'SELECT mr.Snp_lab_Id, mr.LinkageGroup, mr.Position, sl.LabName, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality From map  
                INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id 
                INNER JOIN snp_lab sl ON sl.Snp_lab_Id = mr.Snp_lab_Id';
       
        $sql .=' INNER JOIN fingerprint_snplab fs ON mr.Snp_lab_Id=fs.Snp_lab_Id and fs.IsActive=1
        INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
        INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
        WHERE map.Crop_Id=:crop
        and fm.Fingerprint_Material_Id = :m1 
        and fr.Allele_Id < 4 
        and fr.IsActive=1 
        and map.Map_Id =';
        
        if($Map == null)
            $sql.= ' (SELECT Map_Id FROM map 
                            where Crop_Id=:crop and IsActive=1 and Type="CONSENSUS" and IsCurrent=1 and MapTypeId='.$MapType.'
                            ORDER BY Date asc LIMIT 1)';
        else
            $sql.= ' :map';
               
        $sql.= ' and fr.Allele_Id  <>
                        (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)
        order by mr.LinkageGroup DESC, mr.Position ASC';
                
        $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'db' => Yii::$app->dbGdbms,
                'params' => [':m2' => $material2, ':m1' => $material1, ':crop' => $crop, ':map'=>$Map],

                ]); 
        //print_r($dataProvider->sql); exit;
        
        $dataProvider->setPagination(false);
        $dataProvider->key = "Snp_lab_Id";
        
        return $dataProvider;
      
    }
       
    public function polyAndMonomorfic($crop, $Map, $MapType, $material1, $material2)
    {
        $sql = '(SELECT (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4) as Allele2,
                            
                fr.Allele_Id, mr.Snp_lab_Id, mr.LinkageGroup, mr.Position, sl.LabName, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality, 1 as "Result" From map  
                INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id';
//              'INNER JOIN marker m  ON mr.Snp_lab_Id=mr.Snp_lab_Id';
//        If($MarkerType != 3)/*distinto de Both*/ 
//            $sql.= ' INNER JOIN marker_type mt ON mt.Marker_Type_Id = m.Marker_Type_Id and mt.Marker_Type_Id ='.$MarkerType;
       
        $sql.=' INNER JOIN snp_lab sl ON sl.Snp_lab_Id = mr.Snp_lab_Id
        INNER JOIN fingerprint_snplab fs ON mr.Snp_lab_Id=fs.Snp_lab_Id and fs.IsActive=1
        INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
        INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
        WHERE map.Crop_Id=:crop
        and fm.Fingerprint_Material_Id = :m1 
        and fr.Allele_Id < 4 
        and fr.IsActive=1 
        and map.Map_Id =';
        if($Map == null)
            $sql.= ' (SELECT Map_Id FROM map 
                            where Crop_Id=:crop and IsActive=1 and Type="CONSENSUS" and MapTypeId='.$MapType.'
                            ORDER BY Date asc LIMIT 1)';
        else
            $sql.= ' :map';
        
       
        $sql.= ' and fr.Allele_Id  <>
                        (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)
                        )
        
        UNION ';
        
        $sql .= '(SELECT 
                        (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4) as Allele2, 
                        fr.Allele_Id, mr.Snp_lab_Id, mr.LinkageGroup, mr.Position, sl.LabName, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality, 0 as "Result" From map  
                INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id';
                //'INNER JOIN marker m  ON mr.Snp_lab_Id=mr.Snp_lab_Id';
        //If($MarkerType != 3)/*distinto de Both*/ 
       //     $sql.= ' INNER JOIN marker_type mt ON mt.Marker_Type_Id = m.Marker_Type_Id and mt.Marker_Type_Id ='.$MarkerType;
       
        $sql.=' INNER JOIN snp_lab sl ON sl.Snp_lab_Id = mr.Snp_lab_Id
        INNER JOIN fingerprint_snplab fs ON mr.Snp_lab_Id=fs.Snp_lab_Id and fs.IsActive=1
        INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
        INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
        WHERE map.Crop_Id=:crop
        and fm.Fingerprint_Material_Id = :m1 
        and fr.Allele_Id < 4 
        and fr.IsActive=1 
        and map.Map_Id =';
        if($Map == null)
            $sql.= ' (SELECT Map_Id FROM map 
                            where Crop_Id=:crop and IsActive=1 and Type="CONSENSUS" and MapTypeId='.$MapType.'
                            ORDER BY Date asc LIMIT 1)';
        else
            $sql.= ' :map';
        
       
        $sql.= ' and fr.Allele_Id  =
                        (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)
        order by mr.LinkageGroup DESC, mr.Position123 ASC)';
        
        
        $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'db' => Yii::$app->dbGdbms,
                'params' => [':m2' => $material2, ':m1' => $material1, ':crop' => $crop, ':map'=>$Map],

                ]); 
        
        print_r($dataProvider->sql); exit;
        $dataProvider->setPagination(false);
        $dataProvider->key = "Snp_lab_Id";
        return $dataProvider;
    }
    
    public function polyAndMonomorficByConsensus( $Map, $material1, $material2)
    {
        $sql = $this->queryPolyandMonomorficByConsensus('<>')
               ."UNION"
               .$this->queryPolyandMonomorficByConsensus('=')
               ."ORDER BY LinkageGroup DESC, Position ASC";
        
        $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'db' => Yii::$app->dbGdbms,
                'params' => [':m2' => $material2, ':m1' => $material1, ':map'=>$Map],

                ]); 
        
        $dataProvider->setPagination(false);
        $dataProvider->key = "Snp_lab_Id";
        return $dataProvider;
    }
    
    public function monomorfic($crop, $Map, $MapType, $material1, $material2)
    {
        $sql = 'SELECT mr.Snp_lab_Id,  mr.LinkageGroup, mr.Position, sl.LabName, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality From map  
                INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id';
              //  'INNER JOIN marker m  ON mr.Snp_lab_Id=mr.Snp_lab_Id';
//        If($MarkerType != 3)/*distinto de Both*/ 
//            $sql.= ' INNER JOIN marker_type mt ON mt.Marker_Type_Id = m.Marker_Type_Id and mt.Marker_Type_Id ='.$MarkerType;
       
        $sql.=' INNER JOIN snp_lab sl ON sl.Snp_lab_Id = mr.Snp_lab_Id
        INNER JOIN fingerprint_snplab fs ON mr.Snp_lab_Id=fs.Snp_lab_Id and fs.IsActive=1
        INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
        INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
        WHERE map.Crop_Id=:crop
        and fm.Fingerprint_Material_Id = :m1 
        and fr.Allele_Id < 4 
        and fr.IsActive=1 
        and map.Map_Id =';
        if($Map == null)
            $sql.= ' (SELECT Map_Id FROM map 
                            where Crop_Id=:crop and IsActive=1 and Type="CONSENSUS" and MapTypeId='.$MapType.'
                            ORDER BY Date asc LIMIT 1)';
        else
            $sql.= ' :map';
              
        $sql.= ' and fr.Allele_Id  =
                        (SELECT r2.Allele_Id From fingerprint_result r2
                        inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                        WHERE r2.Fingerprint_Material_Id  = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)
        order by mr.LinkageGroup DESC, mr.Position ASC';
        
        $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'db' => Yii::$app->dbGdbms,
                'params' => [':m2' => $material2, ':m1' => $material1, ':crop' => $crop, ':map'=>$Map],

                ]); 
        $dataProvider->setPagination(false);
        $dataProvider->key = "Snp_lab_Id";
        return $dataProvider;
    }
    
    public function polymorficByConsensus( $Map, $material1, $material2)
    {
        $sql = "SELECT m2.Name, m2.Marker_Id, sl.Snp_lab_Id, sl.LabName ,mr.LinkageGroup, mr.Position, sl.LabName, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality from map 
        INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id 
        INNER JOIN  
            (SELECT sl.Marker_Id, m.Name From snp_lab sl
            inner join marker m on m.Marker_Id=sl.Marker_Id
            INNER JOIN fingerprint_snplab fs ON sl.Snp_lab_Id=fs.Snp_lab_Id 
            INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
            INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
            WHERE 
            fm.Fingerprint_Material_Id = :m1
            and m.IsActive=1 
            and fr.Allele_Id < 4  
            and fr.Allele_Id <>
		(SELECT r2.Allele_Id From fingerprint_result r2
		inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
		WHERE r2.Fingerprint_Material_Id = :m2 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)) 
	m2 on m2.Marker_Id=mr.Marker_Id
        INNER JOIN snp_lab sl ON m2.Marker_Id=sl.Marker_Id
        WHERE map.Map_Id=:map 
        ORDER BY mr.LinkageGroup DESC, mr.Position ASC";
        
        $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'db' => Yii::$app->dbGdbms,
                'params' => [':m2' => $material2, ':m1' => $material1, ':map'=>$Map],

                ]); 
      
        $dataProvider->setPagination(false);
        $dataProvider->key = "Snp_lab_Id";
        
        return $dataProvider;
    }
    
    private function queryPolyAndMonomorficByConsensus($operator)
    {
        $operator == '<>'? $result =1:$result =2;
        $sql = '(SELECT 		
                m2.Name, m2.Marker_Id, sl.Snp_lab_Id, sl.LabName, Allele2, m2.Allele_Id, mr.LinkageGroup, mr.Position, sl.Box, sl.PositionInBox, sl.PIC, sl.Quality, '.$result.' as "Result" from map 
                INNER JOIN map_result mr ON map.Map_Id=mr.Map_Id 
                INNER JOIN  
                        (SELECT sl.Marker_Id, m.Name, fr.Allele_Id,
                                (SELECT r2.Allele_Id From fingerprint_result r2 
                                INNER join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id 
                                WHERE r2.Fingerprint_Material_Id = :m2 and r2.IsActive=1 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4) as Allele2 
                FROM snp_lab sl
               INNER join marker m on m.Marker_Id=sl.Marker_Id
               INNER JOIN fingerprint_snplab fs ON sl.Snp_lab_Id=fs.Snp_lab_Id 
               INNER JOIN fingerprint_material fm ON fm.Fingerprint_Id = fs.Fingerprint_Id and fm.IsActive=1
               INNER JOIN fingerprint_result fr ON fr.Fingerprint_SnpLab_Id= fs.Fingerprint_SnpLab_Id and fr.Fingerprint_Material_Id=fm.Fingerprint_Material_Id
               WHERE fm.Fingerprint_Material_Id = :m1
                     and m.IsActive=1 
                     and fr.Allele_Id < 4  
                     and fr.Allele_Id '.$operator.'
                    (SELECT r2.Allele_Id From fingerprint_result r2
                    inner join fingerprint_snplab fs2 ON fs2.Fingerprint_SnpLab_Id= r2.Fingerprint_SnpLab_Id
                    WHERE r2.Fingerprint_Material_Id = :m2 and fs.Snp_lab_Id = fs2.Snp_lab_Id and r2.Allele_Id < 4)) 
                    m2 on m2.Marker_Id=mr.Marker_Id

                INNER JOIN snp_lab sl ON m2.Marker_Id=sl.Marker_Id
                WHERE map.Map_Id=:map)';
        return $sql;
    }
    
    public function getIdSelectedByResultPartial($checks, $models)
    {
        $found = null;
        $dataToReturn = array();
        foreach($models as $data)
        {
           $found = array_search($data['Snp_lab_Id'], $checks);
        
           if($found !== false)
           {
               $dataToReturn[] = $data;
               unset($checks[$found]);
               $checks = array_values($checks);
           }  
        }
       
        return $dataToReturn;
    }
}
