<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cancel_causes".
 *
 * @property integer $CancelCausesId
 * @property string $Description
 * @property string $Name
 * @property boolean $IsActive
 */
class CancelCauses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cancel_causes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IsActive'], 'boolean'],
            [['Description'], 'string', 'max' => 250],
            [['Name'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CancelCausesId' => 'Cancel Causes',
            'Description' => 'Description',
            'Name' => 'Name',
            'IsActive' => 'Is Active',
        ];
    }
}
