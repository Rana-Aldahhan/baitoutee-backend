<?php

namespace App\Services;

use App\Jobs\SendPushNotification;

class FCMService
{ 
    public static function sendPushNotification($to, $title,$body)
    {
        if($to==null)
            {
                //don't send if the reciever is null
                return;
            }
        SendPushNotification::dispatch($to,$title,$body);
    }
}