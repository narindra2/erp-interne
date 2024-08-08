<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\PointingTemp;
use Illuminate\Http\Request;
use App\Imports\UserCumulativeHour;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\PointingTempResource;

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

    public function import(Request $request) {
        if (!$request->hasFile("file")) {
           return ["success" => false , "message" => "Veuillez choisir le fichier à importer"];
        }
        try {
            Excel::import(new UserCumulativeHour, $request->file("file"), null, \Maatwebsite\Excel\Excel::CSV);
            return ["success" => true , "message" => "Importation faite avec succés."];
        } catch (Exception $e) {
            return ["success" => false , "message" => $e->getMessage()];
        } 
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