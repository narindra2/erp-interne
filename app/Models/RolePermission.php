<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    public function submenu()
    {
        return $this->belongsTo(Submenu::class, "submenus_id");
    }
}
