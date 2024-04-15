<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

   
    public function userJobs()
    {
        return $this->hasMany(UserJob::class, 'jobs_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_jobs', 'jobs_id', 'users_id');
    }

    public static function saveOrUpdate($input)
    {
        if (isset($input["id"])) {
            $job = Job::find($input['id']);
            $job->name = $input['name'];
            $job->save();
            return $job;
        }
        else {
            $job = Job::create($input);
            return $job;
        }
    }
}
