<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\common\models\User */

?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>You can read a new post</p>

    <p><?= Html::a(Html::encode($newsLink), $newsLink) ?></p>
</div>
