<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\TaskSectionCreatedOrUpdatedNotification;
use DB;

class TaskSection extends Model
{
    use HasFactory;
    protected $table = "task_sections";
    protected $fillable = ['title',"permissions","creator_id","deleted","created_at","updated_at"];

    protected $casts = [ 'permissions' => 'array'];
    public function colones()
    {
        return $this->hasMany(TaskStatus::class,"section_id")->whereDeleted(0);
    }
    public function members()
    {
        return $this->belongsToMany(User::class,"task_sections_members","section_id","user_id");
    }
    public function is_member($user_id = 0)
    {
        $auth = Auth::user();
        if ($auth->isAdmin()) {
            return true;
        }
        if ($this->members) {
            return in_array(($user_id ? $user_id: $auth->id), $this->members->pluck("id")->toArray());
        }
        return DB::table("task_sections_members")->where("section_id", $this->id)->where("user_id", $user_id ? $user_id: $auth->id)->first();
    }
    public function is_not_member($user_id = 0)
    {
        return !$this->is_member($user_id);
    }
    public function members_can($access = "")
    {
        $auth = Auth::user();
        if ($this->creator_id == $auth->id || $auth->isAdmin() ) {
            return true;
        }
        return in_array($access ,($this->permissions ?? []));
    }
    public function members_can_not($access = "")
    {
        return !$this->members_can($access);
    }
    public static function boot()
    {
        parent::boot();
        static::updated(function (Model $taskSection) {
            if ($taskSection->isDirty('title')) {
                $taskSection->load("members");
                dispatch(function () use ($taskSection) {
                    $changed["new_title"] = $taskSection->title;
                    $changed["old_title"] = $taskSection->getOriginal('title');
                    \Notification::send($taskSection->members, new TaskSectionCreatedOrUpdatedNotification($taskSection, Auth::user(),$changed,true));
                })->afterResponse();
            }
        });
    }
}
