<?php

namespace App\Http\Controllers;

use App\MilkBox;
use App\MilkBoxArchive;
// use App\Company;
use App\CompanyDetails;
use App\WeekStart;
use App\CompanyRoute;
use App\FruitPartner;
use App\AssignedRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Carbon\CarbonImmutable;

class MilkBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // As the delivery day field on the form is a multiple checkbox, (for each day of the week) there could potentially be 5 delivery days to process.
        // I think it makes the most sense to have a fresh entry for each day because this allows the most flexibility.
        // It allows for the same box name to be on different routes, on different days for example which is important.
        // It also allows for a single day to be easily paused, or tailored.

        foreach (request('company_data.delivery_day') as $delivery_day)
        {
            // Instead of creating a validation rule to check for unique name/day combo's we can make a quick db call, and skip creation if we already have a result.
            if (count(MilkBox::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0)
            {

                $newMilkbox = new MilkBox();
                // $newFruitbox->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newMilkbox->fruit_partner_id = request('company_data.fruit_partner_id');
                $newMilkbox->company_details_id = request('company_data.company_details_id');
                //$newMilkbox->route_id = request('company_data.route_id'); // Not using this for anything.
                $newMilkbox->next_delivery = request('company_data.first_delivery');
                $newMilkbox->frequency = request('company_data.frequency');
                $newMilkbox->week_in_month = request('company_data.week_in_month');
                $newMilkbox->delivery_day = $delivery_day;
                // Milk 2l
                $newMilkbox->semi_skimmed_2l = request('company_data.semi_skimmed_2l');
                $newMilkbox->skimmed_2l = request('company_data.skimmed_2l');
                $newMilkbox->whole_2l = request('company_data.whole_2l');
                // Milk 1l
                $newMilkbox->semi_skimmed_1l = request('company_data.semi_skimmed_1l');
                $newMilkbox->skimmed_1l = request('company_data.skimmed_1l');
                $newMilkbox->whole_1l = request('company_data.whole_1l');
                // Organic Milk 2l
                $newMilkbox->organic_semi_skimmed_2l = request('company_data.organic_semi_skimmed_2l');
                $newMilkbox->organic_skimmed_2l = request('company_data.organic_skimmed_2l');
                $newMilkbox->organic_whole_2l = request('company_data.organic_whole_2l');
                // Organic Milk 1l
                $newMilkbox->organic_semi_skimmed_1l = request('company_data.organic_semi_skimmed_1l');
                $newMilkbox->organic_skimmed_1l = request('company_data.organic_skimmed_1l');
                $newMilkbox->organic_whole_1l = request('company_data.organic_whole_1l');
                // Milk Alternatives
                $newMilkbox->milk_1l_alt_coconut = request('company_data.coconut_1l');
                $newMilkbox->milk_1l_alt_unsweetened_almond = request('company_data.unsweetened_almond_1l');
                $newMilkbox->milk_1l_alt_almond = request('company_data.almond_1l');
                // Milk Alternatives (Pt2)
                $newMilkbox->milk_1l_alt_unsweetened_soya = request('company_data.unsweetened_soya_1l');
                $newMilkbox->milk_1l_alt_soya = request('company_data.soya_1l');
                $newMilkbox->milk_1l_alt_oat = request('company_data.oat_1l');
                // Milk Alternatives (Pt3)
                $newMilkbox->milk_1l_alt_rice = request('company_data.rice_1l');
                $newMilkbox->milk_1l_alt_cashew = request('company_data.cashew_1l');
                $newMilkbox->milk_1l_alt_lactose_free_semi = request('company_data.lactose_free_semi_skimmed_1l');
                $newMilkbox->save();

                // $companyDetails = Company::findOrFail($request['company_data']['company_id']);
                $companyDetails = CompanyDetails::findOrFail(request('company_data.company_details_id'));

                $message = "Milkbox for " . $companyDetails->route_name . " on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                // $companyDetails = Company::findOrFail($request['company_data']['company_id']);
                $companyDetails = CompanyDetails::findOrFail(request('company_data.company_details_id'));

                $message = "Milkbox for " . $companyDetails->route_name . " on $delivery_day saved.";
                Log::channel('slack')->info($message);
            }

            // Once the Milkbox has been created we need to check for an existing route on the given delivery day, or create a new one for populating.
            if (count(CompanyRoute::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0) {

                // Let's grab the current weekstart, we don't really need it but this will give us the week the company started with us.  Which might be nice to know.
                $currentWeekStart = Weekstart::findOrFail(1);

                // A route might not exist yet but when the company was set up a route name was inputted, so let's use that.
                // $companyDetails = Company::findOrFail($request['company_data']['company_id']);
                $companyDetails = CompanyDetails::findOrFail(request('company_data.company_details_id'));
                
                $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();
                
                switch ($delivery_day) {
                    case 'Monday':
                        $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                        break;
                    case 'Tuesday':
                        $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                        break;
                    case 'Wednesday':
                        $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                        break;
                    case 'Thursday':
                        $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                        break;
                    case 'Friday':
                        $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                        break;
                }
                
                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                // $newRoute->week_start = $currentWeekStart->current;
                // $newRoute->previous_delivery_week_start = null // This doesn't need to be here as there will never be a previous delivery for a new route (obvs) but I'm noting all fields in new route table here, for now.
                // $newRoute->next_delivery = $request['company_data']['first_delivery'];
                $newRoute->company_details_id = request('company_data.company_details_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->route_postcode;
                
                //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                // $newRoute->address = $companyDetails->route_summary_address;
                
                // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
                $newRoute->address = $companyDetails->route_address_line_1 . ', '
                                    . $companyDetails->route_address_line_2 . ', '
                                    . $companyDetails->route_address_line_3 . ', '
                                    . $companyDetails->route_city . ', '
                                    . $companyDetails->route_region . ', '
                                    . $companyDetails->route_postcode;
                
                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->assigned_route_id = $assigned_route_id;
                $newRoute->delivery_day = $delivery_day;
                // $newRoute->assigned_to = 'TBC'; // Another one hardcoded here but is also the default (database value).
                // $newRoute->position_on_route = null; // Until it's on a route we can't know its position, this will always start off as null.
                // $newRoute->fruit_crates = 0; // This will also be the default, a new route doesn't mean a new company.
                // $newRoute->snacks = null; // Thinking of keeping these fields in the routes table, they'll be editable fields, updated manually.
                // $newRoute->drinks = null; // Thinking of keeping these fields in the routes table, they'll be editable fields, updated manually.
                // $newRoute->other =  null; // Thinking of keeping these fields in the routes table, they'll be editable fields, updated manually.
                $newRoute->save();

                $message = "Route $newRoute->company_name on $delivery_day saved.";
                Log::channel('slack')->info($message);

                // Let's hold this action for 5 seconds just to give the entry a chance to be created before we check for its existence.
                sleep(5);

                // New routes need to be created before we have an id for them.  At this point we could then apply that route_id to the new milkbox entry if it has been created quickly enough.
                $newlyCreatedRoute = CompanyRoute::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get();

                if (count(MilkBox::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get()) > 0) {

                    // I'm not currently using this column, and have actually stripped it from the db.
                    
                    // MilkBox::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->update([
                    //     'route_id' => $newlyCreatedRoute[0]->id
                    // ]);

                    $message = "Route ID: ". $newlyCreatedRoute[0]->id . " added to Milkbox on $delivery_day saved.";
                    Log::channel('slack')->info($message);

                } else {

                    $message = "Route ID: " . $newlyCreatedRoute[0]->id . " could not be added to Milkbox on $delivery_day saved.";
                    Log::channel('slack')->info($message);
                }

            } else {

                // We can update the existing entry.
                // Scrap that, as we're not saving any fruitbox data to the routes anymore we won't need to update anything here.
                // Or at least that's my current thinking.  -- Well actually we could add the route id to the milkbox entry if we have one. --
                
                // EDIT: ACTUALLY NAH, THERE'S NO POINT.
                // $existingRoute = CompanyRoute::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get();
                // // dd($existingRoute);
                // MilkBox::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->update([
                //     'route_id' => $existingRoute[0]->id
                // ]);
                // 
                // $message = "Route ID: " . $existingRoute[0]->id . " added to Milkbox on $delivery_day saved.";
                // Log::channel('slack')->info($message);

            }

        } // End Of Foreach - ($request['company_data']['delivery_day'] as $delivery_day)
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
    public function update(Request $request, $id)
    {
        // dd($request);
        
        $date = CarbonImmutable::now('Europe/London');
        $invoice_date = $date->format('ymd');
        
        // We want to check whether the milkbox about to be updated has already had its old order invoiced.
        // So before we begin, let's grab the current data from the db.
        $existing_milkbox_entry = MilkBox::findOrFail($id);
        // And specifically the invoiced_at date, which despite being inserted from a Carbon date, is now considered a string?
        // So first it needs passing back into Carbon...
        $converted_invoiced_at_date = new CarbonImmutable($existing_milkbox_entry->invoiced_at);
        
        // if skip-archive is true then we're deliberately trying to update the box and bypassing archive logic entirely.
        // if the current fruitbox order was updated wrongly this'll prevent the unwanted contents from being stored in the archives,
        // as it's just a mistake we all want to forget.
        if (request('skip_archive') === 'false') {

            // ...which we can then use to reformat both dates into the same style (->format('ymd')).
            // Now we can check if the entry was last updated on the same day as it was invoiced.  If so, odds are very good (100%?) that we have nothing new to archive and charge for.
            if ($existing_milkbox_entry->updated_at->format('ymd') == $converted_invoiced_at_date->format('ymd')) {
        
                // Then the old order has already been process (invoiced) so we just need to create an archive, with an 'Inactive' status
                $create_milkbox_archive = new MilkBoxArchive();
                $create_milkbox_archive->is_active = 'Inactive';
                $create_milkbox_archive->fruit_partner_id = request('fruit_partner_id');
                $create_milkbox_archive->company_details_id = request('company_details_id');
                
                $create_milkbox_archive->next_delivery = request('next_delivery');
                $create_milkbox_archive->frequency = request('frequency');
                $create_milkbox_archive->week_in_month = request('week_in_month');
                $create_milkbox_archive->delivery_day = request('delivery_day');
                // Milk 2l
                $create_milkbox_archive->semi_skimmed_2l = request('semi_skimmed_2l');
                $create_milkbox_archive->skimmed_2l = request('skimmed_2l');
                $create_milkbox_archive->whole_2l = request('whole_2l');
                // Milk 1l
                $create_milkbox_archive->semi_skimmed_1l = request('semi_skimmed_1l');
                $create_milkbox_archive->skimmed_1l = request('skimmed_1l');
                $create_milkbox_archive->whole_1l = request('whole_1l');
                // Organic Milk 2l
                $create_milkbox_archive->organic_semi_skimmed_2l = request('organic_semi_skimmed_2l');
                $create_milkbox_archive->organic_skimmed_2l = request('organic_skimmed_2l');
                $create_milkbox_archive->organic_whole_2l = request('organic_whole_2l');
                // Organic Milk 1l
                $create_milkbox_archive->organic_semi_skimmed_1l = request('organic_semi_skimmed_1l');
                $create_milkbox_archive->organic_skimmed_1l = request('organic_skimmed_1l');
                $create_milkbox_archive->organic_whole_1l = request('organic_whole_1l');
                // Milk Alternatives
                $create_milkbox_archive->milk_1l_alt_coconut = request('coconut_1l');
                $create_milkbox_archive->milk_1l_alt_unsweetened_almond = request('unsweetened_almond_1l');
                $create_milkbox_archive->milk_1l_alt_almond = request('almond_1l');
                // Milk Alternatives (Pt2)
                $create_milkbox_archive->milk_1l_alt_unsweetened_soya = request('unsweetened_soya_1l');
                $create_milkbox_archive->milk_1l_alt_soya = request('soya_1l');
                $create_milkbox_archive->milk_1l_alt_oat = request('oat_1l');
                // Milk Alternatives (Pt3)
                $create_milkbox_archive->milk_1l_alt_rice = request('rice_1l');
                $create_milkbox_archive->milk_1l_alt_cashew = request('cashew_1l');
                $create_milkbox_archive->milk_1l_alt_lactose_free_semi = request('lactose_free_semi_skimmed_1l');
                $create_milkbox_archive->save();
                
            } else { // if ($existing_fruitbox_entry->updated_at->format('ymd') == $converted_invoiced_at_date->format('ymd'))
                
                // Then the old order still needs to be processed (invoiced) so we need to create an archive, with an 'Active' status
                // This way it'll get pulled into the next invoicing run of their chosen branding theme.
                
                MilkBoxArchive::updateOrInsert(
                [
                    'milkbox_id' => request('id'),
                    'company_details_id' => request('company_details_id'),
                    'next_delivery' => request('next_delivery'),
                    'delivery_day' => request('delivery_day'), // Technically, milkbox_id + next_delivery_week should be all that's needed but testing will probably prove me wrong.
                    'is_active' => request('is_active'),
                ],
                [            
                    'fruit_partner_id' => request('fruit_partner_id'),
                    'frequency' => request('frequency'),
                    'week_in_month' => request('week_in_month'),
                    // 'delivery_day' => request('delivery_day'); // Moved this data up to the initial check.
                    // Milk 2l
                    'semi_skimmed_2l' => request('semi_skimmed_2l'),
                    'skimmed_2l' => request('skimmed_2l'),
                    'whole_2l' => request('whole_2l'),
                    // Milk 1l
                    'semi_skimmed_1l' => request('semi_skimmed_1l'),
                    'skimmed_1l' => request('skimmed_1l'),
                    'whole_1l' => request('whole_1l'),
                    // Organic Milk 2l
                    'organic_semi_skimmed_2l' => request('organic_semi_skimmed_2l'),
                    'organic_skimmed_2l' => request('organic_skimmed_2l'),
                    'organic_whole_2l' => request('organic_whole_2l'),
                    // Organic Milk 1l
                    'organic_semi_skimmed_1l' => request('organic_semi_skimmed_1l'),
                    'organic_skimmed_1l' => request('organic_skimmed_1l'),
                    'organic_whole_1l' => request('organic_whole_1l'),
                    // Milk Alternatives
                    'milk_1l_alt_coconut' => request('milk_1l_alt_coconut'),
                    'milk_1l_alt_unsweetened_almond' => request('milk_1l_alt_unsweetened_almond'),
                    'milk_1l_alt_almond' => request('milk_1l_alt_almond'),
                    // Milk Alternatives (Pt2)
                    'milk_1l_alt_unsweetened_soya' => request('milk_1l_alt_unsweetened_soya'),
                    'milk_1l_alt_soya' => request('milk_1l_alt_soya'),
                    'milk_1l_alt_oat' => request('milk_1l_alt_oat'),
                    // Milk Alternatives (Pt3)
                    'milk_1l_alt_rice' => request('milk_1l_alt_rice'),
                    'milk_1l_alt_cashew' => request('milk_1l_alt_cashew'),
                    'milk_1l_alt_lactose_free_semi' => request('milk_1l_alt_lactose_free_semi'),
                ]);
            }
        } // if (request('skip-archive') == 'true')
        
        //---------- Start of regular update process ----------//
        
        // Whatever else needs to be done, we need to make sure we update the entry, so let's get that sorted first.
        MilkBox::where('id', $id)->update([
            'is_active' => request('is_active'),
            'fruit_partner_id' => request('fruit_partner_id'),
            // 'company_details_id' => request('company_details_id'); But is this going to change? No, it's not going to change.
            // 'route_id' => request('route_id');  Is this going to change either?  Nope, well not in this part of the function anyway.
            // 'previous_delivery_week_start' => request('previous_delivery_week');
            'next_delivery' => request('next_delivery'), // Being able to change this is actually very important!
            'delivery_day' => request('delivery_day'),
            'frequency' => request('frequency'),
            'week_in_month' => request('week_in_month'),  // This is an optional field only necessary if the frequency is monthly, or potentially bespoke.
            // Milk 2l
            'semi_skimmed_2l' => request('semi_skimmed_2l'),
            'skimmed_2l' => request('skimmed_2l'),
            'whole_2l' => request('whole_2l'),
            // Milk 1l
            'semi_skimmed_1l' => request('semi_skimmed_1l'),
            'skimmed_1l' => request('skimmed_1l'),
            'whole_1l' => request('whole_1l'),
            // Organic Milk 2l
            'organic_semi_skimmed_2l' => request('organic_semi_skimmed_2l'),
            'organic_skimmed_2l' => request('organic_skimmed_2l'),
            'organic_whole_2l' => request('organic_whole_2l'),
            // Organic Milk 1l
            'organic_semi_skimmed_1l' => request('organic_semi_skimmed_1l'),
            'organic_skimmed_1l' => request('organic_skimmed_1l'),
            'organic_whole_1l' => request('organic_whole_1l'),
            // Milk Alternatives
            'milk_1l_alt_coconut' => request('milk_1l_alt_coconut'),
            'milk_1l_alt_unsweetened_almond' => request('milk_1l_alt_unsweetened_almond'),
            'milk_1l_alt_almond' => request('milk_1l_alt_almond'),
            // Milk Alternatives (Pt2)
            'milk_1l_alt_unsweetened_soya' => request('milk_1l_alt_unsweetened_soya'),
            'milk_1l_alt_soya' => request('milk_1l_alt_soya'),
            'milk_1l_alt_oat' => request('milk_1l_alt_oat'),
            // Milk Alternatives (Pt3)
            'milk_1l_alt_rice' => request('milk_1l_alt_rice'),
            'milk_1l_alt_cashew' => request('milk_1l_alt_cashew'),
            'milk_1l_alt_lactose_free_semi' => request('milk_1l_alt_lactose_free_semi'),
        ]);
        
        // Now we need to check that the milkbox still has a deliverable route, if for example we've just changed then delivery day.
        if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get())) {
            
            // We have nothing else we need to do.
            // $company = Company::findOrFail(request('company_id'));
            $company = CompanyDetails::findOrFail(request('company_details_id'));
            // But to log the change for our records.
            $route = CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get();
            // ^^^ Not true, we should be checking if the route is active already because if not we'll need to change that status to get pulled into the routes for exporting etc.
            if ($route[0]->is_active == 'Active') {
                
                $message = "Route for updated box called " . $company->route_name . " on " . request('delivery_day') . " already found.";
                Log::channel('slack')->info($message);
                
            } else {
                
                FruitBox::where('id', $id)->update([
                    'is_active' => 'Active'
                ]);
                
                $message = "Route for updated box " . $company->route_name . " on " . request('delivery_day') . " already found but Inactive, Reactivating now...";
                Log::channel('slack')->info($message);
            }
            
        } elseif (request('fruit_partner_id') == 1) {
            
            // If we're here, a route wasn't found for the new delivery day, and we've confirmed it's an Office Pantry delivery, so we'd better make one.
            // A route might not exist yet but when the company was set up a route name was inputted, so let's use that.
            
            // $companyDetails = Company::findOrFail($request['company_id']);
            $companyDetails = CompanyDetails::findOrFail(request('company_details_id'));
            
            $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
            $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
            $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
            $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
            $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();
            
            switch (request('delivery_day')) {
                case 'Monday':
                    $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                    break;
                case 'Tuesday':
                    $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                    break;
                case 'Wednesday':
                    $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                    break;
                case 'Thursday':
                    $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                    break;
                case 'Friday':
                    $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                    break;
            }
            
            // We need to create a new entry.
            $newRoute = new CompanyRoute();
            // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
            $newRoute->company_details_id = request('company_details_id');
            $newRoute->route_name = $companyDetails->route_name;
            $newRoute->postcode = $companyDetails->route_postcode;
            
            //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
            // $newRoute->address = $companyDetails->route_summary_address;
            
            // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
            $newRoute->address = implode(", ", array_filter([
                    $companyDetails->route_address_line_1, 
                    $companyDetails->route_address_line_2, 
                    $companyDetails->route_address_line_3, 
                    $companyDetails->route_city, 
                    $companyDetails->route_region
                ]));
            
            $newRoute->delivery_information = $companyDetails->delivery_information;
            $newRoute->assigned_route_id = $assigned_route_id;
            $newRoute->delivery_day = request('delivery_day');
            $newRoute->save();


            $message = "Route $newRoute->company_name on " . request('delivery_day') . " saved.";
            Log::channel('slack')->info($message);
            
        } else {
            $fruit_partner = FruitPartner::findOrFail(request('fruit_partner_id'));
            // This is an updated entry for a fruit third party, while we don't need to build a route, we should log the change as the fruit partner will need to know about it.
            // At the moment this will just get lost in the slack feed for laravel log, so we'll need to set up another channel just to send information like this in a readable manner.
            $message = "Route for updated box " . request('name') . " on " . request('delivery_day') . " not needed as it's delivered by our fruit partner " . $fruit_partner->name;
            Log::channel('slack')->info($message);
            
        }
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
