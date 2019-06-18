<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Company;
use App\CompanyDetails;
use App\User;
use App\FruitBox;
use App\MilkBox;
use App\Product;
use App\Preference;
use App\FruitPartner;

class OfficeDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:office');
    //     // dd(Auth::user());
    //
    // }

    // /**
    //  * Show the application dashboard.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()+
    // {
    //     dd(Auth::user());
    //     return view('home');
    // }

    public function show(CompanyDetails $company)
    {
            //dd($company);
            
            //---------- Fruitboxes ----------//
            
            $fruitboxes = $company->fruitbox;
            //dd($fruitboxes);
            // $fruitpartner->name will break if there's a box retrieved without a fruitpartner
            // but this shouldn't be an issue if a placeholder fruitpartner is given when the box is created, 
            // should the actual fruitpartner not be known at that point.
            foreach ($fruitboxes as $fruitbox) {
                // dd($fruitbox['id']);
                $fruitpartner_id = $fruitbox['fruit_partner_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                // dd($fruitpartner);
                $fruitpartner_name = $fruitpartner->name;
                $fruitbox->fruit_partner_name = $fruitpartner_name;
            }
            // dd($fruitboxes);
            
            //---------- Milkboxes ----------//
            
            $milkboxes = $company->milkbox;
            
            // $fruitpartner->name will break if there's a box retrieved without a fruitpartner
            // but this shouldn't be an issue if a placeholder fruitpartner is given when the box is created, 
            // should the actual fruitpartner not be known at that point.
            foreach ($milkboxes as $milkbox) {
                // dd($fruitbox['id']);
                $fruitpartner_id = $milkbox['fruit_partner_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                // dd($fruitpartner);
                $fruitpartner_name = $fruitpartner->name;
                $milkbox->fruit_partner_name = $fruitpartner_name;
            }
            
            //---------- Routes ----------//
            
            $routes = $company->company_routes;
            
            
            //---------- Snackboxes ----------//
            
            // This starts off as a list of snackbox items but we want them grouped by the snackbox_id, so we need to do that either in the snackbox-admin component or here.
            // Let's try doing it here first.
            $snackbox_items = $company->snackboxes;
            $snackboxes = $snackbox_items->groupBy('snackbox_id');
            // While snackboxes are using the hardcoded options we don't need to add the fruitpartner name along with the id.  If/when this changes the foreach loop will be needed too.
            
            //---------- Drinkboxes ----------//
            
            // And now do the same with Drinks but include the fruitpartner name.
            $drinkbox_items = $company->drinkboxes;
            $drinkboxes = $drinkbox_items->groupBy('drinkbox_id');
            
            foreach ($drinkboxes as $drinkbox) {
                $fruitpartner_id = $drinkbox[0]->delivered_by_id;
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                $drinkbox[0]->fruit_partner_name = $fruitpartner->name;
            }
            
            //---------- Otherboxes ----------//
            
            // and Other
            $otherbox_items = $company->otherboxes;
            $otherboxes = $otherbox_items->groupBy('otherbox_id');
            
            foreach ($otherboxes as $otherbox) {
                $fruitpartner_id = $otherbox[0]->delivered_by_id;
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                $otherbox[0]->fruit_partner_name = $fruitpartner->name;
            }
            //dd($drinkboxes);
            
            //---------- Preferences ----------//
            
            $preferences = $company->preference;
            $allergies = $company->allergy;
            $additional_info = $company->additional_info;
            // dd($additional_info);
            
            //---------- Archived Fruitboxes ----------//
            
            $archived_fruitboxes = $company->fruitbox_archive()->where('is_active', 'Active')->get();
            
            foreach ($archived_fruitboxes as $archived_fruitbox) {
                $fruitpartner_id = $archived_fruitbox['fruit_partner_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                // dd($fruitpartner);
                $fruitpartner_name = $fruitpartner->name;
                $archived_fruitbox->fruit_partner_name = $fruitpartner_name;
            }
            
            //---------- Archived Milkboxes ----------//
            
            $archived_milkboxes = $company->milkbox_archive()->where('is_active', 'Active')->get();
            
            foreach ($archived_milkboxes as $archived_milkbox) {
                $fruitpartner_id = $archived_milkbox['fruit_partner_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                // dd($fruitpartner);
                $fruitpartner_name = $fruitpartner->name;
                $archived_milkbox->fruit_partner_name = $fruitpartner_name;
            }
            
            //---------- Archived Snackboxes ----------//
            
            $archived_snackbox_items = $company->snackbox_archive;
            $archived_snackboxes = $archived_snackbox_items->groupBy('snackbox_id');
            
            // dd($company);
        // return view('companies', ['companies' => $company, 'fruitboxes' => $fruitboxes, 'milkboxes' => $milkboxes, 'routes' => $routes]);
        return [    
                    'company' => $company, 'fruitboxes' => $fruitboxes, 'milkboxes' => $milkboxes, 'routes' => $routes,
                    'snackboxes' => $snackboxes, 'drinkboxes' => $drinkboxes, 'otherboxes' => $otherboxes,
                    'preferences' => $preferences, 'allergies' => $allergies, 'additional_info' => $additional_info, 
                    'archived_fruitboxes' => $archived_fruitboxes, 'archived_milkboxes' => $archived_milkboxes, 'archived_snackboxes' => $archived_snackboxes,
                ];
    }

    public function destroy($id) {
        Preference::destroy($id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProduct(Product $id)
    {
        //
        $product = $id;

        return ['product' => $product];
    }


    public function showSelectedCompanyData(Request $request)
    {
        // Instead of grabbing the user id to pull company info, we need to use the company id taken from the company searchbar.
        // $user = User::find(Auth::user()->id);
        // dd($request);
        // $companies = $user->companies;
        // dd($companies);
        $company_details_id = $request['params']['company'];

        // $company = Company::find($company_id);
        $company = CompanyDetails::find($company_details_id);

        if (empty($company)) {
             $company = ['name' => 'None Selected'];
            // $company = new Company;

        }

        // $i = 0;
        $fruitboxes = [];
        $milkboxes = [];
        $routes = [];
        $snackboxes = [];
        // $user_associated_companies = [];

        // foreach ($companies as $company)
        // {
        //     $user_associated_companies[$company->id] = $company->invoice_name;
            // dd($company);
            if (!empty($company->milkbox)) {
                $milkboxes[] = $company->milkbox;
            } else {
                $milkboxes[] = ['name' => 'None Selected'];
            }

            //$milkboxes[0]->prepend($company->invoice_name, 'company_name');

            if (!empty($company->fruitbox)) {
                $fruitboxes[] = $company->fruitbox;
            } else {
                $fruitboxes[] = ['name' => 'None Selected'];
            }

            //$fruitboxes[0]->prepend($company->invoice_name, 'company_name');

            if (!empty($company->route)) {
                $routes[] = $company->route;
            } else {
                $routes[] = ['name' => 'None Selected'];
            }

            if (!empty($company->snackbox)) {
                $snackboxes[] = $company->snackbox;
            } else {
                $snackboxes[] = ['name' => 'None Selected'];
            }
            //$routes[0]->prepend($company->invoice_name, 'company_name');
        //
        //     $i++;
        // }

        // dd($user_associated_companies);
          // dd($company);
        //
        // dd(Auth::user()->company_id);
        // $companies = Company::all()->where('company_id', );
        return ['company' => $company, 'fruitboxes' => $fruitboxes, 'milkboxes' => $milkboxes, 'routes' => $routes, 'snackboxes' => $snackboxes];
    }
}
