<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayoffNatureColor extends Model
{
    use HasFactory;
    protected $table = 'dayoff_nature_color';
    protected $fillable = [
        'nature',
        'type',
        "color",
        "status",
        "deleted",
    ];

    function getTypeText() {
       if ($this->type == "dayoff") {
            return "Congé";
       }elseif ($this->type == "permission") {
            return "Permission";
       }elseif ($this->type == "status_report") {
            return "Rapport d etat";
       }else{
            return null;
       }
    }
    static function  getNaturesByType($type = null , $to_dropdown =  false) {
        $where_query = is_array($type) ? "whereIn" : "where";
        $natures = self::whereDeleted(0)->whereStatus(1)->$where_query("type", $type)->get();
        if ($to_dropdown ) {
           return to_dropdown( $natures , "id" , "nature");
        }
        return  $natures;
    }

}
