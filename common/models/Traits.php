<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "traits".
 *
 * @property integer $TraitsId
 * @property string $Crop_Id
 * @property string $Name
 * @property string $Description
 * @property boolean $IsActive
 *
 * @property Crop $crop
 * @property TraitsByProject[] $traitsByProjects
 */
class Traits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Crop_Id', 'Description'], 'required'],
            [['Crop_Id'], 'integer'],
            [['IsActive'], 'boolean'],
            [['Name', 'Description'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TraitsId' => Yii::t('app', 'Traits ID'),
            'Crop_Id' => Yii::t('app', 'Crop  ID'),
            'Name' => Yii::t('app', 'Name'),
            'Description' => Yii::t('app', 'Description'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
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
    public function getTraitsByProjects()
    {
        return $this->hasMany(TraitsByProject::className(), ['TraitId' => 'TraitsId']);
    }
    
    
}
