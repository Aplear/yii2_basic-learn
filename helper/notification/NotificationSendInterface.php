<?php

namespace app\helper\notification;

interface NotificationSendInterface
{
    /**
     * Implement interface for overwrite send notification for each object
     * */
    public function send();
}