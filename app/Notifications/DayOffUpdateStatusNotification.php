<?php

namespace App\Notifications;

use stdClass;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DayOff;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Http\Controllers\DayOffController;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DayOffUpdateStatusNotification extends Notification
{
    use Queueable;

    private $dayOff;
    private $created_by;
    private $classification = "bell";
    private $event = "dayoff_updated_status";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DayOff $dayOff , User $created_by)
    {
        $this->dayOff  = $dayOff;
        $this->created_by  = $created_by;
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
        $via = ['database','broadcast'];
        if ($this->created_by->id == $notifiable->id) {
            $via = [];
        }
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
                    ->line('The introduction to the notification.')
                    ->subject($this->event)
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
        $notification->data["dayoff_id"] =  $this->dayOff->id;
        $notification->data["event"] = $this->event;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id ;
        $notification->created_at =  Carbon::now();
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable){
        $applicant = User::find($this->dayOff->applicant_id);
        $status= $this->dayOff->getResult();
        $to = $notifiable->id == $applicant->id ?  "votre demande de congé" : ("la demande de congé de " .$applicant->sortname);
        $content = "{$this->created_by->sortname} a ".trans("lang.{$status}") ." $to  ";
        if ($this->dayOff->is_canceled) $content = "{$this->created_by->sortname} a annulé(e) $to";
        return ["content" => $content , "title" => trans("lang.dayoff")] ; 
    }
}
