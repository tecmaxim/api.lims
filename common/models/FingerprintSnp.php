<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fingerprint_snp".
 *
 * @property string $Fingerprint_Snp_Id
 * @property string $Snp_lab_Id
 * @property string $Fingerprint_Id
 * @property integer $Qualtity
 * @property integer $IsActive
 *
 * @property FingerprintResult[] $fingerprintResults
 * @property Fingerprint $fingerprint
 * @property SnpLab $snpLab
 */
class FingerprintSnp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fingerprint_snp';
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
            [['Snp_lab_Id', 'Fingerprint_Id'], 'required'],
            [['Snp_lab_Id', 'Fingerprint_Id', 'IsActive'], 'integer'],
			[['Quality'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Fingerprint_Snp_Id' => Yii::t('app', 'Fingerprint  Snp  ID'),
            'Snp_lab_Id' => Yii::t('app', 'Snp Lab  ID'),
            'Fingerprint_Id' => Yii::t('app', 'Fingerprint  ID'),
            'Quality' => Yii::t('app', 'Quality'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintResults()
    {
        return $this->hasMany(FingerprintResult::className(), ['Fingerprint_Snp_Id' => 'Fingerprint_Snp_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprint()
    {
        return $this->hasOne(Fingerprint::className(), ['Fingerprint_Id' => 'Fingerprint_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSnpLab()
    {
        return $this->hasOne(SnpLab::className(), ['Snp_lab_Id' => 'Snp_lab_Id']);
    }
}
