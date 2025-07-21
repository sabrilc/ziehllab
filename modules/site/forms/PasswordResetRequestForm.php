<?php

namespace app\modules\site\forms;

use Yii;
use yii\base\Model;
use Symfony\Component\CssSelector\Parser\Reader;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['activo' => true],
                'message' => 'There is no user with such email.'
            ],
        ];
    }
    
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'activo' => true,
            'email' => $this->email,
        ]);
        
       
        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
             $user->generatePasswordResetToken();
            
           
            
            if (!$user->save(false)) {
                return false;
            }
        }
        
        return Yii::$app
        ->mailer
        ->compose(
            ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
            ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Cambio de clave para ' . Yii::$app->name)
            ->send();
    }
    
}

