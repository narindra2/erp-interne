<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Notification;
class TicketJobNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $to;
    public $notification;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $notification)
    {
        $this->to = $to;
        $this->notification = $notification;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Notification::send($this->to , $this->notification);
    }
}
