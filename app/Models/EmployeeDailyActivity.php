<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDailyActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'minute_performed',
        'day',
        'users_id',
        'daily_activity_id'
    ];

    public $table = "employee_daily_activity";

    public static $minute = 480;

    public function dailyActivity()
    {
        return $this->belongsTo(DailyActivity::class, "daily_activity_id");
    }

    public static function saveAttendances($minutes, $date)
    {

        if (DailyActivity::where("day", $date)->where("daily_activity_id")->get()->count() != 0) {
            throw new Exception("Une pointage a eu déjà lieu sur cette date");
        }

        $registrationNumber = array_keys($minutes);
        $users = User::whereIn('registration_number', $registrationNumber)->get();
        foreach($users as $user) {
            $emp = EmployeeDailyActivity::create([
                'users_id' => $user->id,
                'minute_performed' => $minutes[$user->registration_number],
                'day' => $date,
                'daily_activity_id' => DailyActivity::$_ATTENDANCE
            ]);

            MvtAttendance::create([
                'users_id' => $user->id,
                'day' => $date,
                'type' => 0
            ]);
        }
    }

    public static function saveDaysOff(DayOff $dayOff) 
    {
        if ($dayOff->result == 1) {
            $dateLoop = Carbon::make($dayOff->start_date);
            while ($dateLoop->diffInDays(Carbon::make($dayOff->return_date))) {
                $minute = EmployeeDailyActivity::calculMinutesDayOff($dayOff, $dateLoop);
                EmployeeDailyActivity::create([
                    'users_id' => $dayOff->applicant_id,
                    'day' => $dateLoop,
                    'daily_activity_id' => DailyActivity::$_DAYOFF,
                    'minute_performed' => $minute
                ]);

                MvtAttendance::create([
                    'users_id' => $dayOff->applicant_id,
                    'day' => $dateLoop,
                    'type' => 1
                ]);
               $dateLoop->addDay();
            }
        }
    }

    public static function calculMinutesDayOff(DayOff $dayOff, $date)
    {
        if (Carbon::make($dayOff->start_date) == $date) {
            if (!$dayOff->start_date_is_morning) {
                return EmployeeDailyActivity::$minute / 2;
            }
        }
        
        if (Carbon::make($dayOff->return_date)->subDay() == $date) {
            if (!$dayOff->return_date_is_morning) {
                return EmployeeDailyActivity::$minute / 2;
            }
        }

        return EmployeeDailyActivity::$minute;
    }

    public static function saveEmployeeActivity($input)
    {
        $minute = 0;
        $tab = explode(":", $input['time']);
        $hour = $tab[0];
        $minute = ((int)$hour) * 60 + ((int)$tab[1]);
        $input['minute_performed'] = $minute;
        EmployeeDailyActivity::create($input);

        $mvt = MvtAttendance::whereDeleted(0)->where("type", 0)->where("day", $input['day'])->where("users_id", $input['users_id'])->first();
        if ($mvt == null) {
            MvtAttendance::create([
                'users_id' => $input['users_id'],
                'day' => $input['day'],
                'type' => 0
            ]);
        }
    }
}
 