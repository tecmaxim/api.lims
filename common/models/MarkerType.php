<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "marker_type".
 *
 * @property string $Marker_Type_Id
 * @property string $Name
 * @property string $IsActive
 *
 * @property Marker[] $markers
 */
class MarkerType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marker_type';
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
            [['Name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Marker_Type_Id' => Yii::t('app', 'Marker  Type  ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarkers()
    {
        return $this->hasMany(Marker::className(), ['Marker_Type_Id' => 'Marker_Type_Id']);
    }
}
