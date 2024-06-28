<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static $_IT = "1";
    public static $_DEV = "8";
    public static $_COMPTA = "2";

    public function scopeGetEmployeeByIdDepartement($query, $idDepartement)
    {
        if (is_array($idDepartement)) {
            return UserJobView::with(['user' =>  function ($query)
            {
                $query->where("users.deleted", 0);
            }])->whereIn("department_id", $idDepartement)->whereDeleted(0);
        }
        return UserJobView::with(['user'=>  function ($query)
        {
            $query->where("users.deleted", 0);
        }])->whereIn("department_id", [$idDepartement])->whereDeleted(0);
    }

    public function scopeGetUserByIdDepartement($query, $idDepartement)
    {
        $userJobs = UserJobView::with(['user'])->where("department_id", $idDepartement)->whereDeleted(0)->get();
        $users = [];
        foreach ($userJobs as $userJob) {
            $users[] = $userJob->user;
        }
        return collect($users);
    }

    public function scopeGetAllEmployee($query)
    {
        return UserJobView::with(['user'])->whereDeleted(0);
    }

    public static function drop() {
        $departments = Department::whereDeleted(0)->orderBy('name')->get();
        $tab = [];
        foreach ($departments as $department) {
            $tab[] = ['text' => $department->name, 'value' => $department->id];
        }
        return $tab;
    }
}
