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

    public function getStartDate()
    {
        return Carbon::make($this->start_date);
    }

    public function getApplicantJob()
    {
        try {
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
        return $duration;
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
        $daysOff = DayOff::with(['applicant.userjob.job', 'author',"nature"]);
        $is_empty_filter = true;
        $myDaysOff = get_array_value($options, 'myDaysOff'); // the auth daysoff or  daysoff in his departemt
        $user = Auth::user();
        if ($myDaysOff) {
            $user->load('userJob');
            if ($user->userJob) {
                if ($user->userJob->is_cp) {
                    $users_ID = UserJobView::where("department_id", $user->userJob->department_id)->get()->pluck("users_id");
                    $daysOff->whereIn("applicant_id", $users_ID);
                } else {
                    $daysOff->where("applicant_id",  $user->id);
                }
            }
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
        $status_dayoff = get_array_value($options, 'status_dayoff'); //finish,in_progress
        if ($status_dayoff) {
            $is_empty_filter = false;
            if ($status_dayoff == "finish") {
                $daysOff->whereDate('return_date', '<=', Carbon::now()->format("Y-m-d"))
                    ->where('result', 'validated')
                    ->where("is_canceled", 0);
            } else if ($status_dayoff == "in_progress") {
                $daysOff->whereDate('start_date', '<=', Carbon::now()->format("Y-m-d"))
                    ->whereDate('return_date', '>', Carbon::now()->format("Y-m-d"))
                    ->where('result', 'validated')
                    ->where("is_canceled", 0);
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
        // retrieve last record of the day for the last 7 days
        if ($is_empty_filter) {
            $daysOff->where('return_date', '>=', now()->subDays(7));
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

        // if (Auth::user()->not_rh()) {
        //     $daysOff->where('applicant_id', Auth::user()->id);
        // }
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
        $dayOff = DayOff::find($id);
        $dayOff->is_canceled = true;
        $dayOff->save();

        $dayOff->load("applicant");
        $dayOff->load("applicant.userjob");
        
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

        $dayOff = DayOff::find($id);
        //Check if the request is already accepted or declined, if yes we get out of the function
        if ($dayOff->result != "in_progress") {
            return $dayOff;
        }
        $data['start_date'] = to_date($data['start_date']);
        $data['return_date'] = to_date($data['return_date']);
        if ($data['result'] != 'in_progress')
            $data['result_date'] = Carbon::now();
        $dayOff->update($data);

        $dayOffType = $dayOff->type;
        if ($dayOffType->impact_in_dayoff_balance && $dayOff->result == "validated") {
            $applicant = $dayOff->applicant;
            $applicant->nb_days_off_remaining -= $dayOff->duration;
            $applicant->save();
        }

        $dayOff->sendNotificationOnUpdate();
        return $dayOff;
    }

    public function sendNotificationOnUpdate()
    {
        if ($this->author_id != $this->applicant_id) {
            $notify_to = User::findMany([$this->applicant_id, $this->author_id]);
        } else {
            $notify_to = User::where("id", $this->applicant_id)->whereDeleted(0)->get();
        }
        /** get CP on user departement */
        $applicant = User::find($this->applicant_id);
        $applicant->load("userJob");
        $department_id = $applicant->userJob->department_id;
        $cp_ids =  UserJobView::where("department_id", $department_id)->where("is_cp", 1)->get()->pluck("users_id");
        if ($cp_ids) {
            $cp = User::findMany($cp_ids);
            $notify_to = $notify_to->merge($cp);
        }
        if ($notify_to->count()) {
            Notification::send($notify_to, new DayOffUpdateStatusNotification($this, Auth::user()));
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
        $input['return_date'] = to_date($input['return_date']);
        $input['result'] = "in_progress";
        $validate_immediately = isset($input['validate_immediately']) &&  $input['validate_immediately'] == "true" &&  auth()->user()->isRhOrAdmin();
        if ($validate_immediately  ) {
            $input['result'] = "validated";
            $input['result_date'] =  Carbon::now()->format("Y-m-d");
            unset($input['validate_immediately']);
        }
        unset($input['files']);
        $dayOff = DayOff::updateOrCreate(["id" => $input['id']], $input);
        if ($validate_immediately && $dayOff->type->impact_in_dayoff_balance) {
            $applicant = $dayOff->applicant;
            $applicant->nb_days_off_remaining -= $dayOff->duration;
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

        $applicant = User::find($dayOff->applicant_id);
        $applicant->load("userJob");
        $department_id = $applicant->userJob->department_id;
        $cp_ids =  UserJobView::where("department_id", $department_id)->where("is_cp", 1)->get()->pluck("users_id");
        $notify_to = get_cache_rh_admin();
        if ($cp_ids) {
            $cp = User::findMany($cp_ids);
            $notify_to = $notify_to->merge($cp);
        }
        dispatch(function ()use($input ,$dayOff,$notify_to){
            if ($input['id']) {
                Notification::send($notify_to, new DayOffCreatedNotification($dayOff, $dayOff->applicant, true));
            }
            Notification::send($notify_to, new DayOffCreatedNotification($dayOff, $dayOff->applicant));
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
        $filters = [];
          if (Auth()->user()->isCp()) {
            $same_department_user = [];
            $users =Department::getUserByIdDepartement(Auth()->user()->userJob->department_id);
            foreach ($users as $user) {
                if ($user->deleted == 0) {
                    $same_department_user[] = ["value" => $user->id, "text" => $user->sortname ." - ". $user->registration_number ];
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
                'options' => $same_department_user,
            ];
         }
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

        $jobs = Job::whereDeleted(0)->get();
        $filters[] = [
            "label" => "Poste",
            "name" => "job_id",
            "type" => "select",
            "options" => to_dropdown($jobs, "id", "name")
        ];
        $projects = ProjectGroup::whereDeleted(0)->get();

        $filters[] = [
            "label" => "Projet",
            "name" => "project_id",
            "type" => "select",
            "options" => to_dropdown($projects, "id", "name")
        ];
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
            "label" => "Date demande",
            "name" => "created_at",
            "type" => "date-range",
            'attributes' => [
                'placeholder' => 'Date demande entre ...',
                'width' => 'w-200px'
            ]
        ];
        $filters[] = [
            "label" => "Etat",
            "name" => "status_dayoff",
            "type" => "select",
            "options" => [
                ["text" => 'En congé', "value" => 'in_progress'],
                ["text" => 'Terminé', "value" => 'finish'],
                ["text" => "Annulé", "value" => "is_canceled"]
            ]
        ];

        return $filters;
    }
}
