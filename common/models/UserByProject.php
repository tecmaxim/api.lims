<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_by_project".
 *
 * @property integer $UserByProjectId
 * @property integer $UserId
 * @property integer $ProjectId
 *
 * @property Project $project
 */
class UserByProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_by_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserId', 'ProjectId'], 'required'],
            [['UserId', 'ProjectId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserByProjectId' => 'User By Project ID',
            'UserId' => 'User ID',
            'ProjectId' => 'Project ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['ProjectId' => 'ProjectId']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['UserId' => 'UserId']);
    }
}
