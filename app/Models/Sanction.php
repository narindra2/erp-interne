<?php

namespace App\Models;

use DB;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reason',
        'type',
        'duration',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

   
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function scopeGetDetails($query , $options = []) {
        $query->with(["user.userJob.job"]);

        $year = get_array_value($options, "year");
        if ($year) {
            $query->whereYear('date', '=', $year);
        } 
        $auth = Auth()->user();
        if ($auth->isCp() &&  !$auth->isRhOrAdmin() ) {
            $usrs_same_dprtmt = Department::getUserByIdDepartement(Auth()->user()->userJob->department_id );
            $usrs_same_dprtmt =  $usrs_same_dprtmt->pluck("id")->toArray();
            $query->whereIn('user_id',  $usrs_same_dprtmt );
        }
        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $query->where('user_id', '=', $user_id);
        }

        $type = get_array_value($options, "type");
        if ($type) {
            $query->where('type', '=', $type);
        } 
        return $query->whereDeleted(0)->latest("date");
    }
    public function getType(&$class="") {
        if ($this->type == 1) {
            $class = "badge badge-light-primary fw-bolder fs-8 px-2 py-1 ms-2";
            return "Verbal";
        }
        if ($this->type == 2) {
            $class = "badge badge-light-warning fw-bolder fs-8 px-2 py-1 ms-2";           
            return "Ecrit";
        }
        if ($this->type == 3) {
            $class = "badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2";
            return "Mis à pied";
        }              
        return "";
    }

    public function getTypeWithCss() {
        $text = '<span class="%s">%s</span';
        $class = "";
        $type = $this->getType($class);
        return sprintf($text, $class, $type);
    }

    public function getDuration() {
        if ($this->type != 3)   return "";
        return $this->duration;
    }

    public static function storeSanction($input) {
        $id = $input['id'];
        $oldSanction = Sanction::find($id);
        $sanction = Sanction::updateOrCreate(
            ['id' => $input['id']], 
            $input
        );
        $user = User::find($input['user_id']);
        if (! $id) {
            $sanction->addUserSanction($user);
        } 
        else {
            $sanction->updateUserSanction($user, $oldSanction);
        }
        return $sanction;
    }

    public function addUserSanction(User $user) {
        if ($this->type == 1) $user->verbal_warning = $user->verbal_warning + 1;
        else if ($this->type == 2) $user->written_warning = $user->written_warning + 1;
        else if ($this->type == 3) $user->layoff = $user->layoff + 1;
        $user->save();
    }

    public function updateUserSanction(User $user, Sanction $sanction) {
        if ( $sanction->type != $this->type) {
            if ($sanction->type == 1) $user->verbal_warning = $user->verbal_warning - 1;
            else if ($sanction->type == 2) $user->written_warning = $user->written_warning - 1;
            else if ($sanction->type == 3) $user->layoff = $user->layoff - 1;
        }
        $this->addUserSanction($user);
    }

    public function deleteSanction() {
        DB::beginTransaction();
        try {
            $user = $this->user;
            if ($this->type == 1) $user->verbal_warning = $user->verbal_warning - 1;
            else if ($this->type == 2) $user->written_warning = $user->written_warning - 1;
            else if ($this->type == 3) $user->layoff = $user->layoff - 1;
            $user->save();
            $this->deleted = 1;
            $this->save();
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
        }
        catch (Error $e) {
            DB::rollBack();
        }
    }
    public static function createFilter($options = [])
    {
        $filters = []; $auth = Auth()->user();
        if ($auth->isRhOrAdmin() ) {
            $filters[] = [
                "label" => " Employés ",
                "name" => "user_id",
                "type" => "select",
                'attributes' => [
                    "data-hide-search" => "false",
                    "data-allow-clear" => "true",
                ],
                'options' => to_dropdown( User::whereDeleted(0)->get(),"id" , "sortname"),
            ];
        }
        $filters[] = [
            "label" => " Types de saction ", 
            "name" =>"type",
            "type" => "select",
            'attributes' => [
                "data-allow-clear" => "true",
            ],
            "options" =>[
                ["text" => "Verbal" , "value" => 1 ],
                ["text" => "Ecrit" , "value" => 2 ],
                ["text" => "Mis à pied" , "value" => 3]
            ],
        ];
        $filters[] = [
            "label" => " Toutes ", 
            "name" =>"year",
            "type" => "select",
            "width"  =>"w-200px",
            'attributes' => [
                "data-allow-clear" => "true",
            ],
            "options" => yearList(3 , true),
        ];
       
       

        return $filters;
    }
}
