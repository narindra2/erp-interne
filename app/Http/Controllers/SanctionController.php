<?php

namespace App\Http\Controllers;

use App\Http\Requests\SanctionRequest;
use App\Http\Resources\SanctionListResource;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
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
