<?php

namespace App\Models;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static $civility = ['Mr','Mme'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function type()
    {
        return $this->belongsTo(CustomerType::class, "customer_type_id");
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }

    public function getFullnameAttribute()
    {
        return $this->firstname . " " . "" .$this->lastname;
    }

}
