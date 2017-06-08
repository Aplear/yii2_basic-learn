<?php

namespace app\models;

use Yii;
use app\models\User;

use yii\db\ActiveRecord;

/**
 * This is the model class for grab users notify.
 */
class UserNotifyGraber extends ActiveRecord
{
    /**
     * @var $email_notify_user
     */
    public static $email_notify_user;

    /**
     * @var $browser_notify_user
     */
    public static $browser_notify_user;

    /**
     * @var $nofity
     */
    public static $nofity;


    /**
     * @param $nofity
     * @return array
     */
    public static function prepareData($nofity)
    {
        static::$nofity = $nofity;

        $active_users = User::find()
            ->where(['user.status'=>10])
            ->all();

        foreach ($active_users as $user) {
            if(!empty($user->profile['rss_email'])) {
                static::$email_notify_user[$user->id] = [
                    'user' => $user,
                    'notification_link' => static::$nofity
                ];
            }

            if(!empty($user->profile['rss_browser'])) {
                static::$browser_notify_user[$user->id] = [
                    'user' => $user,
                    'notification_link' => static::$nofity
                ];
            }
        }

        return [
            'email' => static::$email_notify_user,
            'browser' => static::$browser_notify_user,
        ];
    }
}