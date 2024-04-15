<?php

namespace App\Models;

use App\Imports\EmployeeCheckImport;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_registration_number',
        'date_check'
    ];

    public $table = "employee_check";

    public static function importDataFormExcelFile($file)
    {
        $extension = $file->extension();
        if ($extension != "xlsx") {
            throw new Exception("Veuillez entrer un fichier excel avec un format xlsx");
        }
        (new EmployeeCheckImport)->import($file);
        $nbCheckEmployees = EmployeeCheck::verifyDataIsOdd();
    }

    public static function verifyDataIsOdd() 
    {
        $nbCheckEmployees = EmployeeCheck::selectRaw('COUNT(employee_registration_number) as nb, employee_registration_number')->groupBy('employee_registration_number')->get();
        $error = "";
        foreach ($nbCheckEmployees as $nbCheckEmployee) {
            if ($nbCheckEmployee->nb % 2 != 0) {
                $error .= $nbCheckEmployee->employee_registration_number ." est impaire. ";
            }
        }
        if ($error != "")   throw new Exception($error);
        return $nbCheckEmployees;
    }

    public static function calculHoursPerformed($date)
    {
        $employeeChecks = EmployeeCheck::where('date_check', ">", "2000-1-1")->orderBy("employee_registration_number")->orderBy("date_check")->get();
        $employeeMinutePerformed = [];
        for ($i = 1; $i < $employeeChecks->count(); $i = $i + 2) {
            if (!isset($employeeMinutePerformed[$employeeChecks[$i]->employee_registration_number])) {
                $employeeMinutePerformed[$employeeChecks[$i]->employee_registration_number] = 0;
            }
            $in = Carbon::createFromFormat('Y-m-d H:i:s', $employeeChecks[$i-1]->date_check);
            $out = Carbon::createFromFormat('Y-m-d H:i:s', $employeeChecks[$i]->date_check);
            $employeeMinutePerformed[$employeeChecks[$i]->employee_registration_number] += $in->diffInMinutes($out);
        }
        return $employeeMinutePerformed;
    }
}
