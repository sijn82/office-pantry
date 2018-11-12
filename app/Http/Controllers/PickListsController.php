<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exports;
use Illuminate\Support\Facades\Log;
use App\PickList;
use App\Route;
use Illuminate\Http\Request;
// Use weekStart to make sure everything is run off of the recently imported new week_start variable.
use App\WeekStart;
// Use FruitOrderingDocument to compare database
use App\FruitOrderingDocument;
// use Companies model to compare route name where it doesn't match with picklist names
use App\Company;
// use App\Exports;


// Function index                                                                   Changes for picklist and routing

// public function __construct()                            - 36                    - 38 (Change $week_start date)
// public function full_export()                            - 41
// public function export()                                 - 48
// public function index()                                  - 62
// public function create()                                 - 74
// public function store(Request $request)                  - 85
// public function show($id)                                - 133
// public function edit($id)                                - 144
// public function updatePicklistWithRejiggedRoutes()       - 149 (url - update-picklist-with-routes)
// public function update(Request $request)                 - 329 (url - picklist-vs-fod)
// public function destroy($id)                             - 499
set_time_limit(0);
class PickListsController extends Controller
{


    protected $week_start;
    protected $delivery_days;

    public function __construct()
    {
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];
    }

    public function full_export()
    {
        // $picklistcollection = Picklist::where('week_start', $this->week_start)->get();

        return \Excel::download(new Exports\PicklistsExportFull($this->week_start), 'picklists-full' . $this->week_start . '.xlsx');
    }

    public function export()
    {

    // return (new PicklistsExport)->download('invoices.xlsx');
    // return \Excel::export(new Export);
    return \Excel::download(new Exports\PicklistsExport($this->week_start), 'picklists' . $this->week_start . '.xlsx');

    }
    public function berry_export()
    {

    // return (new PicklistsExport)->download('invoices.xlsx');
    // return \Excel::export(new Export);
    return \Excel::download(new Exports\BerryPicklistsExport($this->week_start), 'berry-picklists' . $this->week_start . '.xlsx');

    }

    public function berry_totals()
    {
        // Grab the picklists for this week, after the routes have been run to update them.
        $picklists_with_berries = Picklist::where('week_start', $this->week_start)->where('seasonal_berries', '>', 0)->OrderBy('delivery_day')->orderBy('assigned_to', 'desc')->get();
        $grouped_by_route_picklists_with_berries = $picklists_with_berries->groupBy('assigned_to', 'desc');
        // dd($picklists_with_berries);
        // dd($grouped_by_route_picklists_with_berries);


        // each distinct route out for delivery this week.
       return view('exports.berry-picklists', [
                   'week_start'         =>   $this->week_start,
                   'routes'             =>   $grouped_by_route_picklists_with_berries,
                   'route_day'          =>   'Monday'
               ]);

        // dd($assigned_picklist_routes);


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

    public function updatePicklistWithRejiggedRoutes()
    {
          // Current Logic
              // Get existing Picklist data
              // $picklists = PickList::where('week_start', $this->week_start)->get();
              // // Just grab Company Names to quickly check if company has received previous deliveries
              // $picklist_company_names = PickList::pluck('company_name')->all();
              // // And any leading/trailing spaces
              // $picklist_company_names = array_map('trim', $picklist_company_names);
          // End of current logic.

          // New Logic
              // This picklist company names could more accurately represent picklist box_names
              $picklists = PickList::where('week_start', $this->week_start)->get();
              $picklist_box_names = PickList::pluck('company_name')->all();
              $picklist_box_names = array_map('trim', $picklist_box_names);
              $company_route_names = Company::pluck('route_name')->all(); // Moving this declaration to the top of function so the call only needs to be made once.
          // End of new logic.

          $newRoutes = Route::where('week_start', $this->week_start)->get();

          foreach($newRoutes as $newRoute) {

                    // $company_route_name_exceptions =    [
                    //                                         'Legal and General London (FAO Simon Chong)' => 'Legal and General London',
                    //                                         'London Business School (FAO Victoria Gilbert)' => 'London Business School',
                    //                                         'JP Morgan (FAO Sara Cordwell 15th Floor)' => 'JP Morgan',
                    //                                         'JP Morgan II (FAO Sara Cordwell 15th Floor)' => 'JP Morgan II',
                    //                                         'TI Media Limited (FAO Ruth Stanley)' => 'TI Media Limited',
                    //                                         'Lloyds (Gatwick - FAO Katie Artlett)' => 'Lloyds (Gatwick)',
                    //                                         'Lloyds (London - London Wall - FAO Elaine Charlery)' => 'Lloyds (London - London Wall)',
                    //                                         'Lloyds (London - 10 Gresham Street – FAO Marytn Shone / Ben Pryce)' => 'Lloyds (London - 10 Gresham Street)',
                    //                                         'Lloyds (London - 25 Gresham Street - FAO James Gamble / Maryn Shone / Ben Pryce)' => 'Lloyds (London - 25 Gresham Street)',
                    //                                         'Lloyds (London - Old Broad Street - FAO Jamie Mcreesh / Daniel Lee / Parul Patel)' => 'Lloyds (London - Old Broad Street)'
                    //                                     ];
                    //
                    //       // If $newRoute->company_name doesn't match a Company route_name, check to see if this value matches a Company route_name exception.
                    //       // These are some of the rare cases where the route name is tailored for the delivery with an FAO attached.
                    //     if (array_search($newRoute->company_name, $company_route_name_exceptions)) {
                    //             // if it finds a matching value, it returns the associated key.
                    //             $newRoute->company_name = array_search($newRoute->company_name, $company_route_name_exceptions);
                    //     }

                        // if the route name matches a route name in the company tables - this should be the case for most entries.
                        if (in_array(strtolower(trim($newRoute->company_name)),
                            array_map('strtolower', $company_route_names))
                            && ($newRoute->fruit_boxes > 0))
                         { // i.e Awin would be but Awin Banana Box (rightly) wouldn't.
                                $company_picklist_box_names = Company::where('route_name', $newRoute->company_name)->pluck('box_names')->all();
                                var_dump($company_picklist_box_names);

                                $company_picklist_box_names = empty($company_picklist_box_names) ? [[]] : $company_picklist_box_names;

                                // now we need to check the company tables for any associated picklist boxes that match entries out for delivery this week
                                foreach ($company_picklist_box_names[0] as $company_picklist_box_name) {
                                        echo $company_picklist_box_name . '<br>';

                                        // this is only checking that a picklist entry exists in the picklist table, not necessarily that it's out for delivery this week.  Should I add week_start?
                                        $currentPicklistEntry = PickList::where('company_name', trim($company_picklist_box_name))
                                            ->where('delivery_day', $newRoute->delivery_day)->where('week_start', $this->week_start)->get();

                                        // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                                        if (count($currentPicklistEntry) !== 0) { // I found unpredictable results with empty(), however I feel there may be an option better than count()?

                                                // If it isn't empty, grab the company name and delivery day as currently listed in database.
                                                $selectedCompany = $currentPicklistEntry[0]->company_name;
                                                $selectedtDeliveryDay = $currentPicklistEntry[0]->delivery_day;
                                                $selectedWeekStart = $currentPicklistEntry[0]->week_start;

                                                PickList::where('company_name', trim($selectedCompany))->where('delivery_day', $selectedtDeliveryDay)->where('week_start', $this->week_start)
                                                    ->update([
                                                      'assigned_to' => $newRoute->assigned_to,
                                                      'position_on_route' => $newRoute->position_on_route,
                                                    ]);

                                                echo 'Updated <strong style="color: green";>' . $selectedCompany . ' on ' . $newRoute->delivery_day . '</strong>'
                                                      . ' to be on ' . $newRoute->assigned_to . ' at position: ' . $newRoute->position_on_route . '<br>';

                                        } else {
                                        echo  $company_picklist_box_name . ' hasn\'t been updated to this week ( ' . $this->week_start . ' ) and doesn\'t appear to be out for delivery on ' . $newRoute->delivery_day . ' this week.  Is it?<br>';
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
                                                    ->where('delivery_day', $newRoute->delivery_day)->where('week_start', $this->week_start)->get();

                                                // Only worry about successfully retrieved picklists, otherwise this variable (array) will be empty
                                                if (count($currentPicklistEntry) !== 0) { // I found unpredictable results with empty(), however I feel there may be an option better than count()?

                                                        // If it isn't empty, grab the company name and delivery day as currently listed in database.
                                                        $selectedCompany = $currentPicklistEntry[0]->company_name;
                                                        $selectedtDeliveryDay = $currentPicklistEntry[0]->delivery_day;
                                                        $selectedWeekStart = $currentPicklistEntry[0]->week_start;

                                                        PickList::where('company_name', trim($selectedCompany))->where('delivery_day', $selectedtDeliveryDay)->where('week_start', $this->week_start)
                                                            ->update([
                                                              'assigned_to' => $newRoute->assigned_to,
                                                              'position_on_route' => $newRoute->position_on_route,
                                                        ]);

                                                        echo 'Updated <strong style="color: green";>' . $selectedCompany . ' on ' . $newRoute->delivery_day . '</strong>'
                                                                  . ' to be on ' . $newRoute->assigned_to . ' at position: ' . $newRoute->position_on_route . '<br>';

                                                } else {
                                                echo  $newRoute->company_name . ' out for delivery on ' . $newRoute->delivery_day . ' ( ' . $newRoute->week_start . ' ) is being treated as an old entry.  Is it?<br>';
                                                }
                                        } // end of foreach - ($company_picklist_box_names[1] as $company_picklist_box_name)
                                } // end of if - ($count($company_picklist_box_names) > 1)

                        // } else {
                        //     'We found an entry for ' . $newRoute->company_name . ' in picklists, but not in routes?  This shouldn\'t happen!';
                        // }// end of if/else (in_array($newRoute->company_name, $company_route_names))

                } else {
                        echo 'Couldn\'t locate route for ' . '<strong style="color: red";>' . $newRoute->company_name
                            . ' - char: ' . strlen($newRoute->company_name) . '</strong> in Company (route_names) to access associated Company (box_names). Checking the exceptions array...<br>  Or fruit boxes for delivery (' . $newRoute->fruit_boxes . ') won\'t be troubling the picklist team<br>';

                        // (If it does), then check the associated company->box_names
                        $company_picklist_box_names = Company::where('route_name', $newRoute->company_name)->pluck('box_names')->all(); // Error:  Call to a member function where() on array
                        if (count($company_picklist_box_names) !== 0) {
                                foreach ($company_picklist_box_names[0] as $company_picklist_box_name) {

                                        // attempt to grab the valid week_start from an entry in the picklists for this week, if this fails we will output 'unavailable' further down.
                                        $picklist_box_week_start = Picklist::where('company_name', $company_picklist_box_name)->where('week_start', $this->week_start)->pluck('week_start')->first();
                                        // grab an array of the week days this compnay is expecting a delivery for this week.
                                        $selected_picklist_box_delivery_day = Picklist::where('company_name', $company_picklist_box_name)->where('week_start', $this->week_start)->pluck('delivery_day')->all();

                                        var_dump($picklist_box_week_start);
                                        var_dump($selected_picklist_box_delivery_day);

                                        if(empty($picklist_box_week_start)) {
                                          $picklist_box_week_start = ' unavailable ';
                                        } else {
                                          $picklist_box_week_start = (int) $picklist_box_week_start;
                                        }


                                        if (in_array($company_picklist_box_name, $picklist_box_names) // If company picklist name in list of picklist box names
                                            && ($picklist_box_week_start == $this->week_start) // If picklist box name has been updated to this weeks start date
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
                                             was last updated on ( ' . $picklist_box_week_start . ' ) and is not due for a delivery on ' . $newRoute->delivery_day . ' this week (' . $this->week_start . '). <br>';
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

    // public function update(Request $request, $id) --Maybe replace $id with $this->week_start? This would help to automate collecting only the recently updated data for the week ahead.
    public function update(Request $request)
    {
        // Create our logging variables to bulk the reponses as successful (regular), successful (created new),
        // omitted (no fruit on delivery) and couldn't locate previous records, created new entry and added to Company box names.
        $success_regular = "*Successful (Found Route/Day Combo)* \n";
        $success_created_entry_newday = "*Successful (Found Route/Created Entry on New Day)* \n";
        $omitted_from_picklist = "*Omitted (No Fruit on Order)* \n";
        $created_entry_added_boxname = "*New Entry (No existing entry in Picklists)* \n";

        // Get existing Picklist data
        $picklists = PickList::all()->toArray();
        // Just grab Company Names to quickly check if company has received previous deliveries
        $picklist_company_names = PickList::pluck('company_name')->all();
        // Get existing (and what should be freshly imported) data to update Picklists with

        // This is limited to only files which have been recently updated to the new Week Start.

        $fruitOrderingDocuments = ($this->delivery_days == 'mon-tue') ? FruitOrderingDocument::where('week_start', $this->week_start)->WhereIn('delivery_day', ['Monday', 'Tuesday'])->get()
                                                                      : FruitOrderingDocument::where('week_start', $this->week_start)->WhereIn('delivery_day', ['Wednesday', 'Thursday', 'Friday'])->get();

        // dd($fruitOrderingDocuments);

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

                            $success_regular .= '• Updated ' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . "\n";
                        // } // This is the end of (optional) if statement that double checks whether the company and delivery day combination match the data about to be updated.
                             // This check is also basically made in the new count() if statement which doesn't break should the picklist company/delivery day combination not already exist.

                        // If the company appears in previous picklists but not on this delivery day, we need a new entry in the picklist.
                    } else {

                           $success_created_entry_newday .= '• Couldn\'t locate delivery for ' . $fruitOrderingDocument->company_name . ' on ' . $fruitOrderingDocument->delivery_day . " in picklist...  Adding now :)\n";

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

                $omitted_from_picklist .= '• Omitting ' . $fruitOrderingDocument->company_name . " from Picklist as no fruit is on their " . $fruitOrderingDocument->delivery_day  . " delivery.\n";

                // If we couldn't find them in the picklist, but they're ordering at least one box of fruit we need to make a new entry in the picklists.
            } else {

                  $created_entry_added_boxname .= '• Couldn\'t locate any previous delivery for ' . $fruitOrderingDocument->company_name . ' on any day, including ' . $fruitOrderingDocument->delivery_day . " in our picklist records. \n";

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

                  // This isn't working as intended unfortunately, as if the picklist name doesn't match either the invoice or route names,
                  // then pulling through the company details to update doesn't work.  As this ection is largely used to create new entries, more often than not this will fail to run.

                  // When we are using a more consistent process for entering companies this will eliminate the issue anyway so putting this on the backburner.

                  $addToCompanyBoxes = Company::where('invoice_name', $fruitOrderingDocument->company_name)->orWhere('route_name', $fruitOrderingDocument->company_name)->orWhere('box_names', 'LIKE', '%' . $fruitOrderingDocument->company_name . '%')->pluck('box_names')->all();

                  $addToCompanyBoxes = empty($addToCompanyBoxes) ? [[]] : $addToCompanyBoxes;
                  // dd($addToCompanyBoxes);
                  if (!in_array($fruitOrderingDocument->company_name, $addToCompanyBoxes[0] )) {

                      $addToCompanyBoxes[0] = array_filter($addToCompanyBoxes[0]);
                      $addToCompanyBoxes[0][] = $fruitOrderingDocument->company_name;
                      // dd($addToCompanyBoxes);
                      Company::where('invoice_name', $fruitOrderingDocument->company_name)->orWhere('route_name', $fruitOrderingDocument->company_name)->update([

                          'box_names' => json_encode($addToCompanyBoxes[0]),
                      ]);
                      $created_entry_added_boxname .= 'Unless this box is named after invoice name or route, ' . $fruitOrderingDocument->company_name . " will need adding to Company (table column) box_names.\n";
                  } else {
                      $created_entry_added_boxname .= 'Box name ' . $fruitOrderingDocument->company_name . " already found in Company table, so no update needed.\n";
                  }

                   // dd($addToCompanyBoxes);

            }
        } // end of foreach - ($fruitOrderingDocuments as $fruitOrderingDocument)

        // Send feedback to slack on the results of each entry - grouped by the 4 main outcomes.
        $title = "*UPDATED PICKLIST RESULTS _for Week Commencing_* - $this->week_start";
        Log::channel('slack')->info($title);
        Log::channel('slack')->info($success_regular);
        Log::channel('slack')->info($success_created_entry_newday);
        Log::channel('slack')->warning($omitted_from_picklist);
        Log::channel('slack')->alert($created_entry_added_boxname);

        return redirect()->route('import-file')->with('picklist_success', 'Picklists Updated');
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

    // this is currently not necessary as adding a second orderby() to the picklistexport view query has created the results I wanted,
    // albeit with a slight php execution error that needed bypassing.
    public function reorder_seasonal_berries()
    {phpinfo();
        // all picklists for this week
        $picklists = PickList::where('week_start', $this->week_start)->get();

        // each distinct route out for delivery this week.
        $assigned_picklist_routes = Picklist::select('assigned_to')->distinct()->get();

        // dd($assigned_picklist_routes);

        $seasonal_berries = [];
        $remainder_of_picklist = [];

        // foreach picklists grouped by route
        foreach($assigned_picklist_routes as $assigned_picklist_route)
        {
            // dd($assigned_picklist_route);
            $picklists = PickList::where('week_start', $this->week_start)->where('assigned_to', $assigned_picklist_route->assigned_to)->orderBy('seasonal_berries')->orderBy('position_on_route')->get();

            // dd($picklists);
            // foreach picklist on this particular route pull the ones with seasonal berries out of the initial array so we can add them in again at the end.
            foreach($picklists as $picklist)
            {

                echo $picklist->company_name . ' has ' . $picklist->seasonal_berries . ' in picklist at position ' . $picklist->position_on_route . '<br>';

            // $reordered_picklist = usort($picklists, function($a, $b)
            //     {
            //         return strcmp($a->seasonal_berries, $b->seasonal_berries);
            //     });
            //     dd($reordered_picklist);

            //     if ($picklist->seasonal_berries > 0)
            //     {
            //         // we want to take these out of the $picklist array,
            //         // reassign the position on those that remain and add the seasonal berries in again at the end,
            //         // so they feature at the bottom of the pick list.
            //         $seasonal_berries[] = $picklist;
            //     } else {
            //
            //         $remainder_of_picklist[] = $picklist;
            //     }
            //
            }
            // var_dump($seasonal_berries);
            // var_dump($remainder_of_picklist);
            // $reordered_picklist = array_merge($remainder_of_picklist, $seasonal_berries);
            // // dd($remainder_of_picklist);
            //
            // foreach($reordered_picklist as $new_picklist)
            // {
            //     echo $new_picklist->company_name . ' has ' . $new_picklist->seasonal_berries . ' on ' . $new_picklist->assigned_to . ' at position ' . $new_picklist->position_on_route . '<br>';
            // }

        }

        // dd($seasonal_berries);
    }
}
