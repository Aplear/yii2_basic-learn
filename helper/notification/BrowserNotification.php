<?php

namespace app\helper\notification;
use app\helper\notification\NotificationSendInterface;
use app\models\Notification;
use yii\helpers\Html;

class BrowserNotification implements NotificationSendInterface {
    /**
     * @var $browser_notify_user
     */
    public $browser_notify_user;

    public function __construct($browser_notify_user) {
        $this->browser_notify_user = $browser_notify_user;
    }

    public function send()
    {
        //if user array

        if(!empty($this->browser_notify_user)) {

            $model = new Notification();
            //set notification for each user
            foreach ($this->browser_notify_user as $key => $item) {
                $notification = "New post - <a href='".$item['notification_link']."'>Link...</a>";
                $model->notification = Html::decode($notification);
                $model->user_id = $key;
                $model->save();
            }
        }
    }

}