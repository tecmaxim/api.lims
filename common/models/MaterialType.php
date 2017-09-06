<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "material_type".
 *
 * @property string $Material_Type_Id
 * @property string $Name
 * @property integer $IsActive
 *
 * @property Material[] $materials
 */
class MaterialType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'IsActive'], 'required'],
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
            'Material_Type_Id' => Yii::t('app', 'Material  Type  ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['Material_Type_Id' => 'Material_Type_Id']);
    }
}
