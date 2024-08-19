<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectGroup extends Model
{
    use HasFactory;

    protected $table = "projects-group";
    protected $fillable = [
        'name',
        'created_by',
        'deleted',
    ];

    public function members()
    {
       return $this->belongsToMany(User::class,"project_group-members","project_id","user_id");
    }
    public function dayoffValidator()
    {
       return $this->belongsToMany(User::class,"project_group-dayoff_validator","project_id","user_id");
    }
}
