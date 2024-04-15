<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysOffType extends Model
{
    use HasFactory;

    protected $table = "days_off_types";
    protected $guarded = [];

    public $typeList = ["Congé", "Permission"];

    public function getType()
    {
        if ($this->type == "daysoff")   return $this->typeList[0];
        return $this->typeList[1];
    }
}
