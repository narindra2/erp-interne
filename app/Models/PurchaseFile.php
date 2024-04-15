<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'filename',
        'src'
    ];
}
