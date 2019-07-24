<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:office')->except('logout');
        $this->middleware('guest:warehouse')->except('logout');
        
        $uri = \Request::getRequestUri();
        $this->something = explode('?', $uri);
        
    }
    
    /**
     * Show the reset email form.
     * 
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm(){
        //dd(get_defined_vars());
        // Pass some data through to this - I need to determine which type of authentication needs to be used, so we can check the corresponding user table.
        // dd(\Request::getRequestUri());
        

        // dd($something[1]);
        
        return view('auth.passwords.email',[
            'title' => 'Admin Password Reset',
            'passwordEmailRoute' => 'admin.password.email',
            'login_type' => $this->something[1],
        ]);
    }
    
    // /**
    //  * Get the guard to be used during authentication
    //  * after password reset.
    //  * 
    //  * @return \Illuminate\Contracts\Auth\StatefulGuard
    //  */
    // public function guard(){
    //     return Auth::guard($this->something[1]);
    // }
    // 
    // 
    //     /**
    //      * Get the broker to be used during password reset.
    //      *
    //      * @return \Illuminate\Contracts\Auth\PasswordBroker
    //      */
    //     public function broker()
    //     {
    //         // This
    // 
    // 
    //          dd(Password::broker());
    //         return Password::broker();
    //     }
}
