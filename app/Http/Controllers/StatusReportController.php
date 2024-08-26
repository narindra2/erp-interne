<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use App\Models\StatusReport;
use Illuminate\Http\Request;
use League\CommonMark\Inline\Element\Strong;

class StatusReportController extends Controller
{
    public function index()  {
        return view("status-report.index" , ["basic_filter"  =>StatusReport::createFilter()]);
    }
    public function modal_form(Request $request)  {
        $statusReport = $request->id ? StatusReport::find( $request->id ) : new StatusReport();
        $users = User::select("id","deleted","name","firstname")->where('user_type_id', "<>", UserType::$_ADMIN)->whereDeleted(0)->get();
        $type = StatusReport::TYPE_STATUS_REPORT;
        return view("status-report.modal-form" , ["statusReport"  => $statusReport ,"users" => $users , "type" => $type ]);
    }
    public function save_status_report(Request $request)  {
        $data = $request->except("_token");
        $data["start_date"] = convert_date_to_database_date($request->start_date);
        if ($request->fin_date) {
            $data["fin_date"] = convert_date_to_database_date($request->fin_date);
        }
        $statusReport  = StatusReport::updateOrCreate(["id" => $request->id],  $data);
        return ["success" =>  true, "data" => $this->_make_row($statusReport), "message" => "Rapport bien sauvegardé."];
    }
    public function data_list(Request $request)  {
        $list = [];
        $data  = StatusReport::getDetail($request->all())->get();
        foreach ($data as $statusReport) {
            $list[] = $this->_make_row($statusReport);
        }
        return ["data" =>  $list];
    }
    public function _make_row(StatusReport $statusReport)  {
        $row = [];
        $row["user"] = $statusReport->user->sortname;
        $type = collect(StatusReport::TYPE_STATUS_REPORT)->where("id",$statusReport->type_status_report_id)->first()["text"];
        $row["type"] = "<strong> $type</strong>";
        $row["status"] = $statusReport->getStatus();
        $row["start_date"] =  $statusReport->start_date->translatedFormat("d-M-Y") . " " .  $statusReport->time_start;
        $row["fin_date"] = $statusReport->fin_date ?  $statusReport->fin_date->translatedFormat("d-M-Y") . " " .  $statusReport->time_fin : "-";
        $row["created_at"] =  convert_to_real_time_humains($statusReport->created_at) ;
        $row["actions"] = modal_anchor(url("/status-report/modal-form"), '<i class="fas fa-pen"></i>   ', ["title" => "Edition " , "data-post-id" => $statusReport->id,"data-modal-lg" => true]);
        $row["delete"] =  " " . js_anchor('<i class="fas fa-trash me-4 "></i>', [ 'data-action-url' => url("/status-report/delete"), "title" => "Supprimer","data-post-id" => $statusReport->id , "data-action" => "delete"]);
        return $row;
    }

    public function delete(Request $request) {
        $statusReport = StatusReport::find($request->id);
        if ($request->input("cancel")) {
            $statusReport->update(["deleted" => 0]);
            return ["success" => true, "message" => trans("lang.success_canceled"), "data" => $this->_make_row($statusReport)];
        } else {
            $statusReport->update(["deleted" => 1]);
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }
    }
}
