<?php

namespace App\Models;

use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\TaskUpdatedNotification;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    protected $table = "tasks";
    public static $_TO_DO = 1;
    protected $fillable = [
        'title',
        'description',
        'creator',
        'assign_to',
        'label',
        'ribbon',
        'status_id',
        'section_id',
        'order_on_board',
        'recurring',
        'recurring_type',
        'recurring_detail',
        'last_set_recycle',
        'start_date_recurring',
        'start_deadline_date',
        'end_deadline_date',
        'last_recurring',
        'deleted',
        'archived',
        'created_at',
        'updated_at'
    ];
    protected $with = ["autor"];
    public static $recurring_type = [
        ["type" => "every_day", "title" => "Chaque jour", "every" => 1],
        ["type" => "every_day_on", "title" => "Chaque jour du ", "every" => 0],
        ["type" => "every_nb_day", "title" => "Chaque {nombre} jours", "every" => 0],
        ["type" => "every_week", "title" => "Chaque semaine", "every" => 7],
        ["type" => "every_month", "title" => "Chaque mois", "every" => 30],
        ["type" => "every_year", "title" => "Chaque année", "every" => 365],
    ];
    public function autor(): object
    {
        return $this->belongsTo(User::class, "creator")->without(["userJob"]);
    }
    public function status(): object
    {
        return $this->belongsTo(TaskStatus::class, "status_id");
    }
    public function section(): object
    {
        return $this->belongsTo(TaskSection::class, "section_id");
    }
    public function comments(): object
    {
        return $this->hasMany(TaskComment::class, "task_id")->whereDeleted(0)->latest();
    }
    public function files(): object
    {
        return $this->hasMany(TaskFile::class, "task_id")->whereDeleted(0)->latest();
    }
    public function checkLists(): object
    {
        return $this->hasMany(TaskCheckList::class, "task_id")->whereDeleted(0)->latest();
    }
    public function getTagAttribute(): string
    {
        return "T-00" . $this->id;
    }
    public function is_the_autor(): bool
    {
        return $this->creator == Auth::id();
    }
    public function getResponsiblesAttribute(): object
    {
        return $this->assign_to 
        ? User::without(["userJob.job"])->findMany(explode(",", $this->assign_to),["id","name","firstname","avatar"]) 
        : collect([]);
    }
    public static function getToDoBoardId($section_id = 0){
        return  TaskStatus::where("acronym","TO_DO")->where("section_id",$section_id)->first()->id;
    }
    public static function getArchivedBoardId($section_id = 0){
        return  TaskStatus::where("acronym","ARCHIVED")->where("section_id",$section_id)->first()->id;
    }
    public function getDetailRecurringAttribute(): array
    {
        if (in_array($this->recurring_type, ["every_day_on", "every_nb_day"])) {
            return unserialize($this->recurring_detail);
        }
        return [];
    }
    public function getNbRecurringEveryDaysAttribute(): int
    {
        if (!$this->recurring) {
            return 0;
        }
        if ($this->recurring_type == "every_nb_day") {
            $recurring_detail = unserialize($this->recurring_detail);
            return get_array_value($recurring_detail, "nb_days", 0);
        }
        if ($this->recurring_type == "every_day_on") {
            $recurring_detail = unserialize($this->recurring_detail);
            return get_array_value($recurring_detail, "day_of_week");
        }
        $recurring_type =  collect(Task::$recurring_type)->firstWhere("type", $this->recurring_type);
        return get_array_value($recurring_type, "every");
    }
    public function get_start_recycle_date()
    {
        return  $this->start_date_recurring ? Carbon::make(($this->start_date_recurring)) :  null;
    }
    /** Calcul real date to re-do a task recycle */
    public function get_last_recycle_date()
    {
        return Carbon::parse(($this->last_recurring ?? $this->start_date_recurring))->addDays($this->nbRecurringEveryDays)->format("Y-m-d");
    }
    public function get_next_recycle_date()
    {
        $last_recurring =  Carbon::parse(($this->last_recurring ?? $this->start_date_recurring));
        return $last_recurring->addDays($this->nbRecurringEveryDays);
    }
    public function need_recycle(): bool
    {
        if ($this->archived) {
            return false;
        }
        /** Task in To do mean already recycled and set recycle */
        if ($this->status_id == Task::$_TO_DO) {
            return true;
        }
        /** Have just recycled mean don't touch  */
        if ($this->last_set_recycle == Carbon::now()->format("Y-m-d")) {
            return false;
        }
         /** Task every a day specific  */
        if ($this->recurring_type == "every_day_on") {
            return $this->nbRecurringEveryDays == Carbon::now()->dayOfWeek;  // ex today is thuesday , $this->nbRecurringEveryDays = 2 an Carbon::now()->dayOfWeek = 2
        }
        /** else count day to need recycle % today */
        $last_recurring = Carbon::parse(($this->last_recurring ?? $this->start_date_recurring));
        return $last_recurring->diffInDays(Carbon::now()) >= $this->nbRecurringEveryDays ? true : false;
    }
    public function set_recycle(): void
    {
        
        DB::table($this->table)->where(["id" => $this->id])->update([
            "order_on_board" => 0,
            "status_id" => TaskStatus::where("acronym","TO_DO")->where("section_id" ,$this->section_id)->first()->id,
            "last_set_recycle" => Carbon::now(),
            "last_recurring" => $this->get_last_recycle_date()
        ]);
    }
    public function is_task_deadline(): bool
    {
        return ($this->start_deadline_date || $this->end_deadline_date) ? true : false;
    }
    public function get_class_deadline(): string
    {
        $week = 7 ;
        $class = "";
        $icon = "<i class='fas fa-tags text-%s'></i>";
        if ($this->is_task_deadline() ) {
            $this->load(["status:id,title,acronym,deleted"]);
            $acronym_status = $this->status->acronym ;
            $end = Carbon::parse($this->end_deadline_date);
            $start = Carbon::parse($this->start_deadline_date);
            if ($end->isPast() && !in_array($acronym_status,["FINISHED", "ARCHIVED"]) ) {
                $class = "danger";
                $icon = "<i class='fas fa-exclamation-triangle text-%s'></i>";
            }elseif($start->isPast() && !in_array($acronym_status,["FINISHED", "ARCHIVED"]) ){
                $diff_normal = $start->diffInDays($end);
                $diff_part = $start->diffInDays( Carbon::now());
                $diff_per_100 =  ($diff_part * 100) / $diff_normal;
                $rest_days = $diff_normal - $diff_part;
                if ($diff_per_100 > 85 || ($rest_days == 1 && $diff_normal <= $week)) {
                    $class = "danger";
                    $icon = "<i class='fas fa-exclamation-triangle text-%s'></i>";
                }elseif ((int) ($diff_per_100 < 85 &&  (int)$diff_per_100 >= 75) ||  ($rest_days == 2  && $diff_normal <= $week) ) {
                    $class = "warning";
                    $icon = "<i class='fas fa-exclamation-triangle text-%s'></i>";
                }
            }
        }
        return sprintf($icon,$class);
    }
    public function label_info(): array
    {
        $info = [];
        if ($this->label =="urgent") {
            $info["class"] = "warning";
            $info["text"] = "Urgent";
        }elseif ($this->label =="tres_urgent") {
            $info["class"]  = "danger";
            $info["text"] = "Trés urgent";
        }
        return $info;    
    }
    public static function createFilter($options = [])
    {
        $filters = $members =  [];
        $section = get_array_value($options,"section");
        if ($section->members_can("can_access_members_task")) {
            foreach($section->members as $user){
                $members[] = ["value" => $user->id , "text" => $user->sortname];
            }
            $filters[] = [
                "label" => " Mes tâches ", 
                "name" =>"user_id", 
                "type" => "select",
                "width"  =>"w-250px",
                'options' => $members 
            ];
        }
        $filters[] = [
            "label" => "Rechercher une tâche", 
            "name" =>"search_task",
            "type" => "text",
            "width"  =>"w-250px",
        ];
        $filters[] = [
            "label" => "Archives", 
            "name" =>"archived",
            "type" => "select",
            "width"  =>"w-200px",
            "disabled_first" => true,
            "options" => [
                ["value" => "no" , "text" => "Sans les archives" ,"selected" => true],
                ["value" => "yes" , "text" => "Avec les archives"]
            ],
        ];
        return $filters;
    }
    public static function boot()
    {
        parent::boot();
        static::created(function (Model $task) {
            $task->load("section");
            \Notification::send($task->responsibles, new TaskAssignedNotification($task, Auth::user()));
        });
        static::updated(function (Model $task) {
            $changed = [];
            $do_notification  = false;
            if ($task->isDirty('status_id')) {
                $changed["new_status"] = TaskStatus::find($task->status_id)->title;
                $changed["old_status"] = TaskStatus::find($task->getOriginal('status_id'))->title;
                $do_notification = true;
            }
            if ($task->isDirty('title')) {
                $changed["new_title"] = $task->title;
                $changed["old_title"] = $task->getOriginal('title');
                $do_notification = true;
            }
            if ($task->isDirty('description')) {
                $changed["description"] = true;
                $do_notification = true;
            }
            if ($task->isDirty('start_deadline_date') || $task->isDirty('end_deadline_date')) {
                $changed["deadline_date"] = true;
                $do_notification = true;
            }
           
            if ($task->isDirty('deleted')) {
                $changed["deleted"] = true;
                $do_notification = true;
            }
            if ($task->isDirty('assign_to')) {
                $reponsibles = [];
                $new_reponsibles = explode(",", $task->assign_to);
                $old_reponsibles = explode(",", $task->getOriginal('assign_to'));
                foreach ($new_reponsibles as $new) {
                    if (!in_array($new, $old_reponsibles)) {
                        $reponsibles[] = $new;
                    }
                }
                if (count($reponsibles)) {
                    $new_user = User::findMany($reponsibles);
                    $changed["new_reponsibles"] = $new_user->implode("sortname", ", ");
                    $task->load("section");
                    dispatch(function () use ($new_user, $task) {
                        \Notification::send($new_user, new TaskAssignedNotification($task, Auth::user()));
                    })->afterResponse();
                    $do_notification = true;
                }
            }
            if (!$do_notification) {
                return;
            }
            /**  Inform responsibles on changement  task*/
            $notify_to = $task->responsibles;
            /**  Inform creator on changement  task */
            $notify_to = $notify_to->push($task->autor);
            dispatch(function () use ($notify_to, $task,$changed) {
                \Notification::send($notify_to, new TaskUpdatedNotification($task, Auth::user(), $changed));
            })->afterResponse();
        });
    }
}
