<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFilter extends Model
{
    use HasFactory;

    protected $table = "customer_filter";
    protected $fillable = [
        "name_filter",
        "filters",
        "creator",
        "deleted",
    ];
}
