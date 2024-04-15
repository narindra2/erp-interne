<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysOffAttachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'days_off_id',
        'url',
        'filename'
    ];
}
