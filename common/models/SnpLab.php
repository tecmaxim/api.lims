<?php

namespace common\models;

use Yii;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use common\models\Assaybrand;
use common\models\Barcode;
use common\models\Snp;
//use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "snp_lab".
 *
 * @property string $Snp_lab_Id
 * @property integer $Snp_Id
 * @property string $LabName
 * @property string $PurchaseSequence
 * @property string $AlleleFam
 * @property string $AlleleVicHex
 * @property string $ValidatedStatus
 * @property string $Quality
 * @property string $Box
 * @property string $PositionInBox
 * @property string $PIC
 * @property integer $IsActive
 * @property string $Observation
 *
 * @property Assaybrand[] $assaybrands
 * @property Barcode[] $barcodes
 * @property FingerprintSnp[] $fingerprintSnps
 * @property Snp $snp
 */
class SnpLab extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'snp_lab';
    }
    
    public static function getDb()
    {
        
        return Yii::$app->dbGdbms;
    }

    /**
     * 
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Marker_Id', 'IsActive'], 'required'],
            [['Marker_Id', 'IsActive'], 'integer'],
            [['PurchaseSequence'], 'string'],
            [['PIC'], 'number'],
            [['LabName'],'string', 'max' => 255],
            [['LabName', 'IsActive'], 'unique', 'targetAttribute' => ['LabName', 'IsActive']],
            [['Barcode'],'string'],
            [['AlleleFam', 'AlleleVicHex', 'PositionInBox'], 'string'],
            [['ValidatedStatus'], 'string', 'max' => 50],
            [['Quality'], 'string', 'max' => 100],
            [['Box'], 'safe'],
            [['Observation'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
     public function attributeLabels()
    {
        return [
            'Snp_lab_Id' => Yii::t('app', 'Snp Lab  ID'),
            'Marker_Id' => Yii::t('app', 'Marker  ID'),
            'LabName' => Yii::t('app', 'Lab Name'),
            'PurchaseSequence' => Yii::t('app', 'Purchase Sequence'),
            'AlleleFam' => Yii::t('app', 'Allele Fam'),
            'AlleleVicHex' => Yii::t('app', 'Allele Vic Hex'),
            'ValidatedStatus' => Yii::t('app', 'Validated Status'),
            'Quality' => Yii::t('app', 'Quality'),
            'Box' => Yii::t('app', 'Box'),
            'PositionInBox' => Yii::t('app', 'Position In Box'),
            'PIC' => Yii::t('app', 'Pic'),
            'IsActive' => Yii::t('app', 'Is Active'),
            'Observation' => Yii::t('app', 'Observation'),
            'Barcode' => Yii::t('app', 'Barcodes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssaybrands()
    {
        return $this->hasMany(Assaybrand::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarcodes()
    {
        return $this->hasMany(Barcode::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintSnps()
    {
        return $this->hasMany(FingerprintSnp::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarker()
    {
        return $this->hasOne(Marker::className(), ['Marker_Id' => 'Marker_Id']);
    }

    /**
    * Snp Quality is the average of all its quality runs
    * @return Array $quality An array with the three qualities values. $quality[0], $quality[1], $quality[2]
    */
    public function getCalculatedQuality()
    {
        $quality = null;

        if($this->Quality != NULL)
        {
            $quality = [0, 0, 0];
		
            $qualityData = json_decode($this->Quality);
			
            $q1 = 0;
            $q2 = 0;
            $q3 = 0;
            foreach($qualityData as $q)
            {
                $q = explode('.', $q);

                $q1 += $q[1];
                $q2 += $q[2];
                $q3 += $q[3];
            }
            $quality[0] = $q1 / count($qualityData);
            $quality[1] = $q2 / count($qualityData);
            $quality[2] = $q3 / count($qualityData);
        }

        return $quality;
    }
    /**
    * Snp Quality is the average of all its quality runs
    * @return String $quality
    */
    public function getCalculatedQualityStr()
    {
        $qualityStr = null;
        $quality = $this->getCalculatedQuality();

        if($quality != null)
            $qualityStr = $quality[0] .'.' .$quality[1] .'.' .$quality[2];

        return $qualityStr;
    }
    
    public function deleteLogic() 
    {
        $this->IsActive = 0;
        return $this->save();
    }
    
    public function import_snplab($file, $crop)
    {		
        $objPHPExcel = new PHPExcel();
        $snp_labs = "";
        ini_set ( "memory_limit" , - 1 );
        ini_set("max_execution_time", "4200");

        //$file = "C:\Advanta\SNP_LAB.xlsx";
        $have_marker = Marker::find()->all();
         if(!$have_marker)
         {
             $result['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong>SNP LABs cannot be imported if the SNPs table is empty.</strong>.</div>";    
             unlink($file);
             return $result;
         }
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
            $headers[] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, 1)->getValue(); 

        }

        $count = 0;
        for($col=0; $col < $highestColumnIndex; $col++) //Iteracion para el conteo de encabezados
        {
            if($ObjXLS->getSheet(0)->getCellByColumnAndRow($col, 1)->getValue() != '')
              $count++;
            else
                break;              
        }
       if(($validate_document = $this->validateDocument($headers)) == false or $count != 12)
        {  
           $result["fail_document"] = "<strong>THE DOCUMENT FORMAT IS NOT VALID</strong>.
                 <br> Remember that the header must have the following order :<br>
                <li> Marker original name </li>
                <li> SNP lab name </li>
                <li> Purchase sequence </li>
                <li> Allele FAM </li>
                <li> Allele VIC/HEX </li>
                <li> Barcodes </li>
                <li> Validated </li>
                <li> Quality </li>
                <li> Assay brand </li>
                <li> Box </li>
                <li> Position in box </li>
                <li> PIC </li>
                ";
        unlink($file);
        return $result;
        }
        /********************************************************/
        unset($validate_document);

        for($row=2; $row <= $lastRow; $row++)
        {
                for($col=0; $col < $count; $col++) //Iteracion para el control ascii que va de la A a la Z
                {
                    $values[$row][$col] = $ObjXLS->getSheet(0)->getCellByColumnAndRow($col, $row)->getValue();
                }			 		
        }      

        $ObjXLS->disconnectWorksheets();
        unset($ObjXLS, $objPHPExcel);

        $length=count($values); //8698
        $width=array_map( 'count',  $values); //9

        $connection = \Yii::$app->dbGdbms;

         /**********************   DUPLICATES     *************************/
        $laquery = "SELECT s.LabName from snp_lab s where s.LabName in (";


        for($l = 2; $l <= ($length+1); $l++)
         {
            if($l == ($length+1))
               $laquery .= '"'.$values[$l][1].'");';
            else
               $laquery .= '"'.$values[$l][1].'",';
         }

         if (($result_duplicate = $connection->createCommand($laquery)->queryColumn() ) != NULL)
         {
             if(count($result_duplicate) == ($length)) 
             {  
                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'><strong>The file  can not be uploaded</strong>. All the SNPs LABS are uploaded.</div>";    
                unlink($file);
                return $result;
             }
             $new_matriz=$this->order_matriz ($values,$result_duplicate);
         }
         else
             $new_matriz=$values;

         $new_matriz= array_values($new_matriz);
        //print_r($new_matriz);
         /******************************************************************/

        $not_marker = null;
        $marker_labs = NULL;
        $quot = "'";
        for($l=0; $l < (count($new_matriz)); $l++)
        {

            $model = new SnpLab();

            $whitQuote = strpos($new_matriz[$l][0], "'");
            if($whitQuote !== false)
            { 
                $nameWithoutQuote = str_ireplace ("'", "\'", $new_matriz[$l][0]);
            }else
                 $nameWithoutQuote = $new_matriz[$l][0];

            $model->LabName = $new_matriz[$l][1];
            //$model-
//                    $connection = \Yii::$app->db;
//                    
//                     try{
//                         $snp = $connection->createCommand($Query)->queryOne();
//                     }catch(Exception $e){	print_r($e); exit;	};
            $marker = Marker::findBySql('SELECT Marker_Id FROM Marker WHERE Name = "'.$nameWithoutQuote.'" and IsActive=1')->scalar();
//                            ->where(['Name' => $new_matriz[$l][0]])
//                            ->asArray()
//                            ->one();

            if(!$marker)
            {
                $not_marker[] =  $new_matriz[$l][1];
            }
            else{
                //"INSERT INTO snp_lab () values ()";
                $model->Marker_Id = $marker;
                unset($marker);
                $model->PurchaseSequence = $new_matriz[$l][2];
                $model->AlleleFam = $new_matriz[$l][3];
                $model->AlleleVicHex = $new_matriz[$l][4];
                //barcode in te excel
                $model->ValidatedStatus = $new_matriz[$l][6];
                if($new_matriz[$l][7])
                {
                    if(strpos($new_matriz[$l][7], ",") === false)
                    {
                        $model->Quality = '["'.$new_matriz[$l][7].'"]';

                    }else{
                        $quality = explode("," , $new_matriz[$l][7]);
                        $model->Quality = json_encode($quality );
                    }
                }
                $model->Box = $new_matriz[$l][9];
                $model->PositionInBox = $new_matriz[$l][10];
                $model->PIC = $new_matriz[$l][11];
                $model->IsActive = 1;

                if(!$model->save())
                {

                    //print_r($model->getErrors()); exit;
                    $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. LabName".$model->LabName." is duplicated in File. Please edit the file and try again (#ERROR - 1).</div>";
                    unlink($file);
                    return $result;
                } else {

                    $snp_labs[$l] = $model->Snp_lab_Id;
                     unset($model);
                }

                if($new_matriz[$l][8]) // if ASSAY BRAND
                {
                    $AssayBrand = new Assaybrand();
                    $AssayBrand->Name = $new_matriz[$l][8];
                    $AssayBrand->Snp_lab_Id =  $snp_labs[$l];
                    $AssayBrand->IsActive = 1;


                    if(!$AssayBrand->save())
                    {
                        $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. Please verify the file and try again (#ERROR - 2).</div>";
                        unlink($file);
                        return $result;
                    }
                }
                if($new_matriz[$l][5]) // IF BARCODE
                {
                    if(strpos($new_matriz[$l][5], ",") === false)
                    {
                        $barcode = new Barcode();
                        $barcode->Number = $new_matriz[$l][5];
                        $barcode->Snp_lab_Id =  $snp_labs[$l];
                        $barcode->IsActive=1;
                        $barcode->save();
                        unset($barcode);
                    }else
                    {
                        $bcode = explode(',',$new_matriz[$l][5]);
                        foreach($bcode as $key => $val)
                        {
                            $barcode = new Barcode();
                            $barcode->Number = $val;
                            $barcode->Snp_lab_Id =  $snp_labs[$l];
                            $barcode->IsActive=1;
                            if(!$barcode->save())
                            {
                                $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>The file  can not be uploaded. Please verify the file and try again (#ERROR - 3).</div>";
                               unlink($file);
                                return $result;
                            }
                            unset ($barcode);
                        }
                    }
                }
                unset($AssayBrand);
            }     
        }

        unset($new_matriz);
        set_time_limit(30);
        unlink($file);
            if($snp_labs)
                $result['success'] = "<div class='alert alert-success' id='notifications'><strong>".count($snp_labs)." of ". $length." SNP LABs are imported successfully!</strong>.</div>";

            if($result_duplicate != NULL)
            {  

                $result['fail'] = "<strong>".count($result_duplicate)." SNPs already exist in the Database</strong>.";
                $result['snps_duplicates'] = $result_duplicate;
            } //else
               // $result['fail_total'] = "<div class='alert alert-danger' id='notifications'>No hay SNPs cargados  SNPs LABs de este archiv.</div>";
            if($not_marker != NULL)
                $result['not_marker'] = $not_marker;
        
        //print_r($not_marker); exit;
        return $result;
    }
    
    private function order_matriz($values, $duplicates)
    {
        $new = array();
       //print_r(count($duplicates));
       //print_r($duplicates);
       foreach($values as $v)
       {  
           
           $i = array_search( $v[1] , $duplicates);
           
            if( $i  !== false)
            {
               // print_r($i); exit;
               unset($duplicates[$i]);
               $p = array_values($duplicates);

            }else
            {
               $new[] = $v;
            }
           
        }
        //sprint_r($new); exit;
        return $new;
    }
    
    private function validateDocument($headers)
    {
        $ok = true;
               
        /*if (count($headers) != 12 )
        {
            $ok = false;
            return $ok;
        }*/
        //print_r($headers); exit;
        foreach($headers as $k => $val)
        { 
            if ($k == 0 and $val != "Marker original name")
                $ok = false;
            if ($k == 1 and $val != "SNP lab name")
                $ok = false;
            if ($k == 2 and $val != "Purchase sequence")
                $ok = false;
            if ($k == 3 and $val != "Allele FAM")
                $ok = false;
            if ($k == 4 and $val != "Allele VIC/HEX")
                $ok = false;
            if ($k == 5 and $val != "Barcodes")
                $ok = false;
            if ($k == 6 and $val != "Validated")
                $ok = false;
            if ($k == 7 and $val != "Quality")
                $ok = false;
            if ($k == 8 and $val != "Assay brand")
                $ok = false;
            if ($k == 9 and $val != "Box")
                $ok = false;
            if ($k == 10 and $val != "Position in box")
                $ok = false; 
             //print_r("sadasd"); var_dump(($val != "Position in box" or $val != "Position in Box")); exit;
                
            if ($k == 11 and $val != "PIC")
                $ok = false;
        }
        
        return $ok; 
    }
    
    public function deleteByIds($Checks)
    {
       $connection = \Yii::$app->dbGdbms;
       $Query = "UPDATE snp_lab SET IsActive=0 WHERE Snp_lab_Id IN (". implode(',',array_values($Checks)) .")";
       try{
            $connection->createCommand($Query)->execute();
       }catch(Exception $e){	print_r($e); exit;	};
    }
    
    public function deleteBarcodes($id=null)
    {
        $connection = Yii::$app->db;
        if($id == null)
            $sqlDelete = "DELETE FROM barcode WHERE Snp_lab_Id=".$this->Snp_lab_Id;
        else
            $sqlDelete = "DELETE FROM barcode WHERE Snp_lab_Id=".$id;
        try
        {
             $connection->createCommand($sqlDelete)->execute();
        }
        catch(Exception $e )
        {
             print_r($e); exit;
        }
    }
    
    static function applyMaskBarcodes($barcodesString)
    {
        if($barcodesString == "")
            return "<kbd> None </kbd>";
        $arrayBarcodes = explode(",", $barcodesString);
        foreach($arrayBarcodes as $b)
        {
            $cont = strlen($b);
            for($i=$cont; $i < 10; $i++)
            {
                $b = '0'.$b;
            }
            $numbers[] = $b;
        }
        $barcodes = implode(",", $numbers);
        return $barcodes;
    }
    
    static function getBarcodesWithMask($model) 
    {
        if (is_object($model))
            $bcodes = \common\models\Barcode::find()
                    ->where(['Snp_lab_Id' => $model->Snp_lab_Id, 'IsActive' => 1])
                    ->all();
        else
            $bcodes = \common\models\Barcode::find()
                    ->where(['Snp_lab_Id' => $model['Snp_lab_Id'], 'IsActive' => 1])
                    ->all();

        if ($bcodes) {
            foreach ($bcodes as $bar)
                $arraycods[] = $bar->Number;

            $stringBarcode = implode(',', $arraycods);

            $mask_bcode = SnpLab::applyMaskBarcodes($stringBarcode);
            return $mask_bcode;
        } else
            return NULL;
    }
    
    static function getColumnsByConsensus()
    {
        $columns = [  
                    [
                        'label'=>'',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'no-link'],
                        //'visible' => $markerOnly == null ? false : true,
                        'value'=>function ($data) {
                                               if( Yii::$app->session['sd'])
                                               {
                                                   if(isset($data['Marker_Id']))
                                                   {    
                                                        if( in_array( $data['Marker_Id'] ,  Yii::$app->session['sd']))
                                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" checked>';
                                                        else
                                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" >';
                                                   }else
                                                   {
                                                       if( in_array( $data['Snp_lab_Id'] ,  Yii::$app->session['sd']))
                                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Snp_lab_Id'].'" checked>';
                                                        else
                                                            return '<input type="checkbox" name="vCheck[]" value="'.$data['Snp_lab_Id'].'" >';
                                                   }
                                               }else
                                                   if(isset($data['Marker_Id']))
                                                       return '<input type="checkbox" name="vCheck[]" value="'.$data['Marker_Id'].'" >';
                                                   else
                                                       return '<input type="checkbox" name="vCheck[]" value="'.$data['Snp_lab_Id'].'" >';
                                                },
                    ],

                    // 'Snp_lab_Id',

                    //'Name',
                    ['label'=> 'Marker Name', 'value'=>'Name'],
                    [
                        'label'=>'LabName',
                        'format' => 'raw',
                        'value'=>function ($data) {
                                                return $data['LabName'] == null ? "<kbd>None</kbd>" : $data['LabName'];
                                                },
                    ],                   
                    [
                        'label' => 'LG',
                        'value'=>function ($data) {                                       
                                                    return $data['LinkageGroup'];
                                                    }, 
                    ],
                    [
                        'label' => 'cM',
                        'value'=>function ($data) {                                       
                                                    return $data['Position'];
                                                    }, 
                    ],
                    [
                        'label' => 'Box',
                        'value'=>function ($data) {                                       
                                                    return $data['Box'];
                                                    },
                    ],
                    [
                        'label' => 'PositionInBox',
                        'value'=>function ($data) {                                       
                                                    return $data['PositionInBox'];
                                                    },
                    ], 
                    //'Crop_Id',
                    [
                        'label'=>'Quality',
                        'format' => 'raw',
                        'value'=>function ($data) {

                                                return $data['Quality'] == null ? "<kbd>None</kbd>" : $data['Quality'];
                                                },
                    ],
                    ['class' => 'frontend\widgets\RowLinkColumn'],
                ];
                                                
        return $columns;
    }
    
    
    
}
