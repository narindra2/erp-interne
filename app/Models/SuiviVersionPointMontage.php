<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviVersionPointMontage extends Model
{
    use HasFactory;
    protected $table ="suivi_version_point_montage";
    protected $guarded = [];
    
    public function version()
    {
        return $this->belongsTo(SuiviVersion::class , "version_id");
    }
    public function base_calcul()
    {
        return $this->belongsTo(SuiviVersion::class , "version_id_base");
    }
}