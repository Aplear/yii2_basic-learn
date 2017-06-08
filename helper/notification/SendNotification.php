<?php

namespace app\helper\notification;

use app\models\User;
use yii\db\Exception;

/**
 * Created by PhpStorm.
 * User: aplear
 * Date: 07.06.17
 * Time: 15:38
 */

class SendNotification
{

    /**
     * @var $nofity
     */
    public $notifications;


    public function __construct($notification = array()) {
        $this->notifications = $notification;
    }


    public function sendNotification()
    {
        foreach ($this->notifications as $notification) {
            if(is_a($notification, 'app\helper\notification\NotificationSendInterface')){
                $notification->send();
                continue;
            }
            throw new Exception('Implement notification interface');
        }
    }
}