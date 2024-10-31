<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuiviUserParams extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table ="suivi_user_params";
    protected $fillable = [
        "user_id",
        "hours_works",
        "seuil_point",
        "days_work",
        "sum_note_quality",
        "month",
        "year",
        "deleted",
    ];
    protected $casts = [
        'hours_works'  => 'float',
        'seuil_point'  => 'float',
        'days_work'  => 'float',
        'sum_note_quality'  => 'float',
    ];
    public function suivi()
    {
        return $this->belongsTo(User::class , "user_id");
    }
}
