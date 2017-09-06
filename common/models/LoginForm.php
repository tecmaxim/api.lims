<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $Username;
    public $Password;
    public $RememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['Username', 'Password'], 'required'],
            // rememberMe must be a boolean value
            ['RememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['Password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
       
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->Password)) 
			{
                $this->addError($attribute, 'Username o Password Incorrecto.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
                  
           return Yii::$app->user->login($this->getUser(), $this->RememberMe ? 3600 * 24 * 30 : 0);
        } else {
			return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->Username);
//            if($this->_user)
//            {
//                //print_r("asda"); exit;
//                $item = $this->_user->
//
//                Yii::$app->session->set('role', $item );
//            }
           
        }

        return $this->_user;
    }
}
