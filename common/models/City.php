<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $CityId
 * @property string $Name
 * @property boolean $IsActive
 * @property integer $CountryId 
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name','CountryId'], 'required'],
            [['CountryId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CityId' => Yii::t('app', 'City ID'),
            'CountryId' => Yii::t('app', 'Country'),
            'Name' => Yii::t('app', 'Location'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }
    
     /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getCountry() 
   { 
       return $this->hasOne(Country::className(), ['CountryId' => 'CountryId']); 
   } 
 
   /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getResearchStations() 
   { 
       return $this->hasMany(ResearchStation::className(), ['CityId' => 'CityId']); 
   } 
}
