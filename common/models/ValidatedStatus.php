<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "validated_status".
 *
 * @property integer $ValidatedStatusId
 * @property string $Name
 * @property string $Value
 * @property boolean $IsActive
 */
class ValidatedStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'validated_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name','Value'], 'required'],
            [['IsActive'], 'boolean'],
            [['Name','Value'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }
    
    public function attributeLabels()
    {
        return [
            'ValidatedStatusId' => Yii::t('app', 'Validated Status ID'),
            'Name' => Yii::t('app', 'Name'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }
    
    static function getStatusValidatedByArray()
    {
        $states = ValidatedStatus::find()->where(["IsActive"=>1])->asArray()->all();
        //print_r($states); exit;
        $i=0;
        foreach ($states as $st)
        {
            $array_check = [$st['Value']=>$st['Name']];
           
            $i++;
        }
        $mas = ['1'=>'asda', '2'=>'rewer', '3'=>'vsnns'];
        print_r($array_check);
        print_r($mas); exit;
        
        
    }
}
