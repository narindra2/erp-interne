<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckInWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $auth = auth()->user();
        if (auth()->check()) {
            if ($auth->last_check == "in" && $auth->last_check_date) {
                if (Carbon::make($auth->last_check_date)->lt(Carbon::today())) {
                    $auth->last_check = "out";
                }
            } else {
                $auth->last_check = "out";
            }
            $auth->last_check_date = Carbon::now()->format('Y-m-d');
            $auth->save();
            return $next($request);
        }
        abort(403);
    }
}
