<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateLeaveBalances::class,
        Commands\MakeSuiviItemFolderPauseOnDayFinish::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('queue:work');
        $schedule->command('schedule:suivi')->everyTwoMinutes();
        // $schedule->command('schedule:make-to-pause-all-suivi-items-inprogress');
        // $schedule->command('updateLeaveBalances:monthly')->monthly();
        //  $schedule->command('DesktopNotification');

        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
