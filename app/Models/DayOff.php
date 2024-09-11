<?php

namespace App\Models;

use Notification;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\DayOffCreatedNotification;
use App\Notifications\DayOffUpdateStatusNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DayOff extends Model
{
    use HasFactory;
    protected $table = 'days_off';
    protected $fillable = [
        'applicant_id',
        'author_id',
        'reason',
        'start_date',
        'return_date',
        'start_date_is_morning',
        'return_date_is_morning',
        'manager_id',
        'result',
        'result_date',
        'comment',
        'type_id',
        'nature_id'
    ];

    protected $casts = [
        'start_date' => 'date:d-M-Y',
        'return_date' => 'datetime:d-M-Y',
    ];

    public $status = ["in_progress", "validated", "refused"];
    public static $idDayOffPayd = "1";
    public static $_max_permission_on_year = 10; // days

    const TYPE_OF_ABSENCE = [
        ["id" => "dayoff" , "text"  => "Congé"],
        ["id" => "permission" , "text"  => "Permission"],
        ["id" => "status_report" , "text"  => "Rapport d'etat"],
    ];

    public function getStartDate()
    {
        return Carbon::make($this->start_date);
    }

    public function getApplicantJob()
    {
        try {
            if ($this->applicant->isAdmin()) {
                return "Admin";
            }
            if ($this->applicant->isHR()) {
                return "Rh";
            }
            return $this->applicant->userjob->job->name;
        } catch (Exception $e) {
            return null;
        }
    }
    public function getReturnDate()
    {
        return Carbon::make($this->return_date);
    }
    public function getDemandeDate()
    {
        return Carbon::make($this->created_at);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function type()
    {
        return $this->belongsTo(DaysOffType::class, "type_id");
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function attachments()
    {
        return $this->hasMany(DaysOffAttachment::class, "days_off_id");
    }
    public function nature()
    {
        return $this->belongsTo(DayoffNatureColor::class, "nature_id");
    }
    public function scopeNotRefused($query)
    {
        return $query->where("result","!=" ,"refused");
    }

    public function getDurationAttribute()
    {
        $start = date_create($this->start_date);
        $end = date_create($this->return_date);
        $interval = date_diff($start, $end);
        $duration = $interval->format("%a");
        if ($this->start_date_is_morning != $this->return_date_is_morning) {
            if ($this->start_date_is_morning == "0"){
                $duration -= 0.5;
            }else{
                $duration += 0.5;
            }                                     
        }
        return $duration * ($duration < 0 ? -1 : 1) ;
    }
    public function getNewCalculDuration($start_date, $return_date ,$start_date_is_morning ,$return_date_is_morning )
    {
        $start = date_create($start_date);
        $end = date_create($return_date );
        $interval = date_diff($start, $end);
        $duration = $interval->format("%a");
        if ($start_date_is_morning != $return_date_is_morning) {
            if ($start_date_is_morning == "0"){
                $duration -= 0.5;
            }else{
                $duration += 0.5;
            }                                     
        }
        return $duration * ($duration < 0 ? -1 : 1) ;
    }

    //This function is used to display the result of the days off request
    public function getResult()
    {
        $result = '<span class="%s">%s</span';
        $class = "";
        if ($this->result == $this->status[0])            $class = "badge badge-light-primary fw-bolder fs-8 px-2 py-1 ms-2";
        else if ($this->result == $this->status[1])       $class = "badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2";
        else if ($this->result == $this->status[2])       $class = "badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2";
        return sprintf($result, $class, __("lang." . $this->result));
    }

    public static function getDetails($options = [])
    {
        $is_empty_filter = true;$auth = Auth::user();
        $daysOff = DayOff::with(['applicant.userjob.job', 'author',"nature"]);

        $myDaysOff = get_array_value($options, 'myDaysOff'); // the auth daysoff or  daysoff in his departemt
        if ($myDaysOff) {
            $auth->load('userJob');
            $dayoff_can_see = [$auth->id];
            /** Auth's and users in departement of auth */
            if ($auth->userJob) {
                if ($auth->userJob->is_cp || $auth->isM2p()) {
                    $user_in_departement = UserJobView::where("department_id", $auth->userJob->department_id)->get()->pluck("users_id")->toArray();
                    $dayoff_can_see = array_merge( $dayoff_can_see ,$user_in_departement );
                }
            }
            /** Auth's can validate dayoff */
            $list_users_where_auth_can_validate_dayoff = User::getListOfUsersCanValidateDayOff($auth->id) ;
            if ($list_users_where_auth_can_validate_dayoff) {
                $dayoff_can_see = array_merge( $dayoff_can_see ,$list_users_where_auth_can_validate_dayoff );
            }
            /** Query auth's see can  dayoff */
            $daysOff->whereIn("applicant_id", array_unique($dayoff_can_see) );
        }
        $user_id = get_array_value($options, 'user_id');
        if ($user_id) {
            $is_empty_filter = false;
            $daysOff->where('applicant_id', $user_id);
        }
        $result = get_array_value($options, 'result');
        if ($result) {
            $is_empty_filter = false;
            $daysOff->where('result', $result);
        }
        $nature_id = get_array_value($options, 'nature_id');
        if ($nature_id) {
            $is_empty_filter = false;
            $daysOff->where('nature_id', $nature_id);
        }
        $project_id = get_array_value($options, 'project_id');
        if ($project_id) {
            $is_empty_filter = false;
            $members = DB::table("project_group-members")->where("project_id", $project_id)->get()->pluck("user_id")->toArray();
            $daysOff->whereIn('applicant_id', $members);
        }
        $created_at = get_array_value($options, 'created_at');
        if ($created_at) {
            $is_empty_filter = false;
            $date = explode("-", $created_at);
           $daysOff->whereBetween('created_at', [to_date($date[0]), to_date($date[1])]);
            
        }
        $absence_date = get_array_value($options, 'absence_date');
        if ($absence_date) {
            $is_empty_filter = false;
            $daysOff->whereDate('start_date', '<=', to_date($absence_date))
                    ->whereDate('return_date', '>', to_date($absence_date) ." 00:00:00" )
                    ->notRefused();
        }
        $status_dayoff = get_array_value($options, 'status_dayoff'); //finish,in_progress
        if ($status_dayoff) {
            $is_empty_filter = false;
            if ($status_dayoff == "finish") {
                $daysOff->whereDate('return_date', '<=', Carbon::now()->format("Y-m-d") ." 00:00:00" )
                    ->where('result', 'validated')
                    ->notRefused();
            } else if ($status_dayoff == "in_progress") {
                $daysOff->whereDate('start_date', '<=', Carbon::now()->format("Y-m-d") ." 00:01:00")
                    ->whereDate('return_date', '>', Carbon::now()->format("Y-m-d") ." 00:00:00")
                    ->where('result', 'validated')
                    ->notRefused();
            } else if ($status_dayoff == "is_canceled") {
                $daysOff->where("is_canceled", 1);
            }
        }
        $job_id = get_array_value($options, "job_id");
        if ($job_id) {
            $is_empty_filter = false;
            $daysOff->whereHas('applicant', function ($query) use ($job_id) {
                $query->whereHas("userjob", function ($query2)  use ($job_id) {
                    $query2->where("jobs_id", $job_id);
                });
            });
        }
        $created_at = get_array_value($options, 'created_at');
        if ($created_at) {
            $is_empty_filter = false;
            $date = explode("-", $created_at);
            $daysOff->whereBetween('created_at', [to_date($date[0]), to_date($date[1])]);
        }
        // retrieve last record of the day for the last 3  days
        if ($is_empty_filter) {
            $daysOff->where('return_date', '>=', now()->subDays(3));
        }
        return $daysOff->orderBy("created_at", "DESC")->whereDeleted(0);
    }

    public static function countDayOffWithoutResponse()
    {
        return DayOff::selectRaw("COUNT(*) as nb")->where("result", "in_progress")->whereDeleted(0)->first()->nb;
    }

    public static function upgradeDaysOffEmployee($input)
    {
        $nb = $input['nb'];
        User::whereDeleted(0)->update([
            'nb_days_off_remaining' => DB::raw("nb_days_off_remaining + $nb")
        ]);
    }

    public static function getDemand($options = [])
    {
        $daysOff = DayOff::with(['applicant', 'attachments']);
        $daysOff->whereNull('result_date');
        $status = get_array_value($options, 'status');
        if ($status) {
            $daysOff->where('status ', $status);
        }

        return $daysOff->whereDeleted(0);
    }
    public static function getDemandInProgress($options = [])
    {

        return self::getDemand(['status' => 'in_progress']);
    }

    public static function cancelDayOff($id)
    {
        $dayOff = DayOff::with(["applicant","applicant.userjob"])->find($id);
        $dayOff->is_canceled = true;
        $dayOff->save();
        $dayOff->returnDuration();
        $dayOff->sendNotificationOnUpdate();
        return $dayOff;
    }

    public function returnDuration()
    {
        if ($this->type_id == "1") {
            $user = $this->applicant;
            $user->nb_days_off_remaining += $this->duration;
            $user->save();
        }
    }

    public static function responseRequest($id, $data)
    {
        /** Old dayoff info */
        $dayOff = DayOff::with(["type","applicant"])->find($id);
        $old_duration = $dayOff->duration;
        $old_result = $dayOff->result;

        /** New  dayoff info */
        $data['start_date'] = to_date($data['start_date']);
        if ($data['start_date_is_morning'] == 0) {
            $data['start_date'] =  $data['start_date'] . " 12:00:00"; // Start in apres midi
        }
        $data['return_date'] = to_date($data['return_date']);
        if ($data['return_date_is_morning'] == 0) {
            $data['return_date'] =  $data['return_date'] . " 12:00:00"; // return in apres midi
        }
        $data['result_date'] = null;
        if ($data['result'] == 'validated'){
            $data['result_date'] = Carbon::now();
        }
        $dayOff->update($data);
        $new_duration =  $dayOff->duration;
        $new_result =  $data["result"];
        $applicant = $dayOff->applicant;
     
        /** Check if  dayOff  type is impact in dayoff user balance */
        $is_impacted_in_dayoff_balance = $dayOff->type->impact_in_dayoff_balance  &&  $dayOff->type->type == "daysoff";
        /** Yeaaah the code is utra dynamic  */
        $the_type_impacted = "";
        if ($dayOff->type->type == "daysoff") {
            $the_type_impacted = "nb_days_off_remaining";
        }elseif($dayOff->type->type == "permission"){
            $the_type_impacted = "nb_permissions";
        }
        /** Exectute the dynamic code start  */
        $already_validated = ($old_result == "validated");
        
        if ($is_impacted_in_dayoff_balance &&  $already_validated && ($new_duration != $old_duration)) {
            /** Returned old deduce duration user dayoff balance and retrivew the new  */
            $applicant->$the_type_impacted = ($applicant->$the_type_impacted  +  $old_duration) - $new_duration;
            $applicant->save();
        }
        
        if( $is_impacted_in_dayoff_balance &&  $already_validated &&  $new_result == "refused" ){
            /** Returned old deduce duration  */
            $applicant->$the_type_impacted  = $applicant->$the_type_impacted  +  $old_duration;
            $applicant->save();
        }
        $dayOff->sendNotificationOnUpdate();
        return $dayOff;
    }
    
    public function sendNotificationOnUpdate()
    {
        $ids_to_notify = [];
        if ($this->author_id != $this->applicant_id) {
            $ids_to_notify[] = $this->applicant_id;
            $ids_to_notify[] =  $this->author_id;
        } else {
            $ids_to_notify[] = $this->applicant_id;
        }

        $this->load(["applicant.userJob"]);
        /** get CP on user departement */
        try {
            $department_id =  $this->applicant->userJob->department_id;
            $cp_ids =  UserJobView::where("department_id", $department_id)->where("is_cp", 1)->get()->pluck("users_id")->toArray();
            $ids_to_notify = array_merge($ids_to_notify ,$cp_ids);
        } catch (\Throwable $th) {
            
        }
        /** Inform validateur of dayoff where user in group*/
        $users_validator_ids = User::getListValidatorUserDayoff($this->applicant_id);
        if ( $users_validator_ids ) {
            $ids_to_notify = array_merge($ids_to_notify ,$users_validator_ids);
        }
        $users_to_notify = User::whereDeleted(0)->whereIn("id",array_unique($ids_to_notify))->get();
        $users_admin = get_cache_rh_admin();
        $users_to_notify = $users_to_notify->merge($users_admin);
        if ($users_to_notify->count()) {
            Notification::send($users_to_notify, new DayOffUpdateStatusNotification($this, Auth::user()));
        }
    }

    public static function getUserDaysOff($idUser)
    {
        $daysOff = DayOff::whereDeleted(0);
        $daysOff->where('users_id', $idUser);
        $daysOff->latest();
        return $daysOff;
    }

    public static function requestDaysOff($input, $files)
    {
        $input['start_date'] = to_date($input['start_date']);
        if ($input['start_date_is_morning'] == 0) {
            $input['start_date'] =  $input['start_date'] . " 12:00:00"; // Start in apres midi
        }
        $input['return_date'] = to_date($input['return_date']);
        if ($input['return_date_is_morning'] == 0) {
            $input['return_date'] =  $input['return_date'] . " 12:00:00"; // return in apres midi
        }
        $input['result'] = "in_progress";
        $validate_immediately = isset($input['validate_immediately']) &&  $input['validate_immediately'] == "true" &&  auth()->user()->isRhOrAdmin();
        if ($validate_immediately  ) {
            $input['result'] = "validated";
            $input['result_date'] =  Carbon::now()->format("Y-m-d");
            unset($input['validate_immediately']);
        }
        unset($input['files']);
        $dayOff = DayOff::updateOrCreate(["id" => $input['id']], $input);

        if ($validate_immediately && $dayOff->type->impact_in_dayoff_balance ) {
            /** Yeaaah the code is utra dynamic  */
            $the_type_impacted = "";
            if ($dayOff->type->type == "daysoff") {
                $the_type_impacted = "nb_days_off_remaining";
            }elseif($dayOff->type->type == "permission"){
                $the_type_impacted = "nb_permissions";
            }
            $applicant = $dayOff->applicant;
            $applicant->$the_type_impacted -= $dayOff->duration;
            $applicant->save();
        }
        if ($files != null) {
            foreach ($files as $file) {
                //Save the file to the server
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads', $fileName, 'public');
                //write the filepath to the database
                DaysOffAttachment::create([
                    'days_off_id' => $dayOff->id,
                    'url' => 'app/public/' . $filePath,
                    'filename' => $file->getClientOriginalName()
                ]);
            }
        }
        $dayOff->load(["applicant.userJob"]);
        $ids_to_notify = $cp_ids =  [];
        try {
            $department_id = $dayOff->applicant->userJob->department_id;
            $cp_ids = UserJobView::where("department_id", $department_id)->where("is_cp", 1)->get()->pluck("users_id")->toArray();
        } catch (\Throwable $th) {
        }
        /** Inform all cp in departement */
        if ($cp_ids) {
            $ids_to_notify = array_merge($ids_to_notify ,$cp_ids);
        }
        /** Inform validateur of dayoff where user in group*/
        $users_validator_ids = User::getListValidatorUserDayoff($dayOff->applicant_id);
        if ( $users_validator_ids ) {
            $ids_to_notify = array_merge($ids_to_notify ,$users_validator_ids);
        }
        /** List user to inform */
        $users_admin = get_cache_rh_admin();
        
        $users_to_inform = User::whereDeleted(0)->whereIn("id",array_unique($ids_to_notify))->get();

        /** All user to inform */
        $users_to_notify = $users_to_inform->merge($users_admin);
        /** Send the notification */
        dispatch(function ()use($input ,$dayOff,$users_to_notify){
            if ($input['id']) {
                Notification::send($users_to_notify, new DayOffCreatedNotification($dayOff, Auth::user(), true));
            }
            Notification::send($users_to_notify, new DayOffCreatedNotification($dayOff, $dayOff->applicant));
        })->afterResponse();
        return $dayOff;
    }

    public static function requestDaysOffForAnEmployee($author, $input, $files)
    {
        $input['author_id'] = $author;
        $input['start_date'] = to_date($input['start_date']);
        $input['return_date'] = to_date($input['return_date']);
        $dayOff = DayOff::create($input);
        if ($files != null) {
            foreach ($files as $file) {
                //Save the file to the server
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads', $fileName, 'public');

                //write the filepath to the database
                DaysOffAttachment::create([
                    'days_off_id' => $dayOff->id,
                    'url' => 'app/public/' . $filePath,
                    'filename' => $file->getClientOriginalName()
                ]);
            }
        }
    }

    public static function countNbDaysOff($idUser, $date = null)
    {
        // $date = toCarbon($date);
        $daysOff = DayOff::whereDeleted(0)
            ->where('users_id', $idUser)
            ->whereRaw('MONTH(start_date) = ? AND YEAR(start_date)', [$date->month, $date->year])->get();

        $count = 0;
        foreach ($daysOff as $dayOff) {
            $count += $dayOff->duration;
        }
    }

    //Create Filter
    public static function filterMyDaysOff()
    {
        $filters =  $users = []; $auth = Auth()->user(); 
        $users[] = ["value" => $auth->id, "text" => "Le mien"  ];
          $can_validate_dayoff = User::getListOfUsersCanValidateDayOff($auth->id);
          if ($can_validate_dayoff) {
            foreach (User::findMany($can_validate_dayoff) as $u) {
               $users[] = ["value" => $u->id, "text" => $u->sortname  ];
            }
          }
          if ($auth->isCp() ||  $auth->isM2p()) {
            $usrs_same_dprtmt = Department::getUserByIdDepartement( Auth()->user()->userJob->department_id );
            foreach ($usrs_same_dprtmt as $user) {
                if ($user->deleted == 0 &&  !in_array($user->id,$can_validate_dayoff)) {
                    $users[] = ["value" => $user->id, "text" => $user->sortname  ];
                }
            }
         }

         $filters[] = [
            "label" => "Congé de",
            "name" => "user_id",
            "type" => "select",
            'attributes' => [
                "data-hide-search" => "false",
                "data-allow-clear" => "true",
            ],
            'options' => $users,
        ];

        // $filters[] = [
        //     "label" => "Nature",
        //     "name" => "nature_id",
        //     "type" => "select",
        //     "options" => to_dropdown( DayoffNatureColor::whereDeleted(0)->whereStatus(1)->latest()->get(),"id" , "nature")
        // ];
        $filters[] = [
            "label" => "Statut",
            "name" => "result",
            "type" => "select",
            "options" => [
                [
                    "text" => 'Validée',
                    "value" => 'validated',
                ],
                [
                    "text" => 'Refusée',
                    "value" => 'refused',
                ],
                [
                    "text" => 'En cours',
                    "value" => 'in_progress',
                ]
            ]
        ];

        $filters[] = [
            "label" => "Etat",
            "name" => "status_dayoff",
            "type" => "select",
            'width' => 'w-150px',
            "options" => [
                ["text" => 'En congé', "value" => 'in_progress'],
                ["text" => 'Terminé', "value" => 'finish'],
                ["text" => "Annulé", "value" => "is_canceled"]
            ]
        ];
        $filters[] = [
            "label" => "Absent au date ...",
            "name" => "absence_date",
            "type" => "date",
            'attributes' => [
                'placeholder' => 'Absent au date ...',
            ]
        ];
       
        return $filters;
    }


    public static function createFilter()
    {
        $filters = [];
        $filters[] = [
            "label" => "Congé de ",
            "name" => "user_id",
            "type" => "select",
            'attributes' => [
                'width' => 'w-250px',
                "data-ajax--url" => url("/search/user"),
                "data-ajax--cache" => true,
                "data-minimum-input-length" => "3",
                "data-allow-clear" => "true",
            ],
            'options' =>  [
                ["value" =>  0, "text" => trans("lang.me")]
            ],
        ];

        // $jobs = Job::whereDeleted(0)->get();
        // $filters[] = [
        //     "label" => "Poste",
        //     "name" => "job_id",
        //     "type" => "select",
        //     "options" => to_dropdown($jobs, "id", "name")
        // ];
        $projects = ProjectGroup::whereDeleted(0)->get();

        $filters[] = [
            "label" => "Projet",
            "name" => "project_id",
            "type" => "select",
            "options" => to_dropdown($projects, "id", "name")
        ];
        $filters[] = [
            // "label" => "Statut",
            "label" => "Validat*",
            "name" => "result",
            "type" => "select",
            'width' => 'w-150px',
            "options" => [
                [
                    "text" => 'Validée',
                    "value" => 'validated',
                ],
                [
                    "text" => 'Refusée',
                    "value" => 'refused',
                ],
                [
                    "text" => 'En cours',
                    "value" => 'in_progress',
                ]
            ]
        ];
        $filters[] = [
            "label" => "Etat",
            "name" => "status_dayoff",
            "type" => "select",
            'width' => 'w-150px',
            "options" => [
                ["text" => 'En congé', "value" => 'in_progress'],
                ["text" => 'Terminé', "value" => 'finish'],
                ["text" => "Annulé", "value" => "is_canceled"]
            ]
        ];
        $filters[] = [
            "label" => "Absent au date ...",
            "name" => "absence_date",
            "type" => "date",
            'attributes' => [
                'placeholder' => 'Absent au date ...',
            ]
        ];
        
        $filters[] = [
            "label" => "Date demande",
            "name" => "created_at",
            "type" => "date-range",
            'attributes' => [
                'placeholder' => 'Date demande entre ...',
                'width' => 'w-200px'
            ]
        ];
        return $filters;
    }
}
