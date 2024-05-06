<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviTypeClient extends Model
{
    use HasFactory;
    protected $table ="suivi_type_client";
    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class , "user_id");
    }
    public static function dropdown(){
        $types = self::select("name","id","deleted","status")->whereStatus("on")->whereDeleted(0)->latest()->orderBy("name","ASC")->get();
        return $types;
    }
    public  function project_types(){
        return $this->belongsToMany(
            SuiviType::class,
            "suivi_points",
            "client_type_id",
            "project_type_id")
            ->orderBy('pivot_created_at', 'desc')
            ->wherePivot ('deleted', 0)
            ->orderByPivot("niveau","desc")
            ->withPivot("id","niveau", "point","pole", "point_sup","version_id","created_at");
    }
}
