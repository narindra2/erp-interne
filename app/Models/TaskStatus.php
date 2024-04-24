<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatus extends Model
{
    use HasFactory;

    protected $table = "task_status";
    protected $fillable = [
        'title',
        'class',
        'section_id',
        'acronym',
        'order_board',
        'deleted',
        'created_at',
        'updated_at'
    ];
    public function tasks()
    {
        return $this->hasMany(Task::class, "status_id");
    }
    public function section()
    {
        return $this->belongsTo(TaskSection::class);
    }

    public function scopeGetDetail($query, $options = [])
    {
        $section_id = get_array_value($options, "section_id");
        $section = TaskSection::find($section_id);
        $status_task = TaskStatus::select(["id", "title", "acronym", "class", "order_board", "section_id", "deleted"])
            ->where("section_id", "=", $section_id)
            ->withCount("tasks")
            ->with(["tasks" => function ($query) use ($options, $section) {
                $user_id = get_array_value($options, "user_id");
                $the_user_task = $user_id;
                if ($user_id == "0" ||  !$user_id) {
                    $user_id = Auth::id();
                }
                if ($user_id == "all"  && $section->members_can("can_access_members_task")) {
                    // no filter it
                }else{
                    if ($section->members_can_not("can_access_members_task") ||  $the_user_task) {
                        $query->where(function ($q) use ($user_id) {
                            $q->whereRaw('FIND_IN_SET("' . $user_id . '", assign_to)')->orWhere("creator", $user_id);
                        });
                    }
                }
                $search = get_array_value($options, "search_task");
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where("title", 'like', '%' . $search . '%');
                        $q->orWhere("description", 'like', '%' . $search . '%');
                    });
                }
                /** Order it if has deadline */
                $query->orderBy(DB::raw('ISNULL(end_deadline_date), end_deadline_date'), 'ASC')
                        ->orderBy("start_deadline_date", "ASC")->latest()
                        ->whereDeleted(0);
            }]);
        $archived = get_array_value($options, "archived", "no");
        if ($archived == "no") {
            $status_task->where("acronym", "<>", "ARCHIVED");
        }
        return $status_task->whereDeleted(0)->orderBy("order_board", "ASC");
    }
}
