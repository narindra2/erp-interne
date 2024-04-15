<?php

use App\Models\NotificationTemplate;

if (!function_exists('get_template_info')) {
    function get_template_info($notification = null , $notifiable = null)
    {
        $event = $notification->data["event"];
        if ($event) {
            if (method_exists(NotificationTemplate::class, $event)) {
                return NotificationTemplate::$event($notification,$notifiable);
            }
        }
    }
}

