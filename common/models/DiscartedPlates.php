<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "discarted_plates".
 *
 * @property integer $DiscartedPlatesId
 * @property integer $PlateId
 * @property integer $CauseByDiscartedPlatesId
 * @property string $Date
 * @property string $Comments
 * @property boolean $IsActive
 *
 * @property CauseByDiscartedPlates $causeByDescartedPlates
 * @property Plate $plate
 */
class DiscartedPlates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discarted_plates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PlateId', 'CauseByDiscartedPlatesId', ], 'required'],
            [['PlateId', 'CauseByDiscartedPlatesId'], 'integer'],
            [['Date','Comments'], 'safe'],
            [['IsActive'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DiscartedPlatesId' => 'Discarted Plates ID',
            'PlateId' => 'Plate ID',
            'CauseByDiscartedPlatesId' => 'Cause By Descarted Plates ID',
            'Date' => 'Date',
            'Comments' => 'Commments',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCauseByDescartedPlates()
    {
        return $this->hasOne(CauseByDiscartedPlates::className(), ['CauseByDiscartedPlatesId' => 'CauseByDiscartedPlatesId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
    {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }
}
