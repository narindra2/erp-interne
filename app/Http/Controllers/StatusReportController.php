<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DayOff;
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
        $statusReports  = $this->get_detail($request->all());
        $dayoffs = $this->enconge($request);
        foreach ($statusReports as $statusReport) {
            $list[] = $this->_make_row($statusReport);
        }
        return ["data" =>  array_merge($dayoffs,$list)];
    }
    private function get_detail($options = [])  {
        return StatusReport::getDetail($options)->get();
    }
    public function _make_row(StatusReport $statusReport)  {
        $row = [];
        $row["user"] = $statusReport->user->sortname;
        $type = collect(StatusReport::TYPE_STATUS_REPORT)->where("id",$statusReport->type_status_report_id)->first()["text"];
        $row["rapport"] = '<span class="badge badge-light-dark fw-bolder fs-8 px-2 py-1 ms-2">'. $type.'</span>';
        $row["status"] = $statusReport->getStatus();
        $date = $statusReport->start_date->translatedFormat("d-M-Y");
        if ($statusReport->time_start) {
            $date .= " à " . $statusReport->time_start;
        }
        if ($statusReport->start_date == $statusReport->fin_date ) {
            $date = $statusReport->start_date->translatedFormat("d-M-Y") ; 
            $date .= " à " . $statusReport->time_start;
        }else{
            if ($statusReport->fin_date) {
                $date .=  "<strong>".  $statusReport->start_date->translatedFormat("d-M-Y"). "</strong>"  . " retour au " .   "<strong>". $statusReport->fin_date->translatedFormat("d-M-Y") . "</strong>"  ;
            }
        }
        if ($statusReport->time_fin ) {
            $date .= "  au " . $statusReport->time_fin ;
        }

        $row["date"] = $date . ".";
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
    public function enconge(Request $request) {
        $options = $request->all(); $list = [];
        $daysOffs = dayOff::with(['applicant']);
        $user_id = get_array_value($options, 'user_id');
        if ($user_id) {
            $daysOffs->where('applicant_id', $user_id);
        }
        $day_report = get_array_value($options, 'day_report' ) ?? now()->format("d/m/Y");
        if ($day_report) {
            $daysOffs->whereDate('start_date', '<=', to_date($day_report))->whereDate('return_date', '>', to_date($day_report) ." 00:00:00" );
        }
        $daysOffs = $daysOffs->whereDeleted(0)->where("is_canceled", 0)->get();
        foreach ($daysOffs as $daysOff) {
            $list[] = $this->_make_row_enconge($daysOff);
        }
        return $list;
    }
    public function _make_row_enconge(dayOff $dayOff ,  $day_report = null )  {
        $row = [];
        $row["user"] = $dayOff->applicant->sortname;
        // $date =  trans("lang.{$dayOff->type->type}") . " :  " ;
        $date =  "" ;

        if ($dayOff->start_date_is_morning == 0) {
            $date .= "Après-midi " ;
        }

        if ($dayOff->start_date == $dayOff->return_date ) {
            $date .=" " .  $dayOff->start_date ; 
        }else{
            $date .=  "<strong>".  $dayOff->start_date->translatedFormat("d-M-Y"). "</strong>"  . " retour au " .   "<strong>". $dayOff->return_date->translatedFormat("d-M-Y") . "</strong>"  ;
        }

        if ($dayOff->return_date_is_morning == 0) {
            $date .= " après-midi" ;
        }
        $row["date"] =  $date . ".";
        $row["status"] =  '<span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">Validé</span>';
        $row["rapport"] = '<span class="badge badge-light-info fw-bolder fs-8 px-2 py-1 ms-2">En congé</span>';
        if ($dayOff->result != "validated") {
            $row["status"] =  '<span class="badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2">Pas encore validé</span>';
            if ($dayOff->start_date->isPast() || $dayOff->start_date->today()) {
                $row["rapport"] = '<span class="badge badge-light-info fw-bolder fs-8 px-2 py-1 ms-2">En congé</span>';
            }else{
                $row["rapport"] = '<span class="badge badge-light-info fw-bolder fs-8 px-2 py-1 ms-2">Sera en congé</span>';
            }
        }
       
        $row["created_at"] = "-";
        $row["actions"]  = $row["delete"] = '<i class="my-2 fas fa-lock" title="Contantez le service RH pour plus info."></i>';
        return $row;
    }
}