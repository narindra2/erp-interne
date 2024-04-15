<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SlackAppNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $classification = "bell";
    private $event = "slack_notification";
    public $slack_data;
    public function __construct($data)
    {
        $this->slack_data = $data;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ["broadcast"];
    }
    public function toBroadcast($notifiable)
    {
     
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "toast" => $this->toast_notification(),
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        return view('notifications.template', ['notification' => $this->slack_data , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $content = "Une activité est decté dans slack";
        return ["content" => $content, "title" => trans("lang.slack") ] ;
    }
}
