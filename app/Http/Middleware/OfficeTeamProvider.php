<?php

namespace App\Http\Middleware;

use App\OfficeTeam;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class OfficeTeamProvider
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }



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

        $user = OfficeTeam::where('id', 1)->first();
        dump($user);

        $admin = Auth::user();

        $guard_status = $this->auth;

        dump($admin);

        dump($guard_status);

        dd($request);
        
        return $next($request);
    }
}
