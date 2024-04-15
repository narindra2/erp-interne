<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;
    protected $table = "tickets_status";
    protected $guarded = [];
    public static $_RESOLVED = [4,5];
    public static $_WAINTING_TO_BUY = 6;

    public static function drop($exlude_id = [], $all = false)
    {
        $data = [];
        if($all) $data[] = ["value" => "all" , "text" => "Tout"];
        
        $status = TicketStatus::query();
        if ($exlude_id) {
            foreach ($exlude_id as $id) {
                $status =  $status->where("id","<>",$id);
            }
        }
        $status =  $status->get();
        foreach ( $status as $st) {
            $data[] = ["value" => $st->id , "text" => trans("lang.{$st->name }")];
        }
        return  $data;
    }

    


    
}
