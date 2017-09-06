<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "map_result".
 *
 * @property string $Map_Result_Id
 * @property string $Map_Id
 * @property integer $Snp_Id
 * @property integer $LinkageGroup
 * @property string $Position
 * @property string $MappedPopulation
 * @property string $MappingTeam
 *
 * @property Map $map
 * @property Snp $snp
 */
class MapResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_result';
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
            [['Map_Id', 'IsActive'], 'required'],
            [['Map_Id', 'Marker_Id', 'LinkageGroup', 'IsActive'], 'integer'],
            [['Position'], 'number'],
            [['MappedPopulation'], 'string', 'max' => 100],
            [['MappingTeam'], 'string', 'max' => 50],
            [['Map_Id', 'Marker_Id'], 'unique', 'targetAttribute' => ['Map_Id', 'Marker_Id'], 'message' => 'Repeated markers where found']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Map_Result_Id' => Yii::t('app', 'Map  Result  ID'),
            'Map_Id' => Yii::t('app', 'Map  ID'),
            'Marker_Id' => Yii::t('app', 'Marker ID'),
            'LinkageGroup' => Yii::t('app', 'Linkage Group'),
            'Position' => Yii::t('app', 'Position'),
            'MappedPopulation' => Yii::t('app', 'Mapped Population'),
            'MappingTeam' => Yii::t('app', 'Mapping Team'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['Map_Id' => 'Map_Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarker()
    {
        return $this->hasOne(Marker::className(), ['Marker_Id' => 'Marker_Id']);
    }
}

