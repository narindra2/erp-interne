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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $task;
    public $creator;
    private $classification = "bell";
    private $event = "task_updated";
    private $fake_id;
    private $updated;
    public function __construct(Task $task , User $creator, $updated= [])
    {
       $this->task = $task;
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
            ->from(env("MAIL_USERNAME") ,"ERP-31")
            ->subject($this->event)
            ->line(get_array_value($this->toast_notification(),"content"))
            ->action('Detail', url('/tâche/list'))
            ->line('Merci ! ');
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
            "updated" => $this->updated,
        ];
    }
    public function toBroadcast($notifiable)
    {
        $controller = new TaskController();
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "item" => $this->prepare_notification_item($notifiable),
            "toast" => $this->toast_notification(),
            "extra_data" => [
                "type" => "kanban",
                "item" => ["board_id" => "board-id-".$this->task->status_id ,"section_id" =>$this->task->section_id ,"deleted" => $this->task->deleted,"archived" => $this->task->archived, "data" => $controller->_make_item_board($this->task)],
            ]
        ]);
    }
    public function prepare_notification_item($notifiable)
    {
        $notification = new stdClass();
        $notification->data["created_by"] =  $this->creator->id;
        $notification->data["task_id"] =  $this->task->id;
        $notification->data["event"] = $this->event;
        $notification->data["updated"] =$this->updated;
        $notification->created_at =  Carbon::now();
        $notification->id = $this->fake_id;
        $notification->read_at =  null;
        return view('notifications.template', ['notification' => $notification , "send_to" => $notifiable])->render();
    }
    private function toast_notification()
    {
        $content = "{$this->creator->sortname} a mis à jour la tâche «"   . str_limite($this->task->title,10). "»" ." dans la section : {$this->task->section->title} ";
        if ($this->task->deleted) {
            $content = "{$this->creator->sortname} a suppriné la tâche :  «"   . str_limite($this->task->title,10). "»";
        }
        $redirect = url("/tâche/list");
        return ["content" => $content, "title" => trans("lang.task") , "position" => "right" ,"duration" => "forever" , "redirect" => $redirect] ;
    }
}
