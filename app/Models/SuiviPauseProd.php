<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviPauseProd extends Model
{
    use HasFactory;
    protected $table = "suivi_pause_history";
    protected $fillable = [ "user_id","status"];
}
