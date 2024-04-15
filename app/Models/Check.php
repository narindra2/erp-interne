<?php

namespace App\Models;

use App\Imports\CheckImport;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;
    protected $table = "pointing";
    public $timestamps = false;
    protected $guarded = [];
    protected $with = ["user"];
    public static $_WEB = "web";
    public static $_DEVICE = "device";
    public $_WORK_HOUR = 8;

    public function user(){
        return $this->belongsTo(User::class ,"user_id")->whereNotNull("id");
    }

    public static function getDetails($on, $date=null, $dateFormat="d/m/Y")
    {
        $date = ($date != null) ? 
            Carbon::make(Carbon::createFromFormat($dateFormat, $date)->format('d-m-Y'))->format("Y-m-d") : 
            Carbon::today()->format("Y-m-d");
        $checkList = Check::whereRaw("date(date_time) = ?", [$date])->where('on', $on)->orderBy('date_time', 'ASC')
        ->get()->groupBy(['registration_number']);
        $array = $checkList;
        $registration_numbers = array_keys($array->toArray());
        $checks = [];
        foreach ($registration_numbers as $registration_number) {
            $check = new Check();
            $check->minute_worked = 0;
            $check->date = $date;
            $check->registration_number = $registration_number;
            for ($i = 1; $i < $checkList[$registration_number]->count(); $i+=2) {
                $in = Carbon::createFromFormat('Y-m-d H:i:s', $checkList[$registration_number][$i - 1]->date_time);
                $out = Carbon::createFromFormat('Y-m-d H:i:s', $checkList[$registration_number][$i]->date_time);
                $check->minute_worked += $out->diffInMinutes($in);

                if ($i == 1)    $check->entry_time = $in;
            }
            $check->exit_time = isset($out) ? $out : null;
            $checks[] = $check;
        }
        return $checks;
    }

    public function getTimeWorks()
    {
        return convertMinuteToHour($this->minute_worked);
    }

    public function getCumulativeHour()
    {
        $data = [];
        $minutes = $this->minute_worked - ($this->_WORK_HOUR * 60);
        if (Carbon::make($this->date)->isWeekend()) {
            $minutes = $this->minute_worked;
        }
        if ($minutes < 0) $data['type'] = 'negative';
        else              $data['type'] = 'positive';
        $minutes = abs($minutes);
        $data['value'] = convertMinuteToHour($minutes);
        return $data;
    }

    public function getCumulativeHourString()
    {
        $cumulativeHour = $this->getCumulativeHour();
        if ($cumulativeHour['type'] == 'positive')  return $cumulativeHour['value'];
        return "- " . $cumulativeHour['value'] ;
    }

    public function getCumulativeHourStringNegative()
    {
        $cumulativeHour = $this->getCumulativeHour();
        if ($cumulativeHour['type'] == 'negative') return "- " . $cumulativeHour['value'] ;
    }

    public function getEntryTimeWithFormat()
    {
        if ($this->entry_time == null)  return null;
        return $this->entry_time->format('H:i:s');
    }

    public function getExitTimeWithFormat()
    {
        if ($this->exit_time == null)  return null;
        return $this->exit_time->format('H:i:s');
    }

    public static function createFilter()
    {
        $filters = [];

        $filters[] = [
            "label" => "Date",
            "name" => "date_time",
            "type" => "date",
            'attributes' => [
                'placeholder' => 'Date de pointage',
                'width' => 'w-200px',
            ]
        ];

        return $filters;
    } 

    public function scopeChrono($query, User $user) {
        $pointings = Check::whereDeleted(0)->where('on', Check::$_WEB)
            ->whereRaw("date(date_time) = ?", [Carbon::today()->format("Y-m-d")])
            ->where('registration_number', $user->registration_number)
            ->orderBy('date_time')->get();
        if ($pointings->count() % 2) {
            $newPointing = new Check();
            $newPointing->registration_number = $user->registration_number;
            $newPointing->date_time = Carbon::now()->format('Y-m-d H:i:s');
            $pointings[] = $newPointing;
        }
        $minute_worked = 0;
        for ($i = 1; $i < $pointings->count(); $i=$i+2) {
            $in = Carbon::createFromFormat('Y-m-d H:i:s', $pointings[$i - 1]->date_time);
            $out = Carbon::createFromFormat('Y-m-d H:i:s', $pointings[$i]->date_time);
            $minute_worked += $out->diffInMinutes($in);
        }
        return $minute_worked;
    }
    /*
    public static function saveToTableCheck()
    {
        $checkVerificationTemp = CheckVerificationTemp::orderBy('registration_number')->orderBy('date_time')->get();
        $i = 0;
        $checks = [];
        foreach ($checkVerificationTemp as $check) {
            if ($i == 0) {
                Check::whereRaw('date(date_time)=date(?)', [$check->date])->where('on', Check::$_DEVICE)->delete();
            }
            $check_event = "in";
            if ($i % 2)    $check_event = "out";
            $checks[] = Check::create([
                'registration_number' => $check->registration_number,
                'on' => 'device',
                'date_time' => $check->date_time,
                'check_event' => $check_event
            ]);
            $i++;
        }
        return collect($checks);
    } */
}
