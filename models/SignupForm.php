<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $secret_key;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'app\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],

            ['status', 'default', 'value' => User::STATUS_ACTIVE, 'on' => 'default'],
            ['status', 'in', 'range' =>[
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE
            ]],
            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE, 'on' => 'emailActivation'],
            ['secret_key', 'unique']
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = $this->status;
        if(isset($this->password)) {
            $user->setPassword($this->password);
        }
        $user->generateAuthKey();
        if($this->scenario === 'emailActivation') {
            $user->generateSecretKey();
        }

        if($user->save(false)) {

            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();

            return $user;
        }
        return null;
    }

    /**
     * Send email with activation link
     * @param $user Object
     * @param $set_password boolean
     *
     * @return boolean
     */
    public function sendActivationEmail($user, $set_password=false)
    {
        return Yii::$app->mailer->compose(['html' => 'activationEmail-html', 'text' => 'activationEmail-text'], ['user' => $user, 'set_password' => $set_password])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (robot sent).'])
            ->setTo($this->email)
            ->setSubject('Activation for '.Yii::$app->name)
            ->send();
    }

}
