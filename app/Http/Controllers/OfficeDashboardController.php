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

use App\WeekStart;

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

    public function __construct()
    {
        $week_start = WeekStart::first();

        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }
    }

    public function show(CompanyDetails $company)
    {
        //dd($this->week_start);



        //---------- Set an array to sort orders by day ----------//

        $monToFri = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        //---------- Company ----------//

        // This is just to have a default fruit partner which is used to pre-populate box forms.
        $company->load('fruit_partner')->get();

        //---------- Fruitboxes ----------//

        $fruitboxes = $company->fruitbox()->where('delivery_week', '>=', $this->week_start)->get();

        foreach ($fruitboxes as $fruitbox) {

            // using the established relationship between models i.e FruitBox belongsTo FruitPartner
            $fruitbox->load('fruit_partner')->get();

        }

        $fruitboxesByMonToFri = $fruitboxes->sortBy( function ($fruitboxes) use ($monToFri) {
            return array_search($fruitboxes->delivery_day, $monToFri);
        });

        // dd($fruitboxes);

        //---------- Milkboxes ----------//

        $milkboxes = $company->milkbox;

        foreach ($milkboxes as $milkbox) {

            $milkbox->load('fruit_partner')->get();

        }

        $milkboxesByMonToFri = $milkboxes->sortBy( function ($milkboxes) use ($monToFri) {
            return array_search($milkboxes->delivery_day, $monToFri);
        });

        //---------- Routes ----------//

        $routes = $company->company_routes;

        // Use day of the week to order the routes mon-fri
        $routesByMonToFri = $routes->sortBy( function ($routes) use ($monToFri) {
            return array_search($routes->delivery_day, $monToFri);
        });

        //dd($ordersByMonToFri);

        //---------- Snackboxes (New Approach) ----------//

        $snackboxes = $company->snackboxes->where('delivery_week', '>=', $this->week_start);
     
        foreach ($snackboxes as $snackbox) {
            // Load relationship info, making box_items available.
            $snackbox->load(['box_items' => function ($query) use ($snackbox) {
                    $query->where('delivery_date', $snackbox->delivery_week);
            }]);

            foreach ($snackbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        //dd($snackboxes);

        //---------- Drinkboxes (New Approach) ----------//

        $drinkboxes = $company->drinkboxes->where('delivery_week', '>=', $this->week_start);
     
        foreach ($drinkboxes as $drinkbox) {
            // Load relationship info, making box_items available.
            $drinkbox->load(['box_items' => function ($query) use ($drinkbox) {
                    $query->where('delivery_date', $drinkbox->delivery_week);
            }]);

            foreach ($drinkbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        //---------- Otherboxes (New Approach) ----------//

        $otherboxes = $company->otherboxes->where('delivery_week', '>=', $this->week_start);
     
        foreach ($otherboxes as $otherbox) {
            // Load relationship info, making box_items available.
            $otherbox->load(['box_items' => function ($query) use ($otherbox) {
                    $query->where('delivery_date', $otherbox->delivery_week);
            }]);

            foreach ($otherbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        //---------- Preferences ----------//

        $preferences = $company->preference;
        $allergy_infos = $company->allergy_info;

        foreach ($allergy_infos as $allergy_info) {
            $allergy_info->load('allergy');
        }
        //dd($allergies); This is an empty array if there are no allergies associated yet.
        $additional_info = $company->additional_info;
        // dd($additional_info);

        //---------- Archived Fruitboxes ----------//

        // Edit - 02/03/2020
        // $archived_fruitboxes = $company->fruitbox_archive()->where('is_active', 'Active')->get();
        $archived_fruitboxes = $company->fruitbox()->where('delivery_date', '<',  $this->week_start)->where('invoiced_at', null)->get();

        foreach ($archived_fruitboxes as $archived_fruitbox) {
            $archived_fruitbox->load('fruit_partner')->get();
        }

        $order_by_delivery_week_archived_fruitboxes = $archived_fruitboxes->sortBy('delivery_date');

        //---------- Archived Milkboxes ----------//

        $archived_milkboxes = $company->milkbox()->where('delivery_date', '<',  $this->week_start)->where('invoiced_at', null)->get();

        foreach ($archived_milkboxes as $archived_milkbox) {
            $archived_milkbox->load('fruit_partner')->get();
        }

        $order_by_delivery_week_archived_milkboxes = $archived_milkboxes->sortBy('delivery_date');

        //---------- Archived Snackboxes (New Approach) ----------//

        $archived_snackboxes = $company->snackboxes->where('delivery_week', '<', $this->week_start)->where('invoiced_at', null);
     
        foreach ($archived_snackboxes as $archived_snackbox) {
            // Load relationship info, making box_items available.
            $archived_snackbox->load(['box_items' => function ($query) use ($archived_snackbox) {
                    $query->where('delivery_date', $archived_snackbox->delivery_week);
            }]);

            foreach ($archived_snackbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        //---------- Archived Drinkboxes ----------//

        $archived_drinkboxes = $company->drinkboxes->where('delivery_week', '<', $this->week_start)->where('invoiced_at', null);
     
        foreach ($archived_drinkboxes as $archived_drinkbox) {
            // Load relationship info, making box_items available.
            $archived_drinkbox->load(['box_items' => function ($query) use ($archived_drinkbox) {
                    $query->where('delivery_date', $archived_drinkbox->delivery_week);
            }]);

            foreach ($archived_snackbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        //---------- Archived Otherboxes ----------//

        $archived_otherboxes = $company->otherboxes->where('delivery_week', '<', $this->week_start)->where('invoiced_at', null);
     
        foreach ($archived_otherboxes as $archived_otherbox) {
            // Load relationship info, making box_items available.
            $archived_otherbox->load(['box_items' => function ($query) use ($archived_otherbox) {
                    $query->where('delivery_date', $archived_otherbox->delivery_week);
            }]);

            foreach ($archived_snackbox->box_items as $box_item) {
                // Load product info for each box_item.
                $box_item->load('product');
            }
        }

        return [
                    'company' => $company, 'fruitboxes' => $fruitboxesByMonToFri->values(), 'milkboxes' => $milkboxesByMonToFri->values(), 'routes' => $routesByMonToFri->values(),
                    'snackboxes' => $snackboxes, 'drinkboxes' => $drinkboxes, 'otherboxes' => $otherboxes,
                    'preferences' => $preferences,
                    'allergies' => $allergy_infos,
                    'additional_info' => $additional_info,
                    'archived_fruitboxes' => $order_by_delivery_week_archived_fruitboxes->values(),
                    'archived_milkboxes' => $order_by_delivery_week_archived_milkboxes->values(),
                    'archived_snackboxes' => $archived_snackboxes,
                    'archived_drinkboxes' => $archived_drinkboxes, 
                    'archived_otherboxes' => $archived_otherboxes
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
