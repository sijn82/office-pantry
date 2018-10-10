<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == "office" && Auth::guard($guard)->check()) {

            return redirect('/office');
        }

        if ($guard == "warehouse" && Auth::guard($guard)->check()) {

            return redirect('/warehouse');
        }

        if (Auth::guard($guard)->check()) {
    
            return redirect('/home');
        }

        // This is the old check, now updated and improved with the code above.
        // if (Auth::guard($guard)->check()) {
        //     return redirect('/home');
        // }

        return $next($request);
    }
}
