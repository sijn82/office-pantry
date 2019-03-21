<?php

namespace App\Http\Controllers;

use Sessions;

use App\Http\Controllers\Exports;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Route;
use App\FruitOrderingDocument;
use App\WeekStart;
use App\Company;

// Function Index                                                                   Changes for Picklist and Routing

// public function __construct()                                - 36                - 38 (Change $week_start date)
// public function download()                                   - 42
// public function index()                                      - 55
// public function create()                                     - 70
// public function updateRouteAndPosition(Request $request)     - 75                - 90/91 (Change the url for file to either regular or wed-thur-fri option)
// public function addDrinksNSnacksToRoute(Request $request)          - 352         - 363/364 (Change the url for file to either regular or wed-thur-fri option)
// public function store(Request $request)                      - 592
// public function update(Request $request)                     - 672   (url - update-routing)


class RoutesController extends Controller
{
    protected $week_start;
    protected $delivery_days;

    public function __construct()
    {
       $week_start = WeekStart::all()->toArray();
       $this->week_start = $week_start[0]['current'];
       $this->delivery_days = $week_start[0]['delivery_days'];
    }

    public function download()
    {
        // $this->week_start = week_start($this->week_start);
        // return (new Exports\RoutesExport($this->week_start))->download('routesheets.xlsx');
        return \Excel::download(new Exports\RoutesExport($this->week_start), 'routelists' . $this->week_start . '.xlsx');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // phpinfo();
        //
          $routes = Route::all();
          $assigned_route = Route::select('assigned_to')->distinct()->get();

                return view ('display-routes', ['routes' => $routes, 'assigned_route' => $assigned_route]);
    }

