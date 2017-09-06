<?php
namespace api\models;

use common\models\User;
use common\models\Cropbyuser;
use common\models\AuthAssignment;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $Username;
    public $Email;
    public $Password;
    public $Role;
    public $Crop;
    public $PasswordConfirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Username'], 'filter', 'filter' => 'trim'],
            [['Username'], 'required'],
            [['Username'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            [['Username'], 'string', 'min' => 2, 'max' => 255],

            [['Email'], 'filter', 'filter' => 'trim'],
            [['Email'], 'email'],
            [['Email'], 'safe'],
            [['Role'], 'required'],
            [['Email'],  'required'],
            [['Email'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            [['Password'], 'required'],
            [['PasswordConfirm'], 'required'],
            ['PasswordConfirm', 'compare', 'compareAttribute' => 'Password'],
            [['Password'], 'string', 'min' => 6],
            
            [['Crop'], 'required'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) 
        {
  
            $user = new User();
            $user->Username = $this->Username;
            $user->Email = $this->Email;
            $user->PasswordHash = $user->setPassword($this->Password);
            $user->PasswordConfirm = $user->PasswordHash;
            $user->generateAuthKey();
            $user->Crop = 0;
            if($user->save())
            {  
                $assign= new AuthAssignment();
                $assign->item_name = $this->Role;
                $assign->user_id = $user->UserId;
                    if(!$assign->save()){print_r($assign->getErrors()); exit;}

                $cropbyUser = new Cropbyuser();
                
                foreach($this->Crop as $k => $v)
                {
                    $cropbyUser->UserId = $user->UserId;
                    $cropbyUser->Crop_Id = $v;
                    $cropbyUser->IsActive = 1;
                    $cropbyUser->save();
                }
            }
            return $user;
        }
        return false;
    }
	
}
