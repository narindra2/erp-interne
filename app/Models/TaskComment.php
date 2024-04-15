<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $table = "task_comments";
    protected $fillable = [
        'content',
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
}
