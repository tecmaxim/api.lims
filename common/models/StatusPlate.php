<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "status_plate".
 *
 * @property integer $StatusPlateId
 * @property string $Name
 *
 * @property Plate[] $plates
 */
class StatusPlate extends \yii\db\ActiveRecord
{
    const SENT          = 1;
    const PENDING       = 2;
    const RECIEVED      = 3;
    const CONTROLLED    = 4;
    const SAVED_METHOD  = 5;
    const ADN_EXTRACTED = 6;
    const CUANTIFICATION= 7;
    const CANCELED      = 8;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_plate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['Name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'StatusPlateId' => 'Status Plate ID',
            'Name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlates()
    {
        return $this->hasMany(Plate::className(), ['StatusPlateId' => 'StatusPlateId']);
    }
}
