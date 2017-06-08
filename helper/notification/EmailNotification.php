<?php

namespace app\helper\notification;

use Yii;
use app\helper\notification\NotificationSendInterface;

class EmailNotification implements NotificationSendInterface {
    /**
     * @var $email_notify_user
     */
    public $email_notify_user;

    /**
     * EmailNotification constructor.
     * @param $email_notify_user
     */
    public function __construct($email_notify_user) {
        $this->email_notify_user = $email_notify_user;
    }

    /**
     * send notification to email user
     * */
    public function send()
    {
        //if user array
        if(!empty($this->browser_notify_user)) {
            //for each user
            foreach ($this->email_notify_user as $item) {

                Yii::$app->mailer->compose(['html' => 'notificationEmail-html'], ['user' => $item['user'], 'newsLink' => $item['notification_link']])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (robot sent).'])
                    ->setTo($item['user']->email)
                    ->setSubject('Notification for '.$item['user']->username)
                    ->send();
            }
        }

    }
}