<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Exports;

use App\FruitBox;
use App\FruitBoxArchive;
// use App\Company;
use App\CompanyDetails;
use App\CompanyRoute;
use App\FruitPartner;
use App\WeekStart;
use App\AssignedRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Carbon\CarbonImmutable;

class FruitBoxController extends Controller
{
    protected $week_start;
    protected $delivery_days;

    public function __construct()
    {
        $week_start = WeekStart::first();

        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }
    }

    // new system fruitbox export replacement for the picklist export,
    // n.b still needs 2 model replacements AssignedRoute and CompanyRoute, for hardcoded options and Route respectively.
    public function fruitbox_export()
    {
        return \Excel::download(new Exports\FruitboxPicklistsExport($this->week_start), 'picklists' . $this->week_start . '.xlsx');
    }

    public function addRouteInfoToFruitPicklists()
    {
        $fruitboxes = FruitBox::where('is_active', 'Active')->where('next_delivery', $this->week_start)->get();

        $alphabetise = function($a, $b)
        {
            // This is using the new and sexy spaceship operator to compare company string names and return them in alphabetical order.
            $outcome = $a->assigned_to <=> $b->assigned_to;

            if ($a->assigned_to == $b->assigned_to) {
                $outcome = $a->position_on_route <=> $b->position_on_route;
            }
            // Combined with usort, some background php magic will return the (alpabetically prior) item.
            return $outcome;
        };

        foreach ($fruitboxes as $fruitbox) {
            $route = CompanyRoute::where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)->get();
            // dd($route);

            if (count($route) > 0) {

                // $combinedPicklist[] = $fruitbox;
                // $combinedPicklist['assigned_to'] = $route[0]->assigned_to;
                // $combinedPicklist['position_on_route'] = $route[0]->position_on_route;

                $fruitbox->assigned_to = $route[0]->assigned_to;
                $fruitbox->position_on_route = $route[0]->position_on_route;
                $fruitbox->fruit_crates = $route[0]->fruit_crates;

                $combinedPicklist[] = $fruitbox;

                usort($combinedPicklist, $alphabetise);

            } else {

                echo 'No matching Route Id for ' . $fruitbox->name . ' ( ' . $fruitbox->company_details_id . ' ) on ' . $fruitbox->delivery_day . '<br/>';
            }

        }

        return view('exports.fruitbox-picklists', [
           'picklists' => $combinedPicklist

       ]);
        // dd($fruitbox);
        // dd($combinedPicklist);
    }



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

        // Ok let's see if I can create some better error messages for user feedback.

        $validatedData = $request->validate([

            'fruit_partner_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'next_delivery' => 'required',
            'frequency' => 'required',
            'delivery_day' => 'required',
            'fruitbox_total' => 'required',
            'deliciously_red_apples' => 'required',
            'pink_lady_apples' => 'required',
            'red_apples' => 'required',
            'green_apples' => 'required',
            'satsumas' => 'required',
            'pears' => 'required',
            'bananas' => 'required',
            'nectarines' => 'required',
            'limes' => 'required',
            'lemons' => 'required',
            'grapes' => 'required',
            'seasonal_berries' => 'required',
            'oranges' => 'required',
            'cucumbers' => 'required',
            'mint' => 'required',
            'organic_lemons' => 'required',
            'kiwis' => 'required',
            'grapefruits' => 'required',
            'avocados' => 'required',
            'root_ginger' => 'required',
            'discount_multiple' => 'required',
        ]);


        foreach (request('company_data.delivery_day') as $delivery_day)
        {
            // Instead of creating a validation rule to check for unique name/day combo's we can make a quick db call, and skip creation if we already have a result.
            if (count(Fruitbox::where('name', request('company_data.name'))->where('delivery_day', $delivery_day)->get()) == 0) // Going to change this !count when I'm testing this section again.
            {
                // dd($request);
                $newFruitbox = new Fruitbox();
                $newFruitbox->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newFruitbox->fruit_partner_id = request('company_data.fruit_partner_id');
                $newFruitbox->name = request('company_data.name');
                $newFruitbox->company_details_id = request('company_data.company_details_id');
                //$newFruitbox->route_id = request('company_data.route_id');
                $newFruitbox->type = request('company_data.type');
                $newFruitbox->next_delivery = request('company_data.first_delivery');
                $newFruitbox->frequency = request('company_data.frequency');
                $newFruitbox->week_in_month = request('company_data.week_in_month');
                $newFruitbox->delivery_day = $delivery_day;
                $newFruitbox->fruitbox_total = request('company_data.fruitbox_total');
                $newFruitbox->deliciously_red_apples = request('company_data.deliciously_red_apples');
                $newFruitbox->pink_lady_apples = request('company_data.pink_lady_apples');
                $newFruitbox->red_apples = request('company_data.red_apples');
                $newFruitbox->green_apples = request('company_data.green_apples');
                $newFruitbox->satsumas = request('company_data.satsumas');
                $newFruitbox->pears = request('company_data.pears');
                $newFruitbox->bananas = request('company_data.bananas');
                $newFruitbox->nectarines = request('company_data.nectarines');
                $newFruitbox->limes = request('company_data.limes');
                $newFruitbox->lemons = request('company_data.lemons');
                $newFruitbox->grapes = request('company_data.grapes');
                $newFruitbox->seasonal_berries = request('company_data.seasonal_berries');
                $newFruitbox->oranges = request('company_data.oranges');
                $newFruitbox->cucumbers = request('company_data.cucumbers');
                $newFruitbox->mint = request('company_data.mint');
                $newFruitbox->organic_lemons = request('company_data.organic_lemons');
                $newFruitbox->kiwis = request('company_data.kiwis');
                $newFruitbox->grapefruits = request('company_data.grapefruits');
                $newFruitbox->avocados = request('company_data.avocados');
                $newFruitbox->root_ginger = request('company_data.root_ginger');
                $newFruitbox->tailoring_fee = request('company_data.tailoring_fee');
                $newFruitbox->discount_multiple = request('company_data.discount_multiple');
                $newFruitbox->save();

                $message = "Fruitbox $newFruitbox->name on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                $box_name = request('company_data.name');

                $message = "Fruitbox $box_name / $delivery_day combo already exists!";
                Log::channel('slack')->info($message);
            }


            // Once the Fruitbox has been created we need to check for an existing route on the given delivery day, or create a new one for populating.
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
                $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                // $newRoute->week_start = $currentWeekStart->current;
                // $newRoute->previous_delivery_week_start = null // This doesn't need to be here as there will never be a previous delivery for a new route (obvs) but I'm noting all fields in new route table here, for now.
                // $newRoute->next_delivery_week_start = $request['company_data']['first_delivery'];
                $newRoute->company_details_id = request('company_data.company_details_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->route_postcode;
                //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                // $newRoute->address = $companyDetails->route_summary_address;

                $newRoute->address = implode(", ", array_filter([
                        $companyDetails->route_address_line_1,
                        $companyDetails->route_address_line_2,
                        $companyDetails->route_address_line_3,
                        $companyDetails->route_city,
                        $companyDetails->route_region
                    ]));

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

                $message = "Route $newRoute->route_name on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                // We can update the existing entry.
                // Scrap that, as we're not saving any fruitbox data to the routes anymore we won't need to update anything here.
                // Or at least that's my current thinking.

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

        // Whatever happens, we want to update the fruitbox entry, so lets get that done first. <-- EDIT: NOT ANY MORE!

        //----- New logic to try and handle accurate invoicing regardless of when boxes are updated and invoiced. -----//

            // Now whenever a box is updated we need to compare the 'invoiced_at' (doesn't currently exist) date with the 'updated_at' date.
            // If the 'invoiced_at' date matches the 'updated_at' date we can go ahead and update the current entry, moving the existing details (copy), into the archive table for posterity.
            // Changing the archived box status to 'inactive'.

            // Alternatively should the 'updated_at' column not match up with the 'invoiced_at' date,
            // we need to save a copy to the archive table but keep its status as 'active' because we still need to invoice it during the next invoicing period.
            // HOWEVER, this also requires the updated box to be advanced to the next delivery date, SO THAT IT ISN'T INVOICED TWICE!!

        // If it passes this check below, the previous box has been paid for and just needs to be archived with a deactived status.
        // There's also a good chance the next_delivery_date has since been advanced,
        // do we want to want to save the archive with the old next_delivery_date? If so we'll need to grab it from the db before updating the stored values.

        //----- If I'm not comparing dates, do I need to worry about any of this? Worth checking... -----//

        $date = CarbonImmutable::now('Europe/London');
        $invoice_date = $date->format('ymd');
        // We want to check whether the fruitbox about to be updated has already had its old order invoiced.
        // So before we begin, let's grab the current data from the db.
        $existing_fruitbox_entry = FruitBox::findOrFail($id);
        // And specifically the invoiced_at date, which despite being inserted from a Carbon date, is now considered a string?
        // So first it needs passing back into Carbon...
        $converted_invoiced_at_date = new CarbonImmutable($existing_fruitbox_entry->invoiced_at);

        //----- If I'm not comparing dates, do I need to worry about any of this? Worth checking... -----//

        // dd(request('skip_archive'));
        // if skip-archive is true then we're deliberately trying to update the box and bypassing archive logic entirely.
        // if the current fruitbox order was updated wrongly this'll prevent the unwanted contents from being stored in the archives,
        // as it's just a mistake we all want to forget.
        // if (request('skip-archive') == 'true') { // <-- This doesn't look right?  It might even be the opposite to what I intended.
        if (request('skip_archive') === 'false') { // Let's try it with the opposite statement, I'm pretty certain that I only want to run this code if 'skip-archive' is set (defaulted) to 'false'.
            // dd('was that why?');
            // ...which we can then use to reformat both dates into the same style (->format('ymd')).

            // EDIT: 19-08-19 I've decided to change the way fruitboxes are continued from one week to the next.
            // Instead of keeping the same box alive and checking whether the box has an invoice date prior to the last time it was updated (in the database),
            // I'm going to destroy the box from one week to the next, so we only need to know whether this box has an invoice date or not.
            // This will bring fruitboxes (and milkboxes) into line with the same approach as snacks, drinks and other.
            // Comparing dates adds an unnecessary level of complication without any discernible benefit.

            // if ($existing_fruitbox_entry->updated_at->format('ymd') == $converted_invoiced_at_date->format('ymd')) {
            if ($existing_fruitbox_entry->invoiced_at !== null) {

                // dump('we\'re here! Now just need to do the rest! ' . $converted_invoiced_at_date->format('ymd') . ' is equal to ' . $existing_fruitbox_entry->updated_at->format('ymd'));

                dump($existing_fruitbox_entry->invoiced_at . ' doesn\'t equal null.  Which means it\'s been invoiced.  At least I hope it does as I\'m saving the archive as inactive.' );

                FruitBoxArchive::updateOrInsert(
                    [ // Check the values contained in this array for a matching record.  If we find it, update the record, otherwise add a new entry.
                        'fruitbox_id' => $existing_fruitbox_entry->id,
                        'next_delivery' => $existing_fruitbox_entry->next_delivery
                    ],
                    [
                        'is_active' => 'Inactive',
                        'company_details_id' => $existing_fruitbox_entry->company_details_id,
                        'fruit_partner_id' => $existing_fruitbox_entry->fruit_partner_id,
                        'name' => $existing_fruitbox_entry->name,
                        'type' => $existing_fruitbox_entry->type,
                        'previous_delivery' => $existing_fruitbox_entry->previous_delivery,
                        'frequency' => $existing_fruitbox_entry->frequency,
                        'week_in_month' => $existing_fruitbox_entry->week_in_month,
                        'delivery_day' => $existing_fruitbox_entry->delivery_day,
                        'fruitbox_total' => $existing_fruitbox_entry->fruitbox_total,
                        'deliciously_red_apples' => $existing_fruitbox_entry->deliciously_red_apples,
                        'pink_lady_apples' => $existing_fruitbox_entry->pink_lady_apples,
                        'red_apples' => $existing_fruitbox_entry->red_apples,
                        'green_apples' => $existing_fruitbox_entry->green_apples,
                        'satsumas' => $existing_fruitbox_entry->satsumas,
                        'pears' => $existing_fruitbox_entry->pears,
                        'bananas' => $existing_fruitbox_entry->bananas,
                        'nectarines' => $existing_fruitbox_entry->nectarines,
                        'limes' => $existing_fruitbox_entry->limes,
                        'lemons' => $existing_fruitbox_entry->lemons,
                        'grapes' => $existing_fruitbox_entry->grapes,
                        'seasonal_berries' => $existing_fruitbox_entry->seasonal_berries,
                        'oranges' => $existing_fruitbox_entry->oranges,
                        'cucumbers' => $existing_fruitbox_entry->cucumbers,
                        'mint' => $existing_fruitbox_entry->mint,
                        'organic_lemons' => $existing_fruitbox_entry->organic_lemons,
                        'kiwis' => $existing_fruitbox_entry->kiwis,
                        'grapefruits' => $existing_fruitbox_entry->grapefruits,
                        'avocados' => $existing_fruitbox_entry->avocados,
                        'root_ginger' => $existing_fruitbox_entry->root_ginger,
                        'tailoring_fee' => $existing_fruitbox_entry->tailoring_fee,
                        'discount_multiple' => $existing_fruitbox_entry->discount_multiple,
                        'invoiced_at' => $existing_fruitbox_entry->invoiced_at,
                        'created_at' => $existing_fruitbox_entry->created_at,
                        'updated_at' => $existing_fruitbox_entry->updated_at // this may not be worth updating as it'll be changed on creation?
                    ]
                );

            } else { // end of if (invoiced_at date == updated_at date)

                dump($existing_fruitbox_entry->invoiced_at . ' must be null, so this box is yet to be invoiced.  Saving the archive as active.');

                // dump('Oh, guess these don\'t match? ' . $converted_invoiced_at_date->format('ymd') . ' isn\'t equal to ' . $existing_fruitbox_entry->updated_at->format('ymd'));
                // dd('WE GOT TO HERE, BUT WHAT HAPPENS NEXT?');
                // If we're here then the invoiced_at date didn't match the updated_at date.  This means we have an order that hasn't been charged yet.
                // This time we need to create an archived version of the old details but with an 'Active' status.

                // I ALSO NEED TO THINK OF WHAT MIGHT GET HERE THAT WE DON'T WANT TO INVOICE AUTOMATICALLY.

                // If an order gets updated, then changed again before invoicing (and delivery) we only want to replace the entry rather than add another entry for invoicing.
                // Let's check for the company_details_id, box number and status 'Active'

                FruitBoxArchive::updateOrInsert(
                    [ // Check the values contained in this array for a matching record.  If we find it, update the record, otherwise add a new entry.
                        'fruitbox_id' => $existing_fruitbox_entry->id,
                        'next_delivery' => $existing_fruitbox_entry->next_delivery
                    ],
                    [
                        'is_active' => 'Active',
                        'company_details_id' => $existing_fruitbox_entry->company_details_id,
                        'fruit_partner_id' => $existing_fruitbox_entry->fruit_partner_id,
                        'name' => $existing_fruitbox_entry->name,
                        // 'company_details_id' => $existing_fruitbox_entry->company_details_id,
                        'type' => $existing_fruitbox_entry->type,
                        'previous_delivery' => $existing_fruitbox_entry->previous_delivery,
                        'frequency' => $existing_fruitbox_entry->frequency,
                        'week_in_month' => $existing_fruitbox_entry->week_in_month,
                        'delivery_day' => $existing_fruitbox_entry->delivery_day,
                        'fruitbox_total' => $existing_fruitbox_entry->fruitbox_total,
                        'deliciously_red_apples' => $existing_fruitbox_entry->deliciously_red_apples,
                        'pink_lady_apples' => $existing_fruitbox_entry->pink_lady_apples,
                        'red_apples' => $existing_fruitbox_entry->red_apples,
                        'green_apples' => $existing_fruitbox_entry->green_apples,
                        'satsumas' => $existing_fruitbox_entry->satsumas,
                        'pears' => $existing_fruitbox_entry->pears,
                        'bananas' => $existing_fruitbox_entry->bananas,
                        'nectarines' => $existing_fruitbox_entry->nectarines,
                        'limes' => $existing_fruitbox_entry->limes,
                        'lemons' => $existing_fruitbox_entry->lemons,
                        'grapes' => $existing_fruitbox_entry->grapes,
                        'seasonal_berries' => $existing_fruitbox_entry->seasonal_berries,
                        'oranges' => $existing_fruitbox_entry->oranges,
                        'cucumbers' => $existing_fruitbox_entry->cucumbers,
                        'mint' => $existing_fruitbox_entry->mint,
                        'organic_lemons' => $existing_fruitbox_entry->organic_lemons,
                        'kiwis' => $existing_fruitbox_entry->kiwis,
                        'grapefruits' => $existing_fruitbox_entry->grapefruits,
                        'avocados' => $existing_fruitbox_entry->avocados,
                        'root_ginger' => $existing_fruitbox_entry->root_ginger,
                        'tailoring_fee' => $existing_fruitbox_entry->tailoring_fee,
                        'discount_multiple' => $existing_fruitbox_entry->discount_multiple,
                        'invoiced_at' => $existing_fruitbox_entry->invoiced_at,
                        'created_at' => $existing_fruitbox_entry->created_at,
                        'updated_at' => $existing_fruitbox_entry->updated_at // this may not be worth updating as it'll be changed on creation?
                    ]
                );

                // The first check is whether we have a record of that box already in the FruitBoxArchive, if we do
                // the second check is to see whether that box is currently 'Active', which means it's still waiting for invoicing.
                // If it's not this is an invoiced & archived order which is only being kept for posterity and should be left as is.

            } //----- End of - New logic to try and handle accurate invoicing regardless of when boxes are updated and invoiced. -----//

        }

        // dd('why did we get this far?');

        //----- Start of regular update process -----//

        // dd($request);
       FruitBox::where('id', $id)->update([
           // 'id' => request('id'), // this shouldn't change so I could delete it but nahh...
           'is_active' => request('is_active'),
           'fruit_partner_id' => request('fruit_partner_id'),
           'name' => request('name'),
           // 'company_details_id' => request('company_details_id'),
           // 'route_id' => request('route_id'),
           // 'type' => request('type'), // I'm not currently offering an update to box type, instead a new box should be created.
           'next_delivery' => request('next_delivery'),
           'delivery_day' => request('delivery_day'),
           'frequency' => request('frequency'),
           'week_in_month' => request('week_in_month'),
           'fruitbox_total' => request('fruitbox_total'),
           'deliciously_red_apples' => request('deliciously_red_apples'),
           'pink_lady_apples' => request('pink_lady_apples'),
           'red_apples' => request('red_apples'),
           'green_apples' => request('green_apples'),
           'satsumas' => request('satsumas'),
           'pears' => request('pears'),
           'bananas' => request('bananas'),
           'nectarines' => request('nectarines'),
           'limes' => request('limes'),
           'lemons' => request('lemons'),
           'grapes' => request('grapes'),
           'seasonal_berries' => request('seasonal_berries'),
           'oranges' => request('oranges'),
           'cucumbers' => request('cucumbers'),
           'mint' => request('mint'),
           'organic_lemons' => request('organic_lemons'),
           'kiwis' => request('kiwis'),
           'grapefruits' => request('grapefruits'),
           'avocados' => request('avocados'),
           'root_ginger' => request('root_ginger'),
           'tailoring_fee' => request('tailoring_fee'),
           'discount_multiple' => request('discount_multiple'),
           'invoiced_at' => request('invoiced_at'),
       ]);

       // When making an update to the fruitbox, we need to check there's still a route for its delivery.
       // If we've changed the delivery day for example this could easily lead us to having an order but not a route to deliver it on.

       // Grab the updated info delivery day, check we have a deliverable route in place.  If we do, we're all sorted.
       if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get())) {

           // We have nothing else we need to do.

           // ^^^ Not true, we should be checking if the route is active already because if not we'll need to change that status to get pulled into the routes for exporting etc.
           $route = CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get();

           if ($route[0]->is_active == 'Active') {

               $message = "Route for updated box " . request('name') . " on " . request('delivery_day') . " already found.";
               Log::channel('slack')->info($message);

           } else {

               FruitBox::where('id', $id)->update([
                   'is_active' => 'Active'
               ]);

               $message = "Route for updated box " . request('name') . " on " . request('delivery_day') . " already found but Inactive, Reactivating now...";
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

           //dd($companyDetails);
           // We need to create a new entry.
           $newRoute = new CompanyRoute();
           // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
           $newRoute->company_details_id = request('company_details_id');
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
           $newRoute->delivery_day = request('delivery_day');
           $newRoute->save();

           $message = "Route $newRoute->route_name on " . request('delivery_day') . " saved.";
           Log::channel('slack')->info($message);

       } else {

           $fruit_partner = FruitPartner::findOrFail(request('fruit_partner_id'));
           // This is an updated entry for a fruit third party, while we don't need to build a route, we should log the change as the fruit partner will need to know about it.
           // At the moment this will just get lost in the slack feed for laravel log, so we'll need to set up another channel just to send information like this in a readable manner.
           $message = "Route for updated box " . request('name') . " on " . request('delivery_day') . " not needed as it's delivered by our fruit partner " . $fruit_partner->name;
           Log::channel('slack')->info($message);
       }

    } // end of update function

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
