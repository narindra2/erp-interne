<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DayOff;
use App\Models\UserType;
use App\Models\Department;
use App\Models\PointingTemp;
use App\Models\StatusReport;
use Illuminate\Http\Request;
use App\Models\DayoffNatureColor;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Inline\Element\Strong;

class StatusReportController extends Controller
{
    private $options = [];
    
    public function index()  {
        return view("status-report.index" , ["basic_filter"  =>StatusReport::createFilter()]);
    }
    public function modal_form(Request $request)  {
        $statusReport = $request->id ? StatusReport::find( $request->id ) : new StatusReport();
        $users = User::select("id","deleted","name","firstname")->where('user_type_id', "<>", UserType::$_ADMIN)->whereDeleted(0)->get();
        $natures = DayoffNatureColor::getNaturesByType("status_report");
        return view("status-report.modal-form" , ["statusReport"  => $statusReport ,"users" => $users , "natures" => $natures ]);
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

        $this->options =  $options= $request->all();
        $reports = [] ;
        $auth = Auth()->user(); $usrs_same_dprtmt = [];
        $from_user_tab_view = get_array_value($options, 'from_user_tab_view');
        if ($from_user_tab_view) {
            if ($auth->isCp() /* ||  $auth->isM2p() */) {
                $usrs_same_dprtmt = Department::getUserByIdDepartement(Auth()->user()->userJob->department_id);
                $usrs_same_dprtmt =  $usrs_same_dprtmt->pluck("id")->toArray();
                $options["user_id"] =  $usrs_same_dprtmt;
            }else{
                $options["user_id"] = [Auth::id()];
            }
        }
        
        $statusReports  = $this->get_detail($options);
        $dayoffs = $this->enconge($options);
        foreach ($statusReports as $statusReport) {
            $reports[] = $this->_make_row($statusReport);
        }
        return ["data" => array_merge($reports,$dayoffs)];
    }
    private function get_detail($options = [])  {
        return StatusReport::getDetail($options)->get();
    }
    public function _make_row(StatusReport $statusReport)  {
        $row = [];
        if (Auth::id() == $statusReport->user_id) {
            $name = "Moi";
        }else{
            $job = $statusReport->user->actualJob;
            $name = $statusReport->user->sortname ." " . '<span class="badge badge-light-info fw-bolder fs-8 px-2 py-1 ms-2">'. $job.'</span>';
        }
        $row["user"] =  $name;
        $row["nature"] =  $statusReport->nature ? '<span class="badge  " style="min-width: 80%;color: white;background-color:'.$statusReport->nature->color.'">'.$statusReport->nature->nature.'</span>'  : ""  ;
        $row["status"] = $statusReport->getStatus();
        $date = $statusReport->start_date->translatedFormat("d-M-Y");
        if ($statusReport->time_start) {
            $date .= " à " . $statusReport->time_start;
        }
        if ($statusReport->start_date == $statusReport->fin_date ) {
            $date = $statusReport->start_date->translatedFormat("d-M-Y"); 
            if ($statusReport->time_start) {
                $date .= " à " . $statusReport->time_start;
            }
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
        if (get_array_value($this->options , "from_user_tab_view")) {
            $row["actions"] = $row["delete"] = '<i class="my-2 fas fa-lock" title="Contactez le service RH pour plus info."></i>';
        }
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
    public function enconge( $options = []) {
        $list = [];
        $daysOffs = dayOff::with(['applicant']);
        $user_id = get_array_value($options, 'user_id');
        if ($user_id) {
            $daysOffs->whereIn('applicant_id', $user_id);
        }
        $day_report = get_array_value($options, 'day_report' ) ?? now()->format("d/m/Y");
        if ($day_report) {
            $daysOffs->whereDate('start_date', '<=', to_date($day_report))->whereDate('return_date', '>', to_date($day_report) ." 00:00:00" );
        }
        $daysOffs = $daysOffs->whereDeleted(0)->where("is_canceled","=" ,  0)->notRefused()->get();
        foreach ($daysOffs as $daysOff) {
            $list[] = $this->_make_row_enconge($daysOff);
        }
        return $list;
    }
    public function _make_row_enconge(dayOff $dayOff ,  $day_report = null )  {
        $row = [];
        if (Auth::id() == $dayOff->applicant_id) {
            $name = "Moi";
        }else{
            $job = $dayOff->getApplicantJob();
            $name = $dayOff->applicant->sortname ." " . '<span class="badge badge-light-info fw-bolder fs-8 px-2 py-1 ms-2">'. $job.'</span>';
        }
        $row["user"] = $name;
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
        $row['nature'] = $dayOff->nature ? '<span class="badge  " style="min-width: 80%;color: white;background-color:'.$dayOff->nature->color.'">'.$dayOff->nature->nature.'</span>'  : "" ;
        if ($dayOff->result != "validated") {
            $row["status"] =  '<span class="badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2">Pas encore validé</span>';
        }
       
        $row["created_at"] = "-";
        $row["actions"]  = $row["delete"] = '<i class="my-2 fas fa-lock" title="Contactez le service RH pour plus d\'info."></i>';
        return $row;
    }
    public function report_cumulative_hour_for_cp(Request $request)  {
        $auth = Auth()->user();
        $data = [] ;$usrs_same_dprtmt = []; 
        if ($auth->isCp()) {
            $usrs_same_dprtmt = Department::getUserByIdDepartement(Auth()->user()->userJob->department_id );
            $usrs_same_dprtmt =  $usrs_same_dprtmt->pluck("id")->toArray();
        }else{
            $usrs_same_dprtmt = User::getListOfUsersCanValidateDayOff($auth->id);
        }
        
        $lists = PointingTemp::whereIn("user_id" , $usrs_same_dprtmt)->get();
        foreach ($lists as $hour) {
            $user = User::select("registration_number","deleted","name","firstname")->whereDeleted(0)->find($hour->user_id);
            if (!$user) {
                continue;
            }
            $data[] = [
                "registration_number" =>  $user->registration_number,
                'name' =>  $user->fullname,
                'minute_worked' => $hour->minute_worked ?? "non reconnue",
                'last_update' => convert_to_real_time_humains($hour->updated_at) ,
            ];
        }
        return ["data" =>  $data];
    }
    public function info_tab_repport( Request $request)
    {
        $auth = Auth()->user();
        $filters[] = [
            "label" => "Rapport du ...",
            "name" => "day_report",
            "type" => "date",
            'attributes' => [
                "value" => $auth->isCp() || $auth->isRhOrAdmin() ? now()->format("d/m/Y") : null,
                'placeholder' => 'Rapport du ...',
            ]
        ];
        return view("users.repport" , ["basic_filter"  =>  $filters]);
    }
    
}