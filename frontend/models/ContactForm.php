<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $Name;
    public $Email;
    public $Subject;
    public $Body;
    public $VerifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['Name', 'Email', 'Subject', 'Body'], 'required'],
            // email has to be a valid email address
            ['Email', 'email'],
            // verifyCode needs to be entered correctly
            ['VerifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'VerifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string  $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->Email => $this->Name])
            ->setSubject($this->Subject)
            ->setTextBody($this->Body)
            ->send();
    }
}
