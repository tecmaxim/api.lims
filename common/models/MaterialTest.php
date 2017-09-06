<?php

namespace common\models;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Cell;
/**
 * This is the model class for table "material_test".
 *
 * @property integer $Material_Test_Id
 * @property integer $Crop_Id
 * @property string $Name
 * @property string $CodeType
 * @property string $OldCode_1
 * @property string $OldCode_2
 * @property string $Owner
 * @property string $Material
 * @property string $HeteroticGroup
 * @property integer $cms
 * @property string $Pedigree
 * @property string $Origin
 * @property string $Country
 * @property string $Type
 * @property integer $IsActive
 */
class MaterialTest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $CopyMaterials;

    public static function tableName()
    {
        return 'material_test';
    }
    
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }

       public function rules()
   {
       return [
              
            [['Crop_Id'], 'required'],
            [['Crop_Id', 'IsActive'], 'integer'],
            [['CodeType', 'Pedigree'], 'string'],
            [['Name', 'PreviousCode', 'Owner', 'Generation'], 'string', 'max' => 50],
            //[['Name', 'PreviousCode', 'Owner', 'Generation'], 'string', 'max' => 50],
            [['HeteroticGroup'], 'string', 'max' => 2],
            //[['Pedigree'], 'string', 'max' => 150],
            [['Type'], 'string', 'max' => 10],
            [['Name', 'Crop_Id'], 'unique', 'targetAttribute' => ['Name', 'Crop_Id'], 'message' => 'The combination of Name and Crop ID has already been taken.'],
            [['CopyMaterials'], 'safe'],
            [['CopyMaterials'], 'required', 'on' => 'controlMaterials']
           
       ];
   }
   /**
    * @inheritdoc
    */
 
   public function attributeLabels()
   {
       return [
           'Material_Test_Id' => Yii::t('app', 'Material ID'),
           'Name' => Yii::t('app', 'MaterialID'),
           'Crop_Id' => Yii::t('app', 'Crop'),
           'Material_Test_Id' => Yii::t('app', 'Material Test ID'),
           'Name' => Yii::t('app', 'Name'),
           'Crop_Id' => Yii::t('app', 'Crop ID'),
           'CodeType' => Yii::t('app', 'Code Type'),
           'PreviousCode' => Yii::t('app', 'Previous Code'),
           'OldCode_1' => Yii::t('app', 'Old Code 1'),
           'OldCode_2' => Yii::t('app', 'Old Code 2'),
           'Owner' => Yii::t('app', 'Owner'),
           
           'Material' => Yii::t('app', 'Material'),
           'HeteroticGroup' => Yii::t('app', 'Heterotic Group'),
           'cms' => Yii::t('app', 'Cms'), 
           'Pedigree' => Yii::t('app', 'Pedigree'),
           'Origin' => Yii::t('app', 'Origin'), 
           'Country' => Yii::t('app', 'Country'), 
           'Type' => Yii::t('app', 'Type'),
           'IsActive' => Yii::t('app', 'Is Active'),
           'CopyMaterials' => Yii::t('app', '')
       ];
   }

    public function getCrop()
    {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }
    
    /** 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getFingerprintMaterials() 
   { 
       return $this->hasMany(FingerprintMaterial::className(), ['Material_Test_Id' => 'Material_Test_Id']); 
   } 
    
    public function deleteByIds($Checks)
    {
       $connection = \Yii::$app->dbGdbms;
       $Query = "UPDATE material_test SET IsActive=0 WHERE Material_Test_Id IN (". implode(',',array_values($Checks)) .")";
       try{
            $connection->createCommand($Query)->execute();
       }catch(Exception $e){	print_r($e); exit;	};
    }
    
    public function updateMaterials($file, $crop)
    {
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", "7200");
        $objPHPExcel = new PHPExcel();

        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader -> setReadDataOnly (true);
        $ObjXLS = $Reader ->load( $file );
        
        $ObjXLS->setActiveSheetIndex(0);
        $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
        $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);  
        
        $count_cols = $this->getCols($ObjXLS, $highestColumnIndex);
        $count_rows = $this->getRows($ObjXLS, $lastRow);
           
        if(($control = $this->validateDocument($ObjXLS, $count_cols)) == false)
        {
            $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>"
                                        ."<strong>THE DOCUMENT FORMAT IS NOT VALID</strong>.
                                        <br> Remember that the header must have the following order:<br>"
                                        ."<li>MaterialID</li>"
                                        ."<li>CodeType</li>"
                                        ."<li>PreviousCode</li>"
                                        ."<li>Owner</li>"
                                        ."<li>Generation</li>"
                                        ."<li>HeteroticGroup</li>"
                                        ."<li>Pedigree</li>"
                                        ."<li>Type</li>"
                                        . "</div>";
            return $result;
        }
        //Name, Crop_Id, CodeType, PreviousCode, Owner, Generation, HeteroticGroup, Pedigree, Type
        
        for($row = 2  ; $row <= $count_rows; $row++)
        {
            for($col=0; $col < $count_cols; $col++) //Iteracion para el control ascii que va de la A a la Z
            {
                $values[$row][$col] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
                 
        $ObjXLS->disconnectWorksheets();
        unset($ObjXLS, $objPHPExcel);
        
        $length=count($values); 
        $width=array_map( 'count',  $values); //394
                    
        $counts = 0;
        $not_save = array();
        $new_matriz = array_values($values);
        for($l = 0; $l < count($new_matriz) ; $l++)
        {              
            $new_material = $this->normalizeString($new_matriz[$l][0]);
            $material = MaterialTest::findOne(["Name"=>$new_material]);
            if($material)
            {
                $material->CodeType = $new_matriz[$l][1];
                $material->PreviousCode = $new_matriz[$l][2];
                $material->Owner = $new_matriz[$l][3];
                $material->Generation =$new_matriz[$l][4];
                $material->HeteroticGroup = $new_matriz[$l][5];
                $material->Pedigree = $new_matriz[$l][6];
                $material->Type = $new_matriz[$l][7];
                if($material->save())
                    $counts++;
                else
                    $not_save[] = $new_matriz[$l][0]; 
            }else
                $not_exist[]=$new_material;
        }
        
        if($counts > 0)
            $result['success'] = "<div class='alert alert-success' id='notifications2'> <strong>".$counts." of ". count($new_matriz) ." Materials were updated successfully!</strong>.</div>";
  
        if(count($not_save) > 0)
            $result['not_updated'] = $not_save;
        
        if(count($not_exist) > 0)
            $result['not_updated'] = $not_exist;
         
        return $result;
    }
    
    public function importMaterials($file, $crop)
    {
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", "7200");
        $objPHPExcel = new PHPExcel();

        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader -> setReadDataOnly (true);
        $ObjXLS = $Reader ->load( $file );
        
        $ObjXLS->setActiveSheetIndex(0);
        $lastCol = $ObjXLS->getActiveSheet()->getHighestColumn();
        $lastRow = $ObjXLS->getActiveSheet()->getHighestRow();			
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastCol);  
        
        $count_cols = $this->getCols($ObjXLS, $highestColumnIndex);
        $count_rows = $this->getRows($ObjXLS, $lastRow);
           
        if(($control = $this->validateDocument($ObjXLS, $count_cols)) == false)
        {
            $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>"
                                        ."<strong>THE DOCUMENT FORMAT IS NOT VALID</strong>.
                                        <br> Remember that the header must have the following order:<br>"
                                        ."<li>MaterialID</li>"
                                        ."<li>CodeType</li>"
                                        ."<li>PreviousCode</li>"
                                        ."<li>Owner</li>"
                                        ."<li>Generation</li>"
                                        ."<li>HeteroticGroup</li>"
                                        ."<li>Pedigree</li>"
                                        ."<li>Type</li>"
                                        . "</div>";
            return $result;
        }
        //Name, Crop_Id, CodeType, PreviousCode, Owner, Generation, HeteroticGroup, Pedigree, Type
        
        for($row = 2  ; $row <= $count_rows; $row++)
        {
            for($col=0; $col < $count_cols; $col++) //Iteracion para el control ascii que va de la A a la Z
            {
                    $values[$row][$col] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        
        $ObjXLS->disconnectWorksheets();
        unset($ObjXLS, $objPHPExcel);
        
        $length=count($values); 
        $width=array_map( 'count',  $values); //394
                    
        $connection = \Yii::$app->dbGdbms;
        
        /**********************   DUPLICATES     *************************/
        $duplicates = $this->getDuplicates($connection, $values, $length);
        if(isset($duplicates['fail_total']))
        {
            return $duplicates;
        }
        
        $new_matriz = $duplicates[0];
        $result_duplicate = $duplicates[1];
        
        /******************************************************************/
                
        $laquery = "INSERT INTO material_test (Name, Crop_Id, CodeType, PreviousCode, Owner, Generation, HeteroticGroup, Pedigree, Type, IsActive) VALUES";
       
        $snps="";
       
        for($l = 0; $l < count($new_matriz) ; $l++)
        {              
            $new_material = $this->normalizeString($new_matriz[$l][0]);
            
            $laquery .= '("'.$new_material.'",'
                            .$crop.',"'
                            .$new_matriz[$l][1].'","'
                            .$new_matriz[$l][2].'","'
                            .$new_matriz[$l][3].'","'
                            .$new_matriz[$l][4].'","'
                            .$new_matriz[$l][5].'","'
                            .$new_matriz[$l][6].'","'
                            .$new_matriz[$l][7].'",';
                              
            if($l == (count($new_matriz)-1))
                $laquery.= "1)";
            else
                $laquery.= "1),";

            $materials[] = $new_matriz[$l][0];
            
        }
        $laquery .= " ON DUPLICATE KEY UPDATE Name=Name;";
        //print_r($laquery); exit;
        $transaction = $connection->beginTransaction();
            
        try
        {
            $afectedRow = $connection->createCommand($laquery)->execute();
            
            $transaction->commit();

            $result['success'] = "<div class='alert alert-success' id='notifications2'> <strong>".$afectedRow." of ".$length." new Lines were imported successfully !</strong>.</div>";
            if($result_duplicate != NULL)
            {  
                $result['fail'] = "<div class='alert alert-info' id='notifications_danger4'><div id='close4'> close </div><strong>".count($result_duplicate)." lines already exist in the database</strong>.";
                $result['mats_duplicates'] = $result_duplicate;
            }
        } catch(\Exception $e) {
                                $transaction->rollBack();  
                                //print_r($e);
                                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file could not be uploaded . Please check the file and try again.</div>";
        }
            
        return $result;
    }
    
    public function getCols($ObjXLS, $highestColumnIndex)
    {
        $count_cols = 0;
        for($col=0; $col <= $highestColumnIndex; $col++) //Iteracion para el conteo de encabezados
        {
            if($ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 1)->getValue() != '')

              ++$count_cols;

            else
                break;              
        }
        
        return $count_cols;
    }
    
    public function getRows($ObjXLS, $lastRow)
    {
        $count_rows = 0;;
        for($row=1; $row <= $lastRow; $row++) //Iteracion para el conteo de encabezados
        {
            if($ObjXLS->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue() != '')
              ++$count_rows;
            else
                break;              
        }
        
        return $count_rows;
    }
    
    public function getDuplicates($connection, $values, $length)
    {
        $laquery = "select m.Name from material_test m where m.Name in (";
      
       //print_r($values); exit;
        for($l = 2; $l <= ($length+1); $l++)
        {
           if($l == ($length+1))
              $laquery .= '"'.$values[$l][0].'");';
           else
              $laquery .= '"'.$values[$l][0].'",';
        }
       
        if (($result_duplicate = $connection->createCommand($laquery)->queryAll()) != NULL)
        {  
            if((count($result_duplicate)) == ($length-1)) // ($length-1 menos el header
            { 
               $result['fail_total'] = "<div class='alert alert-info' id='notifications'><strong> All materials in this file are loaded.</strong></div>";    
               return $result;
            }
            $new_matriz=$this->order_matriz ($values,$result_duplicate);
        }
        else
            $new_matriz=$values;
        
        $array[] = array_values($new_matriz);
        $array[] = $result_duplicate;
        
        return $array;
        
    }
    
    public function normalizeString($string)
    {
        $mat = $string;
        $material_trim2 = iconv( "ASCII", "UTF-8//IGNORE",  $mat);
        $material_trim2 = trim($material_trim2);
        $material_trim2 = str_replace(" ", "", $material_trim2);
        $material_trim2 = strtoupper($material_trim2);
        
        return $material_trim2;
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
                   unset($values[$i2]);
                  
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
    
    private function validateDocument($ObjXLS, $count)
    {  
        for($col=0; $col < $count; $col++) //Iteracion para el control ascii que va de la A a la Z
        {
            $headers[] = $ObjXLS->getActiveSheet()->getCellByColumnAndRow($col, 1)->getValue();
        }
        
        $ok = true;
               
        if (count($headers) != 8)
        {
            $ok = false;
            return $ok;
        }
         
        foreach($headers as $k => $val)
        { 
            //Name, Crop_Id, CodeType, PreviousCode, Owner, Generation, HeteroticGroup, Pedigree, Type
            if ($k == 0 and $val != "MaterialID")
                $ok = false;
            if ($k == 1 and $val != "CodeType")
                $ok = false;
            if ($k == 2 and $val != "PreviousCode")
                $ok = false;
            if ($k == 3 and $val != "Owner")
                $ok = false;
            if ($k == 4 and $val != "Generation")
                $ok = false;
            if ($k == 5 and $val != "HeteroticGroup")
                $ok = false;
            if ($k == 6 and $val != "Pedigree")
                $ok = false;
            if ($k == 7 and $val != "Type")
                $ok = false;
         }
        return $ok; 
    }
    
    //This is a same function of Project Model
    public function normalizeSamplesAsArray($materials) {
        if (strpos($materials, "\n") !== false)
            $array_material = explode("\n", $materials);
        elseif (strpos($materials, " ") !== false)
            $array_material = explode(" ", $materials);
        elseif (strpos($materials, ", ") !== false)
            $array_material = explode(", ", $materials);
        elseif (strpos($materials, "; ") !== false)
            $array_material = explode("; ", $materials);
        else
            $array_material[] = $materials;

        $string_clean = implode(",", $array_material);
        $newString = str_replace(chr(13), "", $string_clean);
        $new_array_clean = explode(",", $newString);
        $length = count($new_array_clean);
        if ($new_array_clean[$length - 1] == "")
            unset($new_array_clean[$length - 1]);

        return $new_array_clean;
    }
}
