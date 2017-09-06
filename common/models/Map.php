<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

/**
 * This is the model class for table "map".
 *
 * @property string $Map_Id
 * @property string $Crop_Id
 * @property string $Name
 * @property string $Date
 * @property string $IsConcensus
 * @property integer $MapTypeId 
 * @property string $IsActive   
 * @property Crop $crop
 * @property MapResult[] $mapResults
 */
class Map extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }
    
    public function rules()
    {
        return [
            [['Crop_Id', 'Name', 'Date', 'IsActive'], 'required'],
           [['Crop_Id', 'IsActive'], 'integer'],
           [['Crop_Id', 'Name', 'MapTypeId', 'Date', 'IsActive', 'IsCurrent', 'Type'], 'required'],
           [['Crop_Id', 'MapTypeId', 'IsActive'], 'integer'],
           [['Date'], 'safe'],
           [['Name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Map_Id' => Yii::t('app', 'Map  ID'),
            'Crop_Id' => Yii::t('app', 'Crop  ID'),
            'Name' => Yii::t('app', 'Name'),
            'MapTypeId' => Yii::t('app', 'Map Type ID'), 
            'Date' => Yii::t('app', 'Date'),
            //'IsConcensus' => Yii::t('app', 'Is Concensus'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrop()
    {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }
    
    public function getMapType() 
    { 
        return $this->hasOne(MapType::className(), ['MapTypeId' => 'MapTypeId']); 
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMapResults()
    {
        return $this->hasMany(MapResult::className(), ['Map_Id' => 'Map_Id']);
    }
    
    public function deleteLogic() 
    {
        $this->IsActive = 0;
        if($this->update())
        {
            $sql = "UPDATE map_result SET IsActive=0 WHERE Map_Id=".$this->Map_Id;
            $connection = \Yii::$app->dbGdbms;
            
            try
            {
                $connection->createCommand($laquery)->execute();
            } catch (Exception $e)
                {
                echo "No se pudo realizar la acción: ".$e;
                exit;
                }
        }
    }
    
    public function importMap($file, $crop, $map_type, $mType, $IsCurrent)
    {
       
        $objPHPExcel = new PHPExcel();
        if($IsCurrent = 1)
            $this->getConsensus($crop, $mType, $map_type);
            
        set_time_limit(60*5);
        ini_set ( "memory_limit" , - 1 );
        //ini_set("max_execution_time", "7200");
        $result = array("success" => NULL, "fail" => NULL, "fail_document" => NULL,  "fail_total" => NULL, "snps_duplicates" => NULL);
			
	$Reader = PHPExcel_IOFactory::createReaderForFile($file);
		
	$Reader -> setReadDataOnly (true);
	$ObjXLS = $Reader ->load( $file );
	$ObjXLS->setActiveSheetIndex(0);
	$lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
	$lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);
	
        /***************   CONTROL DE DOCUMENTO CORRECTO ***************/
        for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
	{
            $headers[] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, 1)->getValue(); 
        }
       
       if(($validate_document = $this->validateDocument($headers)) == false)
        {    $result["fail_document"] = "<strong>THE DOCUMENT FORMAT IS NOT VALID</strong>.
		 <br> Remember that the header must have the following order:<br>
		<li> MarkerID </li>
		<li> Linkage_Group </li>
		<li> Position </li>
		<li> Mapped_Population </li>
		<li> Mapping_Team </li>
		";
        unlink($file);
        return $result;
        }                 
        unset($validate_document);       
        /**************************************************************/
        
	for($row=2; $row <= $lastRow; $row++)
	{
            //ini_set ( "memory_limit" , - 1 );
            if($ObjXLS->getSheet(0)->getCellByColumnAndRow(0, $row)->getValue() == NULL)
                break;
            
            for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
            {

                $values[$row][$col] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, $row)->getValue();
            }
	}
       
        $name_file = substr($file, 8, -5); 
        $length = count($values); 
        $width = array_map( 'count',  $values); 
        $ObjXLS->disconnectWorksheets();
	unset($ObjXLS, $objPHPExcel);
        
        $connection = \Yii::$app->dbGdbms;
        
        /**********************   DUPLICATES     *************************/
        foreach($values as $v)
                $namesMarkers[] = $v[0];
        
        $namesXocurrences = array_count_values($namesMarkers);
        
        foreach($namesXocurrences as $key => $val)
        {    
            if($val > 1)
            {   $result["fail_document"] = "<strong>The File have errors</strong>.<br> "
                                                ."The Marker <b>".$key."</b> is duplicated. Please edit the file."; 
                return $result;
            }
        }
        unset($namesXocurrences);
        unset($namesMarkers);
       
        /******************************************************************/
        $new_matriz= array_values($values);
        
        $laquery = "INSERT INTO map (Crop_Id, Name, MapTypeId, Date, Type, IsCurrent, IsActive) VALUES";
        $laquery .= '('.$crop.',"'.$name_file.'" ,'.$map_type.',"'.date("Y-m-d H:i:s").'","'.$mType.'", 1, 1);';
        
        $laquery .= "SET @map_id = LAST_INSERT_ID();"
                 . "INSERT INTO map_result (Map_Id, Marker_Id,LinkageGroup,Position,MappedPopulation,MappingTeam, IsActive) VALUES";
        $marks = array();
        for($l = 0; $l < count($new_matriz) ; $l++)
        {              
            $id_marker = "";
            $id_snplab = "";
            if($new_matriz[$l][0] )
            {
                if($id_marker = Marker::find()->where(["IsActive" => 1, "Name" => $new_matriz[$l][0]])->scalar())
                {
                    $laquery .= "(@map_id, ".$id_marker.",";
                    if($new_matriz[$l][1] == NULL)
                        $laquery .= "0,";
                    else
                        $laquery .= $new_matriz[$l][1].",";

                    if($new_matriz[$l][2] == NULL)
                            $laquery .= "0,'";
                    else
                            $laquery .= $new_matriz[$l][2].",'";

                    if($new_matriz[$l][3] == NULL)
                            $laquery .= "NULL,'";
                    else
                            $laquery .= $new_matriz[$l][3]."','";

                    if($new_matriz[$l][4] == NULL)
                            $laquery .= "NULL,";
                    else
                            $laquery .= $new_matriz[$l][4]."',";

                    if($l == (count($new_matriz)-1))
                            $laquery.= "1)";
                    else
                            $laquery.= "1),";

                    $maps[] = $new_matriz[$l][0];
                }else
                    $not_maps[] =  $new_matriz[$l][0];
            }
        }
      
        $msj="";
        //print_r($laquery); exit;
        if(isset($maps))
        {
            if($connection->createCommand($laquery)->execute())
            {
                $result['success'] = "<div class='alert alert-success' id='notifications'> <strong>".count($maps)." of ". count($values)." Mapped markers where imported successfully!</strong>.</div>";
                if($not_maps)
                {
                    $result['not_maps'] = $not_maps;
                    
                }
            }
            else
            {
                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. Please verify the file and try again.</div>";   
            }
        }else
            $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. This file markers are not loaded.</div>";   
        
        set_time_limit(30);
        unlink($file);
        return $result;
    }
    
    private function validateDocument($headers)
    {
        $ok = true;     
        foreach($headers as $k => $val)
        { 
            if ($k == 0 and $val != "MarkerID")
                $ok = false;
            if ($k == 1 and $val != "Linkage_Group")
                $ok = false;
            if ($k == 2 and $val != "Position")
                $ok = false;
            if ($k == 3 and $val != "Mapped_Population")
                $ok = false;
            if ($k == 4 and $val != "Mapping_Team")
                $ok = false;
                            
        }
        return $ok; 
    }
    
    public function getMapsAvilablesByType($mapType, $crop)
    {
        $connection = \Yii::$app->dbGdbms;
        $Query = "SELECT  mr.MappedPopulation, m.Map_Id from map m
                  INNER JOIN  map_result mr ON mr.Map_Id=m.Map_Id
                  WHERE m.MapTypeId = ".$mapType." and m.IsActive=1 and m.Crop_Id=".$crop." Group By Map_Id";
               
        $maps = $connection->createCommand($Query)->queryAll();
       
       if($maps)
       {
           $i = 0;
           foreach($maps as $m)
           {
               $mAvilables[$i]["name"] = $m['MappedPopulation'];
               $mAvilables[$i]["id"] = $m['Map_Id'];
               $i++;
           }
           return ($mAvilables);
       } else
           return false;
      
       
    }
    
    private function getConsensus($crop, $mType, $map_type)
    {
        $maps = $this->find()->where(["Type"=>$mType, "Crop_Id" => $crop, "MapTypeId"=>$map_type,"IsActive"=>1])->all();
        if($maps)
        {
            foreach($maps as $map)
            {   
                $map->IsCurrent = 0;
                $map->update();
            }
        }else return false;
    }
    
    public function getRemoveConsensusById($id)
    {
        $mapSelected = $this->find()->where(["Map_Id"=>$id])->one();
        
        if($mapSelected)
        {
            $map = $this->find()->where(["Type"=>$mapSelected->Type, 
                                          "Crop_Id" => Yii::$app->session['cropId'],
                                          "MapTypeId"=>$mapSelected->MapTypeId,
                                          "IsCurrent"=>1,
                                          "IsActive"=>1])->one();
            
            if($map)
            {
                $map->IsCurrent = 0;
                $map->update();
            }
        }else 
            return false;
        
    }
    
    public static function isConsensus($mapId)
    {
        $type= Yii::$app
                    ->dbGdbms
                    ->createCommand("SELECT Type FROM map WHERE Map_Id=".$mapId)
                    ->queryScalar();
        
        if($type == 'CONSENSUS')
            return true;
        else
            return false;
    }
    
    static function mapsAvailables()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) 
        {
            //print_r($_POST['depdrop_parents']); exit;
            $parents = $_POST['depdrop_parents'];
            
            if ($parents != null) 
            {
                $mapType = $parents[0];
                $CropID  = $parents[1];
               
                $map_aviliable=  $$this->getMapsAvilablesByType($mapType, $CropID);
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['output' => $map_aviliable];
            }
        }
    }
   
}
