<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fingerprint_material".
 *
 * @property string $Fingerprint_Material_Id
 * @property string $Origin_Id
 * @property string $Fingerprint_Id
 * @property string $Material_Id
 * @property string $Observation
 * @property string $TissueOrigin
 * @property string $Pedigree
 * @property integer $IsActive
 *
 * @property Material $material
 * @property Fingerprint $fingerprint
 * @property Origin $origin
 * @property FingerprintResult[] $fingerprintResults
 */
class FingerprintMaterial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fingerprint_material';
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
            [['Origin_Id', 'Fingerprint_Id', 'Material_Id'], 'required'],
            [['Origin_Id', 'Fingerprint_Id', 'Material_Id', 'IsActive'], 'integer'],
           [['Fingerprint_Material_Id'], 'safe'],
           // [['TissueOrigin'], 'string', 'max' => 255],
            [['Pedigree'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Fingerprint_Material_Id' => 'Fingerprint  Material  ID',
			 'Fingerprint_Material_Id2' => 'Fingerprint  Material  ID 2',
            'Origin_Id' => 'Origin  ID',
            'Fingerprint_Id' => 'Fingerprint  ID',
            'Material_Id' => 'Material  ID',
            'Observation' => 'Observation',
            'TissueOrigin' => 'Tissue Origin',
            'Pedigree' => 'Pedigree',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::className(), ['Material_Id' => 'Material_Id']);
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
    public function getOrigin()
    {
        return $this->hasOne(Origin::className(), ['Origin_Id' => 'Origin_Id']);
    }
	
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintResults()
    {
        return $this->hasMany(FingerprintResult::className(), ['Fingerprint_Material_Id' => 'Fingerprint_Material_Id']);
    }
	
}
