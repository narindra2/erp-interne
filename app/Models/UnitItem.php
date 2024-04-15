<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
