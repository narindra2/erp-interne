<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SlackActivityNotification extends Notification
{
    use Queueable;
    private $info;
    private $causer_name;
    private $classification = "bell";
    private $event = "slack_activity";
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($causer_name = null, $info = [])
    {
        $this->info = $info;
        $this->causer_name = $causer_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }
    public function toBroadCast($notifiable) {
        return new BroadcastMessage([
            "event" => $this->event,
            "item" => null,
            "toast" => $this->toast_notification($notifiable),
        ]);
    }
    private function toast_notification()
    {
        $info = $this->info;
        $content = $this->causer_name ??  "Une activité dans Slack !";

        $type =  get_array_value($info , "type");
        $channel_name =  get_array_value($info, "channel_name");
        if($type == "message" && $channel_name ){
            $content =   $this->causer_name ." a envoyé une message dans le channel #$channel_name";
        }
        if($type == "message" && !$channel_name ){
            $content =   $this->causer_name ." vous a envoyé une message";
        }
        return ["content" => $content, "title" => trans("lang.slack_app")  , "position" => "right" ,"duration" => "forever"] ;
    }
}
