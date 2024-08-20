<?php

namespace App\Models;

use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\DayOff;
use App\Models\SuiviPauseProd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  HasFactory, Notifiable;
    // protected $guarded = [
    //     'kids_fullname',
    //     'kids_birthdate_first'
    // ];
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [

        'registration_number',
        'name',
        'firstname',
        'sex',
        'birthdate',
        'place_of_birth',
        'cin',
        'cin_delivered',
        'marital_status_id',
        'address',
        'phone_number',
        'email',
        'father_fullname',
        'mother_fullname',
        'qualification',
        'hiring_date',
        'regulation',
        'account_number',
        'password',
        'avatar',
        'user_type_id',
        'email_verified_at',
        'nb_days_off_remaining',
        'nb_permissions',
        'marry_fullname',
        'marry_birthdate',
        'marry_place_of_birth',
        'marry_CIN',
        'marry_cin_delivered',
        'marry_phone_number',
        'marry_email',
        'marry_job',
        // 'verbal_warning',
        // 'written_warning',
        // 'layoff',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $appends = [
        'avatarUrl',
        'fullName',
        'sortName',
        'logo'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $with = ['userJob'];
    protected $idAdmin = 1;
    public static $ID_M2P = 18;
    public static $ID_DESSI = 3;

    public function isAdmin()
    {
        return $this->user_type_id == UserType::$_ADMIN;
    }

    public function isHR()
    {
        return $this->user_type_id == UserType::$_HR;
    }

    public function isTech() {
        return $this->user_type_id == UserType::$_TECH;
    }
   
    public function isCp() {
        try {
            return $this->userJob->is_cp == 1;
        }
        catch (Exception $e) {
            return false;
        }
    }
    public static function isRhOrAdmin($user = null){
        
        $user = $user instanceof User  ?  $user  : (( $user && is_numeric($user) ) ?  User::find($user) :  Auth::user());
        return ($user->isHR() || $user->isAdmin());
    }
    
    public function isIT()
    {
        try {
            return $this->userJob->department_id == Department::$_IT;
        }
        catch (Exception $e) {
            return false;
        }
    }
    public function isCompta() {
        try {
            return $this->userJob->department_id == Department::$_COMPTA;
        }
        catch (Exception $e) {
            return false;
        }
    }
    public function isAContributor() {
        return $this->user_type_id == UserType::$_CONTRIBUTOR;
    }

    public static function isM2pOrAdmin($user = null){
        $user = $user instanceof User  ?  $user  : (( $user && is_numeric($user) ) ?  User::find($user) :  Auth::user());
        return ($user->isM2p() || $user->isAdmin());
    }
    public function isADessignator() {
        try {
            return  Auth::user()->userJob->jobs_id == self::$ID_DESSI;
        }
        catch (Exception $e) {
            return false;
        }
    }
    public function isM2p() {
        
        try {
            return  Auth::user()->userJob->jobs_id == self::$ID_M2P;
        }
        catch (Exception $e) {
            return false;
        }
    }
    public function kids()
    {
        return $this->hasMany(Kid::class)->whereDeleted(0);
    }

    public function type()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, "jobs_id");
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class, "marital_status_id");
    }

    public function employeeDailyActivity()
    {
        return $this->hasMany(EmployeeDailyActivity::class, 'users_id');
    }

    public function employeeTotalMinutesPerformed()
    {
        return $this->hasMany(EmployeeTotalMinutesPerformed::class, "users_id");
    }

    public function userJob()
    {
        return $this->hasOne(UserJobView::class, "users_id");
    }

    public function check()
    {
        return $this->hasMany(Check::class, "user_id")->whereDate('date_time', Carbon::today());
    }
    public function suiviItems()
    {
        return $this->hasMany(SuiviItem::class, "user_id")->where("suivi_items.deleted" ,"=" , 0);
    }
    public function pauseProduction()
    {
        return $this->hasMany(SuiviPauseProd::class, "user_id");
    }
    /** seuil point and day work */
    public function suiviPramsSession()
    {
        return $this->hasOne(SuiviUserParams::class, "user_id");
    }
    public function sectionsTask()
    {
        return $this->belongsToMany(TaskSection::class,"task_sections_members","user_id","section_id");
    }
    public function getSex()
    {
        return ($this->sex == "1") ? "Homme" : "Femme";
    }
    public function customFilter()
    {
        return $this->hasMany(CustomerFilter::class, "creator")->whereDeleted(0)->whereNotNull("filters")->latest();
    }

    public function projectsGroup()
    {
       return $this->belongsToMany(User::class,"project_group-members","user_id","project_id");
    }
    public function dayoffValidatorInGroup()
    {
       return $this->belongsToMany(User::class,"project_group-dayoff_validator","user_id","project_id");
    }
    public function sanctions() {
        return $this->hasMany(Sanction::class);
    }
    public function dayOffs() {
        return $this->hasMany(DayOff::class,"applicant_id");
    }
    public function messageSeen() {
        return $this->hasMany(MessageSeen::class);
    }

    public function pointingTemp() {
        return $this->hasOne(PointingTemp::class, "user_id");
    }
    public function getAvatarUrlAttribute()
    {
        // return $this->getAvatarFormat(100);        
        return $this->getAvatarFormat();        
    }

    public function getAvatarFormat($format = "") {
        $format = $format ? $format . "-" : "";
        if ($this->avatar) {
            return asset("avatar/$format$this->avatar");
        }else{
            return asset(theme()->getMediaUrlPath() . 'avatars/blank.png');
        }
    }
    public function avatarFormat($format)
    {
        if ($format) {
            return asset("avatar/$this->avatar");
        }
        return asset("avatar/$format/$this->avatar");
    }
    public function getHiringDate()
    {
        return Carbon::make($this->hiring_date);
    }
    public function get_tickets_not_resoved()
    {
        return Ticket::whereRaw('FIND_IN_SET(' . auth()->id() . ', assign_to)')->whereNotIn("status_id" ,TicketStatus::$_RESOLVED )->count();
    }

    public function getNameAndRegistrationNumber() {
        return $this->registration_number . " - " . $this->sortname;
    }
    public function updateLeaveBalances($nbDays)
    {
        $lastMonth = Carbon::today()->subMonth();
        $hiringDate = $this->getHiringDate();
        $daysInMonth = $lastMonth->daysInMonth;
        $nbDaysWorked = $daysInMonth;
        if ($hiringDate->gt($lastMonth)) {
            $nbDaysWorked = $hiringDate->diffInDays(Carbon::make("$lastMonth->year-$lastMonth->month-$nbDaysWorked"));
        }
        $this->nb_days_off_remaining += ($nbDaysWorked * $nbDays) / $daysInMonth;
    }

    public static function createNewUser($input)
    {
        try {
            DB::beginTransaction();
            User::checkIfEmailAlreadyUsed($input['email']);
            if ($input['birthdate']|| $input['cin_delivered'] || $input['hiring_date']) {
                $input['birthdate'] = to_date($input['birthdate']);
                $input['hiring_date'] = to_date($input['hiring_date']);
            }
            if($input['cin_delivered']) {
                try {
                    $input['cin_delivered'] = to_date($input['cin_delivered']);
                }
                catch (Exception $e) {}
            }
            if($input['marry_cin_delivered']) $input['marry_cin_delivered'] = to_date($input['marry_cin_delivered']);
            if($input['marry_birthdate']) $input['marry_birthdate'] = to_date($input['marry_birthdate']);

            User::checkHiringDate($input['hiring_date']);
            User::checkBirthDate($input['birthdate']);
            $input['password'] = Hash::make("123456");
            $user = User::create($input);
            $input['users_id'] = $user->id;
            $input['date_user_job'] = $input['hiring_date'];
            UserJob::create($input);

            if(!check_null_array($input['kids_fullname']) || !check_null_array($input['kids_birthdate_first'])){
                $i = 0;
                foreach($input['kids_birthdate_first'] as $kids){
                    Kid::create(['fullname'=> $input['kids_fullname'][$i], 'birthdate'=>to_date($kids),'user_id'=>$input['users_id']]);
                    $i++;
                }
            }
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function checkIfEmailAlreadyUsed($email)
    {
        $count = User::where('email', $email)->count();
        if ($count != 0)    throw new Exception('Ce mail est déjà utilisé par un autre utilisateur');
    }

    public static function checkHiringDate($hiring_date)
    {
        $today = Carbon::now();
        if ($today->lt(Carbon::make($hiring_date)))  throw new Exception("La date d'embauche doit être inferieure à la date d'aujourd'hui");
    }

    public static function checkBirthDate($birthDate)
    {
        $today = Carbon::now();
        if ($today->lt(Carbon::make($birthDate)))  throw new Exception("La date d'embauche doit être inferieure à la date d'aujourd'hui");
    }

    public function getActualJobAttribute()
    {
        try {
            return $this->userJob->job->name;
        }
        catch(Exception $e) {
            return null;
        }
    }

    public static function getUsersWithDailyActivity($year, $month, $page, $nbUserToDisplay)
    {
        return User::with(['userJob.job', 'employeeDailyActivity' => function($query) use ($month, $year){
                $query->whereRaw("MONTH(day) = ? AND YEAR(day) = ?", [$month, $year]);
            }, 'employeeTotalMinutesPerformed' => function($query) use ($month, $year) {
                $query->where("month", $month);
                $query->where("year", $year);
            }])->whereDeleted(0)->where('user_type_id', "<>", UserType::$_ADMIN)->orderBy("registration_number")->skip($page * $nbUserToDisplay)->take($nbUserToDisplay)->get();
    }

    public function convertTotalMinutesPerformedToArray()
    {
        $employeeTotalMinutesPerformed = $this->employeeTotalMinutesPerformed;
        $totalMinutesPerformed = [];
        foreach($employeeTotalMinutesPerformed as $totalMinutesDailyActivity) {
            $totalMinutesPerformed[$totalMinutesDailyActivity->daily_activity_id] = $totalMinutesDailyActivity->minute_performed;
        }
        return $totalMinutesPerformed;
    }

    public function convertMinutePerformedToArray()
    {
        $employeeDailyActivity = $this->employeeDailyActivity;
        $minutePerformed = [];
        foreach($employeeDailyActivity as $activity) {
            $minutePerformed[$activity->day][$activity->daily_activity_id] = $activity->minute_performed;
        }
        return $minutePerformed;
    }


    public function getFullnameAttribute()
    {
        return $this->name . " " . "" .$this->firstname;
    }
   
    public function getSortnameAttribute()
    {
        return ($this->firstname ?? $this->name) ;
    }

    public static function getNewRegistrationNumber()
    {
        return (int) User::selectRaw('(MAX(registration_number) + 1) as registration_number')->first()->registration_number;
    }


    public function changePassword($actualPassword, $newPassword)
    {
        if (!Hash::check($actualPassword, auth()->user()->password)) {
            throw new Exception("Le mot de passe actuel est incorrect");
        }
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    public function getSeniorityAttribute()
    {
        $now = Carbon::today();
        $hiring_date = Carbon::make($this->hiring_date);
        return $now->diffInDays($hiring_date);
    }

    public function getLogoAttribute()
    {
        $file = 'logos/logo2.png';
       
        return  asset(theme()->getMediaUrlPath() . 'logos/logo1.png') ; // Aftert thirtyOne is in local with desineo
        if ($this->userJob) {
            $file = $file = 'logos/logo' . $this->userJob->local . '.png';
        }
        return asset(theme()->getMediaUrlPath() . $file);
    }

    public function scopeListDropdown($query , $options = [])
    {
        $users = User::whereDelete(0);
        $user_id = get_array_value($options , "user_id");
        if ($user_id) {
            if (is_array($user_id)) {
                $users->whereIn("id" ,$user_id );
            }else{
                $users->where("id" ,$user_id );
            }
        }
        $job_id = get_array_value($options , "job_id");
        if ($job_id) {
            $users->whereHas("userJob" , function ($query) {
               
            });
        }
    }

    public static function get_cache_total_permission($user_id){
        $sum = get_cache_total_permission($user_id);
        $rest =   intval($sum[0]->total);
        return  $rest ;  
    }
    public static function get_rest_permission($user){

        $user = $user instanceof User ?  $user  :  User::find($user);
        if ($user && $user->nb_permissions) {
            return $user->nb_permissions;
        }
        $rest =  DayOff::$_max_permission_on_year - self::get_cache_total_permission($user->id);
        return  $rest;  
    }

    public function getCumulativeHour() {
        $value = 0;
        $sign = "";
        $class = "fs-2 fw-bolder";
        if (!$this->isAdmin()) {
            $value = PointingResume::getCumulativeHours($this)->first()->time_worked;
        }
        if ($value < 0) {
            $class .= " text-danger";
            $sign = "- ";
            $value = abs($value);  
        }
        $value = $sign . convertMinuteToHour($value);
        return "<div class='$class'>$value</div>";
    }
    /** Get list of all user can a user validate or see */
    public static function getListOfUsersCanValidateDayOff($user_id = 0) {
        $groups_can_validate_dayoff = DB::table("project_group-dayoff_validator")->where("user_id",$user_id)->get(["user_id","project_id"])->pluck("project_id")->toArray();
        if ($groups_can_validate_dayoff) {
            return DB::table("project_group-members")->whereIn("project_id" ,$groups_can_validate_dayoff)->get(["user_id","project_id"])->pluck("user_id")->toArray();
        }
        return [];
    }
    /** Get list of validator    of a user */
    public static function getListValidatorUserDayoff($user_id = 0) {
        $groups_can_validate_dayoff = DB::table("project_group-dayoff_validator")->where("user_id",$user_id)->get(["user_id","project_id"])->pluck("project_id")->toArray();
        if ($groups_can_validate_dayoff) {
            return DB::table("project_group-members")->whereIn("project_id" ,$groups_can_validate_dayoff)->get(["user_id","project_id"])->pluck("user_id")->toArray();
        }
        return [];
    }
    
}
