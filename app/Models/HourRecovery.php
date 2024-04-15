<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HourRecovery extends Model
{
    use HasFactory;

    /** 
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'job_id',
        'date_of_absence',
        'duration_of_absence',
        'recovery_start_date',
        'recovery_end_date',
        'description',
        'is_validated',
        'hour_absence'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_of_absence' => 'datetime',
        'recovery_start_date' => 'datetime',
        'recovery_end_date' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, "job_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function getRecoveryDateAttribute()
    {
        $start = $this->recovery_start_date->format("d M Y");
        $end = $this->recovery_end_date->format("d M Y");
        return $start . " au " . $end; 
    }

    public function getDuration() {
        return convertMinuteToHour($this->duration_of_absence * 60);
    }

    public function isValidated() {
        $text = "<span class='badge %s'>%s</span>";
        if ($this->is_validated === null)   return sprintf($text, "badge-light-primary", "Nouveau");
        else if ($this->is_validated === 0) return sprintf($text, "badge-light-danger", "Refusée");
        else if ($this->is_validated === 1) return sprintf($text, "badge-light-success", "Validée");
        
        else if (!$this->is_validated) return sprintf($text, "badge-light-danger", "Refusée");
        else if ($this->is_validated) return sprintf($text, "badge-light-success", "Validée");
    }

    public function createForm(User $user) {
        if ($this->id == null) {
            $this->user_id = $user->id;
            $this->job_id = $user->userJob->jobs_id;
        }
    }   

    public function getJobName() {
        if ($this->job_id == null) {
            return $this->user->userJob->job->name;
        }
        return $this->job_id;
    }

    public static function createHourRecovery($input) {
        $input['date_of_absence'] = to_date($input['date_of_absence']);
        $input['recovery_start_date'] = to_date($input['recovery_start_date']);
        $input['recovery_end_date'] = to_date($input['recovery_end_date']);

        if (Carbon::make($input['recovery_start_date'])->gt(Carbon::make($input['recovery_end_date']))) {
            throw new Exception("La date fin de récupération est inférieure par rapport à la date de début");
        }
        return HourRecovery::create($input);
    }

    public function scopeDetail($query, $input) {
        $query->with(['job', 'user']);
        $user = Auth::user();
        //Select only the hour recovery for himself if the user is not a admin nor a hr
        if (!$user->isAdmin() && !$user->isHR()) {
            $query->where('user_id', $user->id);
        }

        //Filter the result by the input given
        //code
        return $query->whereDeleted(0)->get();
    }
}
