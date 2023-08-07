<?php

namespace App\Http\Middleware;

use App\GeneralSettings;
use Closure;
use Illuminate\Support\Facades\Session;

class Demo
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
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')){
            $notify[] = ['error', 'This is Demo version.  You can not change anything!'];
                return back()->withNotify($notify);
        }
        return $next($request);
    }
}
