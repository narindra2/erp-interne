<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/** 
 * Bug on : https://laracasts.com/discuss/channels/laravel/broadcast-notifications-not-working
 * $prefix on redis driver
*/
// $prefix =  env("BROADCAST_DRIVER") == "redis" ? "private-" : "";
//  dd(env("BROADCAST_DRIVER"));
// Broadcast::channel('users.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
// Broadcast::channel('notification.user.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});