<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fingerprint_result".
 *
 * @property string $Fingerprint_Result_Id
 * @property string $Fingerprint_Material_Id
 * @property string $Fingerprint_Snp_Id
 * @property string $Allele_Id
 *
 * @property Allele $allele
 * @property FingerprintMaterial $fingerprintMaterial
 * @property FingerprintSnp $fingerprintSnp
 */
class FingerprintResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fingerprint_result';
    }
    
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Fingerprint_Id', 'Fingerprint_Material_Id', 'Fingerprint_Snp_Id', 'Allele_Id'], 'required'],
            [['Fingerprint_Id', 'Fingerprint_Material_Id', 'Fingerprint_Snp_Id', 'Allele_Id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Fingerprint_Result_Id' => Yii::t('app', 'Fingerprint  Result  ID'),
            'Fingerprint_Material_Id' => Yii::t('app', 'Fingerprint  Material  ID'),
            'Fingerprint_Snp_Id' => Yii::t('app', 'Fingerprint  Snp  ID'),
            'Allele_Id' => Yii::t('app', 'Allele  ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllele()
    {
        return $this->hasOne(Allele::className(), ['Allele_Id' => 'Allele_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintMaterial()
    {
        return $this->hasOne(FingerprintMaterial::className(), ['Fingerprint_Material_Id' => 'Fingerprint_Material_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintSnp()
    {
        return $this->hasOne(FingerprintSnp::className(), ['Fingerprint_Snp_Id' => 'Fingerprint_Snp_Id']);
    }
}
