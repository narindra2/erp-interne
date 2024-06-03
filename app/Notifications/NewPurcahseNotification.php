<?php

namespace App\Notifications;

use stdClass;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\PurchaseController;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewPurcahseNotification extends Notification
{
    use Queueable;


    public $purchase;
    public $creator;
    private $classification = "bell";
    private $event = "purchase_created";
    private $fake_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($purchase , $creator)
    {
       $this->purchase = $purchase;
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
        $via= [];
        if ($notifiable->id != $this->creator->id) {
            $via = ["database", "broadcast"];
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
            "purhcase_id" => $this->purchase->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
        ];
    }
    public function toBroadcast($notifiable)
    {
        $controller = new PurchaseController();
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification($notifiable),
            "extra_data" => [
                "type" => "dataTable",
                "table" => "purchasesList",
                "row" => $controller->_make_row($this->purchase, $notifiable)
            ]
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->purchase->author_id;
        $notification->data["purhcase_id"] =  $this->purchase->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->purchase;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable)
    {
        $content = "Un nouveau  demande  d' achat est ajouté par {$this->purchase->author->sortname}";
        $redirect = url("/purchases");
        return ["content" => $content, "title" => trans("lang.purchases") , "position" => "right" ,"duration" => "forever","redirect" => $redirect];
    }
}
