<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLeaveBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateLeaveBalances:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update employee leave balances every first month';

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

        $nb = DB::table('test_cron')->first()->nb;
        DB::table('test_cron')->update(['nb' => $nb + 1]);
        $this->info('CRON OVH finished successfully');
    }

    private function user_pe_finish(){
        $users = DB::table("users")
                    ->selectRaw('SUM(DATEDIFF(return_date , start_date)) AS total')
                    
                    ->where("deleted" ,0)
                    ->whereYear('created_at', date('Y'))
                    ->get();
    }
}
