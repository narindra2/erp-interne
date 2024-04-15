<?php

namespace App\Notifications;

use App\Models\ContractType;
use stdClass;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ContratChangedNotification extends Notification
{
    use Queueable;

    public $user;
    public $creator;
    public $contrat;
    private $classification = "bell";
    private $event = "contrat_changed";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user ,User $creator , ContractType $contrat)
    {
        $this->user = $user;
        $this->creator = $creator;
        $this->contrat = $contrat;
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
        $via = ["database"];
        if ($this->creator->id != $notifiable->id) {
            $via[] = "broadcast";
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
            "user_id" => $this->user->id,
            "contract_type_id" => $this->contrat->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
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
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["user_id"] =  $this->user->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->contrat;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable)
    {
        if ($notifiable->id == $this->user->id ) {
            $content = "Bonjour {$this->user->sortname}! Votre type de contrat est renouvelé en «{$this->contrat->name}» ({$this->contrat->acronym}) par {$this->creator->fullname}";
        }else{
            $content = "Le type de contrat de {$this->user->sortname} a été renouvelé en «{$this->contrat->name}» ({$this->contrat->acronym}) par {$this->creator->fullname}";
        }
        $redirect = url("account/settings");
        return ["content" => $content, "title" => "Info emploi" , "position" => "right" , "redirect" => $redirect];
    }
}
