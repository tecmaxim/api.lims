<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "genotype_by_plate".
 *
 * @property integer $GenotypeByPlateId
 * @property integer $PlateId
 * @property string $GenotypeName
 * @property integer $IsActive
 *
 * @property Plate $plate
 */
class GenotypeByPlate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'genotype_by_plate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PlateId', 'IsActive'], 'integer'],
            [['GenotypeName'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'GenotypeByPlateId' => Yii::t('app', 'Genotype By Plate ID'),
            'PlateId' => Yii::t('app', 'Plate ID'),
            'GenotypeName' => Yii::t('app', 'Genotype Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlate()
    {
        return $this->hasOne(Plate::className(), ['PlateId' => 'PlateId']);
    }
}
