<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    use HasFactory;

    protected $table = "task_files";
    protected $appends = [
        "uri","created_date"
    ];
    protected $fillable = [
        'task_id',
        'name',
        'originale_name',
        'is_do',
        'uploaded_by',
        'deleted',
        'created_at',
        'updated_at'
    ];
    public function uploader()
    {
       return $this->belongsTo(User::class ,"uploaded_by");
    }
    public function task()
    {
        return $this->belongsTo(Task::class ,"task_id");
    }
    public function getUriAttribute()
    {
       return asset("/task-files/task-$this->task_id/$this->name") ;
    }
    public function getCreatedDateAttribute()
    {
       return convert_to_real_time_humains($this->created_at) ;
    }
}
