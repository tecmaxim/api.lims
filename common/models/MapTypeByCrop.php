<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "map_type_by_crop".
 *
 * @property integer $MapTypeByCropId
 * @property integer $MapTypeId
 * @property string $Crop_Id
 *
 * @property Crop $crop
 * @property MapType $mapType
 */
class MapTypeByCrop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_type_by_crop';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }
    
    public function rules()
    {
        return [
            [['MapTypeByCropId', 'MapTypeId', 'Crop_Id'], 'integer'],
            [['Crop_Id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'MapTypeByCropId' => Yii::t('app', 'Map Type By Crop ID'),
            'MapTypeId' => Yii::t('app', 'Map Type ID'),
            'Crop_Id' => Yii::t('app', 'Crop  ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrop()
    {
        return $this->hasOne(Crop::className(), ['Crop_Id' => 'Crop_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMapType()
    {
        return $this->hasOne(MapType::className(), ['MapTypeId' => 'MapTypeId']);
    }
    
    public static function getMapsByCrop($id=null)
    {
        $model = new MapTypeByCrop;
       
        $mapsT = $model->find()->where(["Crop_Id"=>$id, "IsActive" =>1])->all();
      
        return $mapsT;
    }
    
    
}
