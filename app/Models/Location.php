<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    const STOCK_ID = 1;
    protected $fillable = [
        'name',
        'code_location',
        'deleted',
    ];
}
