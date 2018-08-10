<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exports;
use Illuminate\Http\Request;
use App\Route;
use App\FruitOrderingDocument;
use App\Company;

class RoutesController extends Controller
{
  // public function export($week_start = 300718)
  // {
  //   // This determines what the exported file is called and which exporting controller is used.
  //   // I'd like to name the file after the selected route being exported, ignoring multiples but will need to work on this later.
  //   return \Excel::download(new Exports\RoutesExport, 'routelists' . $week_start . '.xlsx');
  // }

  public function download($week_start = 130818)
{
    // return (new Exports\RoutesExport($week_start))->download('routesheets.xlsx');
    return \Excel::download(new Exports\RoutesExport($week_start), 'routelists' . $week_start . '.xlsx');
}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
          $routes = Route::all();
          $assigned_route = Route::select('assigned_to')->distinct()->get();
                return view ('display-routes', ['routes' => $routes, 'assigned_route' => $assigned_route]);
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

    public function updateRouteAndPosition(Request $request, $week_start = 130818)
    {
      // Get all existing route information
      $routes = Route::where('week_start', $week_start)->get();
      // Just grab Company Names to quickly check if company has received previous deliveries
      $route_company_names = Route::pluck('company_name')->all();

      // var_dump($route_company_names);
      // dd(array_map('strlen', $route_company_names));

        if (($handle = fopen(public_path() . '/rejigged-routing/rejigged-routing-' . $week_start . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
        // if (($handle = fopen(public_path() . '/rejigged-routing/rejigged-routing-' . $week_start . '-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

            while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            // Find that company data and update snacks and drinks column.
            // But only if it matches both the week_start and delivery day.

            $company_name_encoded = json_encode($data[1]);
            $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
            $company_name = json_decode($company_name_fixed);
            $company_name = trim($company_name);


            $currentRoutingEntry = Route::where('company_name', $company_name)->where('week_start', $week_start)
                ->where('delivery_day', $data[26])->get();

            $knownRoute = Route::where('company_name', $company_name)->get();
            $knownRouteAndDay = Route::where('company_name', $company_name)->where('delivery_day', $data[26])->get();
            $specialDelivery = ['park view dairy', 'office pantry', 'choice organics'];

                // Only worry about successfully retrieved routes, otherwise this variable (array) will be empty
                // EDIT: Added the string check to prevent any of these 3 options getting reassigned to another route by accident, when we actually want another entry if it doesn't already exist.
                if ((count($currentRoutingEntry) > 0) && (!in_array(strtolower($company_name), $specialDelivery))) { // this doesn't seem to be working right?
                    // var_dump((count($currentRoutingEntry) !== 0) && (!in_array(strtolower($company_name), $specialDelivery)));
                        echo strtolower($company_name) . '<br>';
                    // If it isn't empty, grab the company name and delivery day as currently listed in database.
                    $selectedCompany = $currentRoutingEntry[0]->company_name;
                    $selectedtDeliveryDay = $currentRoutingEntry[0]->delivery_day;

                    // Grab the routing data, update the snacks and drinks columns where company name and day of delivery match the updating data.
                      Route::where('company_name', trim($company_name))->where('delivery_day', $data[26])->where('week_start', $week_start)
                        ->update([

                          // Update the route address and delivery information to match any updates Nick has made in rejigged routes.
                          'postcode' => $data[2],
                          'address' => $data[3],
                          'delivery_information' => $data[4],

                          // Update drinks, snacks and other column with actual values where applicable.
                          'drinks'	=> $data[22],
                          'snacks'	=> $data[23],
                          // 'other' => $data[24],

                          // Update the route and position on route to match any updates Nick has made in rejigged routes.
                          'assigned_to' => $data[25],
                          'position_on_route' => $data[27],

                      ]);

                      echo 'Entry for ' . $company_name  .  ' found for ' . $data[26] .  ' and has been updated to correct route i.e ' . $data[25] . ' <br>';
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
                                echo 'Entry found for ' . $company_name . ' on route ' . $data[25] . ' for ' . $data[27] . '<br>';
                                echo 'Updating <strong style="color: green";>' . $company_name . '</strong> on route <strong style="color: green";>' . $data[25] . '</strong> for <strong style="color: green";>' . $data[27] . '</strong> now.<br>';
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

                                echo 'No SPECIAL entry found for ' . $company_name . ' on route ' . $data[25] . ' for ' . $data[26] . '<br>';
                                echo 'Adding the <strong style="color: green";>' . $company_name . '</strong> on route <strong style="color: green";>' . $data[25] . '</strong> for <strong style="color: green";>' . $data[26] . '</strong> now.<br>';
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
                          'address_summary'	=> $data[3],
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
                          // 'other' => $data[24],
                          'assigned_to' => $data[25],
                          'delivery_day' => $data[26],
                          'position_on_route' => $data[27],

                      ]);

                      echo 'LAST MINUTE ENTRY added for existing company entry and delivery day ( ' . $company_name . ' / ' . $data[26] . ')';
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

                      echo 'No entry found for ' . $company_name . ' on route ' . $data[25] . ' for ' . $data[27] . '<br>';
                      echo 'Adding LAST MINUTE for <strong style="color: green";>' . $company_name . '</strong> on route <strong style="color: green";>' . $data[25] . '</strong> for <strong style="color: green";>' . $data[27] . '</strong> now.<br>';
                      // break;

                      // If the route to be rejigged doesn't yet exist (which does happen! - last minute entries etc) we actually need to create this entry in the routes.
                  } else {

                      echo 'Uh, oh? <strong style="color: red";>' . $company_name . '</strong> couldn\'t be found anywhere! <br> Adding NEW entry in routes now!';

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
                  }
            }
        }
    }

    // Currently calling this a store function, however it will always(?) be updating an entry so should maybe be renamed?
    public function storeDrinksSnacks(Request $request, $week_start = 130818)
    {
        // Get all existing route information MAYBE => $routes::where('week_start', $week_start)->get()
        $routes = Route::where('week_start', $week_start)->get();
        // Just grab Company Names to quickly check if company has received previous deliveries
        $route_company_names = Route::pluck('company_name')->all();
        $company_invoice_names = Company::pluck('invoice_name')->all();
        // var_dump($route_company_names);
        // dd(array_map('strlen', $route_company_names));

          if (($handle = fopen(public_path() . '/drinks-n-snacks/drinksnsnacks-' . $week_start . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
                // if (($handle = fopen(public_path() . '/drinks-n-snacks/drinksnsnacks-' . $week_start . '-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

              while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                $company_name_encoded = json_encode($data[0]);
                $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
                $company_name = json_decode($company_name_fixed);
                $company_name = trim($company_name);

              // Find that company data and update snacks and drinks column.
              // But only if it matches both the week_start and delivery day.

                $currentRoutingEntry = Route::where('company_name', $company_name) // Current Current route name and delivery day
                  ->where('delivery_day', $data[4])->get();

                  // Only worry about successfully retrieved routes, otherwise this variable (array) will be empty
                  if (count($currentRoutingEntry) !== 0) {
                    // dd($currentRoutingEntry);
                      // If it isn't empty, grab the company name and delivery day as currently listed in database.
                      $selectedCompany = trim($currentRoutingEntry[0]->company_name);
                      $selectedtDeliveryDay = $currentRoutingEntry[0]->delivery_day;

                      // Grab the routing data, update the snacks and drinks columns where company name and day of delivery match the updating data.
                        Route::where('company_name', trim($company_name))->where('delivery_day', $data[4])
                          ->update([
                            // Company Name = $data[0];
                            'drinks' => $data[1],
                            'snacks' => $data[2],
                            // 'other' => $data[3],
                            // Delivery Day = $data[4];
                            'week_start' => $data[5],
                        ]);

                        echo 'Found the Route entry for ' . $company_name . ' updating with drinks and snacks now <br>';

                      // If we can't find the company under its route name then maybe we can check the company tables and look for a connected route name.
                    }  elseif(in_array($company_name, $company_invoice_names)) {

                      $selected_company_details = Company::where('invoice_name', $company_name)->get();
                      $selected_company_route_name = $selected_company_details[0]->route_name;
                      $selected_company_postcode = $selected_company_details[0]->postcode;
                      $selected_company_route_summary_address = $selected_company_details[0]->route_summary_address;
                      $selected_company_route_delivery_information = $selected_company_details[0]->delivery_information;

                      $currentRoutingEntrySecondChance = Route::where('company_name', $selected_company_route_name) // Current Current route name and delivery day
                        ->where('delivery_day', $data[4])->get();

                      if(count($currentRoutingEntrySecondChance) !== 0) { // company invoice name has a matching route with an existing entry and correct delivery day, we can update that entry.
                              Route::where('company_name', trim($selected_company_route_name))->where('delivery_day', $data[4])
                                ->update([
                                  // Company Name = $data[0];
                                  'drinks' => $data[1],
                                  'snacks' => $data[2],
                                  // 'other' => $data[3],
                                  // Delivery Day = $data[4];
                                  'week_start' => $data[5],
                              ]);

                              echo 'Found the Route entry for ' . $company_name . ' under Route: ' . $selected_company_route_name . ', updating with drinks and snacks now <br>';
                      } else {

                                // If no suitable entry exists, we need to create one from scratch.
                                // If the route name is found in Company details but not in the routes ta ble we can go ahead and create a new route entry for the snacks and drinks order.

                                $newSnacksNDrinksRoute = new Route();

                                $newSnacksNDrinksRoute->week_start = $data[5];
                                $newSnacksNDrinksRoute->company_name = $selected_company_route_name;
                                $newSnacksNDrinksRoute->postcode = $selected_company_postcode;
                                $newSnacksNDrinksRoute->address = $selected_company_route_summary_address; // Condensed values of address_line_1, address_line_2, city and county.
                                $newSnacksNDrinksRoute->delivery_information = $selected_company_route_delivery_information;

                                $newSnacksNDrinksRoute->drinks = $data[1];
                                $newSnacksNDrinksRoute->snacks = $data[2];
                                $newSnacksNDrinksRoute->other = $data[3];

                                // $newSpecialRoute->assigned_to = will default to TBC as we don't have any data on this shoud the route not yet exist.
                                $newSnacksNDrinksRoute->delivery_day = $data[4];
                                // $newSpecialRoute->position_on_route = Likewise this will default to null until Nick gives it a home.

                                // Expected Time and Delivery Needed By columns to be either returned empty
                                // or (more likely) Delivery Needed By will be held in the company model.
                                $newSnacksNDrinksRoute->save();


                                echo 'Found the Route entry for ' . $company_name . ' in Company table under Route Name: ' . $selected_company_route_name .  ', but no Route Name / Delivery Day match; creating snacks and drinks ONLY entry now. <br>';
                      }

                    } else {

                      echo 'Uh, oh? ' . $company_name . ' on ' . $data[4] . ' couldn\'t be added! <br>';
                    }
              }
          }
    }


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
    public function update(Request $request, $week_start = 130818)
    {

      // Get existing Route data
      $routes = Route::all()->toArray();

      // Just grab Company Names to quickly check if company has received previous deliveries
      $route_company_names = Route::pluck('company_name')->all();
      $company_invoice_names = Company::pluck('invoice_name')->all();
      $company_route_names = Company::pluck('route_name')->all();
      $company_box_names = Company::pluck('box_names')->all();

      // dd($company_box_names);


      // Get existing (and what should be freshly imported) data to update Routing with
      // This is limited to only files which have been recently updated to the new Week Start.
      // Until the $variable can be updated manually (through users), I will need to remember to adjust it here (top of function, as parameter) before running the update-routing
      $fruitOrderingDocuments = FruitOrderingDocument::where('week_start', $week_start)->get();

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
                                        //'milk_1l_alt_oat' =>  // This field is manually inputted for now.
                                        //'milk_1l_alt_rice' => // This field is manually inputted for now.
                                        //'milk_1l_alt_cashew' => // This field is manually inputted for now.
                                        'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                        // At least temporarily these two fields will be updated from a seperate file.
                                         'drinks' => null,
                                         'snacks' => null,
                                         'other' => null,


                                        // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                        // 'assigned_to' =
                                      ]);
                                      echo 'Updated ' . $selectedCompany . ' this entry had a matching route/delivery day combo but needed address details.';

                                  } else { // we have an address so lets just update the fields that typically change.

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
                                                  //'milk_1l_alt_oat' =>  // This field is manually inputted for now.
                                                  //'milk_1l_alt_rice' => // This field is manually inputted for now.
                                                  //'milk_1l_alt_cashew' => // This field is manually inputted for now.
                                                  'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                                  // At least temporarily these two fields will be updated from a seperate file.
                                                  'drinks' => null,
                                                  'snacks' => null,
                                                  'other' => null,

                                                  // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                                  // 'assigned_to' =

                                                ]);
                                  } // end of if statement which is currently untested,

                    } else { // End of if (count($currentRoutingEntry) !== 0)
                      // They have previously featured on routes, but not for this delivery day.  Create a new entry in routes.
                         echo 'Couldn\'t locate delivery for ' . '<strong style="color: red";>' . $selectedCompany . ' on ' . $fruitOrderingDocument->delivery_day . '</strong> in routes. <br> Adding now :) <br>';

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
                             dd($selectedRouteAddressInfo);
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
                             // $newRoutingData->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
                             // $newRoutingData->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
                             // $newRoutingData->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
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

                             echo 'Updated ' . $fruitOrderingDocument->company_name . ' and pulled some route address info from an existing route on a different day.';
                    }

              } else { // End of if (in_array(strtolower($fruitOrderingDocument->company_name), array_map('strtolower', $route_company_names)))

                    // If we couldn't find them in the routes, they may be listed under an umbrella name, rather than a picklist box.
                    // So lets check if the FOD entry company name is listed in the Company->box_names

                    // if FOD entry isn't a route name, then check to see which company has that box name.
                    $company_box_names = Company::pluck('box_names')->all(); // This is an array of arrays, so to search through the values we need to tackle each one seperately.
                    foreach($company_box_names as $company_box_name)
                    {
                        if(in_array($fruitOrderingDocument->company_name, $company_box_name)) // If fod entry is a listed box in the company details
                        {
                            // echo $fruitOrderingDocument->company_name . ' was found in '; var_dump($company_box_name);
                            // $selected_company = Company::where('box_names', 'LIKE', '%' . $company_box_name[0] . '%')->get();
                            $selected_company = Company::where('box_names', 'LIKE', '%' . $fruitOrderingDocument->company_name . '%')->get();  // Grab the company details where fod entry matches a listed box name
                            // dd($selected_company);
                            // Grab route entry, if the selected company has a declared route name which matches a known route
                            $selected_route_entry = Route::where('company_name', $selected_company[0]->route_name)->where('delivery_day', $fruitOrderingDocument->delivery_day)->get();
                             // var_dump(empty($selected_route_entry[0]->week_start));
                                // most of the time this will return empty, so lets just ignore any of those
                                if((empty($selected_route_entry[0]->week_start) == FALSE)) // what am i really doing here, is there a better way to check?
                                {
                                      echo $fruitOrderingDocument->company_name . ' got this far!<br>';
                                      // if we're here then the selected route is ready to be updated,
                                      // but if the route's week_start matches the $week_start variable for this week, then it already holds some info we need (to add to, not replace)
                                      // so if it doesn't match, we can assume it's safe to update the entry
                                      if($selected_route_entry[0]->week_start !== $week_start)
                                      {
                                        echo $fruitOrderingDocument->company_name . ' with invoice name ' . $selected_company[0]->invoice_name . ' and route entry week start '
                                          . $selected_route_entry[0]->week_start . ' which apparently doesn\'t match ' . $week_start . ' ...updating<br>';
                                          var_dump($selected_company[0]->route_name);

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
                                              //'milk_1l_alt_oat' =>  // This field is manually inputted for now.
                                              //'milk_1l_alt_rice' => // This field is manually inputted for now.
                                              //'milk_1l_alt_cashew' => // This field is manually inputted for now.
                                              'milk_1l_alt_lactose_free_semi' => $fruitOrderingDocument->milk_1l_alt_lactose_free_semi,

                                              // At least temporarily these two fields will be updated from a seperate file.
                                              'drinks' => null,
                                              'snacks' => null,
                                              'other' => null,

                                              // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                              // 'assigned_to' =
                                          ]);
                                            // echo 'Finally found a company listed as receiving this fod entry (' . $fruitOrderingDocument->company_name . '), under the umbrella name ' . $selected_company[0]->route_name . '<br>';
                                            echo 'Updated <strong style="color: green";>' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . ' under route name ' . $selected_company[0]->route_name . '</strong>' . '<br>';

                                      } else { // $selected_route_entry->week_start did equal week start at top of update function
                                          echo 'Week start already current for this delivery, so we need to add to the existing details rather than update them <br>';

                                          var_dump($selected_company[0]->route_name);
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

                                              'week_start' => $fruitOrderingDocument->week_start, // We actually want to keep this the same, so probably don't need it here
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
                                              //'milk_1l_alt_oat' =>  // This field is manually inputted for now.
                                              //'milk_1l_alt_rice' => // This field is manually inputted for now.
                                              //'milk_1l_alt_cashew' => // This field is manually inputted for now.
                                              'milk_1l_alt_lactose_free_semi' => $new_milk_1l_alt_lactose_free_semi_total,

                                              // At least temporarily these two fields will be updated from a seperate file.
                                              'drinks' => null,
                                              'snacks' => null,
                                              'other' => null,

                                              // This is another column which will be manually updated (by Nick) when confirming/rearranging the routes.
                                              // 'assigned_to' =
                                          ]);
                                          echo 'Updated <strong style="color: green";>' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . ' under route name ' . $selected_company[0]->route_name . '</strong>' . '<br>';

                                      }
                                } else {  // $selected_route_entry->week_start was empty
                                          // EDIT: However a box name was found which (if we also have an associated route name) means we can safely add a new route to the route table for that route name on that route day.
                                  echo 'Missing entry for ' . $fruitOrderingDocument->company_name . ' however an associated route name was found (' . $selected_company[0]->route_name . ') in the Company table. Creating new entry now.' . '<br>';

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
                                  // $newRoutingData->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
                                  // $newRoutingData->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
                                  // $newRoutingData->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
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

                                  echo 'Created new route entry for <strong style="color: green";>' . $selected_company[0]->route_name . '</strong> on <strong style="color: green";>' . $selected_company[0]->route_name . '</strong><br>';


                                }

                        } else { // $fruitOrderingDocument->company_name was not found in $company_box_name array
                          // echo ' the box ' . $fruitOrderingDocument->company_name . ' could not be found, is it in the company box name list?<br>';
                          continue;
                          // break; // changed continue to break, to stop the annoying fell through the cracks message repeating on successful entries.
                        }
                    } // end of foreach($company_box_names as $company_box_name)
                    echo 'Uh, oh ' . $fruitOrderingDocument->company_name . ' aka ' . json_encode($fruitOrderingDocument->company_name) . ' fell throught the cracks! <br>';
                } // end of else statement capturing the fod entries where we couldn't find the $fruitOrderingDocument->company_name in the route tables



        } // end of foreach loop(ing through fod document)

                //but they're on for this week we need to make a completely new entry in routes.




              // echo 'Couldn\'t locate any previous delivery for ' . '<strong style="color: red";>' . $fruitOrderingDocument->company_name . ' on any day, including ' . $fruitOrderingDocument->delivery_day . '</strong> in our picklist records. <br> Adding now :) <br>';
              //
              // $newRoutingData = new Route();
              // $newRoutingData->week_start = $fruitOrderingDocument->week_start;
              // $newRoutingData->company_name = $fruitOrderingDocument->company_name;
              // // $newRoutingData->postcode = $data[2];
              // // $newRoutingData->address = $data[3]; // Condensed values of address_line_1, address_line_2, city and county.
              // $newRoutingData->delivery_information = $fruitOrderingDocument->delivery_information;
              // $newRoutingData->fruit_crates = $fruitOrderingDocument->fruit_crates;
              // $newRoutingData->fruit_boxes = $fruitOrderingDocument->fruit_boxes;
              // $newRoutingData->milk_2l_semi_skimmed = $fruitOrderingDocument->milk_2l_semi_skimmed;
              // $newRoutingData->milk_2l_skimmed = $fruitOrderingDocument->milk_2l_skimmed;
              // $newRoutingData->milk_2l_whole = $fruitOrderingDocument->milk_2l_whole;
              // $newRoutingData->milk_1l_semi_skimmed = $fruitOrderingDocument->milk_1l_semi_skimmed;
              // $newRoutingData->milk_1l_skimmed = $fruitOrderingDocument->milk_1l_skimmed;
              // $newRoutingData->milk_1l_whole = $fruitOrderingDocument->milk_1l_whole;
              // $newRoutingData->milk_1l_alt_coconut = $fruitOrderingDocument->milk_1l_alt_coconut; // Not necessarily representative of actual figure as it includes the oat, rice and cashew (plus others?) totals.
              // $newRoutingData->milk_1l_alt_unsweetened_almond = $fruitOrderingDocument->milk_1l_alt_unsweetened_almond;
              // $newRoutingData->milk_1l_alt_almond = $fruitOrderingDocument->milk_1l_alt_almond;
              // $newRoutingData->milk_1l_alt_unsweetened_soya = $fruitOrderingDocument->milk_1l_alt_unsweetened_soya;
              // $newRoutingData->milk_1l_alt_soya = $fruitOrderingDocument->milk_1l_alt_soya;
              // // These 3 fields are manually adjusted in the current system.
              // // $newRoutingData->milk_1l_alt_oat = $data[18]; // This field is manually inputted for now.
              // // $newRoutingData->milk_1l_alt_rice = $data[19]; // This field is manually inputted for now.
              // // $newRoutingData->milk_1l_alt_cashew = $data[20]; // This field is manually inputted for now.
              // $newRoutingData->milk_1l_alt_lactose_free_semi = $fruitOrderingDocument->milk_1l_alt_lactose_free_semi;
              //
              // // At least temporarily these two fields will be updated from a seperate file.
              // // $routeData->drinks = $data[];
              // // $routeData->snacks = $data[];
              //
              // // Typically there will be no data for this (assigned_to) on submission as it's organised later.
              // // In migrations I've now added a default value of 'TBC' which can be overriden if this field contains a value in advance.
              // // $newRoutingData->assigned_to =
              // $newRoutingData->delivery_day = $fruitOrderingDocument->delivery_day;
              //
              // // I haven't worked out how best to do this yet?
              // //$routeData->position_on_route = $data[26]; // This will begin at 1 for each route/day.
              //                                              // Changing this figure will change the position this appears on the route.
              //                                              // i.e 1 = first delivery of the day.
              //
              // // Expected Time and Delivery Needed By columns to be either returned empty
              // // or (more likely) Delivery Needed By will held in the company model.
              // $newRoutingData->save();
              // }
          // }

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
