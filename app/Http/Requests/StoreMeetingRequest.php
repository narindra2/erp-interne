<?php

namespace App\Http\Requests;

use App\Models\MeetingRoomUse;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'room_id' => 'required',
            'subject' => 'nullable',
            'deleted' => 'nullable',
            'type' => 'required',
            // 'day_meeting' => 'required|date_format:d/m/Y|after_or_equal:'.now()->format("d/m/Y"),
            'day_meeting' => 'required',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
        ];
    }
    public function messages()
    {
        return [
            'time_end.after' => 'Le horaire de fin doit etre après le horaire de début.  ',
            'day_meeting.required' => 'La date de réunion est obligatoire. ',
            // 'day_meeting.after_or_equal' => 'La date de réunion doit etre en futur ou aujourd\'hui. ',
            'time_start.date_format' => 'Le horaire de  debut doit format H:i. ',
            'time_end.date_format' => 'Le horaire de fin doit format H:i. ',
            'time_start.required' => 'Le horaire de debut est obligatoire. ',
            'time_end.required' => 'Le horaire de fin est obligatoire.  ',
        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
        $request = request();
        $days = explode(",",$request->day_meeting);
        foreach ($days as $day) {
            $day = convert_date_to_database_date($day);
            $d_start = $day ." ". $request->time_start;
            $d_end = $day ." ". $request->time_end;

            $query = MeetingRoomUse::whereDeleted(0)->where("room_id",$request->room_id)->whereBetween('time_start', [ $d_start, $d_end ])->whereBetween('time_end', [ $d_start, $d_end ]);
            /** Update */
            if ($request->horaire_id != 0) {
                $query->where("id","<>",$request->horaire_id);
            }
            $horaire  = $query->first();
            if ($horaire) {
                die(json_encode([
                    "success" => false, 
                    "validation" => true,  
                    "message" => "L' horaire est déja prise par {$horaire->creator->sortname } :  
                    « le {$day} du {$request->time_start } jusqu' à {$request->time_end} » <u> Info réunion </u> : {$horaire->type}"]));
            }
        }
    }
}
