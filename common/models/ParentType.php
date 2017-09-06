<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parent_type".
 *
 * @property integer $ParentTypeId
 * @property string $Type
 */
class ParentType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const POLLEN_DONNOR =1;
    const POLLEN_RECEPTOR =2;
    
    public static function tableName()
    {
        return 'parent_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ParentTypeId' => Yii::t('app', 'Parent Type ID'),
            'Type' => Yii::t('app', 'Type'),
        ];
    }
}
