<?php

namespace app\models;

use Yii;
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
 * @property string $secret_key
 * @property string $group
 */
class User extends ActiveRecord
{

    public $secret_key;
    public $group_array = [
        'guest' => 'guest',
        'user' => 'user',
        'manager' => 'manager',
        'admin' => 'admin'
    ];

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
            ['username', 'unique', 'targetClass' => '\app\common\models\User', 'message' => 'This username has already been taken.', 'on' => 'create'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'app\common\models\User', 'message' => 'This email address has already been taken.', 'on' => 'create'],

            ['group', 'string'],

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
            'group' => 'Group',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
}
