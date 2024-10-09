<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class MakeSuiviItemFolderPauseOnDayFinish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:suivi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mettre en pause les dossiers en cours à {hour} heure';

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
        $this->info("ERP : Cron en cours ....");
        $response = Http::get(route('make_pause_all_suivi_item'));
        $responseBody = json_decode($response->body(), true);
        if (get_array_value($responseBody,"success")) {
            $this->info(get_array_value($responseBody,"message"));
        }else{
            $this->info("ERP : Cron ....");
            $this->info("ERP : Echec lors de mise en pause des dossiers suivi en cours à 20 heure "  .  now()->format("d-M-Y h:m:s"));
        }
    }
}
