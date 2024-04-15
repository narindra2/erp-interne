<?php

namespace App\Notifications;

use App\Events\EventNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class RedisTestNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    private $classification = "bell";
    private $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message = "")
    {
        $this->message = $message;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast',];
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
            "message" => $this->message,
        ];
    }
    public function toBroadcast($notifiable)
    {
        $brodcast = [
            "message" => $this->message,
        ];
        $this->toEvent($brodcast, $notifiable);
        return new BroadcastMessage( $brodcast);
    }
    public function toEvent($brodcast, $user)
    {
        event(new EventNotification($brodcast, $user));
    }
}
