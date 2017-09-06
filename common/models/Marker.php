<?php

namespace common\models;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "marker".
 *
 * @property integer $Marker_Id
 * @property string $Name
 * @property string $ShortSequence
 * @property string $LongSequence
 * @property integer $PublicLinkageGroup
 * @property string $PublicCm
 * @property integer $AdvLinkageGroup
 * @property string $AdvCm
 * @property string $PhysicalPosition
 * @property integer $IsActive
 *
 * @property FingerprintMarker[] $fingerprintMarkers
 * @property MapResult[] $mapResults
 * @property Crop $crop
 * @property MarkerType $markerType
 * @property SnpLab[] $snpLabs
 */
class Marker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
   
    /* CONSTANTS TO QUERYS *************/
    const SNPLAB=4;
    const SNP=1;
    const MICROSATELLITE=2;
    
    STATIC $SNP = 1;
    STATIC $MICROSATELLITE = 2;
    
    public static function tableName()
    {
        return 'marker';
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
            [['Marker_Type_Id', 'Crop_Id', 'IsActive'], 'integer'],
            [['Crop_Id', 'IsActive'], 'required'],
            [['ShortSequence', 'LongSequence'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['Name'], 'unique'],
            [['Marker_Type_Id'], 'safe']
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Marker_Id' => Yii::t('app', 'Marker  ID'),
            'Name' => Yii::t('app', 'Name'),
            'ShortSequence' => Yii::t('app', 'Short Sequence'),
            'LongSequence' => Yii::t('app', 'Long Sequence'),
            'IsActive' => Yii::t('app', 'Is Active'),
            'CropName' => Yii::t('app', 'Cultivo'),
        ];
    }
    
    public function searchByIdsMarkers($vChecks)
    {
        $query = Marker::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       
        $query->andWhere(['in', ["marker.Marker_Id"], $vChecks]);
       
        return $dataProvider;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSnpLabs()
    {
        return $this->hasMany(SnpLab::className(), ['Marker_Id' => 'Marker_Id']);
    }
    
     public function getFingerprintMarkers()
    {
        return $this->hasMany(FingerprintMarker::className(), ['Marker_Id' => 'Marker_Id']);
    }
    
    public function getCrop()
    {
         return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }
    
    public function getMarkerType()
    {
        return $this->hasOne(MarkerType::className(), ['Marker_Type_Id' => 'Marker_Type_Id']);
         
    }
    
    public function getMapResults()
    {
        return $this->hasMany(MapResult::className(), ['Marker_Id' => 'Marker_Id']);
    }

    
    public function deleteLogic() 
    {
        $this->IsActive = 0;
        return $this->update();
    }
    
    public function import_marker($file, $crop, $marker_type)
    {
        if($marker_type == 2)
        {
            $objPHPExcel = new PHPExcel();

            set_time_limit(60*5);
            ini_set ( "memory_limit" , - 1 );
            //ini_set("max_execution_time", "7200");
            $result = array("success" => NULL, "fail" => NULL, "fail_document" => NULL,  "fail_total" => NULL, "markers_duplicates" => NULL);

            $Reader = PHPExcel_IOFactory::createReaderForFile($file);

            $Reader -> setReadDataOnly (true);
            $ObjXLS = $Reader ->load( $file );
            $ObjXLS->setActiveSheetIndex(0);

            $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
            //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
            $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);
                       
            for($row=1; $row <= $lastRow; $row++)
            {
                    for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
                    {
                            $new_matriz[$row][$col] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, $row)->getValue();
                    }
            }
            $length = count($new_matriz);
           $connection = \Yii::$app->db;
            /**********************   DUPLICATES     *************************/
            $laquery = "SELECT s.Name FROM marker s where s.Name in (";
             for($l = 1; $l <= $length; $l++)
             {
                if($l == $length)
                   $laquery .= '"'.$new_matriz[$l][0].'");';
                else
                   $laquery .= '"'.$new_matriz[$l][0].'",';
             }
             
             if (($result_duplicate = $connection->createCommand($laquery)->queryAll() ) != NULL)
             {
                 if(count($result_duplicate) == ($length)) //por el encabezado del excel, se empieza del 2
                 {  
                    $result['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong>The file  can not be uploaded</strong>. All the Markers are uploaded.</div>";    
                    unlink($file);
                    return $result;
                 } 
                 $new_m=$this->order_matriz ($new_matriz,$result_duplicate);
             }else         
                $new_m= array_values($new_matriz);
             /******************************************************************/
            
            $laquery = "INSERT INTO marker (Name, Marker_Type_Id, Crop_Id, IsActive) VALUES";
            
            for($l = 0; $l < count($new_m) ; $l++)
            {              
                $laquery .= '("'.$new_m[$l][0].'", '.$marker_type.','.$crop.',';

                if($l == (count($new_m)-1))
                        $laquery.= "1)";
                else
                        $laquery.= "1),";

                $markers[] = $new_m[$l][0];

            }
           
            if($connection->createCommand($laquery)->execute())
            {
                $result['success'] = "<div class='alert alert-success' id='notifications'> <strong>".count($markers)." of ". count($new_matriz)." SNPs where imported successfully!</strong>.</div>";
                if($result_duplicate != NULL)
                    {  
                        $result['fail'] = "<strong>".count($result_duplicate)." Markers already exist in the Database</strong>.";
                        $result['marker_duplicates'] = $result_duplicate;
                    }
            }
            else
            {
                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. Please verify the file and try again.</div>";
            }
            
            return $result;
            
        }
        else
        {
            $objPHPExcel = new PHPExcel();

            set_time_limit(60*5);
            ini_set ( "memory_limit" , - 1 );
            //ini_set("max_execution_time", "7200");
            $result = array("success" => NULL, "fail" => NULL, "fail_document" => NULL,  "fail_total" => NULL, "markers_duplicates" => NULL);

            $Reader = PHPExcel_IOFactory::createReaderForFile($file);

            $Reader -> setReadDataOnly (true);
            $ObjXLS = $Reader ->load( $file );
            $ObjXLS->setActiveSheetIndex(0);

            $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
            //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
            $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);

            /***************   CONTROL DE DOCUMENTO CORRECTO ***************/
            for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
            {
                $headers[] =	$ObjXLS->getSheet(0)->getCellByColumnAndRow($col, 1)->getValue(); 

            }

           if(($validate_document = $this->validateDocument($headers)) == false)
            {    $result["fail_document"] = "<strong>THE DOCUMENT FORMAT IS NOT VALID</strong>.
                     <br> Remember that the header must have the following order:<br>
                    <li> Marker original name </li>
                    <li> Short sequence </li>
                    <li> Long sequence </li>";

            unlink($file);
            return $result;
            }

            unset($validate_document);

            /**************************************************************/

            for($row=2; $row <= $lastRow; $row++)
            {
                //ini_set ( "memory_limit" , - 1 );
                    for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
                    {
                        $values[$row][$col] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, $row)->getValue();   
                    }
            }

            $length = count($values); //8698
            $width = array_map( 'count',  $values); //9

            $ObjXLS->disconnectWorksheets();
            unset($ObjXLS, $objPHPExcel);

            $connection = \Yii::$app->db;

            /**********************   DUPLICATES     *************************/
           $laquery = "SELECT s.Name FROM marker s where s.Name in (";
            for($l = 2; $l <= ($length+1); $l++)
            {
               if($l == ($length+1))
                  $laquery .= '"'.$values[$l][0].'");';
               else
                  $laquery .= '"'.$values[$l][0].'",';
            }

            if (($result_duplicate = $connection->createCommand($laquery)->queryAll()) != NULL)
            {
                if(count($result_duplicate) == ($length)) //por el encabezado del excel, se empieza del 2
                {  
                   $result['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong>The file  can not be uploaded</strong>. All the Markers are uploaded.</div>";    
                   unlink($file);
                   return $result;
                }
                $new_matriz=$this->order_matriz ($values,$result_duplicate);
            }
            else
                $new_matriz=$values;

            $new_matriz= array_values($new_matriz);
            /******************************************************************/

            $laquery = "INSERT INTO marker (Name, Marker_Type_Id, Crop_Id, Shortsequence,LongSequence,IsActive) VALUES";
            //$exist_query = "";
            $markers="";
            //$snp_exist = array();

            for($l = 0; $l < count($new_matriz) ; $l++)
            {              
                    $laquery .= '("'.$new_matriz[$l][0].'", '.$marker_type.','.$crop.' ,"'.$new_matriz[$l][1].'","'.$new_matriz[$l][2].'",';

                    if($l == (count($new_matriz)-1))
                            $laquery.= "1)";
                    else
                            $laquery.= "1),";

                    $markers[] = $new_matriz[$l][0];

            }
            $msj="";

           //print_r($laquery); exit;
            $laquery .= " ON DUPLICATE KEY UPDATE Name=Name;";
            //print_r($laquery); exit;
            if($connection->createCommand($laquery)->execute())
                {
                    $result['success'] = "<div class='alert alert-success' id='notifications'> <strong>".count($markers)." of ". count($values)." SNPs where imported successfully!</strong>.</div>";
                    if($result_duplicate != NULL)
                    {  
                        $result['fail'] = "<strong>".count($result_duplicate)." Markers already exist in the Database</strong>.";
                        $result['marker_duplicates'] = $result_duplicate;
                    }
                }
                else
                {
                    $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. Please verify the file and try again.</div>";
                }
            set_time_limit(30);
            unlink($file);
            return $result;
        }
    }
    
    private function order_matriz($values, $duplicates)
    {
        $new = array();
        
        foreach($duplicates as $d)
           $new_dup[] = $d["Name"];
      
        foreach($values as $v)
        { 
           $i = array_search( $v[0] , $new_dup);
           
            if( $i  !== false)
            {
               unset($new_dup[$i]);
               //$p = array_values($new_dup);
            }else
            {
               $new[] = $v;
            }
           
        }
        return $new;
    }
    
    private function validateDocument($headers)
    {
        $ok = true;
               
        if (count($headers) != 3)
        {
            $ok = false;
            return $ok;
        }
        
        foreach($headers as $k => $val)
        { 
            if ($k == 0 and $val != "Marker original name")
                $ok = false;
            if ($k == 1 and $val != "Short sequence")
                $ok = false;
            if ($k == 2 and $val != "Long sequence")
                $ok = false;
//            if ($k == 3 and $val != "QTL associated")
//                $ok = false;
//            if ($k == 4 and $val != "Physical position in Advanta LG")
//                $ok = false;
                      
        }

        return $ok; 
    }
    
    public function deleteByIds($Checks)
    {
       $connection = \Yii::$app->db;
       $Query = "UPDATE marker SET IsActive=0 WHERE Marker_Id IN (". implode(',',array_values($Checks)) .")";
       try{
          
            $connection->createCommand($Query)->execute();
       }catch(Exception $e){	print_r($e); exit;	};
    }
}


