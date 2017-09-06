<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cause_by_discarted_plates".
 *
 * @property integer $CauseByDiscartedPlatesId
 * @property string $Name
 * @property string $Description
 * @property boolean $IsActive
 */
class CauseByDiscartedPlates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cause_by_discarted_plates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'Description'], 'required'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 50],
            [['Description'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CauseByDiscartedPlatesId' => 'Cause By Discarted Plates ID',
            'Name' => 'Name',
            'Description' => 'Description',
            'IsActive' => 'Is Active',
        ];
    }
}
