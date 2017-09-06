<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cropbyuser".
 *
 * @property integer $CropByUserId
 * @property integer $UserId
 * @property string $Crop_Id
 * @property string $IsActive
 *
 * @property Crop $crop
 * @property User $user
 */
class Cropbyuser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cropbyuser';
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
            [['UserId', 'Crop_Id', 'IsActive'], 'integer'],
            [['UserId', 'Crop_Id'], 'unique', 'targetAttribute' => ['UserId', 'Crop_Id'], 'message' => 'The combination of User ID and Crop  ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CropByUserId' => Yii::t('app', 'Crop By User ID'),
            'UserId' => Yii::t('app', 'User ID'),
            'Crop_Id' => Yii::t('app', 'Crop  ID'),
            'IsActive' => Yii::t('app', 'Is Active'), 
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['UserId' => 'UserId']);
    }
    
    public static function getCropsByUser($id=null)
    {
        $model = new Cropbyuser();
       
        $crops = $model->find()->where(["UserId"=>$id == null ? \Yii::$app->user->id : $id, "IsActive" =>1])->all();
      
        return $crops;
    }
    

}
