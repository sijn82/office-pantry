<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    // made this change to allow all redirects to return to the main homepage so all logins are only a click away from logging in with new credentials.
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return 
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:office')->except('logout');
        $this->middleware('guest:warehouse')->except('logout');
    }
    // 
    //     /**
    //  * Get the guard to be used during password reset.
    //  *
    //  * @return \Illuminate\Contracts\Auth\StatefulGuard
    //  */
    // protected function guard()
    // {
    //     //dd(Auth::guard());
    //     return Auth::guard('office');
    // }
}
