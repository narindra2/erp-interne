<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PointingResume extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'day',
        'entry_time',
        'exit_time',
        'minute_worked',
        'additional_hour_type_id'
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'day' => 'datetime'
    ];
    // protected $with = ['user'];
    public $_TIME_WORKED = 8;

    public function user()
    {
        return $this->belongsTo(User::class, 'registration_number', 'registration_number');
    }

    public function additionalHourType() {
        return $this->belongsTo(AdditionalHourType::class, 'additional_hour_type_id');
    }

    public function getTimeWorks()
    {
        return convertMinuteToHour($this->minute_worked);
    }

    public function getEntryTime()
    {
        if ($this->entry_time == null)  return null;
        return $this->entry_time->format('H:i:s');
    }

    public function getExitTime()
    {
        if ($this->exit_time == null)  return null;
        return $this->exit_time->format('H:i:s');
    }

    public function getEndDate(Carbon $startDate)
    {
        $daysInMonth = $startDate->daysInMonth;
        $endMonth = Carbon::make("$startDate->year-$startDate->month-$daysInMonth");
        $today = Carbon::today();
        if ($today->gte($startDate) && $today->lt($endMonth))     return $today;
        return $endMonth;
    }

    public function getSumOfDurationPresence($interval)
    {
        $data = PointingResume::selectRaw("SUM(minute_worked) as minute_worked, registration_number")
            ->whereBetween("day", $interval)
            ->whereDeleted(0)
            ->groupBy("registration_number")->get()->groupBy('registration_number')->toArray();
        return $data;
    }

    public function getFormatDay() {
        if ($this->day) {
            return $this->day->format("Y-m-d");
        }
        return null;
    }

    public function countBusinessDay($publicHolidays, Carbon $dateBegin, Carbon $dateEnd) {
        if ($dateBegin->gt($dateEnd))   return 0;
        return $dateBegin->diffInDaysFiltered(function(Carbon $date) use ($publicHolidays, $dateBegin) {
            if ($dateBegin->gt($date))  return 0;
            foreach($publicHolidays as $publicHoliday) {
                if (Carbon::createFromFormat("d/m/Y", $publicHoliday->day)->equalTo($date)) {
                    return $publicHoliday->duration / $this->_TIME_WORKED;
                }
            }
            return !$date->isWeekend();
        }, $dateEnd);
    }

    public function countNbDaysEmployeeMustPerformed($users, $publicHolidays, Carbon $dateBegin, Carbon $dateEnd)
    {
        $count = $this->countBusinessDay($publicHolidays, $dateBegin, $dateEnd);
        $data = [];
        foreach ($users as $user) {
            if (Carbon::make($user->hiring_date)->gt($dateBegin))   $data[$user->id] = $this->countBusinessDay($publicHolidays, Carbon::make($user->hiring_date), $dateEnd);
            else $data[$user->id] = $count;
        }
        return $data;
    }

    public static function getCumulativeHours(User $user=null, Carbon $startDate=null, Carbon $endDate=null)
    {
        $pointingResume = new PointingResume();

        if ($startDate == null) $startDate = Carbon::today()->firstOfMonth();
        if ($endDate == null)   $endDate = $pointingResume->getEndDate($startDate);
        $interval = [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")];
        
        $users = [];
        if ($user == null) {
            $users = User::whereDeleted(0)->where('user_type_id', "<>" , UserType::$_ADMIN)->get();
        } else {
            $users[] = $user;
            collect($users);
        }

        $sumOfDurationPresence = $pointingResume->getSumOfDurationPresence($interval);
        $publicHolidays = PublicHoliday::findByInterval($interval);
        $nbDaysToWork = $pointingResume->countNbDaysEmployeeMustPerformed($users, $publicHolidays, $startDate, $endDate);
        $data = [];
        foreach ($users as $user) {
            $row = new PointingResume();
            $row->id = $user->id;
            $row->name = $user->fullname;
            $row->registration_number = $user->registration_number;

            $sumTimeWorked = isset($sumOfDurationPresence[$user->registration_number]) ? $sumOfDurationPresence[$user->registration_number][0]['minute_worked'] : 0;
            $row->time_worked = $sumTimeWorked - $nbDaysToWork[$user->id] * 8 * 60;
            $data[] = $row;
        }
        return collect($data);
    }

    public static function getDetails($registration_number, Carbon $startDate=null, Carbon $enDate=null) {
        return PointingResume::with(["additionalHourType"])->where("registration_number", $registration_number)->orderBy("day")->get();
    }

    public static function getComplementHours(String $date=null) {
        $complementHours = PointingResume::with(['user'])->whereNotIn("additional_hour_type_id", [1, 2]);
        if ($date) {
            $dates = explode("-", $date);
            $interval = [to_date($dates[0]), to_date($dates[1])];
            $complementHours->whereBetween("day", $interval);
        }
        return $complementHours->orderBy("day")->orderBy("registration_number")->whereDeleted(0)->get();
    }

    public static function createFilter()
    {
        $filters = [];

        $filters[] = [
            "label" => "Date",
            "name" => "date",
            "type" => "date-range",
            "attributes" => [
                "placeholder" => "--Date--"
            ]
        ];

        return $filters;
    }
}
