<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "crop".
 *
 * @property string $Crop_Id
 * @property string $Name
 * @property string $ShortName
 * @property string $LatinName
 * @property string $IsActive
 *
 * @property Material[] $materials
 */
class Crop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    STATIC $SUNFLOWER = 1;
    STATIC $CORN = 2;
    public $Map;
    
    public $cropId;
    public $TypeDate;
    public $DateFrom;
    public $DateTo;
    // STATIC $ = 1;
    
    public static function tableName()
    {
        return 'crop';
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
            [['IsActive'], 'integer'],
            [['Name', 'LatinName'], 'string', 'max' => 255],
            [['ShortName'], 'string', 'max' => 150],
            [['Map'], 'required'],
            
            //Dashboard
            [['cropId', 'TypeDate'],'integer'],
            //[['cropId', 'TypeDate', 'Date'],'required', 'on' => 'dashboard'],
            [['DateFrom','DateTo'],'string'],
        ];
    }

    public function fields()
    {
        $attrs = parent::fields();
        $attrs[] = 'FullName';
        $attrs[] = 'materials';
        return $attrs;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Crop_Id' => Yii::t('app', 'Crop/Vegetables'),
            'cropId' => Yii::t('app', 'Crop/Vegetables'),
            'Name' => Yii::t('app', 'Name'),
            'ShortName' => Yii::t('app', 'Short Name'),
            'LatinName' => Yii::t('app', 'Latin Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }
   public function getMapTypeByCrops() 
   { 
       return $this->hasMany(MapTypeByCrop::className(), ['Crop_Id' => 'Crop_Id']); 
   } 
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['Crop_Id' => 'Crop_Id']);
    }
    
     public static function getCropsEnabled()
    {
       $connection = \Yii::$app->dbGdbms;
       $Query = "Select c.Crop_Id, c.Name FROM crop c".
                    //inner join marker ON marker.Crop_Id=c.Crop_Id
                   " where c.IsActive=1";
       try{
            return $connection->createCommand($Query)->queryAll();
       }catch(Exception $e){	print_r($e); exit;	};
    }
    
    public function getMapsByCrop()
    {
        $string = "<kbd>";
       
        foreach($this->mapTypeByCrops as $m)
            $string .= $m->mapType->Name."</kbd> <kbd>";
        
        
        return substr($string, 0, -5);
    }
    
    public function getFullName()
    {
        return $this->Name;
    }
   
}
