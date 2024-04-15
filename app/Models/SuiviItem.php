<?php

namespace App\Models;

use Auth;
use App\Models\Suivi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\NewSuiviItemNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuiviItem extends Model
{
    use HasFactory;
    protected $table = "suivi_items";
    public  static $NEW = 1;
    public  static $IN_PROGRESS = 2;
    public  static $PAUSE = 3;
    public  static $FINISH = 4;
    public  static $HOUR_DAYS_WORK = "8.00"; // 08:00:00 /day;

    protected $fillable = [
        "suivi_id", // the folder  (id)
        "status_id",
        "user_id", // the dessi or urba (id)
        "version_id",
        "montage",
        "last_check",
        "duration",
        "level_id",
        "times_estimated",
        "poles",
        "creator_id",
        "follower", // the MDP (id)
        "disabled",
        "deleted",
        "created_at",
        "finished_at",
        "updated_at",
    ];
    protected $casts = [
        'created_at'  => 'date:d-m-Y',
        'finished_at'  => 'date:d-m-Y',
    ];
    protected $touches = ['suivi'];
    protected $appends = ['secondes',"realPointItem","pointOtherVersion","typesName"];
    protected $with = [
        'noteQuality:id,suivi_item_id,note,deleted',
        "suivi:id,ref,folder_name,deleted",
        "user:id,name,firstname,deleted,user_type_id" ,
        "mdp:id,name,firstname,deleted,user_type_id"];
    public  static $MONTAGE = [
        ["value" => 1, "text" =>  "Montage 1 ", "color" => "#009EF7"],
        ["value" => 2, "text" => "Montage 2", "color" => "#FFC700"],
        ["value" => 3, "text" =>  "Montage 3 ", "color" => "#50CD89"]
    ];
    public  static $STATUS = [
        ["value" => 1, "text" =>  "A Faire", "group" =>  "Nouveau", "verbe" =>   "Nouveau", "class" => "info", "color" => "#7239EA"],
        ["value" => 2, "text" =>  "En cours", "group" =>  "En cours", "verbe" =>  "Continuer", "class" => "primary", "color" => "#009EF7"],
        ["value" => 3, "text" => "Pause","group" =>  "Pauses", "verbe" =>  "Pauser", "class" => "warning", "color" => "#FFC700"],
        ["value" => 4, "text" =>  "Terminé", "group" =>  "Terminés","verbe" =>  "Terminer", "class" => "success", "color" => "#50CD89"],
    ];
    public  static $POLES = [
        ["value" => "urba", "text" =>  "Urbaniste", "color" => "#009EF7"],
        ["value" => "cq", "text" => "CQ", "color" => "#FFC700"],
        ["value" => "dessi", "text" => "Dessinateur", "color" => "#50CD89"],
        ["value" => "m2p", "text" => "M2P", "color" => "#7239EA"],
    ];
    public  static $TYPES_FOLDER = [
        ["value" => "B2B", "text" =>  "B2B"],
        ["value" => "B2C", "text" => "B2C"],
        ["value" => "B2B2C", "text" => "B2B2C"],
        ["value" => "PRO", "text" => "PRO"],

    ];
    public  static $TIME_ESTIMATIF = [
        ["value" => "1", "text" =>  "1"],
        ["value" => "2", "text" => "2"],
        ["value" => "3", "text" => "3"],
    ];
    public  static $LEVELS = [
        ["value" => "1", "text" =>  "1"],
        ["value" => "2", "text" => "2"],
        ["value" => "3", "text" => "3"],
        ["value" => "4", "text" => "4"],
        ["value" => "6", "text" => "6"],
    ];
    
   
    public function suivi()
    {
        return $this->belongsTo(Suivi::class, "suivi_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id")->withOut(['userJob']);
    }
    public function mdp()
    {
        return $this->belongsTo(User::class, "follower")->withOut(['userJob']);
    }
    public function version()
    {
        return $this->belongsTo(SuiviVersion::class, "version_id")->whereDeleted(0);
    }
    public function level()
    {
        return $this->belongsTo(SuiviVersionLevelPoint::class, "level_id");
    }
    public function noteQuality()
    {
        return $this->hasMany(SuiviItemNote::class, "suivi_item_id")->whereDeleted(0);
    }
    // public function sumNoteQuality()
    // {
    //        return $this->hasMany(SuiviItemNote::class, "suivi_item_id")->whereDeleted(0)->sum('note');
    // }
    public static function getPole()
    {
        return self::$POLES;
    }
    public static function getStatus()
    {
        return collect(self::$STATUS)->sortBy("value");
    }
    public static function getMontage()
    {
        return self::$MONTAGE;
    }
    public static function getTypeFolder()
    {
        return self::$TYPES_FOLDER;
    }
    public static function getClientType()
    {
        return SuiviTypeClient::select(["id","name","status","deleted"])->whereStatus("on")->whereDeleted(0)->get();
    }
    public static function getTimeEstimatif()
    {
        return self::$TIME_ESTIMATIF;
    }
    public static function getLevels()
    {
        return self::$LEVELS;
    }
    public  function in_progress()
    {
        return $this->status_id == self::$IN_PROGRESS;
    }
    public  function is_paused()
    {
        return $this->status_id == self::$PAUSE;
    }
    public  function is_finish()
    {
        return $this->status_id == self::$FINISH;
    }
    public static function getNumberDayInMonth($month, $year)
    {
        return (Carbon::make("01-$month-$year")->daysInMonth);
    }
    public function getSecondesAttribute()
    {
        /*** On create item */
        if ((!isset($this->id) && !$this->id) || !isset($this->created_at)  ) {
            return 0;
        }
        $secondes = ($this->duration ?? 0);
        if ($this->in_progress()  ) {
            $start  = Carbon::parse(($this->last_check ? $this->last_check :  $this->created_at ));
            $end = now();
            $secondes = $secondes + $end->diffInSeconds($start);
        }
        return $secondes;
    }
    public function getTypesNameAttribute()
    {
        if ($this->suivi) {
            if ($this->suivi->points) {
                return $this->suivi->points->pluck("project_type.name")->implode(",") ;
            }
        }
        return "";
    }
    public function getPointOtherVersionAttribute()
    {   
        $suivi_totalPointBase  = $this->suivi->totalPointBase;
        if ($this->poles == "urba") {
            $point = 2.00; 
           return round($point +  Suivi::$POINT_ADDITIONAL_URBA, 3) ;
        }
        
        if ($this->montage == 1 && isset($this->version->montage_1_point ) ) {
            if ($this->version->montage_1_point->point) {
               return $this->version->montage_1_point->point;
            }else{
                return  ($suivi_totalPointBase * $this->version->montage_1_point->percentage) / 100;
            }
        }
        if ($this->montage == 2 && isset($this->version->montage_2_point ) ) {
            if ($this->version->montage_2_point->point) {
               return $this->version->montage_2_point->point;
            }else{
                return  ($suivi_totalPointBase * $this->version->montage_2_point->percentage) / 100;
            }
        }
        if ($this->montage == 3 && isset($this->version->montage_2_point)) {
            if ($this->version->montage_3_point->point) {
               return $this->version->montage_3_point->point;
            }else{
                return  ($suivi_totalPointBase * $this->version->montage_3_point->percentage) / 100;
            }
        }
        if (isset($this->version->point)) {
            if ($this->version->point) {
                return $this->version->point;
            }
            if ($this->version->base_calcul) {
                $pointForThisVersion =  ($suivi_totalPointBase * $this->version->percentage) / 100;
                return  round($pointForThisVersion, 3) ;
            }
        }
        
        return $suivi_totalPointBase;
    }
    // V1 point
    public function getRealPointItemAttribute()
    {
        if (!$this->id) {
            return "0.00";
        }
        return $this->pointOtherVersion  ? $this->pointOtherVersion : $this->suivi->totalPointBase;
    }
    /** Defind acces  */
    public function can_delete_row($auth_user = null)
    {
        $auth = $auth_user ? $auth_user : Auth::user();
        /** Admin */
        if($auth->isAdmin()){
            return true;
        }
        /** CP */
        if($auth->isCp() || $auth->isM2p() ){
            return true;
        }
         /** Creator of row on not yet finished folder*/
        if($auth->id == $this->user_id && isset($this->id) && !$this->is_finish() ){
            return true;
        }
        return false;
    }
    public function can_update_row($auth_user = null)
    {
        if (!$this->id) {
            return true;
        }
        $auth = $auth_user ? $auth_user : Auth::user();
        if($auth->isAdmin()){
            return true;
        }
        /** Creator of row */
        if($auth->isCp() || $auth->isM2p()){
            return true;
        }
        if($auth->id == $this->user_id && isset($this->id) && !$this->is_finish()  ){
            return true;
        }
        return false;
    }

    public static function getMdp($to_dropdown = true)
    {
        $data = [];
        $mdp =  UserJobView::with(["user" => function($user){
            $user->withOut(["userJob"])->whereDeleted(0);
        }])->whereDeleted(0)->where("jobs_id", User::$ID_M2P)->get();
        foreach ($mdp as $item) {
            if (isset($item->user) && isset($item->user->id)) {
                $data[] = ["value" => $item->user->id, "text" => $item->user->sortname];
            }
        }
        if ($to_dropdown) {
            return $data;
        }
        return $mdp;
    }
    public static function getUsersListViaJob()
    {
        return  UserJobView::with(["user" => function($user){
            $user->withOut(["userJob"])->whereDeleted(0);
        }])->whereDeleted(0)->get();
    }
    public  function scopeFinished($query)
    {
        return  $query->where("status_id",Self::$FINISH);
    }
    public  function scopeGetDetails($query, $options = [])
    {
        $auth = Auth::user();
        $suivi_item = SuiviItem::whereDeleted(0);
        /*** Basic filters */
        // $suivi_item->with(["version.levelsPoint","level" ,"suivi.points.project_type" ]);

        // $suivi_item->with(["version" ,"version.base_calcul", "version.montage_2_point", "version.montage_3_point"]);
        $suivi_item->with(["version" => function ($q_version) {$q_version->with(["montage_2_point" , "montage_3_point" ,"montage_1_point"]); },"version.base_calcul" ]);
        $version_id  = get_array_value($options, "version_id");
        if ($version_id) {
            $suivi_item->where("version_id", $version_id);
        }
        $status_id  = get_array_value($options, "status_id");
        if ($status_id) {
            $suivi_item->where("status_id", $status_id);
        }
        $project_id = get_array_value($options, 'project_id');
        if ($project_id){
            $members = DB::table("project_group-members")->where("project_id",$project_id)->get()->pluck("user_id")->toArray();
            $suivi_item->whereIn('user_id', $members);
        }
        $user_id  = get_array_value($options, "user_id");
        if ($user_id && !$auth->isADessignator()) {
            $suivi_item->where("user_id", $user_id);
            /** Dessignator folder only */
        } else if (( !$auth->isM2pOrAdmin() && !$auth->isCp() && !$auth->isAdmin()) ) {
            $suivi_item->where("user_id", $auth->id);
        }
        $montage  = get_array_value($options, "montage");
        if ($montage) {
            $suivi_item->where("montage", $montage);
        }
        $suivi_id  = get_array_value($options, "suivi_id");
        if ($suivi_id) {
            $suivi_item->where("suivi_id", $suivi_id);
        }
        $m2p_id  = get_array_value($options, "m2p_id");
        if ($m2p_id) {
            $suivi_item->where("follower", $m2p_id);
        }
        $folder_name  = get_array_value($options, "folder_name");
        if ($folder_name) {
            $suivi_item->whereRelation('suivi', function ($folder) use ($folder_name) {
                $folder->where("folder_name", 'like', '%' . $folder_name . '%');
                $folder->orWhere("ref", 'like', '%' . $folder_name . '%');
            });
        }
        $interval = get_array_value($options, "interval");
        if ($interval) {
            $dates = explode("-", $interval);
            if (count($dates) > 1) {
                $suivi_item->where(function ($q1) use ($dates ,  $status_id)  {
                        if ($status_id && $status_id == self::$FINISH) {
                            $q1->whereBetween("finished_at", [to_date_time($dates[0]), to_date_time($dates[1])]);
                        }else{
                            $q1->whereBetween("created_at", [to_date_time($dates[0]), to_date_time($dates[1])]);
                            $q1->orWhereBetween("finished_at", [to_date_time($dates[0]), to_date_time($dates[1])]);
                        }
                    });
                }
        }
        /** Handle custome filter */
        $custom_filter_id = $interval = get_array_value($options, "custom_filter_id");
        $custom_filter =  $custom_filter_id ? unserialize(CustomerFilter::find($custom_filter_id)->filters) : $options;
        if ($custom_filter) {
            $users = get_array_value($custom_filter, "users");
            if ($users) {
                $suivi_item->whereIn("user_id", $users);
            }
            $suivis_custom = get_array_value($custom_filter, "suivis");
            if ($suivis_custom) {
                $suivi_item->whereIn("suivi_id", $suivis_custom);
            }
            $versions_custom = get_array_value($custom_filter, "versions");
            if ($versions_custom) {
                $suivi_item->whereIn("version_id", $versions_custom);
            }
            $montages_custom = get_array_value($custom_filter, "montages");
            if ($montages_custom) {
                $suivi_item->whereIn("montage", $montages_custom);
            }
            $status_custom = get_array_value($custom_filter, "status");
            if ($status_custom) {
                $suivi_item->whereIn("status_id", $status_custom);
            }
            $poles_custom = get_array_value($custom_filter, "poles");
            if ($poles_custom) {
                $suivi_item->whereIn("poles", $poles_custom);
            }
        }
        /** Get the last 07 suivi item checked when no filters  */
        if (!$user_id && !$montage && !$status_id && !$version_id && !$suivi_id && !$interval && !$custom_filter_id && !$folder_name ) {
            $suivi_item->where(function ($q) {
                $q->whereIn("status_id", [self::$NEW , self::$IN_PROGRESS,self::$PAUSE])->orWhere(function($q2){
                    $q2->whereDate('last_check', '>', now()->subDays(7))->orWhereNull('last_check');
                });
            });
        }
        return $suivi_item->with(["user" =>function($q1){ $q1->withOut("userJob"); }])
                          ->with(["mdp" =>function($q2){ $q2->withOut("userJob"); }])
                          ->orderBy("status_id","asc")
                          ->latest()->latest("updated_at")->latest("last_check");
    }
    public static function  get_table_info()
    {
        $auth = Auth::user();
        $headers = [];
        // $headers[] = [
        //     "data" => "null" ,
        //     "orderable"=> false ,
        //     "defaultContent"=> '<i class="fas fa-angle-right mx-2 " title ="Autre detail" style ="cursor:pointer"></i>',
        //     "className"=>"text-center details-row",
        //     "style" =>"cursor:pointer"
        // ];
        $headers[] = [
            "data" => "detail" ,
            "title" => '',
            "className"=>"text-center w-100"
        ];
        $headers[] = [
            "data" => "clone" ,
            // "title" =>  $auth->isM2pOrAdmin() ? '<i class="fas fa-plus clone-row text-primary fs-3" title="Ajouter un nouvelle ligne" style ="cursor:pointer"></i>'  : "",
            "title" =>  '<i class="fas fa-plus clone-row text-primary fs-3" title="Ajouter un nouvelle ligne" style ="cursor:pointer"></i>' ,
            "className"=>" text-center"
        ];
        $headers[] = [
            "data" => "project_name" ,
            "title"=> '<span title="Non du dossier">DOSSIER</span>',
            "className"=>"text-center w-150 text-gray-800"
        ];
       
            
        $headers[] = [
            "data" => "ref" ,
            "title"=> '<span title="Référence">Ref</span>',
            "className"=>"text-center w-150 text-gray-800"
        ];    
        $headers[] = [
            "data" => "types_client" ,
            "title"=> '<span title="Type de projet">Types client</span>',
            "className"=>"text-center w-100 text-gray-800"
        ];    
        $headers[] = [
            "data" => "types" ,
            "title"=> 'types projet',
            "className"=>"text-center text-gray-800"
        ];    
        // if(!$auth->isADessignator()){
        if($auth->isM2pOrAdmin() || $auth->isCp()){
            $headers[] = [
                "data" => "user" ,
                "title" => 'Assigné(e)',
                "className"=>"text-center text-gray-800"
            ]; 
        }
        $headers[] = [
            "data" => "mdp" ,
            "title"=> 'M2p',
            "className"=>"text-center text-gray-800"
        ];    
        $headers[] = [
            "data" => "version" ,
            "title"=> 'VERSION',
            "className"=>"text-center text-gray-800"
        ];    
        $headers[] = [
            "data" => "montage" ,
            "title"=> '<span title="MONTAGE">Montage</span>',
            "className"=>"text-left text-gray-800"
        ];    
        $headers[] = [
            "data" => "poles" ,
            "title"=> 'Pôle',
            "className"=>"text-center text-gray-800"
        ];    
        $headers[] = [
            "data" => "point" ,
            "title"=> 'Point',
            "className"=>"text-center text-gray-800"
        ];    
        $headers[] = [
            "data" => "duration" ,
            "title"=> 'Durée',
            "className"=>"text-center max-w-100px"
        ];    
        $headers[] = [
            "data" => "status" ,
            "title"=> 'Statut',
            "className"=>"text-center text-gray-800"
        ];    
        $headers[] = [
            "data" => "action" ,
            "title"=> '',
            "className"=>"text-center text-gray-800 min-w-50px"
        ];
        $headers[] = [
            "data" => "extra_action" ,
            "title"=> '',
            "className"=>"text-left"
        ];
        /**All hidden columns */
        $headers[] = [
            "data" => "status_hidden" ,
            "title"=> 'status_',
            "className"=>"text-left text-gray-800"
        ];
        $headers[] = [
            "data" => "duration_hidden" ,
            "title"=> 'Durée_',
            "className"=>"text-left text-gray-800"
        ];
        $hidden_headers = [];
        $row_group = null;
        $total_duration = 0;
        $count_header =count( $headers); 
        for ($i = 0 ;$i < $count_header; $i++ ) {
            $string = get_array_value($headers[$i],"data");
            if(str_contains($string, 'hidden')){
                $hidden_headers[] = $i;
            }
            if($string == 'status_hidden'){
                $row_group = $i;
            }
            if($string == 'duration_hidden'){
                $total_duration = $i;
            }
            if($string == 'duration'){
                $duration_footer = $i;
            }
        }
        return compact("headers","hidden_headers","row_group","count_header","total_duration","duration_footer");
    }

    public static  function scopeRecapPoint($options = [])
    {
        $auth = Auth::user();
        $carbon = Carbon::now();
        $options = [];
        $year = get_array_value($options, "year", $carbon->year);
        $month = get_array_value($options, "month", $carbon->month);

        return User::select("id", "name", "avatar", "firstname", "registration_number")
            ->has("suiviItems")
            ->orderBy("firstname", "ASC")
            ->when(!$auth->isM2pOrAdmin() && !$auth->isCp() && !$auth->isAdmin(), function($user) use ($auth) {
                $user->where("id", "=",$auth->id)->limit(1);
            })
            ->whereDeleted(0)
            // ->withSum(['pauseProduction as sum_pause' => function ($pause) use ($year,  $month) {
            //     $pause->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year);
            // }], 'secondes')
            ->with([
                'suiviItems' => function ($suivi_itmes) use ($options, $year,  $month , $auth) {
                    $suivi_itmes->withOut(["user" =>function($q1){ $q1->withOut("userJob"); } , "mdp" =>function($q2){ $q2->withOut("userJob"); }]);
                    $suivi_itmes->whereDeleted(0);
                },
                'suiviItems' => function ($suivi_itmes) use ($year,  $month) {
                    $suivi_itmes->finished();
                },
            ]);
    }
    public static  function productities($options = [])
    {
        $auth = Auth::user();
        $carbon = Carbon::now();
        $year = get_array_value($options, "year", $carbon->year);
        $month = get_array_value($options, "month", $carbon->month);
        return User::select("id", "name", "avatar", "firstname", "registration_number")
            ->has("suiviItems")
            ->orderBy("firstname", "ASC")
            ->withOut("userJob")
            ->withSum(['pauseProduction as sum_pause' => function ($pause) use ($year,  $month) {
                $pause->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year);
            }], 'secondes')
            
            ->when($auth->isADessignator(), function($user) use ($auth) {
                $user->where("id", "=",$auth->id);
            })
            ->with([
                "pointingTemp",
                'suiviItems' => function ($suivi_itmes) use ($options, $year,  $month) {
                    // $suivi_itmes->withSum('noteQuality as sum_note_items ', 'note');
                    $suivi_itmes->withOut(["user" =>function($q1){ $q1->withOut("userJob"); } , "mdp" =>function($q2){ $q2->withOut("userJob"); }]);
                    $suivi_itmes->with(["version:id,title,deleted","suivi:id,ref,folder_name,category,deleted"]);
                    $suivi_itmes->whereYear('finished_at', '=', $year);
                    $suivi_itmes->whereMonth('finished_at', '=', $month);
                    if (get_array_value($options, "version_ids")) {
                        $suivi_itmes->whereIn("version_id", get_array_value($options, "version_ids"));
                    }
                    if (get_array_value($options, "montage_ids")) {
                        $suivi_itmes->whereIn("montage", get_array_value($options, "montage_ids"));
                    }
                     $suivi_itmes->where("status_id", "=" ,self::$FINISH);
                },
                'suiviItems' => function ($suivi_itmes) use ($year,  $month) {
                    $suivi_itmes->withSum("level","point");
                    $suivi_itmes->whereYear('finished_at', '=', $year);
                    $suivi_itmes->whereMonth('finished_at', '=', $month);
                     $suivi_itmes->where("status_id", "=" ,self::$FINISH);
                },
                'suiviPramsSession' => function ($parms) use ($year,  $month) {
                    $parms->where('month', '=', $month);
                    $parms->where('year', '=', $year);
                },
                "dayOffs" => function ($dayoff) use ($year, $month) {
                    $dayoff->whereMonth('start_date', '=', $month);
                    $dayoff->whereYear('start_date', '=', $year);
                    $dayoff->where("result", "=", "validated")->whereDeleted(0);
                },
                
                
            ]);
    }
    public static function boot()
    {
        parent::boot();
        static::created(function (Model $suiviItem) {
            try {
                $to = collect();
            $creator =  Auth::user();
            if ($creator->id != $suiviItem->user->id) {
                $to->push($suiviItem->user);
            }
            $to->push($suiviItem->mdp);
                \Notification::send($to, new NewSuiviItemNotification($suiviItem, $creator));
            } catch (\Throwable $th) {
              return 0;
            }
        });
    }
    
    public static function createFilter()
    {
        $auth = Auth::user();
        $filters[] = [
            "label" => "Nom ou ref. de dossier ...",
            "name" => "folder_name",
            "type" => "text",
            'attributes' => [
                'title' => 'Filtré par nom  ou réference dossier ...',
            ]
           
        ];
        $filters[] = [
            "label" => "Date",
            "name" => "interval",
            "type" => "date-range",
            "data-allow-clear" => "true",
            'attributes' => [
                'placeholder' => 'Date entre ...',
                'title' => 'Filtré par date interval ...',
            ]
        ];
        /*
        $projects = ProjectGroup::whereDeleted(0)->get();
        $filters[] = [
            "label" => " Projet ",
            "name" => "project_id",
            "type" => "select",
            "options" => to_dropdown($projects, "id" , "name")
        ];
        **/

        $filters[] = [
            "label" => "Version",
            "name" => "version_id",
            "type" => "select",
            "data-allow-clear" => "true",
            'options' => SuiviVersion::drop(),
            'attributes' => [
                'placeholder' => 'Date entre ...',
                'title' => 'Filtré par version ...',
            ]
        ];
        $filters[] = [
            "label" => "M2P",
            "name" => "m2p_id",
            "type" => "select",
            "data-allow-clear" => "true",
            'options' => self::getMdp(),
            'attributes' => [
                
                'title' => 'Filtré par M2P ...',
            ]
        ];
        $filters[] = [
            "label" => "Montage",
            "name" => "montage",
            "type" => "select",
            "width" => "w-150px",
            "data-allow-clear" => "true",
            'options' => self::$MONTAGE,
            'attributes' => [
                'title' => 'Filtré par montage ...',
            ]
        ];
        if ($auth->isM2pOrAdmin() || $auth->isCp()) {
            $filters[] = [
                "label" => "Traité par",
                "name" => "user_id",
                "type" => "select",
                'attributes' => [
                    "data-ajax--url" => url("/search/user"),
                    "data-ajax--cache" => true,
                    "data-minimum-input-length" => "3",
                    "data-allow-clear" => "true",
                    'title' => 'Filtré par traiteur ...',
                    
                ],
                'options' =>  [
                    ["value" =>  0, "text" => trans("lang.me")]
                ],
            ];
        }
        // $filters[] = [
        //     "label" => " Dossier ",
        //     "name" => "suivi_id",
        //     "type" => "select",
        //     'attributes' => [
        //         "data-ajax--url" => url("/search/folder"),
        //         "data-ajax--cache" => true,
        //         "data-minimum-input-length" => "3",
        //         "data-allow-clear" => "true",
        //     ],
        //     'options' =>  [
        //         ["value" =>  0, "text" => "Dossier"]
        //     ],
        // ];
         
        $filters[] = [
            "label" => "Statut",
            "name" => "status_id",
            "type" => "select",
            "width" => "w-200px",
            "data-allow-clear" => "true",
            'options' => self::$STATUS,
            'attributes' => [
                'title' => 'Filtré par statut...',
            ]
        ];
        $customs_filter = CustomerFilter::whereDeleted(0)->where("creator", "=", $auth->id)->get();
        
        $custom = [];
        $customs_filter->map(function ($item, $key) use (&$custom) {
            $custom[] = ["value" => $item->id, "text" => $item->name_filter];
        });
        if ($auth->isM2pOrAdmin() || $auth->isCp()) {
            $filters[] = [
                "label" => "Autres",
                "name" => "custom_filter_id",
                "type" => "select",
                'attributes' => [
                    "data-allow-clear" => "true",
                    'title' => 'Filtré  par filtre personalisé...',
                ],
                'options' => $custom
            ];
        }
        return $filters;
    }
    public static function createFilterStat()
    {
        $auth = Auth::user();
        $users = User::withOut("userJob")->whereDeleted(0)->where("id", "<>", $auth->id)->get();
        $user_list[] = ["value" =>  $auth->id, "text" => trans("lang.me")];
        foreach ($users as $user) {
            $user_list[] = ["value" =>  $user->id, "text" => $user->sortname];
        }

        $filters[] = [
            "label" => "Utilisateurs",
            "name" => "user_id",
            "type" => "select",
            'attributes' => [
                "data-allow-clear" => "true",
                "data-hide-search" => "true",
            ],
            'options' =>  $user_list,
        ];
        $filters[] = [
            "label" => "Nom de dossiers",
            "name" => "suivi_id",
            "type" => "select",
            'attributes' => [
                "data-ajax--url" => url("/search/folder"),
                "data-ajax--cache" => "true",
                "data-minimum-input-length" => "3",
                "data-allow-clear" => "true",
            ],
            'options' =>  [
                ["value" =>  0, "text" => "Dossier"]
            ],
        ];
        $filters[] = [
            "label" => "Date",
            "name" => "interval_stats",
            "type" => "date-range",
            'attributes' => [
                "data-allow-clear" => "true",
                'placeholder' => 'Date entre ...',
            ]
        ];
        $customs_filter = CustomerFilter::whereDeleted(0)->where("creator", "=", $auth->id)->get();
        $custom = [];
        $customs_filter->map(function ($item, $key) use (&$custom) {

            $custom[] = ["value" => $item->id, "text" => $item->name_filter];
        });
        $filters[] = [
            "label" => "Autres",
            "name" => "custom_filter_id",
            "type" => "select",
            'attributes' => [
                "data-allow-clear" => "true",
            ],
            'options' => $custom
        ];
        return $filters;
    }
    public static function  createFilterProd()
    {
        $filters = [];

        $filters[] = [
            "label" => "Versions",
            "name" => "version_ids",
            "type" => "select",
            "width" => "w-500px",
            "disabled_first" => true,
            'attributes' => [
                "multiple" => "multiple",
            ],
            'options' =>  SuiviVersion::drop([]),
        ];
        $filters[] = [
            "label" => "Montage",
            "name" => "montage_ids",
            "type" => "select",
            "width" => "w-350px",
            'attributes' => [
                "multiple" => "multiple",
                "data-allow-clear" => "true",
            ],
            'options' => self::$MONTAGE,
        ];
        $filters[] = [
            "label" => "Nom de dossiers",
            "name" => "folder_ids",
            "type" => "select",
            "width" => "w-350px",
            'attributes' => [
                "multiple" => "multiple",
                "data-ajax--url" => url("/search/folder"),
                "data-ajax--cache" => true,
                "data-minimum-input-length" => "3",
                "data-allow-clear" => "true",
            ],
            'options' =>  [
                ["value" => 0, "text" => "Dossier"]
            ],
        ];
        $filters[] = [
            "label" => "Mois",
            "name" => "month",
            "type" => "select",
            "width" => "w-200px",
            "disabled_first" => true,
            'options' => monthList(Carbon::now()->month),
        ];
        $filters[] = [
            "label" => "Année",
            "name" => "year",
            "type" => "select",
            "width" => "w-200px",
            "disabled_first" => true,
            'options' => yearList(),
        ];
        return $filters;
    }
}