    public function reset()
    {
        // This function is a backup for if anything goes wrong when updating the routes.
        // In order to be able to run the update function again without multiplying the values,
        // we must get rid of any week start values for the current week.

        $routes = Route::all()->where('week_start', $this->week_start);
        // dd($routes);
        foreach ($routes as $route)
        {
            // echo $route->week_start;
            Route::where('company_name', $route->company_name)
              ->update([

                'week_start' => '2018-01-01',

              ]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function uploadRejiggedRoutes(Request $request)
    {
        // save the file with either mon-tue or wed-thur-fri in the name
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->rejigged_routes_csv);
        // now we can decode the remainder of the encoded data string
        $requestcsv = base64_decode($request_mime_fix);
        // however it now has some unwated unicode characters i.e the 'no break space' - (U+00A0) and use json_encode to make them visible
        $csv_data_with_unicode_characters = json_encode($requestcsv);
        // now they're no longer hidden characters, we can strip them out of the data
        $csv_data_fixed = str_replace('\u00a0', ' ', $csv_data_with_unicode_characters);
        // and return the file ready for storage
        $ready_csv = json_decode($csv_data_fixed);

        // this is how we determine where to put the file, these variables are populated with the $week_start variable at the top of this class
        // and the request parameters attached to the form on submission.
        Storage::put('public/rejigged-routing/rejigged-routing-' . $this->week_start . '-' . $delivery_days . '-noheaders-utf8-nobom.csv', $ready_csv);
    }

    public function updateRouteAndPosition(Request $request)
    {

        // $request->session()->put('delivery_days', $request->delivery_days);
        //
        // dd($request->session()->get('delivery_days'));

      // Get all existing route information
      $routes = Route::where('week_start', $this->week_start)->get();
      // Just grab Company Names to quickly check if company has received previous deliveries
      $route_company_names = Route::pluck('company_name')->all();

      // Variables for logging and sending to slack
      $regular_rejig = "*Regular Route Entry Rejig* \n";
      $special_delivery = "*Regular Special Delivery (Dairy/Organic Fruit Collection)* \n";
      $new_special_delivery = "*New Special Delivery Entry* \n";
      $last_minute_entry_regular_day = "*Last minute entry but it's a known route/day combo* \n";
      $last_minute_entry_new_day = "*Last minute entry for a known route but on a different day* \n";
      $last_minute_completely_new_entry = "*Last minute entry for a new company* \n";

      // var_dump($route_company_names);
      // dd(array_map('strlen', $route_company_names));

        if (($handle = fopen('../storage/app/public/rejigged-routing/rejigged-routing-' . $this->week_start . '-' . $this->delivery_days . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
        // if (($handle = fopen(public_path() . '/rejigged-routing/rejigged-routing-' . $this->week_start . '-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

            while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            // Find that company data and update snacks and drinks column.
            // But only if it matches both the week_start and delivery day.

            $company_name_encoded = json_encode($data[1]);
            $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
            $company_name = json_decode($company_name_fixed);
            $company_name = trim($company_name);

            $currentRoutingEntry = Route::where('company_name', $company_name)->where('week_start', $this->week_start)
                ->where('delivery_day', $data[26])->get();

            $knownRoute = Route::where('company_name', $company_name)->get();
            $knownRouteAndDay = Route::where('company_name', $company_name)->where('delivery_day', $data[26])->get();
            $specialDelivery = ['DAIRY', 'office pantry', 'choice organics'];

                // Only worry about successfully retrieved routes, otherwise this variable (array) will be empty
                // EDIT: Added the string check to prevent any of these 3 options getting reassigned to another route by accident, when we actually want another entry if it doesn't already exist.
                if ((count($currentRoutingEntry) > 0) && (!in_array(strtolower($company_name), $specialDelivery))) { // this doesn't seem to be working right?
                    // var_dump((count($currentRoutingEntry) !== 0) && (!in_array(strtolower($company_name), $specialDelivery)));
                        echo strtolower($company_name) . '<br>';
                    // If it isn't empty, grab the company name and delivery day as currently listed in database.
                    $selectedCompany = $currentRoutingEntry[0]->company_name;
                    $selectedtDeliveryDay = $currentRoutingEntry[0]->delivery_day;

                    // Grab the routing data, update the snacks and drinks columns where company name and day of delivery match the updating data.
                      Route::where('company_name', trim($company_name))->where('delivery_day', $data[26])->where('week_start', $this->week_start)
                        ->update([

                          // Update the route address and delivery information to match any updates Nick has made in rejigged routes.
                          'postcode' => $data[2],
                          'address' => $data[3],
                          'delivery_information' => $data[4],

                          // Update drinks, snacks and other column with actual values where applicable.
                          'drinks'	=> $data[22],
                          'snacks'	=> $data[23],
                          'other' => $data[24],

                          // Update the route and position on route to match any updates Nick has made in rejigged routes.
                          'assigned_to' => $data[25],
                          'position_on_route' => $data[27],

                      ]);

                      $regular_rejig .= 'Entry for ' . $company_name  .  ' found for ' . $data[26] .  ' and has been updated to correct route i.e ' . $data[25] . " \n";
                      // break;

                      // Note: We also need to take care of the office pantry, park view dairy and choice organics entries which may need to appear on more than one route assigned_to/delivery_day.
                  }  elseif (in_array(strtolower($company_name), $specialDelivery)) {

                            $existingSpecialRoute = Route::where('company_name', $company_name)->where('assigned_to', $data[24])->where('delivery_day', $data[26]);

                            // If special route is populated we have a known match for that company_name/assigned_to (route)/delivery day.
                            // Unlike our usual behavior we want to keep this entry on that route but we can update the position as necessary.
                            // Importantly, we want to make sure these entries are (and remain) unique to that route only.
                            if (!empty($existingSpecialRoute)) {


                                Route::where('company_name', $company_name)->where('assigned_to', $data[25])->where('delivery_day', $data[26])
                                  ->update([

                                    'week_start'	=> $data[0], // we want to ensure that if this route has been included the week_start is updated too.  This will ensure that if we don't need the entry one week, we have a check that can filter it out temporarily.
                                    'position_on_route' => $data[27],

                                ]);
                                $special_delivery .= 'Updating entry found for ' . $company_name . ' on route ' . $data[25] . ' at position ' . $data[27] . " \n";
                                // break;

                              // If $existingSpecialRoute returned empty we don't have a matching entry and need to create one for that company/route/day combo.
                            } else {

                                $newSpecialRoute = new Route();
                                $newSpecialRoute->week_start = $data[0];
                                $newSpecialRoute->company_name = $company_name;
                                $newSpecialRoute->postcode = $data[2];
                                $newSpecialRoute->address = $data[3]; // Condensed values of address_line_1, address_line_2, city and county.
                                $newSpecialRoute->delivery_information = $data[4];
                                $newSpecialRoute->fruit_crates = $data[5];
                                $newSpecialRoute->fruit_boxes = $data[6];
                                $newSpecialRoute->milk_2l_semi_skimmed = $data[7];
                                $newSpecialRoute->milk_2l_skimmed = $data[8];
                                $newSpecialRoute->milk_2l_whole = $data[9];
                                $newSpecialRoute->milk_1l_semi_skimmed = $data[10];
                                $newSpecialRoute->milk_1l_skimmed = $data[11];
                                $newSpecialRoute->milk_1l_whole = $data[12];
                                $newSpecialRoute->milk_1l_alt_coconut = $data[13]; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                $newSpecialRoute->milk_1l_alt_unsweetened_almond = $data[14];
                                $newSpecialRoute->milk_1l_alt_almond = $data[15];
                                $newSpecialRoute->milk_1l_alt_unsweetened_soya = $data[16];
                                $newSpecialRoute->milk_1l_alt_soya = $data[17];
                                //$newSpecialRoute->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
                                //$newSpecialRoute->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
                                //$newSpecialRoute->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
                                $newSpecialRoute->milk_1l_alt_lactose_free_semi = $data[21];

                                // As we have already run the snacks and drinks import, and the only record of this info might be on the route we'll grab the data and add it here for once.
                                // Although as the dairy and office pantry are only totals generated from other entries we only need to worry about choice organics.

                                if ('choice organics' == strtolower($company_name)) {
                                      $newSpecialRoute->drinks = $data[22];
                                      $newSpecialRoute->snacks = $data[23];
                                      $newSpecialRoute->other = $data[24];
                                }

                                $newSpecialRoute->assigned_to = $data[25];
                                $newSpecialRoute->delivery_day = $data[26];
                                $newSpecialRoute->position_on_route = $data[27];

                                // Expected Time and Delivery Needed By columns to be either returned empty
                                // or (more likely) Delivery Needed By will be held in the company model.
                                $newSpecialRoute->save();

                                $new_special_delivery .= 'No SPECIAL entry found for ' . $company_name . ' on route ' . $data[25] . '. Adding now at position ' . $data[26] . ". \n";
                                // echo 'Adding the <strong style="color: green";>' . $company_name . '</strong> on route <strong style="color: green";>' . $data[25] . '</strong> for <strong style="color: green";>' . $data[26] . '</strong> now.<br>';
                                // break;
                            }

                     // Sometimes the entry is a last minute addition to the routes but we have a record of them in the routes, and on that delivery day. (last minute reversal on a cancellation for example) (UPDATE ROUTE ENTRY)
                     // Sometimes this may be an entry where we know the company (on routes) but don't have an entry for that delivery day. (CREATE ROUTE ENTRY)
                     // Sometimes it may be a last minute entry for a company we have never delivered to before, lets create the item but flag the fact we have done this. (CREATE ROUTE ENTRY)
                  } elseif (count($knownRouteAndDay) > 0) {

                      // Update last minute entry for known company

                      Route::where('company_name', $company_name)->where('delivery_day', $data[26])
                        ->update([

                          'week_start'	=> $data[0],
                          // 'company_name'	= $data[1]
                          'postcode'	=> $data[2],
                          'address'	=> $data[3],
                          'delivery_information'	=> $data[4],
                          'fruit_crates'	=> $data[5],
                          'fruit_boxes'	=> $data[6],
                          'milk_2l_semi_skimmed'	=> $data[7],
                          'milk_2l_skimmed'	=> $data[8],
                          'milk_2l_whole'	=> $data[9],
                          'milk_1l_semi_skimmed'	=> $data[10],
                          'milk_1l_skimmed'	=> $data[11],
                          'milk_1l_whole'	=> $data[12],
                          'milk_1l_alt_coconut'	=> $data[13],
                          'milk_1l_alt_unsweetened_almond'	=> $data[14],
                          'milk_1l_alt_almond'	=> $data[15],
                          'milk_1l_alt_unsweetened_soya'	=> $data[16],
                          'milk_1l_alt_soya'	=> $data[17],
                          // Oat Milk	= $data[18]
                          // Rice Milk	= $data[19]
                          // Cashew Milk	= $data[20]
                          'milk_1l_alt_lactose_free_semi'	=> $data[21],
                          'drinks'	=> $data[22],
                          'snacks'	=> $data[23],
                          'other' => $data[24],
                          'assigned_to' => $data[25],
                          'delivery_day' => $data[26],
                          'position_on_route' => $data[27],

                      ]);

                      $last_minute_entry_regular_day .= 'Adding ' . $company_name . ' on ' . $data[26] . 'onto route ' . $data[25] . 'at positon ' . $data[27] . " \n";
                      // break;

                    } elseif (count($knownRoute) > 0) {

                      // We know the company but this last minute addition is new for this day, create a new entry

                      $newSpecialRoute = new Route();
                      $newSpecialRoute->week_start = $data[0];
                      $newSpecialRoute->company_name = $company_name;
                      $newSpecialRoute->postcode = $data[2];
                      $newSpecialRoute->address = $data[3]; // Condensed values of address_line_1, address_line_2, city and county.
                      $newSpecialRoute->delivery_information = $data[4];
                      $newSpecialRoute->fruit_crates = $data[5];
                      $newSpecialRoute->fruit_boxes = $data[6];
                      $newSpecialRoute->milk_2l_semi_skimmed = $data[7];
                      $newSpecialRoute->milk_2l_skimmed = $data[8];
                      $newSpecialRoute->milk_2l_whole = $data[9];
                      $newSpecialRoute->milk_1l_semi_skimmed = $data[10];
                      $newSpecialRoute->milk_1l_skimmed = $data[11];
                      $newSpecialRoute->milk_1l_whole = $data[12];
                      $newSpecialRoute->milk_1l_alt_coconut = $data[13]; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                      $newSpecialRoute->milk_1l_alt_unsweetened_almond = $data[14];
                      $newSpecialRoute->milk_1l_alt_almond = $data[15];
                      $newSpecialRoute->milk_1l_alt_unsweetened_soya = $data[16];
                      $newSpecialRoute->milk_1l_alt_soya = $data[17];
                      //$newSpecialRoute->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
                      //$newSpecialRoute->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
                      //$newSpecialRoute->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
                      $newSpecialRoute->milk_1l_alt_lactose_free_semi = $data[21];

                      // As we have already run the snacks and drinks import, and the only record of this info might be on the route we'll grab the data and add it here for once.
                      // Although as the dairy and office pantry are only totals generated from other entries we only need to worry about choice organics.
                      $newSpecialRoute->drinks = $data[22];
                      $newSpecialRoute->snacks = $data[23];
                      $newSpecialRoute->other = $data[24];

                      $newSpecialRoute->assigned_to = $data[25];
                      $newSpecialRoute->delivery_day = $data[26];
                      $newSpecialRoute->position_on_route = $data[27];

                      // Expected Time and Delivery Needed By columns to be either returned empty
                      // or (more likely) Delivery Needed By will be held in the company model.
                      $newSpecialRoute->save();

                      $last_minute_entry_new_day .= 'No entry found for ' . $company_name . ' on ' . $data[26] . ' Adding onto route ' . $data[25] . ' at position ' . $data[27] . ". \n";

                      // break;

                      // If the route to be rejigged doesn't yet exist (which does happen! - last minute entries etc) we actually need to create this entry in the routes.
                  } else {

                      $newSpecialRoute = new Route();
                      $newSpecialRoute->week_start = $data[0];
                      $newSpecialRoute->company_name = $company_name;
                      $newSpecialRoute->postcode = $data[2];
                      $newSpecialRoute->address = $data[3]; // Condensed values of address_line_1, address_line_2, city and county.
                      $newSpecialRoute->delivery_information = $data[4];
                      $newSpecialRoute->fruit_crates = $data[5];
                      $newSpecialRoute->fruit_boxes = $data[6];
                      $newSpecialRoute->milk_2l_semi_skimmed = $data[7];
                      $newSpecialRoute->milk_2l_skimmed = $data[8];
                      $newSpecialRoute->milk_2l_whole = $data[9];
                      $newSpecialRoute->milk_1l_semi_skimmed = $data[10];
                      $newSpecialRoute->milk_1l_skimmed = $data[11];
                      $newSpecialRoute->milk_1l_whole = $data[12];
                      $newSpecialRoute->milk_1l_alt_coconut = $data[13]; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                      $newSpecialRoute->milk_1l_alt_unsweetened_almond = $data[14];
                      $newSpecialRoute->milk_1l_alt_almond = $data[15];
                      $newSpecialRoute->milk_1l_alt_unsweetened_soya = $data[16];
                      $newSpecialRoute->milk_1l_alt_soya = $data[17];
                      //$newSpecialRoute->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
                      //$newSpecialRoute->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
                      //$newSpecialRoute->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
                      $newSpecialRoute->milk_1l_alt_lactose_free_semi = $data[21];

                      // As we have already run the snacks and drinks import, and the only record of this info might be on the route we'll grab the data and add it here for once.
                      // Although as the dairy and office pantry are only totals generated from other entries we only need to worry about choice organics.
                      $newSpecialRoute->drinks = $data[22];
                      $newSpecialRoute->snacks = $data[23];
                       $newSpecialRoute->other = $data[24];

                      $newSpecialRoute->assigned_to = $data[25];
                      $newSpecialRoute->delivery_day = $data[26];
                      $newSpecialRoute->position_on_route = $data[27];

                      // Expected Time and Delivery Needed By columns to be either returned empty
                      // or (more likely) Delivery Needed By will be held in the company model.
                      $newSpecialRoute->save();

                      $last_minute_completely_new_entry .= 'Adding completely new entry for ' . $company_name . ' on route ' . $data[25] . ' at position ' . $data[27] . " \n";
                  }
            }
        }

        $title = "REJIGGED ROUTING - _rerouting for week commencing_ - $this->week_start";
        Log::channel('slack')->info($title);
        Log::channel('slack')->info($regular_rejig);
        Log::channel('slack')->info($special_delivery);
        Log::channel('slack')->info($new_special_delivery);
        Log::channel('slack')->info($last_minute_entry_regular_day);
        Log::channel('slack')->warning($last_minute_entry_new_day);
        Log::channel('slack')->alert($last_minute_completely_new_entry);

        return redirect()->route('import-file');
    }

    public function storeSnacksNDrinksCSV(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function

        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->snacks_n_drinks_csv);
        // now we can decode the remainder of the encoded data string
        $requestcsv = base64_decode($request_mime_fix);
        // however it now has some unwated unicode characters i.e the 'no break space' - (U+00A0) and use json_encode to make them visible
        $csv_data_with_unicode_characters = json_encode($requestcsv);
        // now they're no longer hidden characters, we can strip them out of the data
        $csv_data_fixed = str_replace('\u00a0', ' ', $csv_data_with_unicode_characters);
        // and return the file ready for storage
        $ready_csv = json_decode($csv_data_fixed);

        // this is how we determine where to put the file, these variables are populated with the $week_start variable at the top of this class
        // and the request parameters attached to the form on submission.
        Storage::put('public/drinks-n-snacks/drinks-n-snacks-' . $this->week_start . '-' . $delivery_days . '-noheaders-utf8-nobom.csv', $ready_csv);
    }

    // Currently calling this a store function, however it will always(?) be updating an entry so should maybe be renamed?
    public function addDrinksNSnacksToRoute(Request $request)
    {
        // Get all existing route information MAYBE => $routes::where('week_start', $this->week_start)->get()
        $routes = Route::where('week_start', $this->week_start)->get();
        // Just grab Company Names to quickly check if company has received previous deliveries
        $route_company_names = Route::pluck('company_name')->all();
        $company_invoice_names = Company::pluck('invoice_name')->all();
        $company_route_names = Company::pluck('route_name')->all();
        // var_dump($route_company_names);
        // dd(array_map('strlen', $route_company_names));

        // Create our logging variables to bulk the reponses as ...
        $regular = "*Found route entry with current week start, adding drinks, snacks and other to route entry* \n";
        $regular_SDO_only = "*Found old route entry, stripping previous totals and adding drinks, snack and other to route entry* \n";
        $associated_route_name = "*Found associated route entry with current week start, adding drinks, snacks and other to route entry* \n";
        $associated_route_name_SDO_only = "*Found old associated route entry, stripping previous totals and adding drinks, snack and other to route entry* \n";
        $new_route_created = "*Unable to find a previous route entry, so created a new one using associated route name from company records.* \n";
        $uh_oh_shit_happened = "*Unable to find route or associated route name in company records, unable to add snacks, drinks or other!* \n";

        if (($handle = fopen('../storage/app/public/drinks-n-snacks/drinks-n-snacks-' . $this->week_start . '-' . $this->delivery_days . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
        // if (($handle = fopen(public_path() . '/drinks-n-snacks/drinksnsnacks-' . $this->week_start . '-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

                while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {


                        // The usual tidying up of data - this one only covers the company name.
                        $company_name_encoded = json_encode($data[0]);
                        $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
                        $company_name = json_decode($company_name_fixed);
                        $company_name = trim($company_name);

                        // Let's set these variables as empty arrays unless/until they get populated through the function later.
                        $currentRoutingEntry = [];
                        $currentRoutingEntrySecondChance = [];
                        $currentRoutingEntryThirdChance = [];

                        // Find that company data and update snacks and drinks column.
                        // But only if it matches both the week_start and delivery day.

                        $currentRoutingEntry = Route::where('company_name', $company_name) // Current Current route name and delivery day
                          ->where('delivery_day', $data[4])->get();

                        // Only worry about successfully retrieved routes, (matching route name/day) otherwise this variable (array) will be empty
                        if (count($currentRoutingEntry) !== 0) {

                                // If it isn't empty, grab the company name and delivery day as currently listed in database.
                                $selectedCompany = trim($currentRoutingEntry[0]->company_name);
                                $selectedtDeliveryDay = $currentRoutingEntry[0]->delivery_day;

                                // if the entry hasn't had its week_start updated by this point then we should strip out its old order values before updating it with snacks, drinks and other.
                                if ($currentRoutingEntry[0]->week_start != $this->week_start) {

                                      Route::where('company_name', trim($company_name))->where('delivery_day', $data[4])
                                        ->update([

                                            'fruit_crates'	=> null,
                                            'fruit_boxes'	=> null,
                                            'milk_2l_semi_skimmed'	=> null,
                                            'milk_2l_skimmed'	=> null,
                                            'milk_2l_whole'	=> null,
                                            'milk_1l_semi_skimmed'	=> null,
                                            'milk_1l_skimmed'	=> null,
                                            'milk_1l_whole'	=> null,
                                            'milk_1l_alt_coconut'	=> null,
                                            'milk_1l_alt_unsweetened_almond'	=> null,
                                            'milk_1l_alt_almond'	=> null,
                                            'milk_1l_alt_unsweetened_soya'	=> null,
                                            'milk_1l_alt_soya'	=> null,
                                            'milk_1l_alt_oat' => null,
                                            'milk_1l_alt_rice' => null,
                                            'milk_1l_alt_cashew' => null,
                                            'milk_1l_alt_lactose_free_semi'	=> null,
                                      ]);
                                      $regular_SDO_only .= 'Updated old entry ' . $company_name . ' / ' . $data[4] . ' by resetting fruit and milk totals.' . " \n";
                                }

                                // Grab the routing data, update the snacks and drinks columns where company name and day of delivery match the updating data.
                                Route::where('company_name', trim($company_name))->where('delivery_day', $data[4])
                                  ->update([
                                    // Company Name = $data[0];
                                    'drinks' => $data[1],
                                    'snacks' => $data[2],
                                    'other' => $data[3],
                                    // Delivery Day = $data[4];
                                    'week_start' => $data[5],
                                ]);

                                // echo 'Found the Route entry for ' . $company_name . ' / ' . $data[4] . ' updating with drinks and snacks now <br>';
                                $regular .= "• Found Route entry for " . $company_name . ' on ' . $data[4] . " \n";

                          // If we can't find the company under its route name then maybe we can check the company tables and look for a connected route name.
                        }  elseif (in_array($company_name, $company_invoice_names) || in_array($company_name, $company_route_names)) {

                                // only attempt to populate these if through the $company_name matches an invoice name, otherwise it'll get upset that [0] is an undefined offset.
                                if (in_array($company_name, $company_invoice_names)) {
                                    $selected_company_details = Company::where('invoice_name', $company_name)->get();
                                    $selected_company_route_name = $selected_company_details[0]->route_name;
                                    $selected_company_postcode = $selected_company_details[0]->postcode;
                                    $selected_company_route_summary_address = $selected_company_details[0]->route_summary_address;
                                    $selected_company_route_delivery_information = $selected_company_details[0]->delivery_information;

                                    $currentRoutingEntrySecondChance = Route::where('company_name', $selected_company_route_name) // Current Current route name and delivery day
                                        ->where('delivery_day', $data[4])->get();
                                }
                                // only attempt to populate these if through the $company_name matches a route name, otherwise it'll get upset that [0] is an undefined offset.
                                if (in_array($company_name, $company_route_names)) {
                                    $selected_company_details_alt = Company::where('route_name', $company_name)->get();
                                    $selected_company_route_name_alt = $selected_company_details_alt[0]->route_name;
                                    $selected_company_postcode_alt = $selected_company_details_alt[0]->postcode;
                                    $selected_company_route_summary_address_alt = $selected_company_details_alt[0]->route_summary_address;
                                    $selected_company_route_delivery_information_alt = $selected_company_details_alt[0]->delivery_information;

                                    $currentRoutingEntryThirdChance = Route::where('company_name', $selected_company_route_name_alt)
                                        ->where('delivery_day', $data[4])->get();
                                }
                                // if company invoice name has a matching route with an existing entry and correct delivery day, we can update that entry.
                                if(count($currentRoutingEntrySecondChance) !== 0) {

                                        // if the entry hasn't had its week_start updated by this point then we should make strip out its old order values before updating it with snacks, drinks and other.
                                        if ($currentRoutingEntrySecondChance[0]->week_start != $this->week_start) {

                                              Route::where('company_name', trim($selected_company_route_name))->where('delivery_day', $data[4])
                                                ->update([

                                                    'fruit_crates'	=> null,
                                                    'fruit_boxes'	=> null,
                                                    'milk_2l_semi_skimmed'	=> null,
                                                    'milk_2l_skimmed'	=> null,
                                                    'milk_2l_whole'	=> null,
                                                    'milk_1l_semi_skimmed'	=> null,
                                                    'milk_1l_skimmed'	=> null,
                                                    'milk_1l_whole'	=> null,
                                                    'milk_1l_alt_coconut'	=> null,
                                                    'milk_1l_alt_unsweetened_almond'	=> null,
                                                    'milk_1l_alt_almond'	=> null,
                                                    'milk_1l_alt_unsweetened_soya'	=> null,
                                                    'milk_1l_alt_soya'	=> null,
                                                    'milk_1l_alt_oat' => null,
                                                    'milk_1l_alt_rice' => null,
                                                    'milk_1l_alt_cashew' => null,
                                                    'milk_1l_alt_lactose_free_semi'	=> null,
                                              ]);
                                               $associated_route_name_SDO_only .= 'Updated old entry ' . $company_name . ' on ' . $data[4] . " \n";
                                        }

                                          Route::where('company_name', trim($selected_company_route_name))->where('delivery_day', $data[4])
                                            ->update([
                                              // Company Name = $data[0];
                                              'drinks' => $data[1],
                                              'snacks' => $data[2],
                                              'other' => $data[3],
                                              // Delivery Day = $data[4];
                                              'week_start' => $data[5],
                                          ]);

                                          $associated_route_name .= 'Found the Route entry for ' . $company_name . ' (under Company \'invoice_name\') Route: ' . $selected_company_route_name . " \n";

                                } elseif(count($currentRoutingEntryThirdChance) !== 0) {

                                        // if the entry hasn't had its week_start updated by this point then we should make strip out its old order values before updating it with snacks, drinks and other.
                                        if ($currentRoutingEntryThirdChance[0]->week_start != $this->week_start) {

                                              Route::where('company_name', trim($selected_company_route_name_alt))->where('delivery_day', $data[4])
                                                ->update([

                                                    'fruit_crates'	=> null,
                                                    'fruit_boxes'	=> null,
                                                    'milk_2l_semi_skimmed'	=> null,
                                                    'milk_2l_skimmed'	=> null,
                                                    'milk_2l_whole'	=> null,
                                                    'milk_1l_semi_skimmed'	=> null,
                                                    'milk_1l_skimmed'	=> null,
                                                    'milk_1l_whole'	=> null,
                                                    'milk_1l_alt_coconut'	=> null,
                                                    'milk_1l_alt_unsweetened_almond'	=> null,
                                                    'milk_1l_alt_almond'	=> null,
                                                    'milk_1l_alt_unsweetened_soya'	=> null,
                                                    'milk_1l_alt_soya'	=> null,
                                                    'milk_1l_alt_oat' => null,
                                                    'milk_1l_alt_rice' => null,
                                                    'milk_1l_alt_cashew' => null,
                                                    'milk_1l_alt_lactose_free_semi'	=> null,
                                              ]);
                                               $associated_route_name_SDO_only .= 'Updated old entry ' . $company_name . ' / ' . $data[4] . " \n";
                                        }

                                          Route::where('company_name', trim($selected_company_route_name_alt))->where('delivery_day', $data[4])
                                            ->update([
                                              // Company Name = $data[0];
                                              'drinks' => $data[1],
                                              'snacks' => $data[2],
                                              'other' => $data[3],
                                              // Delivery Day = $data[4];
                                              'week_start' => $data[5],
                                          ]);

                                          $associated_route_name .= 'Found the Route entry for ' . $company_name . ' (under Company \'route_name\') Route: ' . $selected_company_route_name . " \n";
                                } else {

                                        // If no suitable entry exists, we need to create one from scratch.
                                        // If the route name is found in Company details but not in the routes table we can go ahead and create a new route entry for the snacks and drinks order.

                                        // If nether of these returns a hit the company is absent from the Company table.
                                        // Should we throw an error here or let it go ahead and create an entry with the data we do have?
                                        $newSnacksNDrinksRouteInfo = Company::where('route_name', $company_name)->orWhere('invoice_name', $company_name)->get();

                                        // on the basis we're going to go ahead and let it create an entry, this is how we would do it.
                                        $newRouteName = $newSnacksNDrinksRouteInfo[0]->route_name ? $newSnacksNDrinksRouteInfo[0]->route_name : $company_name;
                                        $newRoutePostcode = $newSnacksNDrinksRouteInfo[0]->postcode ? $newSnacksNDrinksRouteInfo[0]->postcode : 'N/A';
                                        $newRouteSummaryAddress = $newSnacksNDrinksRouteInfo[0]->route_summary_address ? $newSnacksNDrinksRouteInfo[0]->route_summary_address : 'N/A';
                                        $newRouteDeliveryInformation = $newSnacksNDrinksRouteInfo[0]->delivery_information ? $newSnacksNDrinksRouteInfo[0]->delivery_information : 'N/A';

                                        $newSnacksNDrinksRoute = new Route();
                                        $newSnacksNDrinksRoute->week_start = $data[5];
                                        $newSnacksNDrinksRoute->company_name = $newRouteName;
                                        $newSnacksNDrinksRoute->postcode = $newRoutePostcode;
                                        $newSnacksNDrinksRoute->address = $newRouteSummaryAddress;
                                        $newSnacksNDrinksRoute->delivery_information = $newRouteDeliveryInformation;

                                        $newSnacksNDrinksRoute->drinks = $data[1];
                                        $newSnacksNDrinksRoute->snacks = $data[2];
                                        $newSnacksNDrinksRoute->other = $data[3];

                                        // $newSpecialRoute->assigned_to = will default to TBC as we don't have any data on this shoud the route not yet exist.
                                        $newSnacksNDrinksRoute->delivery_day = $data[4];
                                        // $newSpecialRoute->position_on_route = Likewise this will default to null until Nick gives it a home.

                                        // Expected Time and Delivery Needed By columns to be either returned empty
                                        // or (more likely) Delivery Needed By will be held in the company model.
                                        $newSnacksNDrinksRoute->save();

                                        $new_route_created .= 'Found the Route entry for ' . $company_name . ' in Company table under Route Name: ' . $newRouteName .  ", new route entry created \n";
                                } // end of - if (count($currentRoutingEntrySecondChance) !== 0), elseif(count($currentRoutingEntryThirdChance) !== 0), else (make new entry).
                        } else {
                            // this else statement is hit if the route to be updated with drinks and snacks doesn't exist as a recognised name/day combo in the Routes table
                            // or if the company name provided doesn't match a company invoice_name or route_name in the Company table.
                            $uh_oh_shit_happened .= "Uh, oh? ' . $company_name . ' on ' . $data[4] . ' couldn\'t be added! \n";
                        } // end of - if (count($currentRoutingEntry) !== 0), elseif (in_array($company_name, $company_invoice_names) || in_array($company_name, $company_route_names)), else (uh oh!)
                } // end of while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {
        } // end of if (($handle = fopen(public_path() . '/drinks-n-snacks/drinksnsnacks-' . $this->week_start . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

            // Send feedback to slack on the results of each entry - grouped by the 6 main outcomes.
            $title = "SNACKS, DRINKS AND OTHER - _adding to routes for week commencing_ - $this->week_start";
            Log::channel('slack')->info($title);
            Log::channel('slack')->info($regular);
            Log::channel('slack')->info($regular_SDO_only);
            Log::channel('slack')->info($associated_route_name);
            Log::channel('slack')->info($associated_route_name_SDO_only);
            Log::channel('slack')->warning($new_route_created);
            Log::channel('slack')->alert($uh_oh_shit_happened);

            return redirect()->route('import-file');
    } // end of function = public function addDrinksNSnacksToRoute(Request $request)


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if (($handle = fopen(public_path() . '/routing-230718-minusdrinksnsnacks-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

        while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

          $company_name_encoded = json_encode($data[1]);
          $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
          $company_name = json_decode($company_name_fixed);

          $routeData = new Route();
          $routeData->week_start = $data[0];
          $routeData->company_name = trim($company_name);
          $routeData->postcode = $data[2];
          $routeData->address = $data[3]; // Condensed values of address_line_1, address_line_2, city and county.
          $routeData->delivery_information = $data[4];
          $routeData->fruit_crates = $data[5];
          $routeData->fruit_boxes = $data[6];
          $routeData->milk_2l_semi_skimmed = $data[7];
          $routeData->milk_2l_skimmed = $data[8];
          $routeData->milk_2l_whole = $data[9];
          $routeData->milk_1l_semi_skimmed = $data[10];
          $routeData->milk_1l_skimmed = $data[11];
          $routeData->milk_1l_whole = $data[12];
          $routeData->milk_1l_alt_coconut = $data[13]; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
          $routeData->milk_1l_alt_unsweetened_almond = $data[14];
          $routeData->milk_1l_alt_almond = $data[15];
          $routeData->milk_1l_alt_unsweetened_soya = $data[16];
          $routeData->milk_1l_alt_soya = $data[17];
          $routeData->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
          $routeData->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
          $routeData->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
          $routeData->milk_1l_alt_lactose_free_semi = $data[21];

          // At least temporarily these two fields will be updated from a seperate file.
          // $routeData->drinks = $data[22];
          // $routeData->snacks = $data[23];

          $routeData->assigned_to = $data[25];
          // New field to hold company position on route until/unless changed.
          $routeData->delivery_day = $data[26];
          $routeData->position_on_route = $data[27];


          // Expected Time and Delivery Needed By columns to be either returned empty
          // or (more likely) Delivery Needed By will held in the company model.
          $routeData->save();
        }
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

      // Get existing Route data
      $routes = Route::all()->toArray();

      // Just grab Company Names to quickly check if company has received previous deliveries
      $route_company_names = Route::pluck('company_name')->all();
      $company_invoice_names = Company::pluck('invoice_name')->all();
      $company_route_names = Company::pluck('route_name')->all();
      $company_box_names = Company::pluck('box_names')->all();

      // dd($company_box_names);

      // Create our logging variables to bulk the reponses as ...
      $updated_entry_needed_address = "*Regular entry, just missing a postcode/address* \n";
      $updated_regular_entry = "*Regular entry updated.* \n";
      $same_route_different_day = "*Needed a new route day entry* \n";
      $found_route_umbrella_name = "*Found a route entry under an umbrella name* \n";
      $week_start_current_adding_to_totals = "*Route entry already on current week start, added to exisiting totals.* \n";
      $missing_route_but_has_company_associated_route_name = "*New route created through an associated route name.* \n";
      // $uh_oh_shit_happened = "*These FOD entries failed to get updated* \n";

      // Get existing (and what should be freshly imported) data to update Routing with
      // This is limited to only files which have been recently updated to the new Week Start.
      // Until the $variable can be updated manually (through users), I will need to remember to adjust it here (top of function, as parameter) before running the update-routing
      // $fruitOrderingDocuments = FruitOrderingDocument::where('week_start', $this->week_start)->get();

      $fruitOrderingDocuments = ($this->delivery_days == 'mon-tue') ? FruitOrderingDocument::where('week_start', $this->week_start)->WhereIn('delivery_day', ['Monday', 'Tuesday'])->get()
                                                                    : FruitOrderingDocument::where('week_start', $this->week_start)->WhereIn('delivery_day', ['Wednesday', 'Thursday', 'Friday'])->get();

      // dd($fruitOrderingDocuments);
          // Now iterate through the new FOD data
        foreach($fruitOrderingDocuments as $fruitOrderingDocument) {
              // If Company entry in FOD matches a Company Name entry in Routing.
              if (in_array(strtolower($fruitOrderingDocument->company_name),
                  array_map('strtolower', $route_company_names)))
              {
                  // Looks for an entry in the picklist which contains the same combination of delivery day and company name
                  $currentRoutingEntry = Route::where('company_name', $fruitOrderingDocument->company_name)
                    ->where('delivery_day', $fruitOrderingDocument->delivery_day)->get();

                    // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                    if (count($currentRoutingEntry) !== 0) {

                          // If it isn't empty, grab the company name and delivery day as currently listed in database.
                          $selectedCompany = $currentRoutingEntry[0]->company_name;
                          $selectedtDeliveryDay = $currentRoutingEntry[0]->delivery_day;
                          $selectedPostCode = $currentRoutingEntry[0]->postcode;

                          $backupCompanyData = Company::where('route_name', $selectedCompany)->get(); // Grab entry where the route name matches a company route name.
                          $backupRouteData = Route::where('company_name', $selectedCompany)->get();

                          // first check other existing routes for the same company, and look for some address and delivery information
                          foreach($backupRouteData as $backupRouteEntry) {
                              if (!is_null($backupRouteEntry->postcode)) {
                                  $selectedRouteAddressInfo = $backupRouteEntry;
                              }
                          }

                          // if that didn't populate the variable then lets grab the info from the company data.   Either way this variable should now be populated with some data we can use.
                          if (empty($selectedRouteAddressInfo)) {
                              $selectedRouteAddressInfo = $backupCompanyData;
                              $selectedRouteAddressInfo[0]->address = $selectedRouteAddressInfo[0]->route_summary_address;
                          }

                            // this if statement is currently untested, there maybe some quirks to iron out.
                            if (is_null($selectedPostCode)) { // if the postcode is null we can be pretty sure the rest of the fields hold incomplete data at best, update all the fields with company data.

                                    Route::where('company_name', $selectedCompany)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                      ->update([

                                    //extra fields
                                    'postcode' => $selectedRouteAddressInfo[0]->postcode,
                                    'delivery_information' => $selectedRouteAddressInfo[0]->delivery_information,
                                    'address' => $selectedRouteAddressInfo[0]->route_summary_address,    // Add company data here.
                                    // plus the usual
                                    'week_start' => $fruitOrderingDocument->week_start,
                                    'fruit_crates' => $fruitOrderingDocument->fruit_crates,
                                    'fruit_boxes' => $fruitOrderingDocument->fruit_boxes,
                                    'milk_2l_semi_skimmed' => $fruitOrderingDocument->milk_2l_semi_skimmed,
                                    'milk_2l_skimmed' => $fruitOrderingDocument->milk_2l_skimmed,
                                    'milk_2l_whole' => $fruitOrderingDocument->milk_2l_whole,
                                    'milk_1l_semi_skimmed' => $fruitOrderingDocument->milk_1l_semi_skimmed,
                                    'milk_1l_skimmed' => $fruitOrderingDocument->milk_1l_skimmed,
                                    'milk_1l_whole' => $fruitOrderingDocument->milk_1l_whole,
                                    'milk_1l_alt_coconut' => $fruitOrderingDocument->milk_1l_alt_coconut,  // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                    'milk_1l_alt_unsweetened_almond' => $fruitOrderingDocument->milk_1l_alt_unsweetened_almond,
                                    'milk_1l_alt_almond' => $fruitOrderingDocument->milk_1l_alt_almond,
                                    'milk_1l_alt_unsweetened_soya' => $fruitOrderingDocument->milk_1l_alt_unsweetened_soya,
                                    'milk_1l_alt_soya' => $fruitOrderingDocument->milk_1l_alt_soya,
                                    'milk_1l_alt_oat' => null,  // This field is manually inputted for now.
                                    'milk_1l_alt_rice' => null, // This field is manually inputted for now.
                                    'milk_1l_alt_cashew' => null, // This field is manually inputted for now.
                                    'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                    // At least temporarily these two fields will be updated from a seperate file.
                                     'drinks' => null,
                                     'snacks' => null,
                                     'other' => null,


                                    // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                    // 'assigned_to' =
                                  ]);
                                  $updated_entry_needed_address .= '• Updated ' . $selectedCompany . " but needed address details. \n";

                                  } else { // we have an address so lets just update the fields that typically change.

                // -----  I think this is where we could/should have a check for the current week start variable matching up to the route entry current week start ----- //
                // -----  I'm going to add it here but comment it out for now,
                //        as I need to spend time on other tasks before studying the risks of breaking a function which currently, mostly works!  ----- //

                //                  if($currentRoutingEntry[0]->week_start !== $this->week_start) {  // -----  We can update it with new value ----- //
                
                                          Route::where('company_name', $selectedCompany)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                            ->update([

                                                    // These are only the fields which will likely change from one delivery to the next.

                                                  'week_start' => $fruitOrderingDocument->week_start,
                                                  'fruit_crates' => $fruitOrderingDocument->fruit_crates,
                                                  'fruit_boxes' => $fruitOrderingDocument->fruit_boxes,
                                                  'milk_2l_semi_skimmed' => $fruitOrderingDocument->milk_2l_semi_skimmed,
                                                  'milk_2l_skimmed' => $fruitOrderingDocument->milk_2l_skimmed,
                                                  'milk_2l_whole' => $fruitOrderingDocument->milk_2l_whole,
                                                  'milk_1l_semi_skimmed' => $fruitOrderingDocument->milk_1l_semi_skimmed,
                                                  'milk_1l_skimmed' => $fruitOrderingDocument->milk_1l_skimmed,
                                                  'milk_1l_whole' => $fruitOrderingDocument->milk_1l_whole,
                                                  'milk_1l_alt_coconut' => $fruitOrderingDocument->milk_1l_alt_coconut,  // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                                  'milk_1l_alt_unsweetened_almond' => $fruitOrderingDocument->milk_1l_alt_unsweetened_almond,
                                                  'milk_1l_alt_almond' => $fruitOrderingDocument->milk_1l_alt_almond,
                                                  'milk_1l_alt_unsweetened_soya' => $fruitOrderingDocument->milk_1l_alt_unsweetened_soya,
                                                  'milk_1l_alt_soya' => $fruitOrderingDocument->milk_1l_alt_soya,
                                                  'milk_1l_alt_oat' => null, // This field is manually inputted for now.
                                                  'milk_1l_alt_rice' => null, // This field is manually inputted for now.
                                                  'milk_1l_alt_cashew' => null, // This field is manually inputted for now.
                                                  'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                                  // At least temporarily these two fields will be updated from a seperate file.
                                                  'drinks' => null,
                                                  'snacks' => null,
                                                  'other' => null,

                                                  // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                                  // 'assigned_to' =

                                                ]);
                                                $updated_regular_entry .= '• Updated ' . $selectedCompany . " for a regular delivery. \n";
                                                
                                            // -----  Else a box for this route has already been added, we should add to the existing total  ----- //
                                        // } else {
                                        
                                        // $new_fruitcrate_total = ($currentRoutingEntry[0]->fruit_crates + $fruitOrderingDocument->fruit_crates);
                                        // $new_fruitbox_total = ($currentRoutingEntry[0]->fruit_boxes + $fruitOrderingDocument->fruit_boxes);
                                        // $new_milk_2l_semi_skimmed_total = ($currentRoutingEntry[0]->milk_2l_semi_skimmed + $fruitOrderingDocument->milk_2l_semi_skimmed);
                                        // $new_milk_2l_skimmed_total = ($currentRoutingEntry[0]->milk_2l_skimmed + $fruitOrderingDocument->milk_2l_skimmed);
                                        // $new_milk_2l_whole_total = ($currentRoutingEntry[0]->milk_2l_whole + $fruitOrderingDocument->milk_2l_whole);
                                        // $new_milk_1l_semi_skimmed_total = ($currentRoutingEntry[0]->milk_1l_semi_skimmed + $fruitOrderingDocument->milk_1l_semi_skimmed);
                                        // $new_milk_1l_skimmed_total = ($currentRoutingEntry[0]->milk_1l_skimmed + $fruitOrderingDocument->milk_1l_skimmed);
                                        // $new_milk_1l_whole_total = ($currentRoutingEntry[0]->milk_1l_whole + $fruitOrderingDocument->milk_1l_whole);
                                        // $new_milk_1l_alt_coconut_total = ($currentRoutingEntry[0]->milk_1l_alt_coconut + $fruitOrderingDocument->milk_1l_alt_coconut);
                                        // $new_milk_1l_alt_unsweetened_almond_total = ($currentRoutingEntry[0]->milk_1l_alt_unsweetened_almond + $fruitOrderingDocument->milk_1l_alt_unsweetened_almond);
                                        // $new_milk_1l_alt_almond_total = ($currentRoutingEntry[0]->milk_1l_alt_almond + $fruitOrderingDocument->milk_1l_alt_almond);
                                        // $new_milk_1l_alt_unsweetened_soya_total = ($currentRoutingEntry[0]->milk_1l_alt_unsweetened_soya + $fruitOrderingDocument->milk_1l_alt_unsweetened_soya);
                                        // $new_milk_1l_alt_soya_total = ($currentRoutingEntry[0]->milk_1l_alt_soya + $fruitOrderingDocument->milk_1l_alt_soya);
                                        // $new_milk_1l_alt_lactose_free_semi_total = ($currentRoutingEntry[0]->milk_1l_alt_lactose_free_semi + $fruitOrderingDocument->milk_1l_alt_lactose_free_semi);
                                        // 
                                        // Route::where('company_name', $selectedCompany)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                        //   ->update([
                                        // 
                                        //           // These are only the fields which will likely change from one delivery to the next.
                                        // 
                                        //         'week_start' => $fruitOrderingDocument->week_start,
                                        //         'fruit_crates' => $new_fruitcrate_total,
                                        //         'fruit_boxes' => $new_fruitbox_total,
                                        //         'milk_2l_semi_skimmed' => $new_milk_2l_semi_skimmed_total,
                                        //         'milk_2l_skimmed' => $new_milk_2l_skimmed_total,
                                        //         'milk_2l_whole' => $new_milk_2l_whole_total,
                                        //         'milk_1l_semi_skimmed' => $new_milk_1l_semi_skimmed_total,
                                        //         'milk_1l_skimmed' => $new_milk_1l_skimmed_total,
                                        //         'milk_1l_whole' => $new_milk_1l_whole_total,
                                        //         'milk_1l_alt_coconut' => $new_milk_1l_alt_coconut_total,  // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                        //         'milk_1l_alt_unsweetened_almond' => $new_milk_1l_alt_unsweetened_almond_total,
                                        //         'milk_1l_alt_almond' => $new_milk_1l_alt_almond_total,
                                        //         'milk_1l_alt_unsweetened_soya' => $new_milk_1l_alt_unsweetened_soya_total,
                                        //         'milk_1l_alt_soya' => $new_milk_1l_alt_soya_total,
                                        //         'milk_1l_alt_oat' => null, // This field is manually inputted for now.
                                        //         'milk_1l_alt_rice' => null, // This field is manually inputted for now.
                                        //         'milk_1l_alt_cashew' => null, // This field is manually inputted for now.
                                        //         'milk_1l_alt_lactose_free_semi' => $new_milk_1l_alt_lactose_free_semi_total,
                                        // 
                                        //         // At least temporarily these two fields will be updated from a seperate file.
                                        //         'drinks' => null,
                                        //         'snacks' => null,
                                        //         'other' => null,

                                                
                                    // } // -----  This is the end of the commented out upgrade code, which should fix the possiblilty of error but needs further reviewing before using.  ----- //
                                                
                                  } // end of if statement which is currently untested,

                    } else { // End of if (count($currentRoutingEntry) !== 0)
                      // They have previously featured on routes, but not for this delivery day.  Create a new entry in routes.
                         $same_route_different_day .= '• Couldn\'t locate delivery for ' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . " in routes. Adding now :) \n";

                         $backupCompanyData = Company::where('route_name', $fruitOrderingDocument->company_name)->get(); // Grab entry where the route name matches a company route name.
                         $backupRouteData = Route::where('company_name', $fruitOrderingDocument->company_name)->get();

                         // first check other existing routes for the same company, and look for some address and delivery information
                         foreach($backupRouteData as $backupRouteEntry) {
                             if (!is_null($backupRouteEntry->postcode)) {
                                 $selectedRouteAddressInfo = $backupRouteEntry;
                                 // dd($selectedRouteAddressInfo);
                                 $backupAddress = $selectedRouteAddressInfo->address;
                                 $backupPostcode = $selectedRouteAddressInfo->postcode;
                                 $backupDeliveryInfo = $selectedRouteAddressInfo->delivery_information;
                                 break;
                             }
                         }

                         // if that didn't populate the variable then lets grab the info from the company data.   Either way this variable should now be populated with some data we can use.
                         if (empty($selectedRouteAddressInfo)) {
                             $selectedRouteAddressInfo = $backupCompanyData;
                             // dd($selectedRouteAddressInfo);
                             $backupAddress = $selectedRouteAddressInfo[0]->route_summary_address;
                             $backupPostcode = $selectedRouteAddressInfo[0]->postcode;
                             $backupDeliveryInfo = $selectedRouteAddressInfo[0]->delivery_info;
                         }

                             $newRoutingData = new Route();
                             $newRoutingData->week_start = $fruitOrderingDocument->week_start;
                             $newRoutingData->company_name = $fruitOrderingDocument->company_name;

                             // if there's an address in the existing route entry for a different day, we could either take that information or rely on the company route summary and delivery information instead.
                             // there's also the fod data but this isn't reliably updated so should be best avioded for now.

                             // These two fields need to be pulled from Company Data (which is currently empty).
                             $newRoutingData->postcode = $backupPostcode;
                             $newRoutingData->address = $backupAddress; // Condensed values of address_line_1, address_line_2, city and county.

                             $newRoutingData->delivery_information = $backupDeliveryInfo;
                             $newRoutingData->fruit_crates = $fruitOrderingDocument->fruit_crates;
                             $newRoutingData->fruit_boxes = $fruitOrderingDocument->fruit_boxes;
                             $newRoutingData->milk_2l_semi_skimmed = $fruitOrderingDocument->milk_2l_semi_skimmed;
                             $newRoutingData->milk_2l_skimmed = $fruitOrderingDocument->milk_2l_skimmed;
                             $newRoutingData->milk_2l_whole = $fruitOrderingDocument->milk_2l_whole;
                             $newRoutingData->milk_1l_semi_skimmed = $fruitOrderingDocument->milk_1l_semi_skimmed;
                             $newRoutingData->milk_1l_skimmed = $fruitOrderingDocument->milk_1l_skimmed;
                             $newRoutingData->milk_1l_whole = $fruitOrderingDocument->milk_1l_whole;
                             $newRoutingData->milk_1l_alt_coconut = $fruitOrderingDocument->milk_1l_alt_coconut; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                             $newRoutingData->milk_1l_alt_unsweetened_almond = $fruitOrderingDocument->milk_1l_alt_unsweetened_almond;
                             $newRoutingData->milk_1l_alt_almond = $fruitOrderingDocument->milk_1l_alt_almond;
                             $newRoutingData->milk_1l_alt_unsweetened_soya = $fruitOrderingDocument->milk_1l_alt_unsweetened_soya;
                             $newRoutingData->milk_1l_alt_soya = $fruitOrderingDocument->milk_1l_alt_soya;
                             // These 3 fields are manually adjusted in the current system.
                             $newRoutingData->milk_1l_alt_oat = null; // $data[18]; // This field is manually inputted for now.
                             $newRoutingData->milk_1l_alt_rice = null; // $data[19]; // This field is manually inputted for now.
                             $newRoutingData->milk_1l_alt_cashew = null; // $data[20]; // This field is manually inputted for now.
                             $newRoutingData->milk_1l_alt_lactose_free_semi = $fruitOrderingDocument->milk_1l_alt_lactose_free_semi;

                             // At least temporarily these two fields will be updated from a seperate file.
                             $newRoutingData->drinks = null;
                             $newRoutingData->snacks = null;
                             $newRoutingData->other = null;

                             // Typically there will be no data for this (assigned_to) on submission as it's organised later.
                             // In migrations I've now added a default value of 'TBC' which can be overriden if this field contains a value in advance.
                             // $newRoutingData->assigned_to =
                             $newRoutingData->delivery_day = $fruitOrderingDocument->delivery_day;

                             $newRoutingData->save();

                             // echo 'Updated ' . $fruitOrderingDocument->company_name . ' and pulled some route address info from an existing route on a different day.<br>';
                    }

              } else { // End of if (in_array(strtolower($fruitOrderingDocument->company_name), array_map('strtolower', $route_company_names)))

                    // If we couldn't find them in the routes, they may be listed under an umbrella name, rather than a picklist box.
                    // So lets check if the FOD entry company name is listed in the Company->box_names

                    // if FOD entry isn't a route name, then check to see which company has that box name.
                    $company_box_names = Company::pluck('box_names')->all(); // This is an array of arrays, so to search through the values we need to tackle each one seperately.
                    // dd($company_box_names);
                    foreach($company_box_names as $company_box_name)
                    {
                        if(in_array($fruitOrderingDocument->company_name, $company_box_name)) // If fod entry is a listed box in the company details
                        {
                            // var_dump($company_box_name);
                            // echo $fruitOrderingDocument->company_name . ' was found in '; var_dump($company_box_name);
                            // $selected_company = Company::where('box_names', 'LIKE', '%' . $company_box_name[0] . '%')->get();
                            $selected_company = Company::where('box_names', 'LIKE', '%' . $fruitOrderingDocument->company_name . '%')->get();  // Grab the company details where fod entry matches a listed box name
                            Log::channel('slack')->info($fruitOrderingDocument->company_name);
                            Log::channel('slack')->info($selected_company );
                            // Grab route entry, if the selected company has a declared route name which matches a known route
                            $selected_route_entry = Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)->get();
                             // var_dump(empty($selected_route_entry[0]->week_start));
                                // most of the time this will return empty, so lets just ignore any of those
                                if((empty($selected_route_entry[0]->week_start) == FALSE)) // what am i really doing here, is there a better way to check? EDIT - I think it just ensures a failed record retrieval doesn't throw an error.
                                {
                                      // echo $fruitOrderingDocument->company_name . ' got this far!<br>';
                                      // echo 'Found route with start date ' . $selected_route_entry[0]->week_start . '.<br>';
                                      // if we're here then the selected route is ready to be updated,
                                      // but if the route's week_start matches the $this->week_start variable for this week, then it already holds some info we need (to add to, not replace)
                                      // so if it doesn't match, we can assume it's safe to update the entry
                                      if($selected_route_entry[0]->week_start !== $this->week_start)
                                      {
                                        // echo $fruitOrderingDocument->company_name . ' with invoice name ' . $selected_company[0]->invoice_name . ' and route entry week start '
                                        //   . $selected_route_entry[0]->week_start . ' which apparently doesn\'t match ' . $this->week_start . ' ...updating<br>';
                                        //   var_dump($selected_company[0]->route_name);

                                          if (is_null($selected_route_entry[0]->postcode)) {
                                              // Doing this one a slightly different way (just because it's always good to experiement with options :))
                                              Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                                ->update([
                                                    'postcode' => $selected_company[0]->postcode,
                                                    'delivery_information' => $selected_company[0]->delivery_information,
                                                    'address' => $selected_company[0]->route_summary_address,    // Add company data here.
                                                ]);
                                          } // once this check has been made we can go ahead and update all the usual stuff as well.
                                            // update the route entry where it's name matches a company listed route_name, on the correct delivery day
                                          Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                            ->update([

                                              // These are only the fields which will likely change from one delivery to the next.

                                              'week_start' => $fruitOrderingDocument->week_start,
                                              'fruit_crates' => $fruitOrderingDocument->fruit_crates,
                                              'fruit_boxes' => $fruitOrderingDocument->fruit_boxes,
                                              'milk_2l_semi_skimmed' => $fruitOrderingDocument->milk_2l_semi_skimmed,
                                              'milk_2l_skimmed' => $fruitOrderingDocument->milk_2l_skimmed,
                                              'milk_2l_whole' => $fruitOrderingDocument->milk_2l_whole,
                                              'milk_1l_semi_skimmed' => $fruitOrderingDocument->milk_1l_semi_skimmed,
                                              'milk_1l_skimmed' => $fruitOrderingDocument->milk_1l_skimmed,
                                              'milk_1l_whole' => $fruitOrderingDocument->milk_1l_whole,
                                              'milk_1l_alt_coconut' => $fruitOrderingDocument->milk_1l_alt_coconut,  // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                              'milk_1l_alt_unsweetened_almond' => $fruitOrderingDocument->milk_1l_alt_unsweetened_almond,
                                              'milk_1l_alt_almond' => $fruitOrderingDocument->milk_1l_alt_almond,
                                              'milk_1l_alt_unsweetened_soya' => $fruitOrderingDocument->milk_1l_alt_unsweetened_soya,
                                              'milk_1l_alt_soya' => $fruitOrderingDocument->milk_1l_alt_soya,
                                              'milk_1l_alt_oat' => null, // This field is manually inputted for now.
                                              'milk_1l_alt_rice' => null, // This field is manually inputted for now.
                                              'milk_1l_alt_cashew' => null, // This field is manually inputted for now.
                                              'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                              // At least temporarily these two fields will be updated from a seperate file.
                                              'drinks' => null,
                                              'snacks' => null,
                                              'other' => null,

                                              // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                              // 'assigned_to' =
                                          ]);
                                            // echo 'Finally found a company listed as receiving this fod entry (' . $fruitOrderingDocument->company_name . '), under the umbrella name ' . $selected_company[0]->route_name . '<br>';
                                            $found_route_umbrella_name .= '• Updated ' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . ' under route name ' . $selected_company[0]->route_name . " \n";

                                      } else { // $selected_route_entry->week_start did equal week start at top of update function
                                          // echo 'Week start already current for this delivery, so we need to add to the existing details rather than update them <br>';
                                          //
                                          // var_dump($selected_company[0]->route_name);
                                          $current_totals = Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)->get();

                                          // Let's create a new set of variables adding the existing values to the new entries
                                          // We need to include all fields to prevent 0 entries wiping out any existing values.
                                          $new_fruitcrate_total = ($current_totals[0]->fruit_crates + $fruitOrderingDocument->fruit_crates);
                                          $new_fruitbox_total = ($current_totals[0]->fruit_boxes + $fruitOrderingDocument->fruit_boxes);
                                          $new_milk_2l_semi_skimmed_total = ($current_totals[0]->milk_2l_semi_skimmed + $fruitOrderingDocument->milk_2l_semi_skimmed);
                                          $new_milk_2l_skimmed_total = ($current_totals[0]->milk_2l_skimmed + $fruitOrderingDocument->milk_2l_skimmed);
                                          $new_milk_2l_whole_total = ($current_totals[0]->milk_2l_whole + $fruitOrderingDocument->milk_2l_whole);
                                          $new_milk_1l_semi_skimmed_total = ($current_totals[0]->milk_1l_semi_skimmed + $fruitOrderingDocument->milk_1l_semi_skimmed);
                                          $new_milk_1l_skimmed_total = ($current_totals[0]->milk_1l_skimmed + $fruitOrderingDocument->milk_1l_skimmed);
                                          $new_milk_1l_whole_total = ($current_totals[0]->milk_1l_whole + $fruitOrderingDocument->milk_1l_whole);
                                          $new_milk_1l_alt_coconut_total = ($current_totals[0]->milk_1l_alt_coconut + $fruitOrderingDocument->milk_1l_alt_coconut);
                                          $new_milk_1l_alt_unsweetened_almond_total = ($current_totals[0]->milk_1l_alt_unsweetened_almond + $fruitOrderingDocument->milk_1l_alt_unsweetened_almond);
                                          $new_milk_1l_alt_almond_total = ($current_totals[0]->milk_1l_alt_almond + $fruitOrderingDocument->milk_1l_alt_almond);
                                          $new_milk_1l_alt_unsweetened_soya_total = ($current_totals[0]->milk_1l_alt_unsweetened_soya + $fruitOrderingDocument->milk_1l_alt_unsweetened_soya);
                                          $new_milk_1l_alt_soya_total = ($current_totals[0]->milk_1l_alt_soya + $fruitOrderingDocument->milk_1l_alt_soya);
                                          $new_milk_1l_alt_lactose_free_semi_total = ($current_totals[0]->milk_1l_alt_lactose_free_semi + $fruitOrderingDocument->milk_1l_alt_lactose_free_semi);
                                          // End of variables for now.  Future updates will include the manually inputted entries when the fod (or equivalent) has accurate values to work with.

                                          Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                                            ->update([

                                              // These are only the fields which will likely change from one delivery to the next.

                                              // 'week_start' => $fruitOrderingDocument->week_start, // We actually want to keep this the same, so probably don't need it here
                                              'fruit_crates' => $new_fruitcrate_total,
                                              'fruit_boxes' => $new_fruitbox_total,
                                              'milk_2l_semi_skimmed' => $new_milk_2l_semi_skimmed_total,
                                              'milk_2l_skimmed' => $new_milk_2l_skimmed_total,
                                              'milk_2l_whole' => $new_milk_2l_whole_total,
                                              'milk_1l_semi_skimmed' => $new_milk_1l_semi_skimmed_total,
                                              'milk_1l_skimmed' => $new_milk_1l_skimmed_total,
                                              'milk_1l_whole' => $new_milk_1l_whole_total,
                                              'milk_1l_alt_coconut' => $new_milk_1l_alt_coconut_total,  // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                              'milk_1l_alt_unsweetened_almond' => $new_milk_1l_alt_unsweetened_almond_total,
                                              'milk_1l_alt_almond' => $new_milk_1l_alt_almond_total,
                                              'milk_1l_alt_unsweetened_soya' => $new_milk_1l_alt_unsweetened_soya_total,
                                              'milk_1l_alt_soya' => $new_milk_1l_alt_soya_total,
                                              'milk_1l_alt_oat' => null,  // This field is manually inputted for now.
                                              'milk_1l_alt_rice' => null, // This field is manually inputted for now.
                                              'milk_1l_alt_cashew' => null, // This field is manually inputted for now.
                                              'milk_1l_alt_lactose_free_semi' => $new_milk_1l_alt_lactose_free_semi_total,

                                              // At least temporarily these two fields will be updated from a seperate file.
                                              'drinks' => null,
                                              'snacks' => null,
                                              'other' => null,

                                              // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                              // 'assigned_to' =
                                          ]);
                                          $week_start_current_adding_to_totals .= '• Updated (and added to) ' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . ' under route name ' . $selected_company[0]->route_name . " \n";

                                      }
                                } else {  // $selected_route_entry->week_start was empty
                                          // EDIT: However a box name was found which (if we also have an associated route name) means we can safely add a new route to the route table for that route name on that route day.
                                  $missing_route_but_has_company_associated_route_name .= '• Missing entry for ' . $fruitOrderingDocument->company_name . ' however an associated route name was found (' . $selected_company[0]->route_name . ") in the Company table. Creating new entry now. \n";

                                  $newRoutingData = new Route();
                                  $newRoutingData->week_start = $fruitOrderingDocument->week_start;
                                  $newRoutingData->company_name = $selected_company[0]->route_name;
                                  // These two fields need to be pulled from Company Data (which is currently empty).
                                  $newRoutingData->postcode = $selected_company[0]->postcode;
                                  $newRoutingData->address = $selected_company[0]->route_summary_address; // Condensed values of address_line_1, address_line_2, city and county.
                                  $newRoutingData->delivery_information = $selected_company[0]->delivery_information;
                                  $newRoutingData->fruit_crates = $fruitOrderingDocument->fruit_crates;
                                  $newRoutingData->fruit_boxes = $fruitOrderingDocument->fruit_boxes;
                                  $newRoutingData->milk_2l_semi_skimmed = $fruitOrderingDocument->milk_2l_semi_skimmed;
                                  $newRoutingData->milk_2l_skimmed = $fruitOrderingDocument->milk_2l_skimmed;
                                  $newRoutingData->milk_2l_whole = $fruitOrderingDocument->milk_2l_whole;
                                  $newRoutingData->milk_1l_semi_skimmed = $fruitOrderingDocument->milk_1l_semi_skimmed;
                                  $newRoutingData->milk_1l_skimmed = $fruitOrderingDocument->milk_1l_skimmed;
                                  $newRoutingData->milk_1l_whole = $fruitOrderingDocument->milk_1l_whole;
                                  $newRoutingData->milk_1l_alt_coconut = $fruitOrderingDocument->milk_1l_alt_coconut; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
                                  $newRoutingData->milk_1l_alt_unsweetened_almond = $fruitOrderingDocument->milk_1l_alt_unsweetened_almond;
                                  $newRoutingData->milk_1l_alt_almond = $fruitOrderingDocument->milk_1l_alt_almond;
                                  $newRoutingData->milk_1l_alt_unsweetened_soya = $fruitOrderingDocument->milk_1l_alt_unsweetened_soya;
                                  $newRoutingData->milk_1l_alt_soya = $fruitOrderingDocument->milk_1l_alt_soya;
                                  // These 3 fields are manually adjusted in the current system.
                                  $newRoutingData->milk_1l_alt_oat = null; // $data[18]; // This field is manually inputted for now.
                                  $newRoutingData->milk_1l_alt_rice = null; // $data[19]; // This field is manually inputted for now.
                                  $newRoutingData->milk_1l_alt_cashew = null; // $data[20]; // This field is manually inputted for now.
                                  $newRoutingData->milk_1l_alt_lactose_free_semi = $fruitOrderingDocument->milk_1l_alt_lactose_free_semi;

                                  // At least temporarily these two fields will be updated from a seperate file.
                                  $newRoutingData->drinks = null;
                                  $newRoutingData->snacks = null;
                                  $newRoutingData->other = null;

                                  // Typically there will be no data for this (assigned_to) on submission as it's organised later.
                                  // In migrations I've now added a default value of 'TBC' which can be overriden if this field contains a value in advance.
                                  // $newRoutingData->assigned_to =
                                  $newRoutingData->delivery_day = $fruitOrderingDocument->delivery_day;

                                  $newRoutingData->save();

                                  // echo 'Created new route entry for <strong style="color: green";>' . $selected_company[0]->route_name . '</strong> on <strong style="color: green";>' . $selected_company[0]->route_name . '</strong><br>';

                                }

                        } else { // $fruitOrderingDocument->company_name was not found in $company_box_name array
                          // echo ' the box ' . $fruitOrderingDocument->company_name . ' could not be found, is it in the company box name list?<br>';
                          continue;
                          // break; // changed continue to break, to stop the annoying fell through the cracks message repeating on successful entries.
                        }
                    } // end of foreach($company_box_names as $company_box_name)

                } // end of else statement capturing the fod entries where we couldn't find the $fruitOrderingDocument->company_name in the route tables
                // $uh_oh_shit_happened .= 'Uh, oh ' . json_encode($fruitOrderingDocument->company_name) . " fell throught the cracks! \n";
        } // end of foreach loop(ing through fod document)

        // Send feedback to slack on the results of each entry - grouped by the 4 main outcomes.
        $title = "*UPDATED ROUTING RESULTS _for Week Commencing_* - $this->week_start";
        Log::channel('slack')->info($title);
        Log::channel('slack')->info($updated_regular_entry);
        Log::channel('slack')->info($updated_entry_needed_address);
        Log::channel('slack')->info($same_route_different_day);
        Log::channel('slack')->info($found_route_umbrella_name);
        Log::channel('slack')->warning($week_start_current_adding_to_totals);
        Log::channel('slack')->warning($missing_route_but_has_company_associated_route_name);
        // Log::channel('slack')->alert($uh_oh_shit_happened);

        return redirect()->route('import-file');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
