<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\db\Expression;
use common\models\AuthAssignment;

/**
 * User model
 *
 * @property integer $UserId
 * @property string $Username
 * @property string $AuthKey
 * @property string $PasswordHash
 * @property string $PasswordResetToken
 * @property string $Email

 * @property string $CreatedAt
 * @property string $UpdatedAt
 * @property boolean $IsActive
 * @property string $Password write-only password
 */
class User  extends ActiveRecordCustom implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $Role;
    public $Crop;
    public $PasswordConfirm;
   // public $ItemName;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }
    
    public static function getDb()
    {
        return Yii::$app->dbGdbms;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['CreatedAt', 'UpdatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'UpdatedAt',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            ['Username', 'filter', 'filter' => 'trim'],
            ['Username', 'required'],
            ['Username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['Username', 'string', 'min' => 2, 'max' => 255],  
            ['Role', 'string'],
            //['ItemName', 'string'],
            ['Email', 'filter', 'filter' => 'trim'],
            ['Email', 'required'],
            ['Crop', 'required','when' => function($model) {
                                                if(Yii::$app->controller->id == 'plate-history-by-project')
                                                    return false;
                                                if(Yii::$app->user->getIdentity() != null)
                                                    return Yii::$app->user->getIdentity()->ItemName == 'admin';
            }], 
            ['Email', 'email'],
            ['Email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],            
            ['PasswordHash', 'required'],
            [['PasswordConfirm'], 'required','when' => function($model) {
                                                if(Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create')
                                                    return true;
                                                else
                                                    return false;
            }], 
            ['PasswordConfirm', 'compare', 'compareAttribute' => 'PasswordHash'],
            ['PasswordHash', 'string', 'min' => 5],
            ['IsActive', 'default', 'value' => self::STATUS_ACTIVE],
            ['IsActive', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['ViewNews', 'safe']
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserId' => Yii::t('app', 'User ID'),
            'Username' => Yii::t('app', 'User'),
            'Fullname' => Yii::t('app', 'Fullname'),
            'AuthKey' => Yii::t('app', 'Auth Key'),
            'PasswordHash' => Yii::t('app', 'Password'),
            'PasswordResetToken' => Yii::t('app', 'Password Reset Token'),
            'DistributorId' => Yii::t('app', 'Distributor'),
            'StcId' => Yii::t('app', 'Stc'),
            'Email' => Yii::t('app', 'Email'),
            'CreatedAt' => Yii::t('app', 'Created At'),
            'UpdatedAt' => Yii::t('app', 'Updated At'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }
    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'UserId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'UserId']);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['UserId' => $id, 'IsActive' => self::STATUS_ACTIVE]);
    }

    /**user_
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        
        return static::findOne(['AuthKey' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
		return static::findOne(['Username' => $username, 'IsActive' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'PasswordResetToken' => $token,
            'IsActive' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
	 *
	 *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->AuthKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
       // This line was change to maintain compatibility with current Dow Voucher list
	   // Matias Luzardi 2015-01-08
       // return Yii::$app->security->validatePassword($password, $this->PasswordHash);
        return (md5($password) === $this->PasswordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
		// This line was change to maintain compatibility with current Dow Voucher list
		// Matias Luzardi 2015-01-08
		 //$this->PasswordHash = Yii::$app->security->generatePasswordHash(md5($password));
       return md5($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->AuthKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->PasswordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->PasswordResetToken = null;
    }
      
    public function assignIdRol($rol,$id)
    {
        switch ($rol)
        {
           case 'stc' : $this->StcId = $id; break;  
           case 'distributor' : $this->DistributorId = $id; break;              
        }
    }	
		
    public function getItemName()
    { 
		//$item = AuthAssignment::find()->where(["user_id" => $id])->One();
		//return ( $item->item_name);
		$auth_a = $this->authAssignments[0];
		if ($auth_a)
                    return $auth_a->item_name;
    }

    public static function findPasswordByEmail($email)
    {
		$user= static::findOne(['Email' => $email, 'IsActive' => self::STATUS_ACTIVE]);
		if($user)
		{
			$pass = rand(100000, 500000);
		
			$user->PasswordHash = (md5($pass));
                        $user->Crop = "0";
                        $user->PasswordConfirm = $user->PasswordHash;
			$user->update();
			
			return $pass;
		}else
			return false;
		exit;
		
    }
    public function saveUser()
    {
        if ($this->validate()) {
            $user = $this->findOne($this->UserId);
            //$user = new User();
            $user->Username = $this->Username;
            $user->Email = $this->Email;
            if($user->PasswordHash != $this->PasswordHash )
                $user->PasswordHash = $user->setPassword($this->PasswordHash);
            
            $user->generateAuthKey();
            $user->PasswordConfirm = $user->PasswordHash;
            $user->Crop = 0;
            if(!$user->save()){print_r($user->getErrors());}
                       		
            $assign=  AuthAssignment::find()->where(['user_id' => $this->UserId])->one();
            $assign->item_name = $this->Role;
            $assign->user_id = $user->UserId;
            $assign->save();

            return $user;
        }

        return null;
    }
    
    public function deleteLogic() 
    {
        
       $this->IsActive = self::STATUS_DELETED;
       $this->Crop = self::STATUS_DELETED; 
       $crops = Cropbyuser::getCropsByUser($this->UserId);
       foreach($crops as $c)
       {
           $c->IsActive =0;
           $c->save();
       }
       
       if(!$this->save())
       {
           print_r($this->getErrors()); exit;
       };
       
       return true;          
    }
    
    public function getRoles()
    {
        return AuthItem::find()->all();
    }
    
    public function getCropbyusers()
    {
        return $this->hasMany(Cropbyuser::className(), ['UserId' => 'UserId']);
    }
     
    public function getCropbyusersArray()
    {
        //$crops = $this->getCropbyusers();
        
        $string = "<kbd>";
        foreach($this->cropbyusers as $c)
            $string .= $c->crop->Name."</kbd> <kbd>";
        
        
        return substr($string, 0, -5);
    }
    
    static function getUsersAddedByProject($projectId)
    {
        $array_users = [];
        $users = UserByProject::find()->where(["ProjectId" => $projectId])->all();
        
        if($users)
        {
            foreach($users as $user)
            {
                $array_users[] =  $user->UserId;
            }
        }
        //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return $array_users;
    }
    
    static function getUsersMailsAddedByProject($projectId)
    {
        $array_users = [];
        $users = UserByProject::find()->where(["ProjectId" => $projectId])->all();
        
        if($users)
        {
            foreach($users as $user)
            {
                $array_users[] =  $user->user->Email;
            }
        }
        //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return $array_users;
    }
    
    
    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        print_r('asasdsad'); exit;
        $response = $this->response ? : Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ? : Yii::$app->getUser(),
            $this->request ? : Yii::$app->getRequest(),
            $response
        );

        if ($identity !== null) {
            return true;
        } else {
            $this->challenge($response);
            $this->handleFailure($response);
            return false;
        }
    }
    
    
}
