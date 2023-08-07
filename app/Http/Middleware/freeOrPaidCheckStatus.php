<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class freeOrPaidCheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth()->user();
            if ($user->active_status == 1) {
                return $next($request);
            } else {
                $notify[] = ['error', 'Please active your account first.'];

                return redirect()->route('user.home')->withNotify($notify);
            }
        }
    }
}
