<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public $user;
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

        // dump(Auth::guard());
        $check = Auth::guard($guard)->check();
        $auth = Auth::guard();

        //----- OK, sometimes this works, I think? But $guard isn't working as intended, neither are the if statement checks, so a better solution is required 11/09/19 -----//

        // if ($guard == "office" && Auth::guard($guard)->check()) { // As $guard always comes back as null, I'm going to try Auth::guard() for a little bit to see what happens - 30/08/19
        if (Auth::guard() == "office") { //  && Auth::guard($guard)->check() <- removing this bit to see if I get redirected more appropriately, then I can decide what to do with it.

            return redirect('/office');
        }
        // keeping the previous $guard check here though as I'm not really doing anything to warehouse until I get office sorted!
        if ($guard == "warehouse" && Auth::guard($guard)->check()) {

            return redirect('/warehouse');
        }

        if (Auth::guard($guard)->check()) {

            return redirect('/');
        }

        // This is the old check, now updated and improved with the code above.
        // if (Auth::guard($guard)->check()) {
        //     return redirect('/home');
        // }

        return $next($request);
    }
}
