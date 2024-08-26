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
        'type_status_report_id',
        'start_date',
        'fin_date',
        'status',
        'detail',
        'start_date_is_morning',
        'fin_date_is_morning',
        'deleted',
        
    ];
    protected $casts = [
        'fin_date' => 'date',
        'start_date' => 'date'
    ];

    const TYPE_STATUS_REPORT = [
        ["id" => 1 , "text"  => "Retard"],
        ["id" => 2 , "text"  => "Abscence"],
    ];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id")->withOut(['userJob']);
    }
    public function getStatus()
    {
       if ($this->status == "unjustified") {
            return '<span class="badge badge-light-danger fw-bolder fs-8 px-2 py-1 ms-2">Non justifié</span>';
       }elseif ($this->status == "validated") {
            return '<span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">Justifié</span>';
       }else{
            return '<span class="badge badge-light-primary fw-bolder fs-8 px-2 py-1 ms-2">En cours</span>';
       }
    }

    public function scopeGetDetail($query, $options = [])
    {
        $status = get_array_value($options, "status");
        if ($status) {
            $query->where("status_id", $status);
        }
        return $query->whereDeleted(0)->orderBy('created_at', 'DESC');
    }
}
