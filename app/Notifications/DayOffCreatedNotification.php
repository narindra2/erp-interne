<?php

namespace App\Notifications;

use stdClass;
use App\Models\User;
use App\Models\DayOff;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Http\Controllers\DayOffController;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DayOffCreatedNotification extends Notification
{
    use Queueable;

    private $dayOff;
    private $created_by;
    private $classification = "bell";
    private $event = "dayoff_created";
    private $fake_id;
    private $update;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DayOff $dayOff , User $created_by, $update=false)
    {
       $this->dayOff  = $dayOff;
       $this->created_by  = $created_by;
       $this->fake_id = Str::uuid();
       $this->update = $update;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database','broadcast'];
        if ($notifiable->id == $this->created_by->id) $via = [];
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
            ->line(get_array_value($this->toast_notification($notifiable),"content"))
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
            "dayoff_id" => $this->dayOff->id,
            "event" => $this->event,
            "created_by" => $this->created_by->id ?? null,
            "applicant_id" => $this->dayOff->applicant_id,
            'update' => $this->update,
        ]; 
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification($notifiable),
        ]);
    }

    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->created_by->id ?? null;
        $notification->data["update"] = $this->update;
        $notification->data["dayoff_id"] =  $this->dayOff->id;
        $notification->data['applicant_id'] = $this->dayOff->applicant_id;
        $notification->data["event"] = $this->event;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id ;
        $notification->created_at =  Carbon::now();
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable){
        $content = "Une nouvelle demande de congé ";
        if ($this->update)    $content = $this->dayOff->applicant->sortname . " a modifié sa demande de congé.";
        $redirect = url("/days-off");
        return ["content" => $content , "title" => trans("lang.dayoff") , "redirect" => $redirect] ; 
    }
}
