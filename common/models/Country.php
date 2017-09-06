<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $CountryId
 * @property string $Name
 * @property string $Short
 * @property boolean $IsActive
 */
class Country extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['Name'], 'required'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'CountryId' => Yii::t('app', 'Country ID'),
            'Name' => Yii::t('app', 'Name'),
            'CountryId' => Yii::t('app', 'Country ID'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities() {
        return $this->hasMany(City::className(), ['CountryId' => 'CountryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResearchStations() {
        return $this->hasMany(ResearchStation::className(), ['CountryId' => 'CountryId']);
    }

}
