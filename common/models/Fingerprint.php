<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Cell;
use common\models\FingerprintMaterial;
use common\models\FingerprintSnp;
use common\models\FingerprintResult;


/**
 * This is the model class for table "fingerprint".
 *
 * @property string $Fingerprint_Id
 * @property string $Name
 * @property string $DateCreated
 * @property string $Project_Id
 * @property integer $IsActive
 *
 * @property FingerprintMaterial[] $fingerprintMaterials
 * @property FingerprintSnp[] $fingerprintSnps
 */
class Fingerprint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fingerprint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DateCreated'], 'safe'],
            [['Project_Id', 'IsActive'], 'integer'],
            [['Name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }
    
    public function attributeLabels()
    {
        return [
            'Fingerprint_Id' => Yii::t('app', 'Fingerprint  ID'),
            'Name' => Yii::t('app', 'Name'),
            'DateCreated' => Yii::t('app', 'Date Created'),
            'Project_Id' => Yii::t('app', 'Project  ID'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintMaterials()
    {
        return $this->hasMany(FingerprintMaterial::className(), ['Fingerprint_Id' => 'Fingerprint_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintSnps()
    {
        return $this->hasMany(FingerprintSnp::className(), ['Fingerprint_Id' => 'Fingerprint_Id']);
    }
    	
    public function getCrop()
    {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }
    
    public function deleteLogic() 
    {
        $query = "UPDATE fingerprint SET IsActive=0 WHERE Fingerprint_Id=".$this->Fingerprint_Id.";";
        $query .= "UPDATE fingerprint_material SET IsActive=0 WHERE Fingerprint_Id=".$this->Fingerprint_Id.";";
        $query .= "UPDATE fingerprint_marker SET IsActive=0 WHERE Fingerprint_Id=".$this->Fingerprint_Id.";";
        $query .= "UPDATE fingerprint_result SET IsActive=0 WHERE Fingerprint_Id=".$this->Fingerprint_Id.";";
        
        $connection = \Yii::$app->dbGdbms;
        //print_r($query); exit;
        return $connection->createCommand($query)->execute();
      
    }
    
    public function import_file($file, $test= null, $crop)
    {
        ini_set("memory_limit", -1);
        $have_marker = Marker::find()->where(["IsActive"=>1])->all();
        if(!$have_marker )
        {
            $result['FP']['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong>Fingerprints can not be imported if the SNPs LABs table is empty .</strong>.</div>";    
            return $result;
        }
        
        $objPHPExcel = new PHPExcel();

        ini_set ( "memory_limit" , - 1 );
        ini_set("max_execution_time", "7200");
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader -> setReadDataOnly (true);
        $ObjXLS = $Reader ->load( $file );
        
        $sheetNames = $ObjXLS->getSheetNames();
        
        //print_r(count($sheetNames)); exit;
        
        if(count($sheetNames) == 2)
        {
           foreach($sheetNames as $key => $v)
           {
              
                $ObjXLS->setActiveSheetIndex($key);
                $name = $v;
                $lastCol = $ObjXLS->getSheet($key)->getHighestColumn();
                //$dimensiones =  $ObjXLS->getActiveSheet()->calculateWorksheetDimension();
                $lastRow = $ObjXLS->getSheet($key)->getHighestRow();			
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);  
                $count_cols = 0;
                $count_rows = 0;
               //print_r( $highestColumnIndex); exit;
                 /*========== conteo real de filas y columnas ============*/
                for($col=0; $col <= $highestColumnIndex; $col++) //Iteracion para el conteo de encabezados
                {
                    if($ObjXLS->getSheet($key)->getCellByColumnAndRow($col, 1)->getValue() != '')
                    
                      ++$count_cols;
                    
                    else
                        break;              
                }
              //exit;
                for($row=1; $row <= $lastRow; $row++) //Iteracion para el conteo de encabezados
                {
                    if($ObjXLS->getSheet($key)->getCellByColumnAndRow(1, $row)->getValue() != '')
                      ++$count_rows;
                    else
                        break;              
                }
                 /*========== ============ ============ ============ ===*/
                $index_sheet = $key == 0 ? 2 : 1;
                if($key == 0)
                {
                    for($row =  $index_sheet  ; $row <= $count_rows; $row++)
                    {
                            //ini_set ( "memory_limit" , - 1 );
                            for($col=0; $col < $count_cols; $col++) //Iteracion para el control ascii que va de la A a la Z
                            {
                                    $values[$row][$col] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
                            }

                    }
                }else{
                    for($row =  $index_sheet  ; $row <= $lastRow; $row++)
                    {
                            //ini_set ( "memory_limit" , - 1 );
                            for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el control ascii que va de la A a la Z
                            {
                                    $values[$row][$col] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
                            }
                    }
                }
                //print_r($values);
               
               if($key == 0)
                    $result['materials'] = $this->import_materials($values, $crop);
                else{
                    if ($test ==1)
                        
                        $result['FP'] =  $this->query_string_import($values, $test, $crop);
                     else 
                        $result['FP'] =  $this->query_string_import($values, null, $crop);
                }
           }
        }else{
             $result['fail_total'] = "<div class='alert alert-danger' id='notifications_danger'>"
                                                            . "<div id='close'> X </div>"
                                                            . "<strong>The file can not be loaded. The file does not respect the two necessary sheets of Lines and Fingerprint.</strong>"
                                                            . " Please modify the file and try import it again.</div>";
             return $result; 
        }
        $ObjXLS->disconnectWorksheets();
        unset($ObjXLS, $objPHPExcel);
       // print_r($result); exit;
       return ($result); exit;
        //if($sheet_materials)        
    }
          
    private function import_materials($values, $crop)
    {
        $length=count($values); 
        $width=array_map( 'count',  $values); //394
                    
        $connection = \Yii::$app->dbGdbms;
        
        /**********************   DUPLICATES     *************************/
        $laquery = "select m.Name from material_test m where m.Name in (";
      
       //print_r($values); exit;
        for($l = 2; $l <= ($length+1); $l++)
        {
           if($l == ($length+1))
              $laquery .= '"'.$values[$l][0].'");';
           else
              $laquery .= '"'.$values[$l][0].'",';
        }
       //print_r($laquery); exit;
        if (($result_duplicate = $connection->createCommand($laquery)->queryAll()) != NULL)
        {  
            if((count($result_duplicate) == $length)) //por el encabezado del excel, se empieza del 2
            { 
               $result['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong> All materials in this file are loaded.</strong></div>";    
               return $result;
            }
            $new_matriz=$this->order_matriz ($values,$result_duplicate);
        }
        else
            $new_matriz=$values;
        
        $new_matriz= array_values($new_matriz); 
       //print_r($new_matriz); exit;
        /******************************************************************/
                
        $laquery = "INSERT INTO material_test (Name, Crop_Id, CodeType, OldCode_1, OldCode_2, Owner, Material, HeteroticGroup, cms, Pedigree, Origin, Country, Type, IsActive) VALUES";
        //$exist_query = "";
        $snps="";
        //$snp_exist = array();
      
        for($l = 0; $l < count($new_matriz) ; $l++)
        {              
                $laquery .= '("'.$new_matriz[$l][0].'",'
                                .$crop.',"'
                                .$new_matriz[$l][1].'","'
                                .$new_matriz[$l][2].'","'
                                .$new_matriz[$l][3].'","'
                                .$new_matriz[$l][4].'","'
                                .$new_matriz[$l][5].'","'
                                .$new_matriz[$l][6].'","';
                                
                 $laquery .= $new_matriz[$l][7] == "NA" || $new_matriz[$l][7] == NULL ? 'NA","' :$new_matriz[$l][7].'","'; 
                 $laquery .= $new_matriz[$l][8].'","'
                            .$new_matriz[$l][9].'","'
                            .$new_matriz[$l][10].'","'
                            .$new_matriz[$l][11].'",';
                 
                
                if($l == (count($new_matriz)-1))
                        $laquery.= "1);";
                else
                        $laquery.= "1),";
                                
                $materials[] = $new_matriz[$l][0];
            
        }
        //print_r($laquery); exit;
        if($connection->createCommand($laquery)->execute())
            {
		//unlink($file);
                $result['success'] = "<div class='alert alert-success' id='notifications2'> <strong>".count($materials)." of ". count($values) ." new Lines were imported successfully !</strong>.</div>";
                if($result_duplicate != NULL)
                {  
                    $result['fail'] = "<div class='alert alert-danger' id='notifications_danger4'><div id='close4'> close </div><strong>".count($result_duplicate)." lines already exist in the database</strong>.";
                    $result['snps_duplicates'] = $result_duplicate;
                }
            }
            else
            {
                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file could not be uploaded . Please check the file and try again.</div>";
                
            }
            set_time_limit(30);

            return $result;
    }
    
    private function validateDocument($headers)
    {
        $ok = true;
               
        if (count($headers) != 12)
        {
            $ok = false;
            return $ok;
        }
         
        foreach($headers as $k => $val)
        { 
            if ($k == 0 and $val != "MaterialID")
                $ok = false;
            if ($k == 1 and $val != "CodeType")
                $ok = false;
            if ($k == 2 and $val != "Old.Code1")
                $ok = false;
            if ($k == 3 and $val != "Old.Code2")
                $ok = false;
            if ($k == 4 and $val != "Owner")
                $ok = false;
            if ($k == 5 and $val != "Material")
                $ok = false;
            if ($k == 6 and $val != "HeteroticGroup")
                $ok = false;
            if ($k == 7 and $val != "cms")
                $ok = false;
            if ($k == 8 and $val != "Pedigree")
                $ok = false;
            if ($k == 9 and $val != "Origin")
                $ok = false;
            if ($k == 10 and $val != "Country")
                $ok = false;
            if ($k == 11 and $val != "Type")
                $ok = false;
         }

        return $ok; 
    }
    
     private function order_matriz($values, $duplicates)
    {
       $new = array();
       foreach($values as $v)
        {  
           $token = false;  
            for($i2=0; $i2 < (count($duplicates)); $i2++)
            {
               
                if($v[0] == $duplicates[$i2]['Name'])
                {  
                   $token = true;
                   //print_r($duplicates);
                   unset($duplicates[$i2]);
                  
                   $duplicates = array_values($duplicates);
                   //print_r($duplicates); exit;
                   $i2= 0;
                   break;
                   //$i2 = 0;                   
                }                
            }
        //print_r($v); exit;
        if($token == false)
        //print_r($values[$i]);
        $new[] = $v;
        
        }
      
       return $new;
    }
    
    private function query_string_import($values, $example = null, $crop)
    {		
        $length=count($values); 
        $width=array_map( 'count',  $values); //394

        if(($exist = $this->findFpByName($values[3][0])) != null)
        {
            $result['fail_total'] = "<div class='alert alert-danger' id='notifications_danger'>"
                    . "<div id='close'> X </div>"
                    . "The Fingerprint can not be uploaded. Already exist in te DataBase</div>";
            return $result; 
        }
        
        $laQuery = "INSERT INTO fingerprint (Name, Project_Id, Crop_Id, DateCreated, IsActive) VALUES ('".$values[3][0]."',NULL,".$crop." ,'".date("Y-m-d H:i:s")."',1);
                    SET @fingerprint_id = LAST_INSERT_ID();";

        $connection = \Yii::$app->dbGdbms;
        $not_markers = "";
        for($rows=1; $rows <= $length; $rows++)
        {
            if($rows == 1)
            {
                    for($col_snp=4; $col_snp < ($width[$rows]); $col_snp++ )
                    {
                            //$array_snps[] = $values[$rows][$col_snp]; 
//                            $snp    = SnpLab::find()
//                                                ->where(['LabName'=> trim($values[$rows][$col_snp]), 'IsActive' => 1 ])
//                                                ->one();
                            $marker = Marker::find()
                                                ->where(['Name'=> trim($values[$rows][$col_snp]), 'IsActive' => 1 ])
                                                ->one();                            
                            if($marker)
                            {
                                $array_snps[$col_snp] = $marker->Marker_Id;
                                $array_vector= explode(', ', $values[$rows+1][$col_snp]);
                                $array_quality[$col_snp] = json_encode($array_vector);
                            }
                            else
                            { 
                                $not_markers[] = $values[$rows][$col_snp];
                            } 
                    }
                    if($not_markers)  
                    {
                        $result['not_markers'] = $not_markers;
                        return $result;
                        exit;
                    }    
                    $rows=2; 
//                    for($col_qa=4; $col_qa < ($width[$rows]); $col_qa++ )
//                    {
//                            if($values[$rows][$col_qa])
//                            {
//                                    $array_vector= explode(', ', $values[$rows][$col_qa]);
//                                    $array_quality[$col_qa] = json_encode($array_vector);
//                            }
//                    }
            $laQuery2="";
            }
            else // SI ES MAS DE LA 2da VUELTA, EMPIEZA A RECOGER LOS RESuLTAODS DE LOS FINGERPRINT
            {
				
                for($col_allele=0; $col_allele < ($width[$rows]); $col_allele++ ) // rows=3 of fingerprint
                {		
					
                    if($col_allele < 4)	//materials
                    {		
                        if(isset($values[$rows][1])) // Si hay material en el excel
                        {
                           //print_r($values[$rows][1]); exit;
                            $material_trim2 =$values[$rows][2];
                            $material_trim =trim(preg_replace("/&#?[a-z0-9]+;/i","",$values[$rows][1]));
                            //$material_trim2 = //str_replace("&nbsp;", '', $material_trim);
                            //$material_trim2 = utf8_decode($material_trim2);
                            //$material_trim2 = utf8_decode($material_trim2);
                            $material_trim = utf8_decode($material_trim);
							$asd = $values[$rows][2];
							
							$material_trim2 = iconv( "ASCII", "UTF-8//IGNORE",  $asd);
							
							//$material_trim2 = utf8_decode($material_trim2);							
							
                            //if(strpos($values[$rows][2], "CA13-1037-6") !== FALSE){var_dump($material_trim2); exit;}
							
							//if(strpos($values[$rows][2], "1VK" !== FALSE)) die();
                            $Material = MaterialTest::find()
                                                     ->where(['Name'=>$material_trim, 'IsActive' => 1])
                                                     ->one();
                               
								if($Material)
                                {
                                    $laQuery .= "INSERT INTO fingerprint_material (Fingerprint_Id, Material_Id, Material_Test_Id, TissueOrigin, SeedOrigin, IsActive) 
                                                VALUES (@fingerprint_id, NULL, ".$Material->Material_Test_Id.",'".$material_trim2."', '', 1);
                                                SET  @material_id".$rows." = LAST_INSERT_ID();";
                                }else
                                {	

                                   $result['fail_total'] = "<div class='alert alert-danger' id='notifications_danger'>"
                                            . "<div id='close'> X </div>"
                                            ."<strong>The Fingerprint can not be uploaded</strong>. Material". $values[$rows][1] ." is not uploaded.  Please create it and import the file again."
                                            ."</div>";
                                    return $result;    

                                }

                                $col_allele=3;							
                        }else
                        {						
                            $result['fail_total'] = "<div class='alert alert-danger' id='notifications_danger2'>"
                                            . "<div id='close2'> X </div>"
                                            ."<strong> The Fingerprint can not be uploaded</strong>. Material Is not defined  in row: ".$rows. ". Please create it and import the file again."
                                            ."</div>";
                            return $result; 

                                $col_allele=3;
                        }// if que controla la carga e encabezado del material y sus datos										
						
                    }
                    else
                    {// END if si es col_allele es menor o igual a 4
				
                        if(isset($array_snps[$col_allele]))
                        {
                            $Allele_Id='';
                            switch ($values[$rows][$col_allele]) 
                            {
                                case "Allele 1":
                                                $Allele_Id = 1;
                                                break;
                                case "Allele 2":
                                                $Allele_Id = 2;
                                                break;
                                case "Allele 1 & 2":
                                                $Allele_Id = 3;
                                                break;
                                default:
                                                $Allele_Id = 4;
                                                break;
                            }

                            if($rows == 3)
                            {
                                $laQuery .= "INSERT INTO fingerprint_marker (Marker_Id, Fingerprint_Id, Quality, IsActive) 
                                                                      VALUES ( ".$array_snps[$col_allele]." , @fingerprint_id, '".$array_quality[$col_allele]."', 1);
                                                         SET @marker_id".$col_allele."= LAST_INSERT_ID();";
                                                         //DECLARE @marker_id".$col_allele." INT; SET @marker_id".$col_allele." = @@IDENTITY;"; 
                                $laQuery .= "INSERT INTO fingerprint_result (Fingerprint_Id, Fingerprint_Material_Id, Fingerprint_Marker_Id, Allele_Id, IsActive) 
                                                                      VALUES (@fingerprint_id, @material_id".$rows." , @marker_id".$col_allele.", ".$Allele_Id.",1);";																					
                            }elseif($rows >= 4 and $col_allele == 4)
                            {
                                    $laQuery .= "INSERT INTO fingerprint_result (Fingerprint_Id, Fingerprint_Material_Id, Fingerprint_Marker_Id, Allele_Id, IsActive) 
                                                   VALUES (@fingerprint_id, @material_id".$rows." , @marker_id".$col_allele.", ".$Allele_Id.",1),";
                            }
                            elseif($col_allele == $width[$rows]-1){
                                    $laQuery .= "( @fingerprint_id, @material_id".$rows." , @marker_id".$col_allele.", ".$Allele_Id.",1);";
                            }elseif($col_allele < $width[$rows]-1){
                                    $laQuery .= "( @fingerprint_id, @material_id".$rows." , @marker_id".$col_allele.", ".$Allele_Id.",1),";
                            }
                        }
                    }

                }//for que recorre las filas a partir de las 3
            }// if que pregunta si la primera veulta
        }// for principal 
        //print_r($laQuery ); exit;
        if($connection->createCommand($laQuery)->execute())
        {
            $result['succes'] = "<div class='alert alert-success' id='notifications'>The Fingerprint is loaded successfully.!</strong>.</div>";
            
        }
        else
        {
            $result['fail_total'] = "<div class='alert alert-danger' id='notifications_danger'><div id='close'> X </div>The file  can not be uploaded. Please check the file and try again.</div>";
        }
        return $result;
        exit;				
    }
    
//    public function getMateriasResult($id)
//    {
//       $connection = \Yii::$app->dbGdbms;
//       $Query = "SELECT m.Name, f.TissueOrigin, f.Fingerprint_Material_Id from fingerprint_material f
//                INNER JOIN material_test m ON m.Material_Test_Id=f.Material_Test_Id and m.IsActive=1
//                WHERE f.IsActive=1 and f.Fingerprint_Id =".$id;
//       $fingerprint_material = $connection->createCommand($Query)->queryAll();
//       //print_r($fingerprint_material ); exit;
//       $fmaterial_tissue = array();
//       if($fingerprint_material)
//       {
//           $i = 0;
//           foreach($fingerprint_material as $fm)
//           {
//               $fmaterial_name = $fm['Name']." ( ".$fm['TissueOrigin']." )";
//               $fmaterial_id = $fm['Fingerprint_Material_Id'];
//               
//              // [
//            //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
//            //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
//            // ]
//               $result[]= ['id'=>'<$fmaterial_id>', 'name'=>'<$fmaterial_name>'];
//               
//           }
//       } 
//       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//       return ["output" => $result];
//       //print_r($fmaterial_tissue); exit;
//    }
    
    public function getMaterialResult($crop = null)
    {
       $connectionGdbms = \Yii::$app->dbGdbms;
       $Query = "SELECT  m.Name, f.TissueOrigin, m.Material_Test_Id from fingerprint_material f
                INNER JOIN material_test m ON m.Material_Test_Id=f.Material_Test_Id and m.IsActive=1
                INNER JOIN fingerprint f1 ON f1.Fingerprint_Id=f.Fingerprint_Id  and f1.Crop_Id=".$crop."
                WHERE f.IsActive=1 GROUP BY m.Name ORDER BY m.Name ";
       $fingerprint_material = $connectionGdbms->createCommand($Query)->query();
       
       $fmaterial_tissue = array();
       if($fingerprint_material)
       {
           $i = 0;
           foreach($fingerprint_material as $fm)
           {
               $fmaterial_tissue[$i]["name"] = $fm['Name']." ( ".$fm['TissueOrigin']." )";
               $fmaterial_tissue[$i]["id"] = $fm['Material_Test_Id'];
               $i++;
           }
       } 
       
       return $fmaterial_tissue;
       //print_r($fmaterial_tissue); exit;
    }
    
    public function findFpByName($name)
    {
        return  $this->find()
                    ->where(["fingerprint.Name" => $name])
                    ->one();
    }
    
    static function getMaterialsInfoByParentesSelected($cropId)
    {
        $connectionGdbms = \Yii::$app->dbGdbms;
        $Query = "SELECT  m.Name, f.TissueOrigin, m.Material_Test_Id from fingerprint_material f
                 INNER JOIN material_test m ON m.Material_Test_Id=f.Material_Test_Id and m.IsActive=1
                 INNER JOIN fingerprint f1 ON f1.Fingerprint_Id=f.Fingerprint_Id  and f1.Crop_Id=".$cropId."
                 WHERE f.IsActive=1 GROUP BY m.Name ORDER BY m.Name ";
        return $connectionGdbms->createCommand($Query)->query();
    }
}
