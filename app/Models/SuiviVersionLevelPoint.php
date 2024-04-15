<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviVersionLevelPoint extends Model
{
    use HasFactory;

    protected $table = "suivi_level_points";
    public $timestamps = false;
    protected $fillable = [
        'version_id',
        'level',
        'point',
        'deleted',
    ];
}
