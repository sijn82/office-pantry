<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exports;
use App\PickList;
use App\Route;
use Illuminate\Http\Request;


// Use FruitOrderingDocument to compare database
use App\FruitOrderingDocument;
// use Companies model to compare route name where it doesn't match with picklist names
use App\Company;
// use App\Exports;

class PickListsController extends Controller
{
  // public $week_start = 230718;

  public function export($week_start = 270818)
  {

    // return (new PicklistsExport)->download('invoices.xlsx');
    // return \Excel::export(new Export);
    return \Excel::download(new Exports\PicklistsExport($week_start), 'picklists' . $week_start . '.xlsx');

  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $picklists = Picklist::all();
        return $picklists;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if (($handle = fopen(public_path() . '/picklist-import-test-noheaders.csv', 'r')) !== FALSE) {

          while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            $company_name_encoded = json_encode($data[1]);
            $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
            $company_name = json_decode($company_name_fixed);

            $picklistData = new PickList();
            $picklistData->week_start = $data[0];
            $picklistData->company_name = $company_name;
            $picklistData->fruit_crates = $data[2];
            $picklistData->fruit_boxes = $data[3];
            $picklistData->deliciously_red_apples = $data[4];
            $picklistData->pink_lady_apples = $data[5];
            $picklistData->red_apples = $data[6];
            $picklistData->green_apples = $data[7];
            $picklistData->satsumas = $data[8];
            $picklistData->pears = $data[9];
            $picklistData->bananas = $data[10];
            $picklistData->nectarines = $data[11];
            $picklistData->limes = $data[12];
            $picklistData->lemons = $data[13];
            $picklistData->grapes = $data[14];
            $picklistData->seasonal_berries = $data[15];
            $picklistData->oranges = $data[16];
            $picklistData->cucumbers = $data[17];
            $picklistData->mint = $data[18];
            $picklistData->assigned_to = $data[19];
            $picklistData->position_on_route = $data[20];
            $picklistData->delivery_day = $data[21];
            $picklistData->save();

        }
        fclose ($handle);
      }
        return redirect('/');
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

