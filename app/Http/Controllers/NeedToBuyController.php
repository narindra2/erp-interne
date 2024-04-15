<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetailNeedRequest;
use App\Http\Requests\NeedToBuyRequest;
use App\Http\Resources\DetailNeedResource;
use App\Http\Resources\NeedFileResource;
use App\Http\Resources\NeedToBuyResource;
use App\Models\Department;
use App\Models\DetailNeed;
use App\Models\ItemType;
use App\Models\NeedFile;
use App\Models\NeedToBuy;
use App\Models\UnitItem;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;

class NeedToBuyController extends Controller
{
    public function index() {
        return view('need-to-buy.index');
    }

    public function getPageList() {
        $data = [];
        $data['basic_filter'] = NeedToBuy::createFilter();
        return view('need-to-buy.tabs.list', $data);
    }

    public function getDataList(Request $request) {
        $data = NeedToBuy::findNeedToBuy($request->input());
        return NeedToBuyResource::collection($data);
    }

    public function modalForm(NeedToBuy $needToBuy) {
        $data = [];
        $data['needToBuy'] = $needToBuy;
        $data['itemTypes'] = ItemType::whereDeleted(0)->orderBy('name')->get();
        $data['departments'] = Department::whereDeleted(0)->orderBy('name')->get();
        $data['statuses'] = NeedToBuy::$_STATUSES;
        $data['user'] = auth()->user();
        $data['units'] = UnitItem::whereDeleted(false)->get();
        return view('need-to-buy.modal.modal-form', $data);
    }

    public function store(NeedToBuyRequest $request) {
        try {
            $input = $request->input();
            $need = NeedToBuy::updateOrCreate([
                'id' => $request->id
            ], $input);
            return ["success" => true ,"message" => "Besoin sauvegardé", "row_id" => $request->id ? row_id("need", $need->id) : null, "data" => new NeedToBuyResource($need)];
        } 
        catch (Exception $e) {
            return ['success' => false, "message" => $e->getMessage()];
        }
    }

    public function destroy(NeedToBuy $needToBuy) {
        $needToBuy->deleted = true;
        $needToBuy->save();
        return ['success' => true, "message" => "Suppression effectué avec succès"];
    }

    public function showInfosModal(NeedToBuy $needToBuy) {
        $needToBuy->load('unit');
        $data = [];
        $data['statuses'] = DetailNeed::$_STATUSES;
        $data['needToBuy'] = $needToBuy;
        $data['units'] = UnitItem::whereDeleted(false)->get();
        return view('need-to-buy.modal.infos', $data);
    }

    public function getDetails(NeedToBuy $needToBuy) {
        $needToBuy->load("details");
        return DetailNeedResource::collection($needToBuy->details);
    }

    public function storeDetail(DetailNeedRequest $request) {
        DB::beginTransaction();
        try {
            $input = $request->input();
            $user = Auth::user();
            $detail = DetailNeed::saveToStock($input, $user);
            DB::commit();
            return ['success' => true, 'message' => "Information sauvegardé avec succès", 'data' => new DetailNeedResource($detail)];
        }
        catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getPageStatistics() {
        $data = [];
        $data['basic_filter'] = DetailNeed::createFilterStat();
        return view('need-to-buy.tabs.statistic', $data);
    }

    public function getDataListStatistic(Request $request) {
        $details = DetailNeed::getStatistic($request->input());
        $data = [];
        $sum = 0;
        foreach ($details as $detail) {
            $data[] = $detail->rowStatistic();
            $sum += $detail->total_price;
        }
        return ['data' => $data, 'sum' => $sum];
    }

    public function exportToPDF(Request $request) 
    {
        //Generate PDF

        //Save PDF to the server
        //Send PDF to user
        return NeedFile::createInvoice($request->input());
    }

    public function getPageFileList() {
        $data = []; 
        $data['basic_filter'] = NeedToBuy::createFilter();
        return view('need-to-buy.tabs.fileList', $data);
    }

    public function getFileList() {
        $needFiles = NeedFile::whereDeleted(false)->get();
        return NeedFileResource::collection($needFiles);
    }

    public function downloadInvoice(NeedFile $needFile) {
        return response()->download($needFile->src);
    }
    
}
