<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "map_type".
 *
 * @property integer $MapTypeId
 * @property string $Name
 * @property integer $IsActive
 *
 * @property Map[] $maps
 */
class MapType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_type';
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
            [['IsActive'], 'integer'],
            [['Name'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'MapTypeId' => Yii::t('app', 'Map Type ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaps()
    {
        return $this->hasMany(Map::className(), ['MapTypeId' => 'MapTypeId']);
    }
    
    public function getMapsTypeByCrop($crop_id)
    {
//        $connection = \Yii::$app->db;
//      
//           $Query = "SELECT  Name, MapTypeId from map_type_by_crop
//                  WHERE Crop_Id=".$crop_id;
//       
//        $maps = $connection->createCommand($Query)->queryAll();
        $mT = MapTypeByCrop::find()->where(["Crop_Id"=>$crop_id, "IsActive"=>1])->all();
        
       if($mT)
       {
           $i = 0;
           foreach($mT as $m)
           {
               $mType[$i]["name"] = $m->mapType->Name;
               $mType[$i]["id"] = $m->MapTypeId;
               $i++;
           }
       } 
      
       return $mType;
    }
    
    public static function getNamesMapTypes()
    {
       $connection = \Yii::$app->db;
       $Query = "Select MapTypeId, Name FROM map_type mt".
                    //inner join marker ON marker.Crop_Id=c.Crop_Id
                   " where IsActive=1";
       try{
            return $connection->createCommand($Query)->queryAll();
       }catch(Exception $e){	print_r($e); exit;	};
    }
    
    public function getMapTypeByCrop()
    {
        if(\Yii::$app->session['cropId'] != null)
            $mapsByCrop = MapTypeByCrop::findAll(["Crop_Id"=>  \Yii::$app->session['cropId'], "IsActive" =>1]);
        else
            $mapsByCrop = MapTypeByCrop::findAll(["IsActive" =>1]);
        return $mapsByCrop;
    }
}