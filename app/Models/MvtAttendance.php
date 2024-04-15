<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvtAttendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'day',
        'users_id',
        'type'
    ];

    public static $WORK = "0";
    public static $DAYOFF = "1";

    public static function getEmployeeMvtByType($idUser, $type, $year, $month)
    {
        $mvts = MvtAttendance::whereDeleted(0);
        $mvts->where('users_id', $idUser);
        $mvts->where('type', $type);
        $mvts->whereRaw("MONTH(day) = ? AND YEAR(day) = ?", [$month, $year]);
        return $mvts->get();
    }

    public static function countNbDays($idUser, Carbon $date1, Carbon $date2, $type)
    {
        return MvtAttendance::whereDeleted(0)->where('users_id', $idUser)
            ->whereBetween("day", [$date1->format("Y-m-d"), $date2->format("Y-m-d")])
            ->whereType($type)
            ->get()->count();
    }

    public static function countNbDaysPerformed($idUser, $year, $month, $now=false) 
    {
        //Information of the user
        $user = User::find($idUser);
        //Date's configuration
        $beginningOfTheMonth = Carbon::make("$year-$month-1");
        $nbDaysInMonth = $beginningOfTheMonth->daysInMonth;
        $endOfMonth = Carbon::make("$year-$month-$nbDaysInMonth");
        $response = [];
        if ($now)   $endOfMonth = Carbon::now();
        if ($user->getHiringDate()->gt($beginningOfTheMonth)) $beginningOfTheMonth = $user->getHiringDate();
        
        if ($user->getHiringDate()->gt($endOfMonth)) {
            $response['work'] = 0;
            $response['dayOff'] = 0;
        }
        else {
            $nbDaysPerformed = MvtAttendance::countNbDays($idUser,$beginningOfTheMonth, $endOfMonth, MvtAttendance::$WORK);
            $nbDaysOffTaken = MvtAttendance::countNbDays($idUser,$beginningOfTheMonth, $endOfMonth, MvtAttendance::$DAYOFF);
            $nbPublicHolidays = PublicHoliday::whereDeleted(0)->whereRaw("WEEKDAY(day) NOT IN (5,6)")->whereBetween("day", [$beginningOfTheMonth->format("Y-m-d"), $endOfMonth->format("Y-m-d")])->get()->count();
            $nbWeekEnd = countDaysInWeekEnd($beginningOfTheMonth, $endOfMonth);
            $nbDaysPerformed += $nbPublicHolidays + $nbWeekEnd;
            if ($nbDaysPerformed + $nbDaysOffTaken > $nbDaysInMonth)    $nbDaysPerformed = $nbDaysInMonth - $nbDaysOffTaken;  
            
            $response['work'] = $nbDaysPerformed;
            $response['dayOff'] = $nbDaysOffTaken;
            return $response;   
        }
    }
}
