<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class PublicHoliday extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'day',
        'name',
        'duration'
    ];

    public function setDayAttribute($day)
    {
        $this->attributes['day'] = to_date($day);
    }

    public function getDayAttribute($day)
    {
        if ($day == null) return null;
        return Carbon::make($day)->format("d/m/Y");
    }

    public static function getPublicHolidays($year=null, $month=null) 
    {   
        if ($year == null)  $year = Carbon::today()->year;
        if ($month == null) $month = Carbon::today()->month;
        return PublicHoliday::whereDeleted(0)->get();
        // return PublicHoliday::whereRaw("YEAR(day) = ? AND MONTH(day) = ?", [$year, $month])->whereDeleted(0)->get();
    }

    public static function findByInterval($interval = [])
    {
        return PublicHoliday::whereDeleted(0)
            ->whereBetween('day', $interval)
            ->get();
    }
}
