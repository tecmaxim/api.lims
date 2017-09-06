<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "material".
 *
 * @property string $Material_Id
 * @property string $Crop_Id
 * @property string $Origin_Id
 * @property string $Material_Type_Id
 * @property string $seedBreederId
 * @property string $Name
 * @property string $Pedigree
 * @property integer $IsActive
 * @property string $Observations
 * @property integer $IsMaterial
 *
 * @property FingerprintMaterial[] $fingerprintMaterials
 * @property Crop $crop
 * @property MaterialType $materialType
 * @property Origin $origin
 */
class Material extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Crop_Id', 'Origin_Id', 'Material_Type_Id', 'seedBreederId'], 'required'],
            [['Crop_Id', 'Origin_Id', 'Material_Type_Id', 'seedBreederId', 'IsActive', 'IsMaterial'], 'integer'],
            [['Name', 'Pedigree'], 'string', 'max' => 255],
            [['Observations'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Material_Id' => Yii::t('app', 'Material  ID'),
            'Crop_Id' => Yii::t('app', 'Crop  ID'),
            'Origin_Id' => Yii::t('app', 'Origin  ID'),
            'Material_Type_Id' => Yii::t('app', 'Material  Type  ID'),
            'seedBreederId' => Yii::t('app', 'Seed Breeder ID'),
            'Name' => Yii::t('app', 'Name'),
            'Pedigree' => Yii::t('app', 'Pedigree'),
            'IsActive' => Yii::t('app', 'Is Active'),
            'Observations' => Yii::t('app', 'Observations'),
            'IsMaterial' => Yii::t('app', 'Is Material'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintMaterials()
    {
        return $this->hasMany(FingerprintMaterial::className(), ['Material_Id' => 'Material_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrop()
    {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(MaterialType::className(), ['Material_Type_Id' => 'Material_Type_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigin()
    {
        return $this->hasOne(Origin::className(), ['Origin_Id' => 'Origin_Id']);
    }
}
