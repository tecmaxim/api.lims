<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $ProductId
 * @property integer $PerformanceId
 * @property string $Name
 * @property boolean $IsActive
 *
 * @property Performance $product
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PerformanceId', 'Name'], 'required'],
            [['PerformanceId'], 'integer'],
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
            'ProductId' => Yii::t('app', 'Product ID'),
            'PerformanceId' => Yii::t('app', 'Performance ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Performance::className(), ['PerformanceId' => 'ProductId']);
    }
}
