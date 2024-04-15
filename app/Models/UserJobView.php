<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobView extends UserJob
{
    use HasFactory;
    public $table = "v_userjob";

    protected $with = [
        'job',
    ];
}
