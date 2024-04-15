<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplementHourRequest;
use App\Http\Resources\ComplementHourResource;
use App\Models\AdditionalHourType;
use App\Models\PointingResume;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;

class ComplementHourController extends Controller
{
    public function index() {
        return view('complement-hours.index');
    }

    public function data_list(Request $request) {
        $data = [];
        $date = $request->date;
        $complementHours = PointingResume::getComplementHours($date);
        return ComplementHourResource::collection($complementHours);
    }

    public function modal_form(PointingResume $pointingResume) {
        $users = User::whereDeleted(0)->where("user_type_id", "<>", UserType::$_ADMIN)->orderBy('registration_number')->get();
        $types = AdditionalHourType::whereNotIn("id", [1, 2])->whereDeleted(0)->get();
        return view('complement-hours.modals.form', compact('users', 'types', 'pointingResume'));
    }

    public function store(ComplementHourRequest $request) {
        $minute_worked = $request->minute_worked ?? "";
        $minute_worked = explode(":", $minute_worked);
        if (count($minute_worked) < 2) {
            return ['success' => false, "message" => 'Veuillez bien écrire le format de la durée'];
        }
        $input = $request->input();
        $input['minute_worked'] = (int)$minute_worked[0] * 60 + (int)$minute_worked[1];
        $p = PointingResume::updateOrCreate(['id' => $request->id], $input);    
        return ["success" => true, "message" => "Operation faite avec succès", "row_id" => $request->id ? row_id("complementHour", $p->id) : null, "data" => new ComplementHourResource($p)];
    }

    public function destroy(PointingResume $pointingResume) {
        $id = $pointingResume->id;
        $pointingResume->deleted = 1;
        $pointingResume->save();
        return ["success" => true, "message" => "Operation faite avec succès"];
    }
}
