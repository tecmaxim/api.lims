<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reason_by_project".
 *
 * @property integer $ProjectId
 * @property string $Description
 */
class ReasonByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reason_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'required'],
            [['Description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectId' => 'Project ID',
            'Description' => 'Description',
        ];
    }
}
