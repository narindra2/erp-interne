<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        "name"
    ];

    public $table = "daily_activity";

    public static $_ATTENDANCE = 1;
    public static $_DAYOFF = 2;
}
