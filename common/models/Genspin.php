<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "genspin".
 *
 * @property integer $GenspinId
 * @property integer $AdnExtractionId
 * @property string $Plates
 * @property boolean $IsActive
 *
 * @property AdnExtraction $adnExtraction
 */
class Genspin extends \yii\db\ActiveRecord
{
    public $plateSelected;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'genspin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['Plates'], 'required'],
            [['AdnExtractionId'], 'integer'],
            [['IsActive'], 'boolean'],
            [['Plates','plateSelected'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'GenspinId' => 'Genspin ID',
            'AdnExtractionId' => 'Adn Extraction ID',
            'Plates' => 'Plates',
            'IsActive' => 'Is Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdnExtraction()
    {
        return $this->hasOne(AdnExtraction::className(), ['AdnExtractionId' => 'AdnExtractionId']);
    }
    
    static function getGenSpinByPlateId($id)
    {
        $plate_json = json_encode(["PlateId" => $id]);
        $sql = "SELECT * FROM genspin WHERE Plates LIKE '%".$plate_json."%'";
        
        $genspin = Yii::$app->db->createCommand($sql)->queryOne();

        return $genspin;
        
    }
    
    static function deleteById($id)
    {
        $sql = "DELETE FROM genspin WHERE GenspinId=".$id;
        
        Yii::$app->db->createCommand($sql)->execute();
    }
}
