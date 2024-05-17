<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DayOff;
use App\Models\UserType;
use App\Models\DaysOffType;
use App\Models\UserJobView;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DayoffNatureColor;
use App\Models\DaysOffAttachment;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DayOffRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AskDaysOffRequest;
use App\Http\Requests\DaysOffTypeRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpgradeEmployeeDaysOffBalanceRequest;

class DayOffController extends Controller
{

    //List of the request of dayOff by employee
    public function index()
    {
        
        $data = [];
        $data['basic_filter'] = DayOff::createFilter();
        return view('days_off.index', $data);
    }

    public function saveModal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'result' => ['required'],
            'comment' => ['required_if:result,0']
        ]);

        if ($validator->fails()) {
            die(json_encode(['success' => false, 'message' => $validator->errors()->all()]));
        }
        return response()->json(['success' => true, "message" => "OK"]);
    }

    public function loadModal(Request $request)
    {
        $can =  $can_make_request = false;
        $user = Auth::user();
        if ($user->isAdmin() || $user->isHR()) {
            $can = true;
            $can_make_request = true;
        } else {
            if ($user->nb_days_off_remaining) {
                $can_make_request = true;
            }
        }
        $natures = DayoffNatureColor::whereDeleted(0)->whereStatus(1)->latest()->get();
        return view("modal.modal-form", ["can_create_other_request" => $can, "natures"  =>  $natures , "can_make_request" => $can_make_request]);
    }

    public function loadModalInfo(DayOff $dayOff)
    {
        $natures = DayoffNatureColor::whereDeleted(0)->whereStatus(1)->latest()->get();
        return view("days_off.modal.more-information", ["dayOff" => $dayOff , "natures" => $natures]);
    }

    

    private function row_status_dayoff($daysOff)
    {
        $status_dayoff = "-";
        if ($daysOff->return_date != $daysOff->start_date) {
            $end_date =  date('Y-m-d', strtotime($daysOff->return_date . ' - 1 day'));
            if (
                Carbon::make($end_date)->lt(Carbon::now()->format("Y-m-d"))
                && $daysOff->result == "validated"
            ) {
                $status_dayoff = "finish_dayoff";
            }
            if (
                Carbon::make($daysOff->start_date)->lte(Carbon::now())
                && Carbon::make(Carbon::now())->lte(Carbon::make($daysOff->return_date))
                && $daysOff->result == "validated"
            ) {
                $status_dayoff = "in_progress_dayoff";
            }
        } else {
            $end_date = $daysOff->return_date;
            if (
                Carbon::make($end_date)->lte(Carbon::now()->format("Y-m-d"))
                && $daysOff->result == "validated"
            ) {
                $status_dayoff = "finish_dayoff";
            }
            if (
                Carbon::make($daysOff->start_date)->lte(Carbon::now())
                && Carbon::make(Carbon::now())->lte(Carbon::make($daysOff->return_date))
                && $daysOff->result == "validated"
            ) {
                $status_dayoff = "in_progress_dayoff";
            }
        }

        return $status_dayoff;
    }
    

    public function data_list(Request $request)
    {
        $options = $request->except("_token");
        $daysOff = DayOff::getDetails($options)->get();
        $data = [];
        foreach ($daysOff as $dayOff) {
            $data[] = $this->make_row($dayOff);
        }
        return response()->json(["data" => $data]);
    }
    public function make_row(DayOff $dayOff)
    {
        $row = [
            'DT_RowId' => row_id("dayoff", $dayOff->id),
            "created_at" => $dayOff->created_at->translatedFormat('d-M-Y'),
            "registration_number" => $dayOff->applicant->registration_number,
            "name" => $dayOff->applicant->sortname,
            "job" => $dayOff->getApplicantJob(),
            "start_date" => $dayOff->start_date->translatedFormat("d-M-Y"),
            "return_date" => $dayOff->return_date->translatedFormat("d-M-Y"),
            "duration" => $dayOff->duration . "jrs",
            "status" => $dayOff->getResult(),
            'nature'=> $dayOff->nature ? '<span class="badge  " style="min-width: 90%;color:white;background-color:'.$dayOff->nature->color.'">'.$dayOff->nature->nature.'</span>'  : "" ,
            "status_dayoff" => view("days_off.columns.status", ["status" => $this->row_status_dayoff($dayOff), "is_canceled" => $dayOff->is_canceled])->render(),
            "action" => view("days_off.columns.action", ["dayOff" => $dayOff])->render()
        ];
        return $row;
    }
    /** Get les user qui sont en congé ou absent  $options["status_dayoff"] = "in_progress"*/
    public function days_off_gantt(Request $request)
    {
        $series =  [];
        $user = Auth::user();
        $query = DayOff::with(['applicant',"nature"])->whereDeleted(0);
        /** User dayoff validate and not yet finish  */
        $query->whereDate('return_date', '>', Carbon::now()->format("Y-m-d"))->where('is_canceled', 0);
        if ($user->isRhOrAdmin()) {
         /** Dont filter dayoff  */   
        }else{
            if ($user->isCp()) {
                $user->load('userJob');
                $users_ID = UserJobView::where("department_id", $user->userJob->department_id)->get()->pluck("users_id");
                $query->whereIn("applicant_id", $users_ID);
            }
        }
        
        /** End  user not yet return work by return_date asc */
        $daysOff =  $query->where('result', 'validated')->oldest("return_date")->get();
        foreach ($daysOff as $dayOff) {
            $series[] = $this->make_row_gantt($dayOff);
        }
        return [["id" => 1, "name" => "Plannig des absences", "series" => $series] ];
    }
    public function make_row_gantt(DayOff $dayOff)
    {
        $has_nature = $dayOff->nature ? true : false;
        $start_date = Carbon::make($dayOff->start_date);
        $return_date = Carbon::make($dayOff->return_date);
        if ($dayOff->start_date != $dayOff->return_date) {
            $return_date->subDay(); // cause "retrun day" mean user is at  work
        };
        $duration_title = ($dayOff->duration < 2) ? " (le {$start_date->format('d-M-Y')}) "  : "(du {$start_date->format('d-M-Y')} au {$return_date->format('d-M-Y') })";
        return [
                "name" => $dayOff->applicant->registration_number . " - " . $dayOff->applicant->sortName , 
                "title" => ($has_nature ? $dayOff->nature->nature : $dayOff->reason) . " $duration_title :  $dayOff->duration jour(s) " , 
                "label" => ($has_nature ?  $dayOff->nature->nature : $dayOff->reason ), 
                "start" => $start_date->format("Y-m-d"), 
                "end" =>   $return_date->format("Y-m-d"), 
                "color" =>  $has_nature  ? $dayOff->nature->color : "#181C32" //black
            ];
    }
    public function save_dayoff_nature(Request $request)  {
        $nature = DayoffNatureColor::updateOrCreate(
            ['id' => $request->id],
            $request->except("_token", "status") + ["status" => ( $request->status == 1 ? 1 : 0)],
        );
        return ["success" => true, "message" => trans("lang.success_record"), "row_id" =>  $request->id ? row_id("type", $nature->id) : null, "data" => $this->row_dayoff_nature($nature)];
    }
    public function nature_data_list(DayoffNatureColor $nature)  {
        $data = [];
        $natures = DayoffNatureColor::whereDeleted(0)->latest()->get();
        foreach ($natures as $nature) {
            $data[] = $this->row_dayoff_nature($nature);
        }
        return ["success" => true , "data" => $data];
    }
    public function row_dayoff_nature(DayoffNatureColor $nature)  {
        $class = $nature->status ? "success" : "dark";
        $status = $nature->status ? "Activé" : "Desactivé";
        return [
            "nature" => $nature->nature . '<br><span class="badge badge-'.$class.'">'. $status  .'</span> ',
            "color" =>  '<input type="color" class="form-control form-control-solid form-control-lg" disabled readonly="true" value="'.$nature->color.'" >' ,
            "actions" =>  modal_anchor(url("/days-off/daysOffNature/modal_form"), '<i class="fas fa-pen"></i>', ["title" => "editer" ,"data-post-nature_id" =>$nature->id ]),
        ];
    }
    public function addDayoffNature(Request $request)  {
        $nature = DayoffNatureColor::find($request->nature_id) ?? new DayoffNatureColor();
        return view("days_off.dayoff-nature-color.modal-form", ["nature" => $nature]);
    }
    public function seeMyRequestDaysOff()
    {
        $data = [];
        $user = Auth::user();
        $data['user'] = $user;
        $data['basic_filter'] = DayOff::filterMyDaysOff();
        return view("days_off.my_days_off", $data);
    }

    public function data_list_my_request_days_off(Request $request)
    {
        $options = $request->except("_token");
        $options["myDaysOff"] = true;
        $daysOff = DayOff::getDetails($options)->get();
        $data = [];
        foreach ($daysOff as $dayOff) {
            $data[] = $this->make_row_my_request_days_off($dayOff);
        }
        die(json_encode(["data" => $data]));
    }

    public function make_row_my_request_days_off($dayOff)
    {
        $row = [];
        $row['DT_RowId'] = row_id("dayoff", $dayOff->id);
        $row["created_at"] = $dayOff->created_at->translatedFormat("d-M-Y");
        $row["matricule"] =  $dayOff->applicant->registration_number;
        $row["applicant"] = auth()->id() == $dayOff->applicant_id ? 'Moi' : $dayOff->applicant->sortname;
        $row["author"] = auth()->id() == $dayOff->author_id ? 'Moi-même' : $dayOff->author->sortname;
        $row['start_date'] = $dayOff->start_date->translatedFormat("d-M-Y");
        $row['return_date'] = $dayOff->return_date->translatedFormat("d-M-Y");
        $row['duration'] = $dayOff->duration . "jrs";
        $row['type'] = trans("lang.{$dayOff->type->type}");
        $row['status'] = $dayOff->getResult();
        $row['nature'] = $dayOff->nature ? '<span class="badge  " style="min-width: 90%;color: white;background-color:'.$dayOff->nature->color.'">'.$dayOff->nature->nature.'</span>'  : "" ;
        $row['reason'] = $dayOff->reason ;
        $actions = "";
        if ($dayOff->result == "in_progress") {
            if (auth()->user()->isCp()) {
                $actions = modal_anchor(url("/days-off/information/modal/$dayOff->id"), '<i class="far fa-edit text-primary fs-3"></i>', ["title" => "Plus d'informations", "data-modal-lg" => true,]);
            } else {
                $actions = modal_anchor(url('/request_days_off/modal/' . $dayOff->id), '<i class="far fa-edit text-primary fs-3"></i>', ['title' => 'Editer la demande', 'data-modal-lg' => true, 'data-post-id' => 1]);
            }
            $actions .= "&nbsp;&nbsp;&nbsp;" . js_anchor('<div class="mx-2"><i class="far fa-trash-alt text-danger fs-3"></i></div>', ['data-action-url' => url("/dayOff/delete/" . $dayOff->id), "title" => "Supprimer", "data-action" => "delete"]);
        }
        $row['actions'] = $actions;
        return $row;
    }

    public function destroy(DayOff $dayOff)
    {
        $id = $dayOff->id;
        $dayOff->deleted = 1;
        $dayOff->save();
        return ["success" => true, "message" => "Operation faite avec succès"];
    }

    public function loadModalToRequestDayOff(Request $request, DayOff $dayOff)
    {
        $can = false;
        $can_make_request = false;
        $auth = Auth::user();
        if ($auth->isAdmin() ||  $auth->isHR()) {
            $can = true;
            $can_make_request = true;
        } else {
            if ($auth->nb_days_off_remaining) {
                $can_make_request = true;
            }
        }
        $natures = DayoffNatureColor::whereDeleted(0)->whereStatus(1)->latest()->get();
        if ($auth->isAdmin() ||  $auth->isHR()){
            return view("days_off.modal.requestDayOffModal", ["dayOff" => $dayOff,  "natures" => $natures, "auth"  =>  $auth, "can_create_other_request" =>  $can, "can_make_request" => $can_make_request,  'users' => User::where('user_type_id', '<>', UserType::$_ADMIN)->whereDeleted(0)->get()]);
        }else {
            return view("days_off.modal.requestDayOffContributorModal", ["dayOff" => $dayOff,  "natures" => $natures, "auth"  =>  $auth, "can_create_other_request" =>  $can, "can_make_request" => $can_make_request,  'users' => User::where('user_type_id', '<>', UserType::$_CONTRIBUTOR)->whereDeleted(0)->get()]);
        }
    }

    public function loadSelect(Request $request)
    {
        $daysOffTypes = DaysOffType::whereType($request->type)->whereDeleted(0)->get();
        $select = "<option value='0' selected disabled>" . trans("lang.type") . ' ' . trans("lang.$request->type")  .  "</option>";
        foreach ($daysOffTypes as $type) {
            $name = $type->name . " => " . ($type->nb_days ? $type->nb_days . " jour(s)" : "");
            $select .= "<option value='$type->id'>$name</option>";
        }
        return ["data" => $select];
    }

    public function store(DayOffRequest $request)
    {
        $input = $request->input();
        $files = $request->hasFile('files') ? $request->file("files") : null;
        if (!$request->applicant_id) {
            $input['applicant_id'] = Auth::id();
        }
        if ((!User::isRhOrAdmin(Auth::user()))) { // the creator in not RH or Admin
            if ($request->request_type == "permission") {
                $this->_can_make_request_permission($input['applicant_id'], $request);
                delete_cache("get_cache_total_permission_{$input['applicant_id']}");
            }
            if ($request->request_type == "daysoff") {
                $this->_can_make_request_dayoff($input['applicant_id'], $request);
            }
        }
        $input['author_id'] = Auth::id();
        
        $dayOff = DayOff::requestDaysOff($input, $files);

        die(json_encode(["success" => true, "data" => $dayOff, "message" => "La demande a été bien sauvegardée"]));
    }

    private function _can_make_request_permission($applicant_id, DayOffRequest $request)
    {
        $type_dayOff = DaysOffType::find($request->type_id);
        if (!$type_dayOff->impact_in_dayoff_balance) {
            return false;
        }
        $sum_created = User::get_cache_total_permission($applicant_id);

        $start =  Carbon::make(to_date($request->start_date));
        $return  =   Carbon::make(to_date($request->return_date));

        $sum_request =  $return->diffInDays($start);
        $sum_total = $sum_created + $sum_request;
        if ($sum_total > (DayOff::$_max_permission_on_year + 1)) {
            die(json_encode(["success" => false, "message" => trans("lang.max_perssion_on_year_executed") . ". Déjà demandé :  $sum_created jr(s)" . " , Demandé : $sum_request jr(s) "]));
        }
    }
    private function _can_make_request_dayoff($applicant_id, DayOffRequest $request)
    {
        $type_dayOff = DaysOffType::find($request->type_id);
        if (!$type_dayOff->impact_in_dayoff_balance) {
            return false;
        }
        $nb_days_off_remaining = User::find($applicant_id)->nb_days_off_remaining;

        $start =  Carbon::make(to_date($request->start_date));
        $return  =   Carbon::make(to_date($request->return_date));

        $sum_request =  $return->diffInDays($start);
        if ($sum_request > ($nb_days_off_remaining + 1)) {
            die(json_encode(["success" => false, "message" => trans("lang.max_dayoff_executed") . ". Solde congé restant :  $nb_days_off_remaining " . " jr(s)"]));
        }
    }

    public function giveResult(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'result' => ['required'],
                "return_date" =>  'required|date_format:d/m/Y|after_or_equal:'. request('start_date')
            ]);
            if ($validator->fails()) {
                die(json_encode(["success" => false,  "message" => $validator->errors()->all()]));
            }
            if ($request->is_canceled) {
                $dayOff = DayOff::cancelDayOff($request->id);
                return ["success" => true, "row_id" => row_id("dayoff", $request->id),  "data" => $this->make_row($dayOff), "message" => "La demande a été annulé"];
            } else {
                if ($request->hasFile('files')) {
                    foreach ($request->file("files") as $file) {
                        //Save the file to the server
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads', $fileName, 'public');
                        //write the filepath to the database
                        DaysOffAttachment::create([
                            'days_off_id' => $request->id,
                            'url' => 'app/public/' . $filePath,
                            'filename' => $file->getClientOriginalName()
                        ]);
                    }
                }
                $dayoff = DayOff::responseRequest($request->id, $request->except(["id", "_token"]));
                
                $dayoff->load("applicant");
                $dayoff->load("applicant.userjob");
                die(json_encode(["success" => true, "row_id" => row_id("dayoff", $request->id),  "data" => $this->make_row($dayoff), "message" => "La demande a été bien sauvegardé"]));
            }
        }
    }

    public function requestDaysOffForMyself(AskDaysOffRequest $request)
    {
        $input = $request->input();
        $files = $request->file('files');
        $input['applicant_id'] = Auth::id();
        return $this->requestDaysOff($input, $files);
    }

    public function requestDaysOffForAnEmployee(AskDaysOffRequest $request)
    {
        $input = $request->input();
        $files = $request->file('files');
        $input['author_id'] = Auth::id();
        return $this->requestDaysOff($input, $files);
    }

    public function requestDaysOff($input, $files = null)
    {
        try {
            DB::beginTransaction();
            DayOff::requestDaysOff($input, $files);
            DB::commit();
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }

    public function downloadAttachment($idAttachment)
    {
        $attachment = DaysOffAttachment::find($idAttachment);
        $url = storage_path($attachment->url);
        return response()->download($url);
    }

    public function userDaysOff()
    {
        $data = [];
        $data['title'] = "31 - Demande de congé des employés";
        $data['users'] = User::whereDeleted(0)->where('user_type_id', '<>', UserType::$_ADMIN)->get();
        $data['daysOffTypes'] = DaysOffType::whereDeleted(0)->get();
        return view('days-off.user-days-off', $data);
    }

    public function upgradeDayOffEmployees()
    {
        $data = [];
        $data['basic_filter'] = [];
        return view('days_off.upgrade-nb-days-off-remaining', $data);
    }

    public function data_list_upgrade_days_off(Request $request)
    {
        $data = [];
        $users = User::with("userjob.job")->whereDeleted(0)->get();
        foreach ($users as $user) {
            try {
                $data[] = $this->make_row_upgrade_days_off($user);
            } catch (Exception $e) {
            }
        }
        return response()->json(["data" => $data]);
    }

    public function make_row_upgrade_days_off(User $user)
    {
        $row = [];
        $row['registration_number'] = $user->registration_number;
        $row['name'] = $user->fullname;
        $row['job'] = $user->userjob->job->name;
        $row['nb_days_off'] = $user->nb_days_off_remaining;
        return $row;
    }

    public function saveUpgradeDayOff(UpgradeEmployeeDaysOffBalanceRequest $request)
    {
        DayOff::upgradeDaysOffEmployee($request->input());
        die(json_encode(["success" => true, "data" => [], "message" => "Solde de congé modifié"]));
    }
    /** CRUD Dayoff's type Leave or Permission */
    public function dayoff_type()
    {
        return view("days_off.day-off-type.index");
    }
    public function dayoff_modal_form(DaysOffType $daysOffType)
    {
        return view("days_off.day-off-type.modal-form", ["dayoffType" => $daysOffType]);
    }
    public function save_dayoff_type(DaysOffTypeRequest $request)
    {
        $enable =  $request->enable ? 1 : 0;
        // $impact_in_daysoff =  ($request->impact_in_dayoff_balance == "1") ? 1 : 0;
        $daysOffType = DaysOffType::updateOrCreate(
            ['id' => $request->id],
            $request->except("_token", "enable") + ["enable" => $enable],
        );
        return ["success" => true, "message" => trans("lang.success_record"), "row_id" =>  $request->id ? row_id("type", $daysOffType->id) : null, "data" => $this->_make_row_dayoff_type($daysOffType)];
    }
    public function daysoff_type_data_list()
    {
        $data = [];
        $types = DaysOffType::whereDeleted(0)->get();
        foreach ($types as $type) {
            $data[] = $this->_make_row_dayoff_type($type);
        }
        return ["data" => $data];
    }
    private function _make_row_dayoff_type(DaysOffType $type)
    {
        return [
            "DT_RowId" => row_id("type", $type->id),
            "type" => trans("lang.$type->type"),
            "name" => $type->name,
            "nb_days" => $type->nb_days,
            "impact_in_dayoff_balance" => $type->impact_in_dayoff_balance  ? trans("lang.yes") : trans("lang.no"),
            "description" => Str::limit($type->description, 50),
            "enable" => $type->enable ? '<span class="badge badge-success">Activé</span>' : '<span class="badge badge-danger">désactivé</span>',
            "actions" => modal_anchor(url("/days-off/daysOffType/modal_form/$type->id"), '<i class="fas fa-pen"></i>', ["class" => "btn btn-sm btn-active-light-primary btn-clean"])
        ];
    }
    public function export_pdf(DayOff $dayOff, $pre_view = false)
    {
        $pdf = PDF::loadView('days_off.pdf.index', ["dayOff" => $dayOff]);
        $file = "demande-{$dayOff->type->getType()}-{$dayOff->applicant->firstname}-{$dayOff->getDemandeDate()->format('d-m-Y')}";
        if ($pre_view) {
            return view('days_off.pdf.index', ["dayOff" => $dayOff]);
        } else {
            return $pdf->download("$file.pdf");
        }
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|exists:days_off,id',
            'start_date' => 'required|date_format:d/m/Y',
            'return_date' => 'required|date_format:d/m/Y|after:start_date',
            'nature_id' => 'required|exists:dayoff_nature_color,id',
            'reason' => 'required|string',
        ]);
        if($validator->fails()){

            // return ["success" => false, "message" => 'validation error'];
            die(json_encode(["success" => false,  "message" => $validator->errors()->first()]));
        //    return ["success" => false, "message" => $validator->errors()->first()];
        }


        $dayOff = DayOff::find($request->id);
        $dayOff->start_date = convert_date_to_database_date($request->start_date);
        $dayOff->return_date = convert_date_to_database_date($request->return_date);
        $dayOff->nature_id =$request->nature_id ;
        $dayOff->reason =$request->reason ;
        $dayOff->save();

        // DayOff::where('id',$request->id)->update($validator->validated());


        return ["success" => true, "row_id" => row_id("dayoff", $request->id),  "data" => $this->make_row($dayOff), "message" => "La demande de congé modifiée avec succès!"];
        // return ["success" => true, "message"  => 'Demande de congé modifiée avec succès!'];
    }
}
