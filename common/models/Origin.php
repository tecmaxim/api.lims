<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "origin".
 *
 * @property string $Origin_Id
 * @property string $Name
 * @property integer $IsActive
 *
 * @property FingerprintMaterial[] $fingerprintMaterials
 * @property Material[] $materials
 */
class Origin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'origin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IsActive'], 'integer'],
            [['Name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Origin_Id' => Yii::t('app', 'Origin  ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFingerprintMaterials()
    {
        return $this->hasMany(FingerprintMaterial::className(), ['Origin_Id' => 'Origin_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['Origin_Id' => 'Origin_Id']);
    }
}
