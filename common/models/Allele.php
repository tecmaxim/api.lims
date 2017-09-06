<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "allele".
 *
 * @property string $Allele_Id
 * @property integer $IsActive
 * @property string $LongDescription
 *
 * @property FingerprintResult[] $fingerprintResults
 */
class Allele extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'allele';
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
            [['IsActive', 'LongDescription'], 'required'],
            [['IsActive'], 'integer'],
            [['LongDescription'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Allele_Id' => Yii::t('app', 'Allele  ID'),
            'IsActive' => Yii::t('app', 'Is Active'),
            'LongDescription' => Yii::t('app', 'Long Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintResults()
    {
        return $this->hasMany(FingerprintResult::className(), ['Allele_Id' => 'Allele_Id']);
    }
}
