<?php

use yii\helpers\Html;
echo 'Hi '.Html::encode($user->username).'.';
echo Html::a(Html::encode('Foward this link for activation your account.'),
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/activate-account',
            'key' => $user->secret_key,
            'set_password' => $set_password
        ],
        ['target' => '_blank']
    ));