<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProspectCompany extends Model
{
    use HasFactory;
    protected $table = "prospects_company";

    protected $casts = [

    ];

    protected $fillable = [
        "name_company",
        "tel_company",
        "email_company",
        "linkedin_company",
        "site_company",
        "size_company" ,
        "deleted"
    ];

    public function managers()
    {
        return $this->hasMany(ProspectCompanyManager::class, "company_id");
    }
    
}
