<?php

namespace App\Notifications;

use App\Http\Controllers\CheckInController;
use App\Models\Check;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use stdClass;
use Str;

class NegativeCumulativeHourNotification extends Notification
{
    use Queueable;

    private $hour_cumul;
    private $classification = "bell";
    private $event = "negative_cumulative_hour";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($hour_cumul ="")
    {
        $this->hour_cumul = $hour_cumul;
        
        $this->fake_id = Str::uuid();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database', 'broadcast'];
        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(env("MAIL_USERNAME") ,"ERP-31")
            ->subject($this->event)
            ->line(get_array_value($this->toast_notification(),"content"))
            ->action('Notification Action', url('/tickets'))
            ->line('Merci ! ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "event" => $this->event,
            "hour_cumul" => $this->hour_cumul,
            "created_by" =>null,
        ];
    }

    public function toBroadcast($notifiable){
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification(),
        ]);
    }

    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  null;
        $notification->data["event"] = $this->event;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id ;
        $notification->read_at =  null;
        $notification->hour_cumul = $this->hour_cumul;

        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }

    private function toast_notification(){
        $content = "Notification d'heure cumulée négative";
        return ["content" => $content , "title" => trans("lang.hour-cumul-negative")] ; 
    }

}
