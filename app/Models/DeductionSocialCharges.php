<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionSocialCharges extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_payments_id',
        'social_charges_id',
        'percent'
    ];

    public function socialCharge()
    {
        return $this->belongsTo(SocialCharge::class, 'social_charges_id');
    }

    public function employeePayment()
    {
        return $this->belongsTo(EmployeePayment::class, 'employee_payments_id');
    }

    public function getSalaryDeduction($salary)
    {
        return $salary * $this->percent / 100;
    }
}
