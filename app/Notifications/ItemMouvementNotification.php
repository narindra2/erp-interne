<?php

namespace App\Notifications;

use stdClass;
use App\Models\Item;
use App\Models\User;
use App\Models\Location;
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
    private $event = "new_item_mouvement";
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
        $notification->data["item_id"] =  $this->item->id;
        $notification->data["event"] = $this->event;
        $notification->data["updated"] =$this->updated;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $redirect = url("/stock/gerer");
        $content = self::get_content_sentence($this->creator->sortname ,$this->item , $this->updated );
        return ["content" => $content, "title" => trans("lang.task") , "position" => "right" ,"duration" => "forever" , "redirect" => $redirect] ;
    }
    static function get_content_sentence($creator , $item , $updated = [])
    {
        $sentence = "{$creator} a effectué(e) un mouvement sur l'article : « {$item->article->name} » dont le code article est «{$item->codeDetail} »";
        $old_location = get_array_value($updated ,"old_location");
        if ($old_location) {
            $new_location = get_array_value($updated ,"new_location");
            $places  = Location::findMany([$old_location,$new_location]);
            $place_old_info = $places->firstWhere("id",$old_location);
            $place_new_info = $places->firstWhere("id",$new_location);
            $sentence .= "<br>" ."<u>Lieu d'emplacement</u> : <strike> $place_old_info->name</strike> ->  $place_new_info->name ";
        }
        $old_place = get_array_value($updated ,"old_place");
        $new_place = get_array_value($updated ,"new_place");
        if ($old_place || $new_place) {
            $sentence .= "<br>" ."<u>Place</u> : <strike> $old_place </strike> ->  $new_place ";
        }
        $old_assigned = get_array_value($updated ,"old_assigned") ?? [];
        $new_assigned = get_array_value($updated ,"new_assigned" )?? [];
        
        if ($old_assigned || $new_assigned) {
            $users  = User::findMany(array_merge($old_assigned,$new_assigned));
            $old_assigned_users = $old_assigned ?  $users->whereIn("id",$old_assigned) : collect(["Personne"]);
            $new_assigned_users = $new_assigned ? $users->whereIn("id",$new_assigned) : collect(["Personne"]);
            $sentence .= "<br>" ."<u>En usage de</u> : <strike> {$old_assigned_users->implode('sortname',', ')} </strike> -> {$new_assigned_users->implode('sortname',', ')} ";
        }
        return $sentence;
    }
}
