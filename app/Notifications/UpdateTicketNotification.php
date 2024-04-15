<?php

namespace App\Notifications;

use stdClass;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Http\Controllers\TicketController;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UpdateTicketNotification extends Notification
{
    use Queueable;
    public $ticket;
    public $creator;
    private $classification = "bell";
    private $event = "ticket_updated";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket ,User $creator)
    {
        $this->ticket = $ticket;
        $this->creator = $creator;
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
      
        $via = [];
        if ($this->creator->id != $notifiable->id) {
            $via = ["database", "broadcast"];
        }
        return $via;
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
            "ticket_id" => $this->ticket->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
        ];
    }
    public function toBroadcast($notifiable)
    {
        $controller = new TicketController();
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification($notifiable),
            "extra_data" => [
                "type" => "dataTable",
                "table" => "ticketsTable",
                "row_id" => row_id("tickets",$this->ticket->id),
                "row" => $controller->_make_row($this->ticket,$notifiable)
            ]
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["ticket_id"] =  $this->ticket->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->ticket;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable)
    {   
        $tag = "le ticket " . $this->ticket->tag();

        if ($notifiable->id == $this->ticket->author_id) {
            $tag = "le ticket  de {$this->ticket->owner->sortname} " . $this->ticket->tag();
        }
        if ($notifiable->id == $this->ticket->proprietor_id) {
            $tag =  "votre ticket ";
        }
      
        if ($this->ticket->is_resolved()) {
            $content = "{$this->creator->sortname} a  " . trans("lang.{$this->ticket->status->name}")." {$tag} ";
        }else{
            $content = "{$this->creator->sortname} a mis ".trans("lang.{$this->ticket->status->name}"). " {$tag} ";
        }
        return ["content" => $content, "title" => trans("lang.ticket") , "position" => "right" ,"duration" => "forever"];
    }
}