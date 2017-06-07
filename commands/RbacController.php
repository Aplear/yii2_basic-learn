<?php
namespace app\commands;

use app\rbac\UserNewsOwnerRule;
use yii\console\Controller;
use \app\rbac\UserGroupRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = \Yii::$app->authManager;

        // Create roles
        $guest  = $authManager->createRole('guest');
        $manager  = $authManager->createRole('manager');
        $user = $authManager->createRole('user');
        $admin  = $authManager->createRole('admin');

        // add the rule
        $userNewsOwnerRule = new UserNewsOwnerRule();
        $authManager->add($userNewsOwnerRule);

        // Create simple, based on action{$NAME} permissions
        $login  = $authManager->createPermission('login');
        $logout = $authManager->createPermission('logout');
        $error  = $authManager->createPermission('error');
        $signUp = $authManager->createPermission('sign-up');
        $index  = $authManager->createPermission('index');
        $view   = $authManager->createPermission('view');
        $update = $authManager->createPermission('update');
        $delete = $authManager->createPermission('delete');
        $create = $authManager->createPermission('create');
        $details = $authManager->createPermission('details');
        $change_status = $authManager->createPermission('change-status');
        $userModuleCrud = $authManager->createPermission('userModuleCrud');
        $deleteOwnNews = $authManager->createPermission('deleteOwnNews');

        // Add permissions in Yii::$app->authManager
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($error);
        $authManager->add($signUp);
        $authManager->add($index);
        $authManager->add($view);
        $authManager->add($update);
        $authManager->add($delete);
        $authManager->add($create);
        $authManager->add($details);
        $authManager->add($change_status);
        $authManager->add($userModuleCrud);
        $authManager->add($deleteOwnNews);

        // Add rule, based on UserExt->group === $user->group
        $userGroupRule = new UserGroupRule();
        $authManager->add($userGroupRule);

        // Add rule "UserGroupRule" in roles
        $guest->ruleName  = $userGroupRule->name;
        $manager->ruleName  = $userGroupRule->name;
        $user->ruleName = $userGroupRule->name;
        $admin->ruleName  = $userGroupRule->name;
        $deleteOwnNews->ruleName = $userNewsOwnerRule->name;


        // Add roles in Yii::$app->authManager
        $authManager->add($guest);
        $authManager->add($manager);
        $authManager->add($user);
        $authManager->add($admin);


        // Add permission-per-role in Yii::$app->authManager
        // Guest
        $authManager->addChild($guest, $login);
        $authManager->addChild($guest, $logout);
        $authManager->addChild($guest, $error);
        $authManager->addChild($guest, $signUp);
        $authManager->addChild($guest, $index);
        $authManager->addChild($guest, $view);

        // MANAGER
        $authManager->addChild($manager, $details);
        $authManager->addChild($manager, $update);
        $authManager->addChild($manager, $deleteOwnNews);
        $authManager->addChild($manager, $guest);

        // USER
        $authManager->addChild($user, $details);
        $authManager->addChild($user, $guest);

        // Admin
        $authManager->addChild($admin, $delete);
        $authManager->addChild($admin, $create);
        $authManager->addChild($admin, $change_status);
        $authManager->addChild($admin, $userModuleCrud);
        $authManager->addChild($admin, $user);
        $authManager->addChild($admin, $manager);


    }
}