<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Company;
use App\User;
use App\FruitBox;
use App\MilkBox;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // dd(Auth::user());

    }

    // /**
    //  * Show the application dashboard.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()
    // {
    //     dd(Auth::user());
    //     return view('home');
    // }

    public function showCompany()
    {
        // dd(Auth::user()->id);
        $user = User::find(Auth::user()->id);
        // dd($user);
         // $user->companies()->attach(899);
         // $user->companies()->attach(934);
         // $user->companies()->sync(899, false);
         // $user->companies()->syncWithoutDetaching(899);
         // $user->companies()->attach(934);
        // if ($user !== null) {
            // dd($user);
            $companies = $user->companies;
             // dd($companies);
            $i = 0;
            $fruitboxes = [];
            $milkboxes = [];
            $routes = [];
            $user_associated_companies = [];
            foreach ($companies as $company)
            {
                $user_associated_companies[$company->id] = $company->invoice_name;
                // dd($company);
                $milkboxes[] = $company->milkbox;
                $milkboxes[$i]->prepend($company->invoice_name, 'company_name');


                $fruitboxes[] = $company->fruitbox;
                $fruitboxes[$i]->prepend($company->invoice_name, 'company_name');

                $routes[] = $company->route;
                $routes[$i]->prepend($company->invoice_name, 'company_name');

                $i++;
            }
        // }

        // dd($user_associated_companies);
         // dd($fruitboxes);
        //
        // dd(Auth::user()->company_id);
        // $companies = Company::all()->where('company_id', );
        return view ('home', ['companies' => $companies, 'fruitboxes' => $fruitboxes, 'milkboxes' => $milkboxes, 'routes' => $routes, 'user_associated_companies' => $user_associated_companies]);
    }
}
