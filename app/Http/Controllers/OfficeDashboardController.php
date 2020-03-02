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

        // THIS ISN'T TAKING ADVANTAGE OF EAGER LOADING, AS I'M NOT MAKING THE INITIAL DB QUERY HERE, I DON'T WANT TO SPEND TIME REFACTORING EVERYTHING.
        // INSTEAD I'LL MAKE IT WORK LIKE THIS FOR NOW AND THEN, WHEN THE NEED OR TIME IS GREATER I CAN REDO IT.
        // YES I KNOW I ALWAYS SAY THIS, IT ISN'T MY FAULT!

            // $snackboxes = $company->snackboxes;
            // foreach ($snackboxes as $snackbox) {
            //     if ($snackbox->allergies_and_dietary_requirements) {
            //         // dd($snackbox->allergies_and_dietary_requirements);
            //     }
            //
            // }

            //---------- Set an array to sort orders by day ----------//

            $monToFri = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];


            //---------- Fruitboxes ----------//

            $fruitboxes = $company->fruitbox()->where('next_delivery', '>=', $this->week_start)->get();
            //dd($fruitboxes);
            // $fruitpartner->name will break if there's a box retrieved without a fruitpartner
            // but this shouldn't be an issue if a placeholder fruitpartner is given when the box is created,
            // should the actual fruitpartner not be known at that point.
            foreach ($fruitboxes as $fruitbox) {

                $fruitbox->load('fruit_partner')->get();
                // dd($fruitbox->fruit_partner->name);
                // // dd($fruitbox['id']);
                // $fruitpartner_id = $fruitbox['fruit_partner_id'];
                // $fruitpartner = FruitPartner::find($fruitpartner_id);
                // // dd($fruitpartner);
                // $fruitpartner_name = $fruitpartner->name;
                // $fruitbox->fruit_partner_name = $fruitpartner_name;
            }

            $fruitboxesByMonToFri = $fruitboxes->sortBy( function ($fruitboxes) use ($monToFri) {
                return array_search($fruitboxes->delivery_day, $monToFri);
            });

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

            $milkboxesByMonToFri = $milkboxes->sortBy( function ($milkboxes) use ($monToFri) {
                return array_search($milkboxes->delivery_day, $monToFri);
            });

            //---------- Routes ----------//

            $routes = $company->company_routes;
            //dump($routes);
            //foreach ($routes as $route) {
                // Use day of the week to order the routes mon-fri
                $routesByMonToFri = $routes->sortBy( function ($routes) use ($monToFri) {
                    return array_search($routes->delivery_day, $monToFri);
                });
            //}
            //dd($ordersByMonToFri);


            //---------- Snackboxes ----------//

            // // This starts off as a list of snackbox items but we want them grouped by the snackbox_id, so we need to do that either in the snackbox-admin component or here.
            // // Let's try doing it here first.
            // $snackbox_items = $company->snackboxes;
            // $snackboxes = $snackbox_items->groupBy('snackbox_id');
            // // While snackboxes are using the hardcoded options we don't need to add the fruitpartner name along with the id.  If/when this changes the foreach loop will be needed too.
            //  foreach ($snackboxes as $snackbox) {
            //     // Check to see if this box has any specific allergy requirements.
            //      if ($snackbox[0]->allergies_and_dietary_requirements) {
            //         // If it does we can pass it into the snackbox[0] entry.
            //         // We always use snackbox[0] to display box specific information because we can guarantee it will be there if there's an active box.
            //         // This looks a little odd because the value on the right is a relationship between models and the left is a just the creation of another property on the object! :)
            //         // EDIT: NOT SURE I ACTUALLY NEED TO DO THIS AS THE RELATIONSHIP SEEMS TO BE AVAILABLE ALREADY, NEXT QUESTION IS CAN I ACCESS THE RELATIONSHIP IN THE SNACKBOX.VUE COMPONENT AS WELL?
            //         // EDIT: WE SURE CAN!!!! UPDATE: ERR ACTUALLY NOW IT SEEMS NOT, MAYBE THE OLD VALUES WERE STILL CACHED?!!! BALLS.
            //          $snackbox[0]->allergies = $snackbox[0]->allergies_and_dietary_requirements['allergy'];
            //          $snackbox[0]->dietary_requirements = $snackbox[0]->allergies_and_dietary_requirements['dietary_requirements'];
            //      }
            //
            //  }

             //---------- Snackboxes New Approach ----------//

             $snackboxes = $company->snackboxes->where('invoiced_at', null);
             // foreach ($snackbox->box_items->where('delivery_date', $snackbox->next_delivery_week) as $order_item) {
             //     dump($order_item->product->brand . ' ' . $order_item->product->flavour);
             // }
             foreach ($snackboxes as $snackbox) {
                // Load relationship info, making box_items available.
                $snackbox->load(['box_items' => function ($query) use ($snackbox) {
                     $query->where('delivery_date', $snackbox->next_delivery_week);
                }]);
                foreach ($snackbox->box_items as $box_item) {
                    // Load product info for each box_item.
                    $box_item->load('product');
                }
             }


             //dd($snackboxes);


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
            $archived_fruitboxes = $company->fruitbox()->where('next_delivery', '<',  $this->week_start)->get();

            foreach ($archived_fruitboxes as $archived_fruitbox) {
                $fruitpartner_id = $archived_fruitbox['fruit_partner_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                // dd($fruitpartner);
                $fruitpartner_name = $fruitpartner->name;
                $archived_fruitbox->fruit_partner_name = $fruitpartner_name;
            }

            // WOULD WE RATHER SORT THE ARCHIVED ENTRIES BY DAY, DELIVERY DATE OR LET ME GUESS, BOTH?

            // Actually using groupby change the way the data is structured, requiring frontend changes too.  I'd rather it was ordered here in the expected format.
            // 1)
            // Nope this attempt to use sort, orders them by next_delivery but I can't get it to pull in the monToFri array_search to order by day.
            // $archived_fruitboxes_ordered = $archived_fruitboxes->sort(function ($a, $b) use ($monToFri) {
            //     if ($a->next_delivery === $b->next_delivery) {
            //         return [
            //             array_search($a, $monToFri),
            //             array_search($b, $monToFri)
            //         ];
            //     }
            //     return $a->next_delivery < $b->next_delivery ? -1 : 1;
            // });

            // $archived_fruitboxes_monToFri = $archived_fruitboxes->sortBy( function ($archived_fruitboxes) use ($monToFri) {
            //     return array_search($archived_fruitboxes->delivery_day, $monToFri);
            // });

            // Urghh, just next_delivery for now, I'll play with this again another time.
            $order_by_delivery_week_archived_fruitboxes = $archived_fruitboxes->sortBy('next_delivery');

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

            // Currently using hardcoded dropdown of options OP, APC and dpd
            // but this will be the additional code needed when switching to fruitpartners id/name

            // foreach ($archived_snackbox_items as $archived_snackbox_item) {
            //     $fruitpartner_id = $archived_snackbox_item['fruit_partner_id'];
            //     $fruitpartner = FruitPartner::find($fruitpartner_id);
            //     $fruitpartner_name = $fruitpartner->name;
            //     $archived_snackbox_item->fruit_partner_name = $fruitpartner_name;
            // }

            $archived_snackboxes = $archived_snackbox_items->groupBy(['snackbox_id', 'next_delivery_week']);
            //dump($archived_snackboxes);

            //---------- Archived Drinkboxes ----------//

            $archived_drinkbox_items = $company->drinkbox_archive;

            foreach ($archived_drinkbox_items as $archived_drinkbox_item) {
                $fruitpartner_id = $archived_drinkbox_item['delivered_by_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                $fruitpartner_name = $fruitpartner->name;
                $archived_drinkbox_item->fruit_partner_name = $fruitpartner_name;
            }

            $archived_drinkboxes = $archived_drinkbox_items->groupBy(['drinkbox_id', 'next_delivery_week']);

            //---------- Archived Otherboxes ----------//

            $archived_otherbox_items = $company->otherbox_archive;

            foreach ($archived_otherbox_items as $archived_otherbox_item) {
                $fruitpartner_id = $archived_otherbox_item['delivered_by_id'];
                $fruitpartner = FruitPartner::find($fruitpartner_id);
                $fruitpartner_name = $fruitpartner->name;
                $archived_otherbox_item->fruit_partner_name = $fruitpartner_name;
            }

            $archived_otherboxes = $archived_otherbox_items->groupBy(['otherbox_id', 'next_delivery_week']);

             // dd($snackboxes);
        // return view('companies', ['companies' => $company, 'fruitboxes' => $fruitboxes, 'milkboxes' => $milkboxes, 'routes' => $routes]);
        return [
                    'company' => $company, 'fruitboxes' => $fruitboxesByMonToFri->values(), 'milkboxes' => $milkboxesByMonToFri->values(), 'routes' => $routesByMonToFri->values(),
                    'snackboxes' => $snackboxes, 'drinkboxes' => $drinkboxes, 'otherboxes' => $otherboxes,
                    'preferences' => $preferences,
                    'allergies' => $allergy_infos,
                    'additional_info' => $additional_info,
                    'archived_fruitboxes' => $order_by_delivery_week_archived_fruitboxes->values(), 'archived_milkboxes' => $archived_milkboxes, 'archived_snackboxes' => $archived_snackboxes,
                    'archived_drinkboxes' => $archived_drinkboxes, 'archived_otherboxes' => $archived_otherboxes
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
