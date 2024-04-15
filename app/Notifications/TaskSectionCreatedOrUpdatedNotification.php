<?php

namespace App\Notifications;

use stdClass;
use App\Models\User;
use App\Models\TaskSection;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskSectionCreatedOrUpdatedNotification extends Notification
{
    use Queueable;


    public $creator;
    private $fake_id;
    private $changed;
    public $taskSection;
    private $classification = "bell";
    private $event = "task_section_created";

    public function __construct(TaskSection $taskSection, User $creator, $changed = [] , $update = false)
    {
        $this->taskSection = $taskSection;
        $this->creator = $creator;
        $this->fake_id = Str::uuid();
        $this->changed = $changed;
        $this->update = $update;
        if ($update) {
            $this->event = "task_section_updated";
        }
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

    
    public function toArray($notifiable)
    {
        return [
            "section_id" => $this->taskSection->id,
            "event" => $this->event,
            "created_by" => $this->creator->id,
            "updated" => $this->changed,
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" =>  $this->prepare_notification_item($notifiable) ,
            "toast" => $this->toast_notification(),
            "extra_data" => [
                "type" => "section_task",
                "item" => ["update" => $this->update ,"deleted" =>$this->taskSection->deleted ,"section_id" => $this->taskSection->id ,"data" => view("tasks.crud.section-item", ["section" => $this->taskSection ,"for_user" => $notifiable])->render()] ,
            ]
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["section_id"] =  $this->taskSection->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->taskSection;
        $notification->data["updated"] = $this->changed;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $content = "{$this->creator->sortname} a vous ajouter dans la section tâche :  «" .str_limite($this->taskSection->title,10). "»";
        if ($this->update) {
            $new = get_array_value( $this->changed , "new_title");
            $old = get_array_value( $this->changed , "old_title");
            $content = "{$this->creator->sortname} a modifié le non du section tâche  « $old » en «". str_limite($new,10). "»";
        }
        if ($this->taskSection->deleted) {
            $content = "{$this->creator->sortname} a supprimé la section tâche  «". str_limite($this->taskSection->title,10). "»";
        }
        $redirect = url("/tâche/list");
        return ["content" => $content, "title" => trans("lang.task") , "position" => "right" ,"duration" => "forever" , "redirect" => $redirect] ;
    }
}