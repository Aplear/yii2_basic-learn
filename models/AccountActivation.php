<?php

namespace app\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use app\common\models\User;


class AccountActivation extends Model
{
    /* @var $user \app\models\User */
    private $_user;
    /* @var $password \app\models\User */
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

    /**
     * AccountActivation constructor.
     * @param array $key
     * @param array $config
     */
    public function __construct($key, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('The key can not be blank!');
        $this->_user = User::findBySecretKey($key);
        if(!$this->_user)
            throw new InvalidParamException('Invalid key!');
        parent::__construct($config);
    }

    /**
     * Save user with activate status
     * @return bool
     */
    public function activateAccount()
    {
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removeSecretKey();
        return $user->save();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        $user = $this->_user;
        return $user->username;
    }

    /**
     * @return bool|null
     */
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