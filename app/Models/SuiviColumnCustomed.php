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
        "3" => "DOSSIER",
        "4" => "Réference",
        "5" => "Type de client",
        "6" => "Types",
        "2" => "Ankizy",
        "7" => "Pôles",
        "8" => "Point",
        "9" => "M2p",
        "10" => "Version",
        "11" => "Montage",
        "12" => "Duration",
        "13" => "Statut",
        "14" => "Action",
        "15" => "duration_hidden",
        "16" => "extra_action",
    ];
    public static $NOT_CUSTOMABLE_COLUMNS = [0,1,8,13,14,15,16];
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