<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public static $address_project_declarant = ['Oui','Non'];

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

}
