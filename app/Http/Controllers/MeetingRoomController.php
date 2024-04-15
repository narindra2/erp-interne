<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use App\Models\MeetingRoomUse;
use Illuminate\Support\Carbon;
use App\Http\Requests\StoreMeetingRequest;

class MeetingRoomController extends Controller
{
    
    public function index()
    {

        $day_in_week = $this->get_days_in_week();
        $rooms = MeetingRoom::get_available_room();
        return view("metting-rooms.index",[
            "day_in_week" => $day_in_week, "rooms"=>$rooms,
            "now" =>  now()->format("d/m/Y"),
            "types" => $this->meeting_type()
        ]);
    }
    public function meeting_modal_form(Request $request)
    {
        return view("metting-rooms.meeting-modal-form",[
            "types" => $this->meeting_type(), 
            "now" =>  now()->format("d/m/Y"),
            "horaire" => MeetingRoomUse::find($request->horaire_id),
            "room" =>   MeetingRoom::find( $request->room_id)]);
    }
    private function meeting_type () {
        return [
            "POINT CLIENT", "REUNION BREVE","TEST","FORMATION",
            "ENTRETIEN" ,"REUNION","Virment Salaire"
        ];
    }
    public function store(StoreMeetingRequest $request)
    {
        MeetingRoomUse::create_day_metting($request);
        return ["success" => true ,"message" => "Réunion horaire bien ajouté et créee."];
    }
    public function update(StoreMeetingRequest $request)
    {
        $data = $request->validated();
        $data["day_meeting"] =  convert_date_to_database_date($request->day_meeting);
        $data["creator_id"] =  Auth::id();
        $data["time_start"] = $data["day_meeting"] ." " .  $request->time_start;
        $data["time_end"] =   $data["day_meeting"] ." " .  $request->time_end;
        $data["deleted"] =   $request->deleted ? 1 :  0;
        MeetingRoomUse::where("id",$request->horaire_id)->update($data);
        return ["success" => true ,"message" => "Réunion horaire bien ajournée."];
    }
    public function get_days_in_week($day ="")
    {
        if (!$day) {
           $day = now()->format("Y-m-d");
        }
        $week_in_fr = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi","Samedi"];
        $now = Carbon::createFromFormat('Y-m-d', now()->format("Y-m-d")); 
        $week = []; 
        for ($i = 0; $i < count( $week_in_fr) ; $i++) {
            $a_day = $now->startOfWeek()->addDay($i);
            $week[] = ["date" =>  $a_day->format('Y-m-d'),"is_past" => $a_day->isPast() ,"date_string" =>  $week_in_fr[$i ] /**$a_day->format('l')*/];
        }
        return $week ;
    }
    public function horaires(Request $request)
    {
        $day_meeting = explode(",",$request->day_meeting);
        $horaires = MeetingRoomUse::horaires($request->except("day_meeting") + [ "day_meeting" =>$day_meeting] )->get();
        return view("metting-rooms.horaires.list",["horaires" =>  $horaires, "multiple_select" => count($day_meeting)> 1]);
    }
    public function calendar(Request $request)
    {
        $horaires = [];
        $data = MeetingRoomUse::where("room_id",$request->room_id)->whereDeleted(0)->get();
        foreach ($data as $meeting ) {
            $horaires[] = $this->_make_data_calandar($meeting);
        }
        return view("metting-rooms.horaires.calandar",["horaires" => ($horaires) ,"today" => now()->format('Y-m-d')]);
    }
    public function _make_data_calandar($meeting)
    {
        return [
            "title"=> $meeting->type,
            "start"=> $meeting->time_start,
            "end"=>  $meeting->time_end,
        ];
    }
}
