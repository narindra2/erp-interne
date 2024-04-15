<?php

namespace App\Notifications;

use App\Models\Contact;
use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\User;
use Broadcast;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use stdClass;

class NewMessageNotification extends Notification
{
    use Queueable;
    private $messages;
    private $author;
    private $messageGroup;
    private $conversations;
    private $contactView;
    private $event = "new_message";
    private $classification = "chat";
    private $fake_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $author, $messageGroup, $messages, $conversations, $contactView) 
    {
        $this->messages = $messages;
        $this->author = $author;
        if ($messageGroup != null)
            $this->messageGroup = $messageGroup;
        $this->conversations = $conversations;
        $this->contactView = $contactView;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }


    public function getBubbleView() {
        if ($this->messageGroup != null) {
            return view('messaging.bubbles.bubble-group', ['messageGroup' => $this->messageGroup, 'count' => $this->messages->count(), 'name' => $this->messageGroup->name, 'id' => $this->messageGroup->id])->render();
        }
        return view('messaging.bubbles.bubble', ['message' => $this->messages->last(), 'count_messages_not_seen' => $this->messages->count()])->render();
    }

    public function toBroadCast($notifiable) {
        // dd($this->messageGroup->id);
        $this->messages->last()->_my_message = false;
        return new BroadcastMessage([
            "classification" => $this->classification,
            "event" => $this->event,
            "extra_data" => [
                "type" => "message",
                "group_id" => ($this->messageGroup != null) ? $this->messageGroup->id : 0,
                "sender_id" => $this->author->id,
                "contact_view" => $this->contactView,
                "conversations" => $this->conversations,
                "notification_count" => $this->messages->count(),
                "messageModalView" => Message::getMultipleMessageView($this->messages),
                "bubbleView" => $this->getBubbleView()
                
                // "target" => "user-item-message-" . $this->message->sender_id . '-' . $this->message->receiver_id,
                // "target2" => "user-item-message-" . $this->message->receiver_id . '-' . $this->message->sender_id,
                // "target_master" => "#list-message",
                // "item" =>  view('messaging.message-body', ['message' => $this->message])->render(),
                // "sender" => $this->message->sender_id,
                // "contact" => [
                //     'sender_id' => $this->message->sender_id,
                //     "view" => view('messaging.contact-item', ['contact' => Contact::createContactInstanceByMessage($this->message)])->render()
                // ]
            ]
        ]);
    }
}
