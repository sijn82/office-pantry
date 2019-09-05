<?php

namespace App\Http\Middleware;

use Closure;

class OfficeTeamProvider
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
        // following basic guide from https://stackoverflow.com/questions/52851208/laravel-passport-multiple-authentication-using-guards
        // it's similar to some of the steps I've already implemented and looks like it was submitted recently (6/8/2019) - todays date is 28/08/2019
        // currently unproven but let's see what happens...
        
        config(['auth.guards.api.provider' => 'officeteam']);
        
        return $next($request);
    }
}
