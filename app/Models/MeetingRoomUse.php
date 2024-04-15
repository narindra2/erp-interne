<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingRoomUse extends Model
{
    use HasFactory;

    protected $table = "meeting_room_use";
    protected $fillable = [
        'room_id',
        'creator_id',
        'type',
        'subject',
        'day_meeting',
        'time_start',
        'time_end',
        'deleted',
        'created_at',
        'updated_at'
    ];

    protected $appends = ["status","start", "end"];

    public  function creator()
    {
        return $this->belongsTo(User::class,"creator_id");
    }
    public static function horaires($where =[])
    {
        $daysIn = [];
        foreach ($where["day_meeting"] as $day) {
            $daysIn[] = convert_date_to_database_date($day);
        }
        return self::with(["creator"])
            ->whereDeleted(0)
            ->whereIn("day_meeting", $daysIn)
            ->orderBy("day_meeting","ASC")
            ->orderBy("time_start")
            ->orderBy("time_end")
            ->where("room_id", "=", $where["room_id"]);
    }
    public  function getStartAttribute()
    {
        return Carbon::create($this->time_start)->format("H:i");
        
    }
    public  function getEndAttribute()
    {
        return Carbon::create($this->time_end)->format("H:i");
    }
    public  function getStatusAttribute()
    {
        $meeting_start = Carbon::create($this->time_start);
        $meeting_end = Carbon::create( $this->time_end);
        if ($meeting_start->isPast() && $meeting_end->isPast() ) {
            return ["class" => "danger" , "text" =>  "Terminé"];
        }elseif($meeting_start->isPast() && $meeting_end->isFuture()){
            return ["class" => "success" , "text" =>  "En cours"];
        }
        if ($meeting_start->isFuture()) {
            return ["class" => "info" , "text" =>  "A venir"];
        }
        
    }
    public  function is_past()
    {
        return Carbon::create($this->time_start)->isPast() ? true : false;
    }
    public  function is_in_progress()
    {
        $meeting_start = Carbon::create( $this->time_start);
        $meeting_end = Carbon::create( $this->time_end);
        if($meeting_start->isPast() && $meeting_end->isFuture()){
            return true;
        }
    }
    public  function get_begin()
    {
        $meeting_start = Carbon::create( $this->time_start);
        $now = Carbon::now();
        if($meeting_start->isToday() && !$this->is_in_progress()){
            return $meeting_start->diff( $now );
        }
        return "";
        
    }

    public static function  create_day_metting($request){
        $data = [];
        $days = explode(",",$request->day_meeting);
        foreach ($days as $day) {
            $d_metting = convert_date_to_database_date($day);
            $time_start =   $d_metting ." " .  $request->time_start;
            $time_end =   $d_metting ." " .  $request->time_end;
            $data[] = [
                "creator_id" => Auth::id(),
                "day_meeting" => $d_metting,
                "time_start" =>$time_start,
                "time_end" =>$time_end,
                "room_id" =>$request->room_id,
                "type" =>$request->type,
                "subject" =>$request->subject,
            ];
        }
        self::insert($data);
    }
}
