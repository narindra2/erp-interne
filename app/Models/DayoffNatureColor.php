<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayoffNatureColor extends Model
{
    use HasFactory;
    protected $table = 'dayoff_nature_color';
    protected $fillable = [
        'nature',
        "color",
        "status",
        "deleted",
    ];
}
