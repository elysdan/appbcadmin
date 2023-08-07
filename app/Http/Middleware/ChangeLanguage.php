<?php

namespace App\Http\Middleware;

use App\Language;
use Closure;
use session;

class ChangeLanguage
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
        session()->forget('active_template');
          session()->put('lang', $this->get_code());
        app()->setLocale(session('lang',  $this->get_code()));
        return $next($request);
    }
    public function get_code()
    {
        if (session()->has('lang')) {
            return session()->get('lang');
        }
        $language = Language::where('is_default', 1)->first();
        return $language->code;
    }
}
