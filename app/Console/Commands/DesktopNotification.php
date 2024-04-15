<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DesktopNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desktop:notification {title} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desktop notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->notify(
            $this->argument('title'),
            $this->argument('message'),
            public_path('icons/erp.png')
        );
    }
}
