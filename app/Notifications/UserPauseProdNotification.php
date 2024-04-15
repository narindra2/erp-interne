<?php

namespace App\Notifications;

use stdClass;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserPauseProdNotification extends Notification
{
    use Queueable;
    public $user;
    private $classification = "bell";
    private $event = "user_pause_prod";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return ["database", "broadcast"];
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
            "created_by" => $this->user->id,
            "message" => $this->toast_notification()["content"],
        ];
    }

    public function toBroadcast($notifiable)
    {
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
        $notification->data["created_by"] =  $this->user->id;
        $notification->data["event"] = $this->event;
        $notification->data["message"] = $this->toast_notification()["content"];
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification(){
        return ["content" => $this->user->sortname ." a mis son statut « en attent de dossier ».", "title" => trans("lang.folder")];
    }
}
