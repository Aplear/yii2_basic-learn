<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\common\models\User as CommonUser;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logined_at
 */
class User extends ActiveRecord
{

    public $secret_key;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

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

            ['status', 'default', 'value' => CommonUser::STATUS_ACTIVE, 'on' => 'default'],
            ['status', 'in', 'range' =>[
                CommonUser::STATUS_NOT_ACTIVE,
                CommonUser::STATUS_ACTIVE
            ]],
            ['status', 'default', 'value' => CommonUser::STATUS_NOT_ACTIVE, 'on' => 'emailActivation'],
            ['secret_key', 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'logined_at' => 'Logined At',
        ];
    }

    /**
     * Create user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function createUser()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new CommonUser();
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

        return $user->save(false) ? $user : null;
    }

    public function sendActivationEmail($user, $set_password=false)
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user, 'set_password' => $set_password])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (robot sent).'])
            ->setTo($this->email)
            ->setSubject('Activation for '.Yii::$app->name)
            ->send();
    }
}
