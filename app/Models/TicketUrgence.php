<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUrgence extends Model
{
    use HasFactory;
    protected $table = "tickets_urgence";
    protected $guarded = [];

    public function getLabelAttribute($value)
    {
        return "(" . $value . ")";
    }

    public static function drop($all = false, $moreInfo=false)
    {
        $data = [];
        $all ?  $data[] = ["value" => 0 , "text" => "Tout"] : "";
        $status = TicketUrgence::all();
        foreach ( $status as $u) {
            $label = "";
            if ($moreInfo)
                $label = "<span class='text-muted'>$u->label</span>";
            $data[] = ["value" => $u->id , "text" => trans("lang.{$u->name}") . "  " . $label];
        }
        return  $data;
    } 
}
