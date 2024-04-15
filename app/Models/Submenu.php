<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    use HasFactory;

    public function rolePermission()
    {
        return $this->hasMany(RolePermission::class, "submenus_id");
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menus_id');
    }

    public function getPermissionAttribute($value)
    {
        $value = unserialize($value);
        unset($value[UserType::$_ADMIN]);
        return $value;
    }

    public function hasPermission($user_type_id)
    {   
        return false;
    }

    public static function updateAllSubMenu()
    {
        $userTypes = UserType::whereDeleted(0)->get();
        $submenus = Submenu::all();
        foreach ($submenus as $submenu) {
            $permissions = $submenu->permission;
            foreach($permissions as $permission) {
                // if () {
                // }
            }

            $submenu->save();
        }
    }
}
