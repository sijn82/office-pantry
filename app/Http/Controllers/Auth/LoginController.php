<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:office')->except('logout');
        $this->middleware('guest:warehouse')->except('logout');
    }

    // Login Form for Admins, determine template and url path
    public function showOfficeLoginForm()
    {
        return view('auth.login', ['url' => 'office']);
    }

    // Choose which fields require validation and what to do after the authentication check i.e redirects
    public function officeLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('office')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

            return redirect()->intended('/office');
        }
        return back()->withInput($request->only('email', 'remember'));
    }

    // Login for Warehouse Operatives, determine template and url path
    public function showWarehouseLoginForm()
    {
        return view('auth.login', ['url' => 'warehouse']);
    }
    
    // Choose which fields require validation and what to do after the authentication check i.e redirects
    public function warehouseLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('warehouse')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

            return redirect()->intended('/warehouse');
        }
        return back()->withInput($request->only('email', 'remember'));
    }
}
