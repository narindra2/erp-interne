<?php

namespace App\Http\Controllers;

use App\Http\Resources\PointingTempResource;
use App\Models\PointingTemp;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\Request;

class PointingTempController extends Controller
{
    public function index() {
        $data = [];
        $data['basic_filter'] = PointingTemp::createFilter();
        return view('pointing-temp.index', $data);
    }

    public function getData(Request $request) {
        $data = get_users_cache();
        if ($request->local) {
            $local = $request->local;
            $data = $data->where("local", $local);
        }
        return PointingTempResource::collection($data);
    }

    public function store(Request $request) {
        try {
            PointingTemp::saveOrUpdatePointingTemp($request->input());
            return ['success' => true, "message" => "Le pointage a été enregistré"];
        }
        catch(Exception $e) {
            return ['success' => false, "message" => $e->getMessage()]; 
        }
    }
}
