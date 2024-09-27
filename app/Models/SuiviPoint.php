<?php

namespace App\Models;

use App\Models\Suivi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuiviPoint extends Model
{
    use HasFactory;

    protected $table ="suivi_points";
    protected $guarded = [];
    // protected $with = ["version"];

    public function suivi()
    {
        return $this->belongsToMany(Suivi::class , "suivi_and_points","point_id","suivi_id");
    }
    
    public function client_type()
    {
        return $this->belongsTo(SuiviType::class , "client_type_id");
    }
    public function project_type()
    {
        return $this->belongsTo(SuiviType::class , "project_type_id");
    }
    public function version()
    {
        return $this->belongsTo(SuiviVersion::class , "version_id");
    }
   
}
