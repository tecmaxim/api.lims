<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "assaybrand".
 *
 * @property string $AssayBrandId
 * @property string $Snp_lab_Id
 * @property string $Name
 * @property integer $IsActive
 *
 * @property SnpLab $snpLab
 */
class Assaybrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'assaybrand';
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
            [['Snp_lab_Id', 'IsActive'], 'integer'],
            [['Name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'AssayBrandId' => Yii::t('app', 'Assay Brand ID'),
            'Snp_lab_Id' => Yii::t('app', 'Snp Lab  ID'),
            'Name' => Yii::t('app', 'Name'),
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
