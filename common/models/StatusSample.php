<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "status_sample".
 *
 * @property integer $StatusSampleId
 * @property string $Description
 */

class StatusSample extends \yii\db\ActiveRecord
{
    const RECIVED_OK = 1;
    const DEAD = 2;
    const _EMPTY = 3;
    const DNA_EXCTRACTED_OK = 4;
    const BAD_EXTRACTION = 5;    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_sample';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'StatusSampleId' => 'Status Sample ID',
            'Description' => 'Description',
        ];
    }
}
