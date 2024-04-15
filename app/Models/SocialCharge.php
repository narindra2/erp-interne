<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'percent',
        'employee_payments_id'
    ];

    public function deduction($grossSalary)
    {
        return $this->percent * $grossSalary / 100;
    }
}
