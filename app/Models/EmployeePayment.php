<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeePayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_date',
        'net_salary',
        'users_id',
        'nb_days_off_last_month'
    ];

    public function deductionSocialCharge()
    {
        return $this->hasMany(DeductionSocialCharges::class, 'employee_payments_id');
    }

    public function socialCharge()
    {
        return $this->hasMany(SocialCharge::class, 'social_charges_id');
    }

    public static function saveForm($input, $socialCharges)
    {
        try {
            DB::beginTransaction();

            $form = EmployeePayment::getForm($input['users_id'], $input['year'], $input['month']);
            if (is_null($form->created)) {
                
                //Save employeePayment
                $employeePayment = new EmployeePayment();
                $employeePayment->users_id = $input['users_id'];
                $employeePayment->net_salary = $form->getNetSalary();
                $employeePayment->month = $form->month;
                $employeePayment->year = $form->year;
                $employeePayment->nb_days_off_last_month = $form->getNbDaysOffRemaining();
                $employeePayment->save();

                $user = User::find($input['users_id']);
                $user->nb_days_off_remaining = $form->getNbDaysOffRemaining();
                $user->save();

                //Save deduction
                foreach($socialCharges as $socialCharge) {
                    $deductionSocialCharge = new DeductionSocialCharges();
                    $deductionSocialCharge->percent = $input['socialCharge-' . $socialCharge->id];
                    $deductionSocialCharge->social_charges_id = $socialCharge->id;
                    $deductionSocialCharge->employee_payments_id = $employeePayment->id;
                    $deductionSocialCharge->save();
                }
            }
            else {
                throw new Exception("Une fiche a été déjà créé pour cette date");
            }

            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function getFormById($id)
    {
        $document = EmployeePayment::with(['deductionSocialCharge.socialCharge'])->find($id);
        if ($document == null)  throw new Exception("Fiche de paie introuvable");
        $document->addVariousInformation();
        return $document;
    }

    public static function getForm($idUser, $year=null, $month=null)
    {
        if ($year == null)  $year = Carbon::now()->year;
        if ($month == null) $month = Carbon::now()->month;
        $document = EmployeePayment::with(['deductionSocialCharge.socialCharge'])
            ->whereDeleted(0)
            ->where('users_id', $idUser)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($document == null) {
            $document = new EmployeePayment();
            $document->month = $month;
            $document->year = $year;
            $document->users_id = $idUser;
        }
        
        else {
            $document->payslip_exist = true;
        }

        $document->addVariousInformation();
    
        return $document;
    }

    public function addVariousInformation()
    {
        //Set user information to the document
        $this->employeeInformation = UserJob::getActualUserJob($this->users_id)->first();
        $nbDays = MvtAttendance::countNbDaysPerformed($this->users_id, $this->year, $this->month) ;
        //Set the days performed and days off of the user to the month and year selected
        $this->nbDaysPerformed = $nbDays["work"];
        $this->nbDaysOff = $nbDays["dayOff"];
    }

    public function calculNbDaysPerformed()
    {
        $today = Carbon::now();
        $dateBegin = Carbon::make("$this->year-$this->month-1");
        
        // getNbDaysInMonth();
        $nbDaysPerformed = MvtAttendance::getEmployeeMvtByType($this->users_id, 0, $this->year, $this->month)->count();
    }

    public function getNbDaysOffLastMonth()
    {
        if ($this->nb_days_off_last_month == null) return $this->employeeInformation->user->nb_days_off_remaining;
        return $this->nb_days_off_last_month;
    }

    public function getNbDaysOffRemaining()
    {
        $bonus = 2.5;
        $hiringDate = Carbon::make($this->employeeInformation->user->hiring_date);
        if ($hiringDate->gt(Carbon::make("$this->year-$this->month-1"))) {
            $bonus = ($this->getNbDaysInMonth() - $hiringDate->day) * $bonus  / $this->getNbDaysInMonth();
        }
        return $this->getNbDaysOffLastMonth() - $this->nbDaysOff + $bonus;
    }

    public function getNetSalary()
    {
        if (!isset($this->net_salary)) {
            return $this->daily_salary * $this->nbDaysPerformed + $this->daily_salary * $this->nbDaysOff - $this->total_salary_advance;
        }
        else {
            return $this->net_salary;
        }
    }

    public function getGrossSalaryAttribute()
    {
        $grossSalary = $this->employeeInformation->salary;
        foreach($this->deductionSocialCharge as $deduction) {
            $grossSalary += $deduction->getSalaryDeduction($this->employeeInformation->salary);
        }
        return $grossSalary;
    }

    public function getDailySalaryAttribute()
    {
        return $this->employeeInformation->salary / $this->getNbDaysInMonth();
    }

    public function getAmountDay($type=0)
    {
        if ($type==0) return $this->daily_salary * $this->nbDaysPerformed;
        return $this->daily_salary * $this->nbDaysOff;
    }

    public function getPeriodAttribute()
    {
        $date = Carbon::make("$this->year-$this->month-1");
        return "1 $date->monthName au $date->daysInMonth $date->monthName";
    }

    public function getTotalSalaryAdvanceAttribute()
    {
        $total = 0;
        $salaryAdvances = SalaryAdvance::whereDeleted(0)->where('users_id')
            ->whereRaw("MONTH(created_at) = ? AND YEAR(created_at) = ?", [$this->year, $this->month])
            ->get();
        foreach($salaryAdvances as $salaryAdvance){
            $total += $salaryAdvances->amount;
        }
        return $total;
    }

    public function getNbDaysInMonth()
    {
        $date = Carbon::make("$this->year-$this->month-1");
        return $date->daysInMonth;
    }

    public function getDaysOffRemaining()
    {
        return 0;
    }
}
