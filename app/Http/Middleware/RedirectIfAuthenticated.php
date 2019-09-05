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
        // dump($guard);
        // dd($request);
        
        dump(Auth::guard());
        dd(Auth::guard($guard)->check());
        
        // if ($guard == "office" && Auth::guard($guard)->check()) { // As $guard always comes back as null, I'm going to try Auth::guard() for a little bit to see what happens - 30/08/19
        if (Auth::guard() == "office" && Auth::guard($guard)->check()) {

            return redirect('/office');
        }
        // keeping the previous $guard check here though as I'm not really doing anything to warehouse until I get office sorted!
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
