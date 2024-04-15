<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTotalMinutesPerformed extends Model
{
    use HasFactory;
    //name of view sql
    public $table = "total_minutes_performed";

    //script SQL: CREATE OR REPLACE VIEW total_minutes_performed AS SELECT SUM(minute_performed) AS minute_performed, daily_activity_id, users_id, MONTH(day) AS month, YEAR(day) AS year FROM employee_daily_activity GROUP BY daily_activity_id, users_id, MONTH(day), YEAR(day)

    public function dailyActivity()
    {
        return $this->belongsTo(DailyActivity::class, "daily_activity_id");
    }
}
