<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectCompanyManager extends Model
{
    use HasFactory;
    protected $table = "prospects_company_manager";
    
    protected $casts = [
        
    ];

    protected $fillable = [
        "name_manager",
        "tel_manager",
        "email_manager",
        "site_manager",
        "deleted"
    ];
}
