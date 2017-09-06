<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "generation".
 *
 * @property integer $GenerationId
 * @property string $Description
 * @property boolean $IsActive
 */
class Generation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'generation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'required'],
            [['IsActive'], 'boolean'],
            [['IsF1', 'IsActive'], 'boolean'],
            [['Description'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'GenerationId' => Yii::t('app', 'Generation ID'),
            'Description' => Yii::t('app', 'Description'),
            'IsF1' => 'Is F1',
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['GenerationId' => 'GenerationId']);
    }
}
