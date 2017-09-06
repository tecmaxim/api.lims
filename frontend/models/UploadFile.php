<?php

namespace frontend\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadFile extends Model
{
    /**
     * @var UploadedFile file attribute
     */
        public $file;
	public $type;
        public $Comments;
        public $ProjectId;
        
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['xlsx','xls','csv'], 'checkExtensionByMimeType'=>false],
            [['file'], 'required'],
            [['type','ProjectId'], 'integer'],
            [['Comments'],'string'],
                        
        ];
    }
	
	public function attributeLabels()
    {
        return [
            
            'file' => 'File',
            'type' => 'Type',
            'comments' => 'Comments',
        ];
    }
}

?>