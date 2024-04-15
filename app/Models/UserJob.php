<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserJob extends Model
{
    use HasFactory , Notifiable;
    
    protected $fillable = [
        'date_user_job',
        'salary',
        'jobs_id',
        'users_id',
        'contract_type_id',
        'department_id',
        'local',
        'category',
        'group',
        'is_cp'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'jobs_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, "contract_type_id" );
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getDailySalaryAttribute()
    {
        return $this->salary / $this->getNbDaysAttribute();
    }

    public function getNbDaysAttribute()
    {
        return date('t');
    }

    public static function getActualUserJob($id=null)
    {
        $userJobSubQuery = UserJob::query();
        $userJobs = UserJob::with(['job', 'user.type'])
            ->selectRaw('*')->from(
                $userJobSubQuery->selectRaw('MAX(date_user_job) as date_user_job_max, users_id as users_id_max')
                    ->whereDeleted(0)
                    ->groupBy('users_id')
            )->join('user_jobs', 'users_id_max', 'users_id')
            ->whereDeleted(0)
            ->whereColumn('date_user_job_max', 'date_user_job');

        if ($id != null) {
            $userJobs->where('users_id', $id);
        }    
        return $userJobs;
    }

    public function getLocal()
    {
        return "Local " . $this->local;
    }

    public function scopeLocalList($query)
    {
        $data = [];
        for ($i=1; $i<=2; $i++) {
            $local = [];
            $local['id'] = $i;
            $local['name'] = 'Local ' . $local['id'];
            $data[] = $local;
        }
        return $data;
    }

    public function scopeGetCategories($query)
    {
        return ['HC', '2A', '2B', '5A', '5B'];
    }

    public function scopeGetGroups($query)
    {
        return ['I', 'II', 'III', 'IV', 'V'];
    }

    public static function createFilter()
    {
        /** Departement filter */
        $department_data = $filters = [];
        $departments  = Department::whereDeleted(0)->get();
        foreach ($departments as $department) {
            $department_data[] = ["value" => $department->id , "text" => $department->name];
        } 
        $filters[] = [
            "label" => "Departement",
            "name" => "department",
            "type" => "select",
            "attributes" => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            'options' => $department_data,
        ];
        $contract_type_data = [];
        $contract_type  = ContractType::whereDeleted(0)->get();
        foreach ($contract_type as $type) {
            $contract_type_data[] = ["value" => $type->id , "text" => $type->name];
        } 
        $filters[] = [
            "label" => "Type de contrat",
            "name" => "contract_type",
            "type" => "select",
            "attributes" => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            'options' => $contract_type_data,
        ];
        $filters[] = [
            "label" => "Local",
            "name" => "local",
            "type" => "select",
            "attributes" => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            'options' => [
                ["value" => 1 , "text" => "Local 1"],
                ["value" => 2 , "text" => "Local 2"],
            ],
        ];

        return $filters;
    }

}
