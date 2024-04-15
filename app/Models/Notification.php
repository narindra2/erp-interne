<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = "notifications";
    public static $_PER = 20;
    protected $casts = [
        'data' => 'array', 
        'id' => 'string', // if it  is not casts string $notification->id retrun 0 ; 
        'data.updated' => 'array' 
    ];
    protected $fillable = [
        'read_at',
    ];
}

