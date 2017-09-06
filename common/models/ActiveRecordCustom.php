<?php
namespace common\models;

use Yii;

class ActiveRecordCustom extends \yii\db\ActiveRecord
{
    
       	public function __construct()
	{
        	$this->IsActive = 1;
	}                   
}
?>