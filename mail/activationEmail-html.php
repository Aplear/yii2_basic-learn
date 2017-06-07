<?php

use yii\helpers\Html;

$activateLink = Yii::$app->urlManager->createAbsoluteUrl([
        '/site/activate-account',
        'key' => $user->secret_key,
        'set_password' => $set_password
    ],
    ['target' => '_blank']
)
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

<p>Follow the link below to activate your account:</p>

<p><?= Html::a(Html::encode($activateLink), $activateLink) ?></p>
</div>
