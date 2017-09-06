<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adn_extraction".
 *
 * @property integer $AdnExtractionId
 * @property integer $PlateId
 * @property string $Method
 * @property string $Dilution
 * @property string $Cuantification
 * @property string $Comments
 * @property boolean $IsActivce
 *
 * @property Plate $plate
 * @property Genspin[] $genspins 
 */
class AdnExtraction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adn_extraction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PlateId', 'Method'], 'required'],
            [['PlateId','AdnExtractionId'], 'integer'],
            [['Method', 'Comments','Dilution', 'Cuantification',], 'string'],
            [['IsActivce'], 'boolean'],
            [['Dilution'], 'string', 'max' => 4],
            [['Cuantification'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'AdnExtractionId' => 'Adn Extraction ID',
            'PlateId' => 'Plate ID',
            'Method' => 'Method',
            'Dilution' => 'Dilution',
            'Cuantification' => 'Cuantification',
            'Comments' => 'Comments',
            'IsActivce' => 'Is Activce',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
    {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }
    
    /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getGenspins() 
   { 
       return $this->hasMany(Genspin::className(), ['AdnExtractionId' => 'AdnExtractionId']); 
   } 
   
   static function getMethodByPlateId($plateId)
   {
        $method = AdnExtraction::find()
                        ->where(['PlateId'=> $plateId])
                        ->select('Method')
                        ->scalar();
       return $method;        
   }
   
   static function findByArrayPlateId($array_plateId)
   {
       foreach($array_plateId as $plate)
       {
           $model = AdnExtraction::findOne(["PlateId" => $plate->PlateId]);
           
           if($model)
           {
               return $model;
           }
       }
       return null;
   }
   
   static function findByPlateId($plateId)
   {
        $model = AdnExtraction::findOne(["PlateId" => $plateId]);
           
        if(!$model)
        {
            $genspin = Genspin::getGenSpinByPlateId($plateId);
            
            $model = AdnExtraction::findOne($genspin['AdnExtractionId']);
        }
       
       return $model;
   }
   
}