    public function updatePicklistWithRejiggedRoutes($week_start = 270818)
    {
          // Current Logic
              // Get existing Picklist data
              // $picklists = PickList::where('week_start', $week_start)->get();
              // // Just grab Company Names to quickly check if company has received previous deliveries
              // $picklist_company_names = PickList::pluck('company_name')->all();
              // // And any leading/trailing spaces
              // $picklist_company_names = array_map('trim', $picklist_company_names);
          // End of current logic.

          // New Logic
              // This picklist company names could more accurately represent picklist box_names
              $picklists = PickList::where('week_start', $week_start)->get();
              $picklist_box_names = PickList::pluck('company_name')->all();
              $picklist_box_names = array_map('trim', $picklist_box_names);
              $company_route_names = Company::pluck('route_name')->all(); // Moving this declaration to the top of function so the call only needs to be made once.
          // End of new logic.

          $newRoutes = Route::where('week_start', $week_start)->get();

        foreach($newRoutes as $newRoute) {
                // If Company entry in Routes matches a Company Name entry in Picklist and has at least one fruit box which needs picking.
                // if (in_array(strtolower(trim($newRoute->company_name)),
                //     array_map('strtolower', $picklist_box_names)) // why am i checking the picklist box names, what if the route name has no matching picklist box name?
                //     // array_map('strtolower', $picklist_company_names))
                //     && ($newRoute->fruit_boxes > 0))
                // {
                        // if the route name matches a route name in the company tables - this should be the case for most entries.
                        if (in_array(strtolower(trim($newRoute->company_name)),
                            array_map('strtolower', $company_route_names))
                            && ($newRoute->fruit_boxes > 0))
                         { // i.e Awin would be but Awin Banana Box (rightly) wouldn't.
                                $company_picklist_box_names = Company::where('route_name', $newRoute->company_name)->pluck('box_names')->all();
                                var_dump($company_picklist_box_names);
                                // now we need to check the company tables for any associated picklist boxes that match entries out for delivery this week
                                foreach ($company_picklist_box_names[0] as $company_picklist_box_name) {
                                        echo $company_picklist_box_name . '<br>';

                                        // this is only checking that a picklist entry exists in the picklist table, not necessarily that it's out for delivery this week.  Should I add week_start?
                                        $currentPicklistEntry = PickList::where('company_name', trim($company_picklist_box_name))
                                            ->where('delivery_day', $newRoute->delivery_day)->where('week_start', $week_start)->get();

                                        // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                                        if (count($currentPicklistEntry) !== 0) { // I found unpredictable results with empty(), however I feel there may be an option better than count()?

                                                // If it isn't empty, grab the company name and delivery day as currently listed in database.
                                                $selectedCompany = $currentPicklistEntry[0]->company_name;
                                                $selectedtDeliveryDay = $currentPicklistEntry[0]->delivery_day;
                                                $selectedWeekStart = $currentPicklistEntry[0]->week_start;

                                                PickList::where('company_name', trim($selectedCompany))->where('delivery_day', $selectedtDeliveryDay)->where('week_start', $week_start)
                                                    ->update([
                                                      'assigned_to' => $newRoute->assigned_to,
                                                      'position_on_route' => $newRoute->position_on_route,
                                                    ]);

                                                echo 'Updated <strong style="color: green";>' . $selectedCompany . ' on ' . $newRoute->delivery_day . '</strong>'
                                                      . ' to be on ' . $newRoute->assigned_to . ' at position: ' . $newRoute->position_on_route . '<br>';

                                        } else {
                                        echo  $company_picklist_box_name . ' hasn\'t been updated to this week ( ' . $week_start . ' ) and doesn\'t appear to be out for delivery on ' . $newRoute->delivery_day . ' this week.  Is it?<br>';
                                        }
                                } // end of foreach - ($company_picklist_box_names[0] as $company_picklist_box_name)

                                // if the company has more than one entry in the company table due to being invoiced seperately, but is delivered to the same address for routing purposes.
                                // there isn't currently a case where this needs to be evaluated above 1 extra entry, (i.e JP Morgan & JP Morgan II is the only instance)
                                // so I'm CURRENTLY hardcoding this second check to ONLY loop through the optional extra array in $company_picklist_box_names[1].
                                if (count($company_picklist_box_names) > 1) {
                                        foreach($company_picklist_box_names[1] as $company_picklist_box_name) {
                                                echo $company_picklist_box_name . '<br>';

                                                // this is only checking that a picklist entry exists in the picklist table, not necessarily that it's out for delivery this week.  Should I add week_start?
                                                $currentPicklistEntry = PickList::where('company_name', trim($company_picklist_box_name))
                                                    ->where('delivery_day', $newRoute->delivery_day)->where('week_start', $week_start)->get();

                                                // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                                                if (count($currentPicklistEntry) !== 0) { // I found unpredictable results with empty(), however I feel there may be an option better than count()?

                                                        // If it isn't empty, grab the company name and delivery day as currently listed in database.
                                                        $selectedCompany = $currentPicklistEntry[0]->company_name;
                                                        $selectedtDeliveryDay = $currentPicklistEntry[0]->delivery_day;
                                                        $selectedWeekStart = $currentPicklistEntry[0]->week_start;

                                                        PickList::where('company_name', trim($selectedCompany))->where('delivery_day', $selectedtDeliveryDay)->where('week_start', $week_start)
                                                            ->update([
                                                              'assigned_to' => $newRoute->assigned_to,
                                                              'position_on_route' => $newRoute->position_on_route,
                                                        ]);

                                                        echo 'Updated <strong style="color: green";>' . $selectedCompany . ' on ' . $newRoute->delivery_day . '</strong>'
                                                                  . ' to be on ' . $newRoute->assigned_to . ' at position: ' . $newRoute->position_on_route . '<br>';

                                                } else {
                                                echo  $selectedCompany . ' out for delivery on ' . $selectedtDeliveryDay . ' ( ' . $selectedWeekStart . ' ) is being treated as an old entry.  Is it?<br>';
                                                }
                                        } // end of foreach - ($company_picklist_box_names[1] as $company_picklist_box_name)
                                } // end of if - ($count($company_picklist_box_names) > 1)

                        // } else {
                        //     'We found an entry for ' . $newRoute->company_name . ' in picklists, but not in routes?  This shouldn\'t happen!';
                        // }// end of if/else (in_array($newRoute->company_name, $company_route_names))

                } else {
                        echo 'Couldn\'t locate route for ' . '<strong style="color: red";>' . $newRoute->company_name
                            . ' - char: ' . strlen($newRoute->company_name) . '</strong> in Company (route_names) to access associated Company (box_names). Checking the exceptions array...<br>  Or fruit boxes for delivery (' . $newRoute->fruit_boxes . ') won\'t be troubling the picklist team<br>';

                        $company_route_name_exceptions =    [
                                                                'Legal and General London (FAO Simon Chong)' => 'Legal and General London',
                                                                'London Business School (FAO Victoria Gilbert)' => 'Legal and General London',
                                                                'JP Morgan (FAO Sara Cordwell 15th Floor)' => 'JP Morgan',
                                                                'TI Media Limited (FAO Ruth Stanley)' => 'TI Media Limited',
                                                                'Lloyds (Gatwick - FAO Katie Artlett)' => 'Lloyds (Gatwick)',
                                                                'Lloyds (London - London Wall - FAO Elaine Charlery)' => 'Lloyds (London - London Wall)',
                                                                'Lloyds (London - 10 Gresham Street – FAO Marytn Shone / Ben Pryce)' => 'Lloyds (London - 10 Gresham Street)',
                                                                'Lloyds (London - 25 Gresham Street - FAO James Gamble / Maryn Shone / Ben Pryce)' => 'Lloyds (London - 25 Gresham Street)',
                                                                'Lloyds (London - Old Broad Street - FAO Jamie Mcreesh / Daniel Lee / Parul Patel)' => 'Lloyds (London - Old Broad Street)'
                                                            ];

                          // If $newRoute->company_name doesn't match a Company route_name, check to see if this value matches a Company route_name exception.
                          // These are some of the rare cases where the route name is tailored for the delivery with an FAO attached.
                        if (array_search($newRoute->company_name, $company_route_name_exceptions)) {
                                // if it finds a matching value, it returns the associated key.
                                $newRoute->company_name = array_search($newRoute->company_name, $company_route_name_exceptions);
                        }

                        // (If it does), then check the associated company->box_names
                        $company_picklist_box_names = Company::where('route_name', $newRoute->company_name)->pluck('box_names')->all(); // Error:  Call to a member function where() on array
                        if (count($company_picklist_box_names) !== 0) {
                                foreach ($company_picklist_box_names[0] as $company_picklist_box_name) {

                                        // attempt to grab the valid week_start from an entry in the picklists for this week, if this fails we will output 'unavailable' further down.
                                        $picklist_box_week_start = Picklist::where('company_name', $company_picklist_box_name)->where('week_start', $week_start)->pluck('week_start')->first();
                                        // grab an array of the week days this compnay is expecting a delivery for this week.
                                        $selected_picklist_box_delivery_day = Picklist::where('company_name', $company_picklist_box_name)->where('week_start', $week_start)->pluck('delivery_day')->all();

                                        var_dump($picklist_box_week_start);
                                        var_dump($selected_picklist_box_delivery_day);

                                        if(empty($picklist_box_week_start)) {
                                          $picklist_box_week_start = ' unavailable ';
                                        } else {
                                          $picklist_box_week_start = (int) $picklist_box_week_start;
                                        }


                                        if (in_array($company_picklist_box_name, $picklist_box_names) // If company picklist name in list of picklist box names
                                            && ($picklist_box_week_start == $week_start) // If picklist box name has been updated to this weeks start date
                                            && (in_array($newRoute->delivery_day, $selected_picklist_box_delivery_day))) // And the picklist name is down for a delivery on this particular day of the week.
                                        {
                                            // if all checks are passed we have an entry to update with the new 'assigned_to' and 'position_on_route' values.
                                                PickList::where('company_name', trim($company_picklist_box_name))->where('delivery_day', $newRoute->delivery_day)
                                                  ->update([
                                                    'assigned_to' => $newRoute->assigned_to,
                                                    'position_on_route' => $newRoute->position_on_route,
                                                ]);

                                                echo 'Found and updated ' . '<strong style="color: green";>' . $company_picklist_box_name . '</strong> as an associated picklist to <strong style="color: green";>'
                                            . $newRoute->company_name . '</strong> which is out for delivery (' . $newRoute->delivery_day . ' / ' . $picklist_box_week_start . ') <br>';

                                        } else {
                                            echo 'Couldn\'t find picklist for ' . $newRoute->company_name . ', or company listed box name: ( ' . $company_picklist_box_name . ' )
                                             was last updated on ( ' . $picklist_box_week_start . ' ) and is not due for a delivery on ' . $newRoute->delivery_day . ' this week (' . $week_start . '). <br>';
                                        }
                                  } //end of foreach ($company_picklist_box_names[0] as $company_picklist_box_name)
                        } else {
                            echo $newRoute->company_name . ' does not appear to have any associated picklists, are they snacks and/or drinks only? <br>';
                        } // end of if/else (count($company_picklist_box_names) !== 0)
                }
          } // end of - foreach($newRoutes as $newRoute)
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function update(Request $request, $id) --Maybe replace $id with $week_start? This would help to automate collecting only the recently updated data for the week ahead.
    public function update(Request $request, $week_start = 270818)
    {

        // Get existing Picklist data
        $picklists = PickList::all()->toArray();
        // Just grab Company Names to quickly check if company has received previous deliveries
        $picklist_company_names = PickList::pluck('company_name')->all();
        // Get existing (and what should be freshly imported) data to update Picklists with

        // This is limited to only files which have been recently updated to the new Week Start.
        // Until the $variable can be updated manually (through users), I will need to remember to adjust it here (top of function, as parameter) before running the fod-vs-picklist
        $fruitOrderingDocuments = FruitOrderingDocument::where('week_start', $week_start)->get();

        // Now iterate through the new FOD data
        foreach($fruitOrderingDocuments as $fruitOrderingDocument) {
            // If Company entry in FOD matches a Company Name entry in Picklist and has at least one fruit box which needs picking.
            if (in_array(strtolower($fruitOrderingDocument->company_name),
                array_map('strtolower', $picklist_company_names))
                && ($fruitOrderingDocument->fruit_boxes > 0))
            {
                // Looks for an entry in the picklist which contains the same combination of delivery day and company name
                $currentPicklistEntry = PickList::where('company_name', $fruitOrderingDocument->company_name)
                  ->where('delivery_day', $fruitOrderingDocument->delivery_day)->get();

                  // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                  if (count($currentPicklistEntry) !== 0) {

                        // If it isn't empty, grab the company name and delivery day as currently listed in database.
                        $selectedCompany = $currentPicklistEntry[0]->company_name;
                        $selectedtDeliveryDay = $currentPicklistEntry[0]->delivery_day;

                        // Now that we know the company is listed in the picklists, check to see if new FOD entry delivery day matches up with an old record - EDIT: Not really necessary commenting out for now.
                        // if((strtolower($selectedCompany) == strtolower($fruitOrderingDocument->company_name))
                        // && ($selectedtDeliveryDay == $fruitOrderingDocument->delivery_day))
                        // {

                        // This finds the existing Picklist entry (with a the same combination of delivery day and company name), updating it to match the (newer) FOD table data.
                        PickList::where('company_name', $selectedCompany)->where('delivery_day', $fruitOrderingDocument->delivery_day)
                          ->update([
                            'week_start' => $fruitOrderingDocument->week_start,
                            'fruit_crates' => $fruitOrderingDocument->fruit_crates,
                            'fruit_boxes' => $fruitOrderingDocument->fruit_boxes,
                            'deliciously_red_apples' => $fruitOrderingDocument->deliciously_red_apples,
                            'pink_lady_apples' => $fruitOrderingDocument->pink_lady_apples,
                            'red_apples' => $fruitOrderingDocument->red_apples,
                            'green_apples' => $fruitOrderingDocument->green_apples,
                            'satsumas' => $fruitOrderingDocument->satsumas,
                            'pears' => $fruitOrderingDocument->pears,
                            'bananas' => $fruitOrderingDocument->bananas,
                            'nectarines' => $fruitOrderingDocument->nectarines,
                            'limes' => $fruitOrderingDocument->limes,
                            'lemons' => $fruitOrderingDocument->lemons,
                            'grapes' => $fruitOrderingDocument->grapes,
                            'seasonal_berries' => $fruitOrderingDocument->seasonal_berries,

                            // Update will only be done if the company name and delivery day match, so this doesn't need updating.
                            // 'delivery_day' => $fruitOrderingDocument->delivery_day,

                            // These aren't currently in the fod csv so will probably throw an error right now.
                            // 'oranges' => $fruitOrderingDocument->,
                            // 'cucumbers' => $fruitOrderingDocument->,
                            // 'mint' => $fruitOrderingDocument->,
                          ]);
                          echo 'Updated <strong style="color: green";>' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . '</strong>' . '<br>';
                        // } // This is the end of (optional) if statement that double checks whether the company and delivery day combination match the data about to be updated.
                             // This check is also basically made in the new count() if statement which doesn't break should the picklist company/delivery day combination not already exist.

                        // If the company appears in previous picklists but not on this delivery day, we need a new entry in the picklist.
                    } else {

                           echo 'Couldn\'t locate delivery for ' . '<strong style="color: red";>' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . '</strong> in picklist. <br> Adding now :) <br>';

                           $newPicklistData = new PickList();
                           $newPicklistData->week_start = $fruitOrderingDocument->week_start;
                           $newPicklistData->company_name = $fruitOrderingDocument->company_name;
                           $newPicklistData->fruit_crates = $fruitOrderingDocument->fruit_crates;
                           $newPicklistData->fruit_boxes = $fruitOrderingDocument->fruit_boxes;
                           $newPicklistData->deliciously_red_apples = $fruitOrderingDocument->deliciously_red_apples;
                           $newPicklistData->pink_lady_apples = $fruitOrderingDocument->pink_lady_apples;
                           $newPicklistData->red_apples = $fruitOrderingDocument->red_apples;
                           $newPicklistData->green_apples = $fruitOrderingDocument->green_apples;
                           $newPicklistData->satsumas = $fruitOrderingDocument->satsumas;
                           $newPicklistData->pears = $fruitOrderingDocument->pears;
                           $newPicklistData->bananas = $fruitOrderingDocument->bananas;
                           $newPicklistData->nectarines = $fruitOrderingDocument->nectarines;
                           $newPicklistData->limes = $fruitOrderingDocument->limes;
                           $newPicklistData->lemons = $fruitOrderingDocument->lemons;
                           $newPicklistData->grapes = $fruitOrderingDocument->grapes;
                           $newPicklistData->seasonal_berries = $fruitOrderingDocument->seasonal_berries;

                           // These entries aren't currently in the fruit FruitOrderingDocument so lets not worry about them for now.
                           // This will need to be addressed before too long however!

                           // $picklistData->oranges = $data[16];
                           // $picklistData->cucumbers = $data[17];
                           // $picklistData->mint = $data[18];
                           // $picklistData->assigned_to = $data[19];
                           $newPicklistData->delivery_day = $fruitOrderingDocument->delivery_day;
                           $newPicklistData->save();
                    }

                 // If they're a new company but not requesting fruit (either this week or ever) we just need to omit them from the picklist.
            } elseif ($fruitOrderingDocument->fruit_boxes == 0) {
                echo 'Omitting ' . $fruitOrderingDocument->company_name . ' from Picklist as no fruit is on delivery. <br>';

                // If we couldn't find them in the picklist, but they're ordering at least one box of fruit we need to make a new entry in the picklists.
            } else {

                  echo 'Couldn\'t locate any previous delivery for ' . '<strong style="color: red";>' . $fruitOrderingDocument->company_name . ' on any day, including ' . $fruitOrderingDocument->delivery_day . '</strong> in our picklist records. <br> Adding now :) <br>';

                  $newPicklistData = new PickList();
                  $newPicklistData->week_start = $fruitOrderingDocument->week_start;
                  $newPicklistData->company_name = $fruitOrderingDocument->company_name;
                  $newPicklistData->fruit_crates = $fruitOrderingDocument->fruit_crates;
                  $newPicklistData->fruit_boxes = $fruitOrderingDocument->fruit_boxes;
                  $newPicklistData->deliciously_red_apples = $fruitOrderingDocument->deliciously_red_apples;
                  $newPicklistData->pink_lady_apples = $fruitOrderingDocument->pink_lady_apples;
                  $newPicklistData->red_apples = $fruitOrderingDocument->red_apples;
                  $newPicklistData->green_apples = $fruitOrderingDocument->green_apples;
                  $newPicklistData->satsumas = $fruitOrderingDocument->satsumas;
                  $newPicklistData->pears = $fruitOrderingDocument->pears;
                  $newPicklistData->bananas = $fruitOrderingDocument->bananas;
                  $newPicklistData->nectarines = $fruitOrderingDocument->nectarines;
                  $newPicklistData->limes = $fruitOrderingDocument->limes;
                  $newPicklistData->lemons = $fruitOrderingDocument->lemons;
                  $newPicklistData->grapes = $fruitOrderingDocument->grapes;
                  $newPicklistData->seasonal_berries = $fruitOrderingDocument->seasonal_berries;

                  // These entries aren't currently in the fruit FruitOrderingDocument so lets not worry about them for now.
                  // This will need to be addressed before too long however!

                  // $picklistData->oranges = $data[16];
                  // $picklistData->cucumbers = $data[17];
                  // $picklistData->mint = $data[18];
                  // $picklistData->assigned_to = $data[19];
                  $newPicklistData->delivery_day = $fruitOrderingDocument->delivery_day;
                  $newPicklistData->save();

                  // Update the company table box_names with the new box added to array.

                  $addToCompanyBoxes = Company::where('invoice_name', $fruitOrderingDocument->company_name)->orWhere('route_name', $fruitOrderingDocument->company_name)->pluck('box_names')->all();

                  $addToCompanyBoxes = empty($addToCompanyBoxes) ? '[0] => []' : $addToCompanyBoxes;
                  if (!in_array($fruitOrderingDocument->company_name, $addToCompanyBoxes[0] )) {

                      $addToCompanyBoxes[0] = array_filter($addToCompanyBoxes[0]);
                      $addToCompanyBoxes[0][] = $fruitOrderingDocument->company_name;
                      Company::where('invoice_name', $fruitOrderingDocument->company_name)->orWhere('route_name', $fruitOrderingDocument->company_name)->update([

                          'box_names' => json_encode($addToCompanyBoxes[0]),
                      ]);
                      echo 'Adding box name ' . $fruitOrderingDocument->company_name . ' to Company (table column) box_names.';
                  } else {
                      echo 'Box name ' . $fruitOrderingDocument->company_name . ' already found in Company table, so no update needed.';
                  }

                   // dd($addToCompanyBoxes);


            }
        } // end of foreach - ($fruitOrderingDocuments as $fruitOrderingDocument)

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
