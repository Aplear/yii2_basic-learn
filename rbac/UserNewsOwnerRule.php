<?php
namespace app\rbac;

use yii\rbac\Rule;
use yii\rbac\Item;

class UserNewsOwnerRule extends Rule
{
    public $name = 'isNewsOwner';

    /**
     * @param string|integer $user   the user ID.
     * @param Item           $item   the role or permission that this rule is associated with
     * @param array          $params parameters passed to ManagerInterface::checkAccess().
     *
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (\Yii::$app->user->identity->group == 'admin') {
            return true;
        }
        return isset($params['news_id']) ? \Yii::$app->user->id == $params['news_id'] : false;
    }
}