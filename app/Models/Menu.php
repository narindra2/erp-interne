<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $with = ['submenu'];
    
    public static $USER_ALLOWED_PART_ACCESS = [
        "suivi_testeur" => [
            100121, 100043, 100036, 100082, 100155,
            100139,100167, 100109,100042, 100047,
            100053,100057,100090,100055,100110,100080,100134,100116,100130,
        ],
        "debug_tools" => [100043],
        "complement_hours" => [100043]
    ];
    public function submenu() 
    {
        return $this->hasMany(Submenu::class, 'menus_id');
    }
}
