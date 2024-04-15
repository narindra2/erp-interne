<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlackDataCapture extends Model
{
    use HasFactory;
    
    protected $table = "slack_request";
    protected $fillable = [
        "data",
    ];
}
