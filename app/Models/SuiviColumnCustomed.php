<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuiviColumnCustomed extends Model
{
    use HasFactory;
    protected $table = "suivi_custome_columns";
    public $timestamps = false;
    protected $fillable = ["user_id","columns_hidden"];
    public  static $TABLE_ALLOWED_COLUMNS = [
        "0" => "details",
        "1" => "clone",
        "2" => "DOSSIER",
        "3" => "Réference",
        "4" => "Type de client",
        "5" => "Types",
        "6" => "Ankizy",
        "8" => "Pôles",
        "9" => "Point",
        "10" => "M2p",
        "11" => "Version",
        "12" => "Montage",
        "13" => "Duration",
        "14" => "Statut",
        "15" => "Action",
        "16" => "duration_hidden",
        "17" => "extra_action",
    ];
    public static $NOT_CUSTOMABLE_COLUMNS = [0,1,8,13,14,15,16,17];
    public static  function get_static_columns(){
        return collect( self::$TABLE_ALLOWED_COLUMNS);
    }
    public static  function get_user_hidden_columns(){
        return self::where("user_id",Auth::id())->first() ?? new SuiviColumnCustomed();
    }
    public static  function get_user_hidden_columns_array(){
        $data = self::get_user_hidden_columns();
        if ($data->columns_hidden) {
            return explode(",",$data->columns_hidden);
        }
        return [];
    }
}