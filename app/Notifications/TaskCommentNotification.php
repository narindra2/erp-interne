<?php

namespace App\Notifications;

use stdClass;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Http\Controllers\TaskController;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskCommentNotification extends Notification
{
    use Queueable;
/**
     * Create a new notification instance.
     *
     * @return void
     */
    public $task;
    public $creator;
    private $fake_id;
    private $classification = "bell";
    private $event = "task_commented";
    public function __construct(Task $task , User $creator)
    {
       $this->task = $task;
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
        return true;
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
            "task_id" => $this->task->id,
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
            "toast" => $this->toast_notification(),
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["task_id"] =  $this->task->id;
        $notification->data["event"] = $this->event;
        $notification->data["object"] = $this->task;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $string = "a commenté la tâche " ;
        if ($this->task->creator == $this->creator->id) {
            $string = "a ajouté une commentaire dans la tâche" ;
        }
        $content = "{$this->creator->sortname} $string : «"   . str_limite($this->task->title,30). "» - Section :  {$this->task->section->title} ";
        $redirect = url("/tâche/list");
        return ["content" => $content, "title" => trans("lang.task") , "position" => "right" ,"duration" => "forever" , "redirect" => $redirect] ;
    }
}
