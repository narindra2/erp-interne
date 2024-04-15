<?php

namespace App\Notifications;

use App\Http\Controllers\SuiviController;
use App\Models\SuiviItem;
use App\Models\User;
use stdClass;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewSuiviItemNotification extends Notification
{
    use Queueable;

    private $classification = "bell";
    private $event = "new_suivi_add";
    private $creator ;
    private $fake_id;
    private $item;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SuiviItem $item , User $creator)
    {
        $this->item = $item;
        $this->creator = $creator;
        $this->fake_id  = Str::uuid();;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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
            "suivi_item_id" => $this->item->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
        ];
    }
    public function toBroadcast($notifiable)
    {
        // $controller = new SuiviController();
        // $options = $controller->get_options();
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification($notifiable),
            // "extra_data" => [
            //     "type" => "dataTable",
            //     "table" => "suiviTable",
            //     "row" => $controller->_make_row( $this->item,$notifiable,$options)
            // ]
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["suivi_item_id"] =   $this->item->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->item;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification($notifiable)
    {
        $content = "Un nouveau dossier  vous a été attribué « {$this->item->suivi->folder_name} »";
        $redirect = url("/suivi/v2/projet?tab=tableau");
        return ["content" => $content, "title" => trans("lang.suivi") , "position" => "right" ,"duration" => "forever","redirect" => $redirect];
    }
}
