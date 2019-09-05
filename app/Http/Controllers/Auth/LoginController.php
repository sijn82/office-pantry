<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Auth;

// it looks like I need to grab the user model?  let's get it just in case.
use App\OfficeTeam;
// need hash as well by the looks of things
use Illuminate\Support\Facades\Hash;

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
    // protected $redirectTo = '/home';

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
        // dd($request);
        
        
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        //dd(Auth::guard());
        
        // commenting this out for now until I know whether the new version of this works.
        
        if (Auth::guard('office')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
        
            return redirect()->intended('/office');
        }
        return back()->withInput($request->only('email', 'remember'));
        
        // new version resulted in locking me out of the whole site.  Not good, so going back a couple of steps and gathering more info!
        
        // get user object
        // $user = OfficeTeam::where('email', request()->email)->first();
        // // do the passwords match?
        // if (!Hash::check(request()->password, $user->password)) {
        //     // no they don't
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        // // log the user in (needed for future requests)
        // Auth::login($user);
        // // get new token
        // $tokenResult = $user->createToken('office-pantry-passport');
        // // return token in json response
        // return response()->json(['success' => ['token' => $tokenResult->accessToken]], 200);
        
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
