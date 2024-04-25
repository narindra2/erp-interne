<?php

namespace App\Http\Controllers;

use App\Http\Requests\SanctionRequest;
use App\Http\Resources\SanctionListResource;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Http\Request;

class SanctionController extends Controller
{

    public function index(Request $request) {
        return view('users.sanctions.index', [ "basic_filter"  => Sanction::createFilter()]);
    }
    public function data_list(Request $request) {
        $data = [];
        // :id,name,firstname,deleted,user_type_id,registration_number
        $sanctions = Sanction::getDetails($request->all())->get();
        foreach ($sanctions as $sanction) {
            $data[] = $this->_make_row($sanction);
        }
        return ["data" => $data];
    }
    public function _make_row(Sanction $sanction) {
        $job = "";
        if ( $sanction->user->userJob) {
          $job_name =   $sanction->user->userJob->job->name;
          $job =  $sanction->user->userJob ? "<span class='badge badge-light-info'>$job_name</span>" : "";
        }
       return [
        "avatar" => view("tasks.crud.member-avatar", ["user" => $sanction->user])->render(),
        "user" => $sanction->user->sortname .  " " . $job,
        "motif" =>  $sanction->reason,
        "type" =>  $sanction->getTypeWithCss(),
        "duration" =>  $sanction->duration,
        "date" =>  convert_to_real_time_humains($sanction->date , "d-m-Y" , false),
       ];
    }
    
    public function getData(User $user, Request $request) {
        $sanctions = Sanction::where('user_id', $user->id)->whereDeleted(0)->get();
        return SanctionListResource::collection($sanctions);
    }

    
    public function formModal(Sanction $sanction, Request $request) {
        $types = ["Verbal", "Ecrit", "Mis à pied"];
        return view('users.sanctions.sanction-form', ['sanction' => $sanction, 'user_id' => $request->user_id, 'types' => $types]);
    }

    public function store(SanctionRequest $request) {
        $sanction = Sanction::storeSanction($request->input());
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" =>  $request->id ? row_id("sanction", $sanction->id) : null, "data" => new SanctionListResource($sanction) ];
    }

    public function destroy(Sanction $sanction) {
        $sanction->deleteSanction();
        return ['success' => true, "message" => "Suppression effectué avec succès"];
    }
}
