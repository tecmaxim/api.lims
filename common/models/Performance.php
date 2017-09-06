<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "performance".
 *
 * @property integer $PerformanceId
 * @property integer $CategoryId
 * @property string $Name
 * @property boolean $IsActive
 *
 * @property Category $category
 * @property Product $product
 */
class Performance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'performance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CategoryId', 'Name'], 'required'],
            [['CategoryId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['Name'], 'string', 'max' => 50],
            [['Name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PerformanceId' => Yii::t('app', 'Performance ID'),
            'CategoryId' => Yii::t('app', 'Category ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['CategoryId' => 'CategoryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['ProductId' => 'PerformanceId']);
    }
}
