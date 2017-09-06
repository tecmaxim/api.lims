<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "traits_by_project".
 *
 * @property integer $TraitsByProjectId
 * @property integer $TraitsId
 * @property integer $ProjectId
 * @property integer $IsActive
 *
 * @property Project $project
 * @property Traits $traits
 */
class TraitsByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'traits_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TraitsId', 'ProjectId', 'IsActive'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TraitsByProjectId' => Yii::t('app', 'Traits By Project ID'),
            'TraitsId' => Yii::t('app', 'Traits ID'),
            'ProjectId' => Yii::t('app', 'Project ID'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTraits()
    {
        return $this->hasOne(Traits::className(), ['TraitsId' => 'TraitsId']);
    }
    
    
}
