<?php

namespace App\Http\Controllers\Auth;

use App\User;

use App\OfficeTeam;
use App\WarehouseTeam;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
// added for token authentication in laravel docs - 29/8/19
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
        $this->middleware('guest:office');
        $this->middleware('guest:warehouse');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Same process as in the login Controller, determining the template used for the newly registered url paths.
    public function showOfficeRegisterForm()
    {
        return view('auth.register', ['url' => 'office']);
    }

    // See above.
    public function showWarehouseRegisterForm()
    {
        return view('auth.register', ['url' => 'warehouse']);
    }
    
    // Validate the new admin info and create suitable entries as new admins
    protected function createOffice(Request $request)
    {
        $this->validator($request->all())->validate();
        $admin = OfficeTeam::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'api_token' => Str::random(60),
        ]);

        return redirect()->intended('login/office');
    }

    // Validate the new warehouse user info and create suitable entries as new operatives
    protected function createWarehouse(Request $request)
    {
        $this->validator($request->all())->validate();
        $writer = WarehouseTeam::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return redirect()->intended('login/warehouse');
    }
}
