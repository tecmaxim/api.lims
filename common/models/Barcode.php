<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "barcode".
 *
 * @property string $Barcode_Id
 * @property string $Snp_lab_Id
 * @property string $Number
 * @property integer $IsActive
 *
 * @property SnpLab $snpLab
 */
class Barcode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'barcode';
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
            [['Snp_lab_Id'], 'required'],
            [['Snp_lab_Id', 'Number', 'IsActive'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Barcode_Id' => Yii::t('app', 'Barcode  ID'),
            'Snp_lab_Id' => Yii::t('app', 'Snp Lab  ID'),
            'Number' => Yii::t('app', 'Number'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSnpLab()
    {
        return $this->hasOne(SnpLab::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }
}
