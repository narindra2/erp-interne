<?php

namespace App\Notifications;

use stdClass;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


class ItemMouvementNotification extends Notification
{
    use Queueable;

    public $item;
    public $creator;
    private $classification = "bell";
    private $event = "item_mouvement";
    private $fake_id;
    private $updated;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Item $item , User $creator, $updated= [])
    {
        $item->load("article");
       $this->item = $item;
       $this->creator = $creator;
       $this->fake_id = Str::uuid();
       $this->updated = $updated;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ["database", "broadcast"];
        if ($this->creator->id == $notifiable->id) {
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
            "item_id" => $this->item->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
            "updated" => $this->updated,
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
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["task_id"] =  $this->item->id;
        $notification->data["event"] = $this->event;
        $notification->data["updated"] =$this->updated;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $content = "{$this->creator->sortname} a mis à jour la tâche «"   . str_limite($this->item->title,10). "»" ." dans la section : {$this->task->section->title} ";
        if ($this->item->deleted) {
            $content = "{$this->creator->sortname} a suppriné la tâche :  «"   . str_limite($this->item->title,10). "»";
        }
        $redirect = url("/tâche/list");
        return ["content" => $content, "title" => trans("lang.task") , "position" => "right" ,"duration" => "forever" , "redirect" => $redirect] ;
    }
}
