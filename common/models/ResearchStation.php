<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "research_station".
 *
 * @property integer $ResearchStationId
 * @property integer $CountryId
 * @property integer $CityId
 * @property string $Short
 * @property boolean $IsActive
 *
 * @property City $city
 * @property Country $country
 */
class ResearchStation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'research_station';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CountryId', 'CityId', 'Short'], 'required'],
            [['CountryId', 'CityId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['Short'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ResearchStationId' => Yii::t('app', 'Research Station ID'),
            'CountryId' => Yii::t('app', 'Country'),
            'CityId' => Yii::t('app', 'Location'),
            'Short' => Yii::t('app', 'Short'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['CityId' => 'CityId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['CountryId' => 'CountryId']);
    }
}
