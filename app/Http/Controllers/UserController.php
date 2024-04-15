<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Exception;
use App\Models\Job;
use App\Models\Kid;
use App\Models\User;
use App\Models\Check;
use App\Models\UserJob;
use App\Models\Sanction;
use App\Models\UserType;
use App\Models\SuiviItem;
use App\Models\Department;
use App\Models\UserJobView;
use App\Models\ContractType;
use App\Models\Notification;
use App\Models\ProjectGroup;
use Illuminate\Http\Request;
use App\Models\MaritalStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\updateUserInfoRequest;
use App\Notifications\ContratChangedNotification;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class UserController extends Controller
{
    private $gateUser = "users";

    public function index()
    {
        
        $data = array();
        $data['title'] = '31 - Gestion Utilisateur';
        $data['basic_filter'] = UserJob::createFilter() ;
        return view('users.index', $data);
    }

    public function data_list(Request $request)
    {
        $data = [];
        $userJobs = get_users_cache();
        $userJobs = $this->filter( $userJobs , $request->all());
        foreach ($userJobs as $userJob) {
            $data[] = $this->make_row($userJob);
        }
        die(json_encode(["data" => $data]));
    }
    private function filter($userJobs , $filter = [])
    {
        $contract_type = get_array_value( $filter,"contract_type");
        if ($contract_type) {
            $userJobs= $userJobs->where("contract_type_id", $contract_type);
        }
        $local = get_array_value( $filter,"local");
        if ($local) {
            $userJobs = $userJobs->where("local", $local);
        }
        $department = get_array_value( $filter,"department");
        if ($department) {
            $userJobs = $userJobs->where("department_id", $department);
        }
        
        return $userJobs;
    }
    public function make_row($userJob)
    {
        $id = $userJob->user->id;
        $row = [];
        $row['registration_number'] = $userJob->user->registration_number;
        $row['name'] = anchor(url("/user/form/$id"), $userJob->user->fullname .( $userJob->user->deleted ? '<span class="badge badge-light-danger">Supprimé</span>' : "")   , ["class" => "text-primary"]);
        $row['avatar'] =  "<div class='symbol symbol-30px symbol-circle'><img alt='avatar' src=".  $userJob->user->avatarUrl ."></div>";
        $row['job'] = $userJob->job->name;
        $contractType = $userJob->contractType; 
        $row['contract_type'] =  $contractType ->acronym ;
        if ($userJob->contractType->id == ContractType::$_PE_CONTRAT || $userJob->contractType->id == ContractType::$_PE_CONTRAT_RENEW) {
            $row['contract_type'] .= " " . $this->progress_pe($userJob->user->hiring_date, $userJob->contract_type_id);
        }
        $row['user_type'] = $userJob->user->type->name;
        $row['department'] = $userJob->department->name;
        $row['local'] = $userJob->getLocal();
        $row['detail'] = anchor(url("/user/form/$id"), '<i class=" text-hover-primary fas fa-edit" style="font-size:15px"></i>', ["class" => "btn btn-sm btn-clean "])
        ." " .modal_anchor(url("/user/delete-modal"), '<i class=" text-hover-danger fas fa-trash " style="font-size:12px" ></i>', ['title' => trans('lang.delete'), 'data-post-user_id' =>  $id]); 
        ;
        return $row;
    }
    private function progress_pe($hiring_date, $contractTypeID){
        $max = ContractType::getEndDayPE($contractTypeID);
        $finished = Carbon::parse($hiring_date)->diffInDays(Carbon::now());
        $title = "$finished%";
        if ($finished == 0) {
            $title = "1%  ";
            $finished = 1;
        }
        if ($finished >= $max) {
            $title = "100%";
            $finished = $max;
        }
        $rest_days = $max -  $finished;
        return "<br> <progress title = '$title terminé dans $rest_days jour(s)' max='$max' value='$finished' ></progress> ";
    }
    public function goToNewUserForm(User $user)
    {
        $user->load('userJob.department');
        $data = [];
        $data['user_id'] = $user->id ?? "";
        $data['title'] = "31 - Gestion Utilisateur";
        $data['jobs']  = Job::whereDeleted(0)->get();
        $data['maritalStatuses'] = MaritalStatus::whereDeleted(0)->get();
        $data['contractTypes'] = ContractType::whereDeleted(0)->get();
        $data['userTypes'] = UserType::whereDeleted(0)->orderBy('id', 'desc')->get();
        $data['departments'] = Department::whereDeleted(0)->get();
        $data['registrationNumber'] = User::getNewRegistrationNumber();
        $data['locals'] = UserJob::localList();
        $data['categories'] = UserJob::getCategories();
        $data['groups'] = UserJob::getGroups();
        return view('users.user-form', $data, compact('user'));
    }

    public function store(CreateUserRequest $request)
    {
        try {
            // kids required
            $kids = checkKids($request["kids_fullname"],$request["kids_birthdate_first"]);
            if($kids!="") return ['success' => false, 'message' => 'Erreur sur le champ enfant numero '.$kids];

            $get_valid_lines = check_valid_line($request["kids_fullname"],$request["kids_birthdate_first"]);
            if(count($get_valid_lines["kids_fullname"]) && count($get_valid_lines["kids_birthdate_first"])){
                $request->merge([
                    'kids_fullname' => $get_valid_lines["kids_fullname"],
                    'kids_birthdate_first' => $get_valid_lines["kids_birthdate_first"],
                ]);
            }
            User::createNewUser($request->input());
            delete_users_cache();
            delete_cache("get_cache_rh_admin");
            delete_cache("getUserList");
            return ["success" => true,"message" => "Enregistrement effectué"];
        }
        catch(Exception $e) {
            return ["success" => false,"message" => "Echec d'enregistrement: " . $e->getMessage()];
        }
    }

    public function edit($id, CreateUserRequest $request)
    {

        $user = User::find($id);
        $user->registration_number = $request->registration_number;
        $user->name = $request->name;
        $user->firstname = $request->firstname;
        $user->sex = $request->sex;
        $user->birthdate = to_date($request->birthdate);
        $user->place_of_birth = $request->place_of_birth;
        $user->cin = $request->cin;
        try {
            $user->cin_delivered = to_date($request->cin_delivered);
        }
        catch (Exception $e) {
        }
        $user->marital_status_id = $request->marital_status_id;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        $user->father_fullname = $request->father_fullname;
        $user->mother_fullname = $request->mother_fullname;
        $user->qualification = $request->qualification;
        $user->account_number = $request->account_number;
        $user->hiring_date = to_date($request->hiring_date);
        $user->regulation = $request->regulation;
        $user->user_type_id = $request->user_type_id;
        // $user->verbal_warning = $request->verbal_warning;
        // $user->written_warning = $request->written_warning;
        // $user->layoff = $request->layoff;
        // Notifications update dayoff
        $user->nb_days_off_remaining = $request->nb_days_off_remaining;
        if($request->marital_status_id == 2 ){
            $user->marry_fullname = $request->marry_fullname;
            if($request->marry_birthdate!=null) $user->marry_birthdate = to_date($request->marry_birthdate);
            $user->marry_place_of_birth = $request->marry_place_of_birth;
            $user->marry_CIN = $request->marry_CIN;
            if($request->marry_cin_delivered!=null) $user->marry_cin_delivered = to_date($request->marry_cin_delivered);
            $user->marry_phone_number = $request->marry_phone_number;
            $user->marry_email = $request->marry_email;
            $user->marry_job = $request->marry_job;
        }else{
            $user->marry_fullname = null;
            $user->marry_birthdate = null;
            $user->marry_place_of_birth = null;
            $user->marry_CIN = null;
            $user->marry_cin_delivered = null;
            $user->marry_phone_number = null;
            $user->marry_email = null;
            $user->marry_job = null;
        }

        // job
        $user_job = UserJob::find($user->userJob->id);
        $user_job->salary = $request->salary;
        $user_job->jobs_id = $request->jobs_id;
        if ($request->contract_type_id !=$user_job->contract_type_id) {
            $this->contrat_changed_notification($user,$request->contract_type_id);
        }
        $user_job->contract_type_id = $request->contract_type_id;
        $user_job->local = $request->local;
        $user_job->category = $request->category;
        $user_job->group = $request->group;
        $user_job->department_id = $request->department_id;
        $user_job->is_cp = ($request->is_cp == 1) ? 1 : 0; // est un Chef de projet

        DB::table("kids")->where("user_id",$user->id)->delete();

        // kids required
        $kids = checkKids($request["kids_fullname"],$request["kids_birthdate_first"]);

        if($kids!="") return ['success' => false, 'message' => 'Erreur sur le champ enfant numero '.$kids];

        $get_valid_lines = check_valid_line($request["kids_fullname"],$request["kids_birthdate_first"]);
        if(count($get_valid_lines["kids_fullname"]) && count($get_valid_lines["kids_birthdate_first"])){
            $request->merge([
                'kids_fullname' => $get_valid_lines["kids_fullname"],
                'kids_birthdate_first' => $get_valid_lines["kids_birthdate_first"],
            ]);
        }

        if(!check_null_array($request["kids_fullname"]) || !check_null_array($request["kids_birthdate_first"])){
            $i = 0;
            foreach($request["kids_birthdate_first"] as $kids){
                Kid::create(['fullname'=> $request['kids_fullname'][$i], 'birthdate'=>to_date($kids),'user_id'=>$user->id]);
                $i++;
            }
        }
        delete_users_cache();
        delete_cache("get_cache_rh_admin");
        delete_cache("getUserList");
        if ($user->save() && $user_job->save()) {
            die(json_encode(["success" => true, "message" => "Modification effectué" ]));
        }
    }
    public function detail($id)
    {
        Gate::authorize($this->gateUser);
        $data = [];
        $data['title'] = "31 - Gestion utilisateur";
        $data['historicJobs'] = UserJob::with(['job', 'contractType'])->whereDeleted(0)->where('users_id', $id)->orderByDesc("date_user_job")->get();
        $data['infoUser'] = $data['historicJobs'][0];
        $data['icofont'] = ($data['infoUser']->user->sex == "1") ?  "icofont-boy" : "icofont-girl";
        $data['jobs'] = Job::whereDeleted(0)->get();
        $data['contractTypes'] = ContractType::whereDeleted(0)->get();
        $data['marital_statuses'] = MaritalStatus::whereDeleted(0)->get();
        return view('users.detail', $data);
    }
    private function contrat_changed_notification($user ,$contract_type_id){
        $notify_to = get_cache_rh_admin()->push($user);
        \Notification::send($notify_to,new ContratChangedNotification($user,  Auth::user(), ContractType::find($contract_type_id)));
    }
    public function addNewJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => ['required'],
            'jobs_id' => ['required'],
            'salary' => ['required', 'numeric', 'min:1'],
            'contract_type_id' => ['required'],
            'date_user_job' => ['required', 'date']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'errors' => $validator->errors()->all()
            ]);
        }

        UserJob::updateOrCreate([
                'id' => $request->id
            ], [
                'users_id' => $request->users_id,
                'jobs_id' => $request->jobs_id,
                'salary' => $request->salary,
                'contract_type_id' => $request->contract_type_id,
                'date_user_job' => $request->date_user_job
            ]);

        return response()->json([
            'status' => 200,
            'success' => 'OK'
        ]);
    }

    public function check(Request $request)
    {
        $auth =  Auth::user();
        if ($request->check_event == "fin" || $request->check_event == "out") {
            $folder_in_progress = SuiviItem::where("user_id", $auth->id)->where("status_id",SuiviItem::$IN_PROGRESS)->whereDeleted(0)->count();
            if ($folder_in_progress) {
                $message  = 'Vous avez un dossier «En cours» de statut. <br> Mettez le en « Pause » ou « Terminer »  avant de deconnecte. <br> Réf : Dans tableau de dossier';
                return ["success" => false, "message" =>  $message];
            }
         }
        Check::create([
            "user_id" => Auth::id(),
            "registration_number" => Auth::user()->registration_number,
            "check_event" =>$request->check_event,
            "on" => "web",
            "date_time" => now(),
        ]);
        $buttons = $this->_make_hide_btn($request->check_event);
        $auth->last_check = ($request->check_event === "fin" ? "out" : $request->check_event);
        $auth->save();
        if ($request->check_event === "fin") {
           (new AuthenticatedSessionController)->destroy($request);
        }
        return [
            "success" => true ,
            "event" => $request->check_event ,
            "message" =>"Thank you !" ,
            "check_event" => trans("lang.$request->check_event") ,
            "to_hide_btn" =>   get_array_value($buttons , "to_hide") ,
            "to_active_btn" => get_array_value($buttons , "to_active"),
            "clear_chrono" => in_array($request->check_event,["out","pause"]) ?? false,
            "block_page" => ($request->check_event == "pause" || $request->check_event == "out" ) ,
        ];
    }

    private function _make_hide_btn($check_event = "") {
        $to_hide_btn = $to_active_btn  = [];
        if($check_event === "in" ){
            $to_hide_btn[] = "in";
            $to_active_btn[] = "pause";
            $to_active_btn[] = "out";
        }
        if($check_event === "pause" ){
            $to_hide_btn[] = "pause";
            $to_hide_btn[] = "out";
            $to_active_btn[] = "in";
        }
        if($check_event === "out" ){
            $to_hide_btn[] = "pause";
            $to_hide_btn[] = "out";

            $to_active_btn[] = "in";
        }
        return ["to_hide" => $to_hide_btn , "to_active" => $to_active_btn];
    }
    public function check_history_modal()
    {
        return view('check.history');
    }
    public function check_history_list(Request $request)
    {
        $data = [];
        $histories = Auth::user()->check()->orderBy('date_time', 'DESC')->get();
        foreach ($histories as $check) {
            $data[] =  $this->_check_history_row($check);
        }
        return ["data" => $data];
    }
    private function _check_history_row($check)
    {
        return [
            "user" => $check->user->fullname,
            "check_event" =>  view("check.column.check-event" ,["check" => $check])->render(),
            "date_time" => $check->date_time,
        ];
    }
    public function delete_user(Request $request )
    {
        $auth = Auth::user();
        if (!$auth->isAdmin() && !$auth->isHR()) {
            return ["success" => false, "message" => "Acceés refusé"];
        }
        $user= User::find($request->user_id);
        $user->deleted = 1;
        $user->deleted_by = $auth->id;
        if ($user->save()) {
            delete_users_cache();
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }else{
            return ["success" => false, "message" => trans("lang.error_occured")];
        }
    }
    public function delete_user_modal(Request $request )
    {
        $user= User::find($request->user_id);
        $user->load("job");
        return view("users.delete-user-modal",compact("user"));
    }

    public function info_tab( Request $request)
    {
        $user = Auth::user();
        $maritalStatuses = MaritalStatus::whereDeleted(0)->get();
        return view("users.info" , compact("user","maritalStatuses"));
    }
    public function update_info( updateUserInfoRequest $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->firstname = $request->firstname;
        $user->sex = $request->sex;
        $user->birthdate = to_date($request->birthdate);
        $user->place_of_birth = $request->place_of_birth;
        $user->cin = $request->cin;
        $user->cin_delivered = to_date($request->cin_delivered);
        $user->marital_status_id = $request->marital_status_id;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        $user->father_fullname = $request->father_fullname;
        $user->mother_fullname = $request->mother_fullname;
        if($request->marital_status_id == 2 ){
            $user->marry_fullname = $request->marry_fullname;
            if($request->marry_birthdate) $user->marry_birthdate = to_date($request->marry_birthdate);
            $user->marry_place_of_birth = $request->marry_place_of_birth;
            $user->marry_CIN = $request->marry_CIN;
            if($request->marry_cin_delivered) $user->marry_cin_delivered = to_date($request->marry_cin_delivered);
            $user->marry_phone_number = $request->marry_phone_number;
            $user->marry_email = $request->marry_email;
            $user->marry_job = $request->marry_job;
        }else{
            $user->marry_fullname = null;
            $user->marry_birthdate = null;
            $user->marry_place_of_birth = null;
            $user->marry_CIN = null;
            $user->marry_cin_delivered = null;
            $user->marry_phone_number = null;
            $user->marry_email = null;
            $user->marry_job = null;
        }
        if ($user->save()) {
            delete_users_cache();
            delete_cache("getUserList");
            return ["success" => true, "message" => "Modification effectué" ];
        }else{
            return ["success" => false, "message" => trans("lang.error_occured") ];

        }
    }    

    public function setting_account_tab( Request $request)
    {
        $data['user']  = Auth::user();
        $data['jobs']  = Job::whereDeleted(0)->get();
        $data['maritalStatuses'] = MaritalStatus::whereDeleted(0)->get();
        $data['contractTypes'] = ContractType::whereDeleted(0)->get();
        $data['userTypes'] = UserType::whereDeleted(0)->orderBy('id', 'desc')->get();
        $data['departments'] = Department::whereDeleted(0)->get();
        return view("pages.account.settings._signin-method" , $data);
    }
    public function user_work_info( Request $request)
    {
        $data['user']  = Auth::user();
        $data['jobs']  = Job::whereDeleted(0)->get();
        $data['maritalStatuses'] = MaritalStatus::whereDeleted(0)->get();
        $data['contractTypes'] = ContractType::whereDeleted(0)->get();
        $data['userTypes'] = UserType::whereDeleted(0)->orderBy('id', 'desc')->get();
        $data['departments'] = Department::whereDeleted(0)->get();
        return view("users.user-work-info" , $data);
    }
    public function update_avatar( Request $request)
    {
        $avatar = null; 
        if($request->hasFile("avatar")){
            $file_info = upload($request->file("avatar"), "avatar", "public", ["folder" => "", "format" => 100]);
            $avatar = $file_info["name"];
        }
        Auth::user()->update(["avatar" =>  $avatar]);
        return ["success" => true, "message" => "Avatar mise ajour !" ];
    }

    public function get_sanction()
    {
        $user = Auth::user();
        $data = [];
        $data['user'] = $user;
        $data['sanctions'] = Sanction::where('user_id', $user->id)->whereDeleted(0)->get();
        return view('users.sanction', $data);
    }
    public function set_notification_as_seen(Request $request)
    {
        $notifications = [];
        $data  = is_array($request->notifications) ? $request->notifications : explode(",",$request->notifications);
        foreach ($data as $notification) {
            if ($notification) {
                $notifications[] = $notification;
            }
        }
        Notification::whereIn('id', $notifications)->update(['read_at' => now()]);
        return ["success" => true , "id" =>$notifications];
    }
    public function mark_as_read(Request $request)
    {
        $notification = Notification::find($request->id);
        $notification->read_at = now();
        $notification->save();
        return ["success" => true , "data" => view('notifications.template', ['notification' => $notification])->render()];
    }

    public  function load_more_notification(Request $request){
        $html = "";
        $has_more = false;
        $auth = Auth::user();

        $per = Notification::$_PER;
        $more = Notification::where("notifiable_id", $auth->id)->latest()->take($per);
        if ($request->offset){
            $more->skip($request->offset);
        }
        $notifications = $more->get();
        if ($notifications->count()) {
            $has_more = true;
        }
        foreach ($notifications as $notification) {
            $html.= view('notifications.template', ['notification' => $notification ,"from_load_more" => true])->render();
        }
        return ["success" => true ,"item" => $html , "offset" => $per + $request->offset ,"has_more" =>  $has_more ];
    }

    public function renewPasswordByRegistrationNumber($registration_number) {
        $user = User::where('registration_number', $registration_number)->first();
        if ($user) {
            $user->password = Hash::make('123456');
            $user->save();
        }
        return "OK";
    }
    public function project_member() {
        $basic_filter = [];
        return view('users.project-members.index', ["basic_filter" =>$basic_filter ]);
    }
    public function  add_project_from() {
        return view('users.project-members.project-modal-form');
    }
    public function  create_project(Request $request) {
       $project = ProjectGroup::updateOrCreate(["id" => $request->id], ["name" => $request->name]);
       return ["success" => true ,"message" => trans("lang.success_record") , "data"=> $this->_make_project_member_data_list($project)];
    }
    public function  project_member_data_list() {
        $data =[];
        $projects = ProjectGroup::with(["members" => function($q){
            $q->whereDeleted(0)->without(["userJob"]);
        }])->whereDeleted(0)->get();
        foreach ($projects as $project) {
            $data[] = $this->_make_project_member_data_list($project);
        }
        return ["data" =>  $data];
    }
    public function  _make_project_member_data_list(ProjectGroup $project ) {
        $row = [];
        $row["DT_RowId"] = row_id("project",$project->id);
        $row["name"] =  '<div class="d-flex align-items-center mb-6">
                            <span data-kt-element="bullet" class="bullet bullet-vertical d-flex align-items-center min-h-40px mh-100 me-4 bg-success"></span>
                            <div class="flex-grow-1 me-5">
                                <div class="text-gray-800 fw-semibold fs-2">'.$project->name.'</div>
                            </div>
                        </div>';
        $row["members_name_hidden"] = $project->members->implode("sortname","|");
        $row["members"] = view("users.project-members.member-list-column",["project" => $project])->render();
        $row["action"] =modal_anchor(url('/project/edit/modal'), '<i class="fas fa-edit fs-4"></i> ', ['title' => "Edit/supprimer des membres dans ce projet","data-post-id" => $project->id, "data-modal-lg" => true, ]) ;
        return $row;
    }
    public function  add_project_new_member_modal(Request $request) {
        $project = ProjectGroup::with(["members" => function($q){
                $q->whereDeleted(0)->without(["userJob"]);
        }])->find($request->id);
        $users = User::whereNotIn("id",$project->members->pluck("id")->toArray())->whereDeleted(0)->get();
        return view('users.project-members.add-members-modal',["users" => $users, "project" => $project ]);
    }
    public function  save_project_new_member_modal(Request $request) {
        $project = ProjectGroup::find($request->id);
        $project->members()->syncWithoutDetaching($request->users);
        $project->load("members");
        return ["success" => true ,"message" => trans("lang.success_record") ,"row_id" => row_id("project",$project->id) , "data"=> $this->_make_project_member_data_list($project)];
    }
    public function  edit_project_member_modal(Request $request) {
        $project =  ProjectGroup::with(["members" => function($q){
            $q->whereDeleted(0)->without(["userJob"]);
        }])->find($request->id);
        return view('users.project-members.edit-project-group-modal-form', compact("project"));
    }
    public function  save_edit_project_group(Request $request) {
        $project =  ProjectGroup::find($request->id);
        $update = [];
        if($request->new_name){
            $update["name"] =$request->new_name ;
        }
        if($request->deleted ){
            $update["deleted"] = 1 ;
        }
        if($request->exlude_users){
            $project->members()->detach($request->exlude_users);
        }
        if(count($update)){
            $project->update($update);
        }
        return ["success" => true ,"deleted" => $request->deleted, "message" =>  $request->deleted ? trans("lang.success_deleted") :  trans("lang.success_record") ,"row_id" => row_id("project",$project->id) , "data"=> $this->_make_project_member_data_list($project)];

    }
}
