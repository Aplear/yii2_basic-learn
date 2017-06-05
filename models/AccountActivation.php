<?php

namespace app\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use app\common\models\User;
use Yii;
/* @property string $username */
class AccountActivation extends Model
{
    /* @var $user \app\models\User */
    private $_user;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6]
        ];
    }

    public function __construct($key, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('The key can not be blank!');
        $this->_user = User::findBySecretKey($key);
        if(!$this->_user)
            throw new InvalidParamException('Invalid key!');
        parent::__construct($config);
    }
    public function activateAccount()
    {
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removeSecretKey();
        return $user->save();
    }
    public function getUsername()
    {
        $user = $this->_user;
        return $user->username;
    }

    public function setNewPassword()
    {
        if (!$this->validate()) {
            return null;
        }

        if(isset($this->password)) {
            $this->_user->setPassword($this->password);
        }

        return true;
    }
}