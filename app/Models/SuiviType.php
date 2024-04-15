<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviType extends Model
{
    use HasFactory;
    protected $table ="suivi_types";
    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class , "user_id");
    }
    public function suivi()
    {
        return $this->belongsToMany(Suivi::class , "suivi_and_types","type_id","suivi_id");
    }
    public function point()
    {
        return $this->belongsToMany(Suivi::class , "suivi_and_types","type_id","suivi_id");
    }

    public static function dropdown(){
        $types = self::select("name","id","deleted")->whereDeleted(0)->orderBy("name","ASC")->get();
        return $types;
    }
    public  function client_types(){
        return $this->belongsToMany(
            SuiviTypeClient::class,
            "client_type_id",
            "suivi_points",
            "project_type_id")
            ->withPivot(["niveau", "point", "point_sup"]);
    }
}
