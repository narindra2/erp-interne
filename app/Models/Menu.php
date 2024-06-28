<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    public static $USER_ALLOWED_PART_ACCESS = [
        "suivi_testeur" => [
            100121, 100043, 100036, 100082, 100155,
            100139, 100167, 100109, 100042, 100047,
            100053, 100057, 100090, 100055, 100110, 
            100080, 100134, 100116, 100130,
        ],
        "debug_tools" => [ /*100043 */],
        "complement_hours" => [100043],
        "purchase" => [],
        "stock" => [100121],
    ];

    public static function  _can_access_purchase($user = null)
    {
        $auth_user = $user ? $user : Auth::user();
        $taggued_on_purchase = Purchase::whereRaw('FIND_IN_SET("' . $auth_user->id . '", tagged_users)')->whereDeleted(0)->first();
        if ($taggued_on_purchase || $auth_user->isCompta() || $auth_user->isRhOrAdmin() || in_array($auth_user->registration_number,  self::$USER_ALLOWED_PART_ACCESS["purchase"])) {
            return true;
        }
        return false;
    }
    public static function  _can_access_stock($user = null)
    {
        $auth_user = $user ? $user : Auth::user();
        if ($auth_user->isTech() || $auth_user->isCompta() || $auth_user->isRhOrAdmin() || in_array($auth_user->registration_number,  self::$USER_ALLOWED_PART_ACCESS["stock"])) {
            return true;
        }
        return false;
    }
}