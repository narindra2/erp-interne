<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusReport extends Model
{
    use HasFactory;

    protected $table = "statut_report";
    protected $fillable = [
        'user_id',
        'nature_id',
        'start_date',
        'time_start',
        'fin_date',
        'time_fin',
        'status',
        'detail',
        'start_date_is_morning',
        'fin_date_is_morning',
        "report",
        'deleted',
        
    ];
    protected $casts = [
        'fin_date' => 'date',
        'start_date' => 'date'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, "user_id")->withOut(['userJob']);
    }
    public function nature()
    {
        return $this->belongsTo(DayoffNatureColor::class, "nature_id");
    }
    public function getStatus()
    {
       if ($this->status == "unjustified") {
            return '<span class="badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2">Non reglé</span>';
       }elseif ($this->status == "validated") {
            return '<span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">Reglé</span>';
       }else{
            return '<span class="badge badge-light-primary fw-bolder fs-8 px-2 py-1 ms-2">En cours</span>';
       }
    }
    
    public function scopeGetDetail($query, $options = [])
    {
        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $whereQuery = is_array($user_id) ?  "whereIn"  : "where" ;
            $query->$whereQuery("user_id", $user_id);
        }
        $status = get_array_value($options, "status");
        if ($status) {
            $query->where("status_id", $status);
        }
        $day_report = get_array_value($options, 'day_report' , now()->format("Y-m-d"));
        if ($day_report) {
            $query->whereDate('start_date', '=', convert_date_to_database_date($day_report));
        }
        return $query->whereDeleted(0)->orderBy('created_at', 'DESC');
    }
    public static function createFilter($show_user_filter = true)
    {
        $filters = [];
        if ($show_user_filter) {
            $filters[] = [
                "label" => "Employé ",
                "name" => "user_id",
                "type" => "select",
                'attributes' => [
                    'width' => 'w-300px',
                    "data-ajax--url" => url("/search/user"),
                    "data-ajax--cache" => true,
                    "data-minimum-input-length" => "3",
                    "data-allow-clear" => "true",
                ],
                'options' =>  [],
            ];
        }
       
        
        $filters[] = [
            "label" => "Rapport du ...",
            "name" => "day_report",
            "type" => "date",
            'attributes' => [
                "value" => now()->format("d/m/Y"),
                'placeholder' => 'Rapport du ...',
            ]
        ];
        return $filters;
    }
}
