<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    
    public  function command($cmd = "")
    {
        if ($cmd == "cache") {
            Artisan::call('cache:clear');
        }elseif ($cmd == "all" || !$cmd ) {
            Artisan::call('optimize');
        }elseif ($cmd == "schedule_run") {
            Artisan::call('schedule:run');
        }else{
            Artisan::call("$cmd");
        }
        return ["success" => true ,"command" => $cmd ?? "optimize" ];
    }
}
