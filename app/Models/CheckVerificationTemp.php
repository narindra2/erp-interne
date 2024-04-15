<?php

namespace App\Models;

use App\Imports\CheckImport;
use Carbon\Carbon;
use Composer\Command\CheckPlatformReqsCommand;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckVerificationTemp extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "check_verification_temps";
    public $timestamps = false;
    public static $_RESUME_DATE_TO_CHANGE = [];

    public static function importExcel($file)
    {
        if ($file == null)  throw new Exception("Veuillez entrer un fichier excel n");
        if ($file->extension() != "xlsx")
            throw new Exception("Veuillez entrer un fichier excel");   
        (new CheckImport)->import($file);
        self::$_RESUME_DATE_TO_CHANGE = [];
    }

    public static function verifyDataIsOdd() 
    {
        $nbCheckEmployees = CheckVerificationTemp::selectRaw('COUNT(registration_number) as nb, registration_number')->groupBy('registration_number')->get();
        $error = "";
        foreach ($nbCheckEmployees as $nbCheckEmployee) {
            if ($nbCheckEmployee->nb % 2 != 0) {
                $error .= $nbCheckEmployee->registration_number ." est impaire. ";
            }
        }
        if ($error != "")   throw new Exception($error);
        return $nbCheckEmployees;
    }

    public static function saveToTableCheck()
    {
        $checkVerificationTemp = CheckVerificationTemp::orderBy('registration_number')->orderBy('date_time')->get();
        $i = 0;
        $checks = [];
        $data = [];
        foreach ($checkVerificationTemp as $check) {
            if ($i == 0) {
                Check::whereRaw('date(date_time)=date(?)', [$check->date])->where('on', Check::$_DEVICE)->delete();
            }
            $check_event = "in";
            if ($i % 2)    $check_event = "out";
            $data[] = [
                'registration_number' => $check->registration_number,
                'on' => 'device',
                'date_time' => $check->date_time,
                'check_event' => $check_event
            ];
            $i++;
        }
        Check::upsert($data, ['registration_number'], ['registration_number']);
        return Check::whereRaw('date(date_time)=date(?)', [$check->date])->where('on', Check::$_DEVICE)->get();
    }
    
    /**
     * 
     */
    public static function getDateTemp() {
        return CheckVerificationTemp::selectRaw("DISTINCT (date(date_time)) as date")->where("on", Check::$_DEVICE)->pluck("date")->toArray()[0];
    }

    public static function saveToPointingResume()
    {
        $date = self::getDateTemp();
        $checks = Check::getDetails(Check::$_DEVICE, $date, "Y-m-d");
        $data = [];
        foreach ($checks as $check) {
            $data[] = [
                'registration_number' => $check->registration_number,
                'day' => $check->date,
                'entry_time' => $check->entry_time->format("Y-m-d H:i"),
                'exit_time' => $check->exit_time->format("Y-m-d H:i"),
                'minute_worked' => $check->minute_worked,
                'additional_hour_type_id' => 1
            ];
        }
        DB::table("pointing_resumes")->upsert($data, ['registration_number', 'day', 'additional_hour_type_id'], ['entry_time', 'exit_time', 'minute_worked']);
        CheckVerificationTemp::query()->delete();
    }

    //delete from pointing_resumes
    //delete from pointing where `on` = 'device'
}
