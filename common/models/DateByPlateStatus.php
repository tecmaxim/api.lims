<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "date_by_plate_status".
 *
 * @property integer $DateByPlateStatusId
 * @property integer $PlateId
 * @property integer $StatusPlateId
 * @property string $Date
 */
class DateByPlateStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'date_by_plate_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PlateId', 'StatusPlateId', 'Date'], 'required'],
            [['PlateId', 'StatusPlateId'], 'integer'],
            [['Date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DateByPlateStatusId' => 'Date By Plate Status ID',
            'PlateId' => 'Plate ID',
            'StatusPlateId' => 'Status Plate ID',
            'Date' => 'Date',
        ];
    }
    
    /**
    * @return \yii\db\ActiveQuery
    */

   public function getStatusPlate()
   {
       return $this->hasOne(StatusPlate::className(), ['StatusPlateId' => 'StatusPlateId']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPlate()
   {
       return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
   }

    
    /*
     * Save relation between status_plate and $plate with the DATE
     * parameters: int, int
     * return null
     */
    static function saveDateByStatus($plateId, $statusId)
    {
        $model = new DateByPlateStatus();
        
        $model->Date = date('Y-m-d');
        $model->PlateId = $plateId;
        $model->StatusPlateId = $statusId;
        $model->save();
    }
}
