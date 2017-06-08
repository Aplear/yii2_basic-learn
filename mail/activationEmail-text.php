<?php

/* @var $this yii\web\View */
/* @var $user app\common\models\User */

$activateLink = Yii::$app->urlManager->createAbsoluteUrl([
    '/site/activate-account',
    'key' => $user->secret_key,
    'set_password' => $set_password
])
?>
Hello <?= $user->username ?>,

Follow the link below to activate your account:

<?= $activateLink ?>
