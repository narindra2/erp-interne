<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCheckList extends Model
{
    use HasFactory;
    protected $table = "task_checklists";
    protected $fillable = [
        'description',
        'task_id',
        'user_id',
        'deleted',
        'created_at',
        'updated_at'
    ];

    public function creator()
    {
       return $this->belongsTo(User::class ,"user_id");
    }
    public function task()
    {
       return $this->belongsTo(Task::class ,"task_id");
    }
}
