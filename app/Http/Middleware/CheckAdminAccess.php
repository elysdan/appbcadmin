<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAdminAccess
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
        if(Auth::guard('admin')->user()->role == 0)
        {
            return $next($request);

        }

        return redirect()->route('admin.ticket');

    }
}
