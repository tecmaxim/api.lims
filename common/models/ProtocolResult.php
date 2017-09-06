<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "protocol_result".
 *
 * @property integer $ProtocolResultId
 * @property string $Name
 * @property string $Description
 * @property boolean $IsActive
 *
 * @property Protocol[] $protocols
 */
class ProtocolResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'protocol_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['Description'], 'string'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProtocolResultId' => 'Protocol Result ID',
            'Name' => 'Name',
            'Description' => 'Description',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProtocols()
    {
        return $this->hasMany(Protocol::className(), ['ProtocolResultId' => 'ProtocolResultId']);
    }
}
