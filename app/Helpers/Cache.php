<?php

use App\Models\User;
use App\Models\Message;
use App\Models\DaysOffType;
use App\Models\MessageView;
use App\Models\UserJobView;
use App\Models\MessageReaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('get_cache_rh_admin')) {
     function get_cache_rh_admin(){
        return Cache::rememberForever('get_cache_rh_admin', function () {
            return User::whereIn("user_type_id",[1,2])->whereDeleted(0)->get();
        });
    }
}

if (!function_exists('get_cache')) {
    function get_cache(Builder $builder, $cache_name) {
        return Cache::rememberForever($cache_name, function () use ($builder) {
            return $builder->whereDeleted(0)->get();
        });
    }
}

if (!function_exists('delete_cache')) {
    function delete_cache($name = "") {
        Cache::forget($name);
    }
}

if (!function_exists('get_users_cache')) {
    function get_users_cache() {
        return Cache::rememberForever('users_cache', function () {
            return UserJobView::with(['user.type', 'job', 'contractType', 'department'])->get()->filter(function($userJob) {
                return $userJob->user->deleted == 0;
            });
        }); 

    }
}

if (!function_exists('delete_users_cache')) {
    function delete_users_cache() {
        Cache::forget('users_cache');
    }
}

if (!function_exists('get_cache_total_permission')) {
    function get_cache_total_permission($user_id = 0) {
        return Cache::rememberForever("get_cache_total_permission_$user_id", function () use ($user_id) {
            $permissions = DaysOffType::select(["deleted","type","id"])->whereDeleted(0)->where("type","permission")->get()->pluck("id")->toArray();
            return DB::table("days_off")
                    ->selectRaw('SUM(DATEDIFF(return_date , start_date)) AS total')
                    ->where("applicant_id", $user_id)
                    ->whereIn("type_id", $permissions)
                    ->where("result", "validated")
                    ->where("is_canceled", 0)
                    ->whereYear('created_at', date('Y'))
                    ->get();
        });
    }
}

if (!function_exists('getMessagesNotSeen')) {
    function getMessagesNotSeen(User $user) {
        return Cache::rememberForever("getMessagesNotSeen_$user->id", function () use ($user) {
            return MessageView::getMessagesNotSeen($user);
        });
    }
}

if (!function_exists('getMessagesNotSeenGroup')) {
    function getMessagesNotSeenGroup(User $user) {
        return Cache::rememberForever("getMessagesNotSeenGroup_$user->id", function () use ($user) {
            return MessageView::getMessagesNotSeenGroup($user);
        });
    }
}

if (!function_exists('getIconsReaction')) {
    function getIconsReaction() {
        return Cache::rememberForever("messageReaction", function () {
            return MessageReaction::whereDeleted(false)->get();
        });
    }
}

if (!function_exists('getUserList')) {
    function getUserList() {
        return Cache::rememberForever("getUserList", function() {
            return User::withOut('userJob')->whereDeleted(false)->get();
        });
    }
}