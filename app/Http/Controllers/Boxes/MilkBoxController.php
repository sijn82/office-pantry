<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;

use App\Http\Controllers\Controller;

use App\MilkBox;
use App\MilkBoxArchive;
use App\CompanyDetails;
use App\WeekStart;
use App\CompanyRoute;
use App\FruitPartner;
use App\AssignedRoute;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

use App\Traits\Routes;

class MilkBoxController extends Controller
{
    // This trait gives me access to createNewRoute()
    // This will be the first of many!
    use Routes;

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
        //dd(request());

        // As the delivery day field on the form is a multiple checkbox, (for each day of the week) there could potentially be 5 delivery days to process.
        // I think it makes the most sense to have a fresh entry for each day because this allows the most flexibility.
        // It allows for the same box name to be on different routes, on different days for example which is important.
        // It also allows for a single day to be easily paused, or tailored.

        foreach (request('delivery_day') as $delivery_day)
        {
            // Instead of creating a validation rule to check for unique name/day combo's we can make a quick db call, and skip creation if we already have a result.
            // if (count(MilkBox::where('company_details_id', request('company_data.company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0)
            if (count(MilkBox::where('company_details_id', request('company_details_id'))->where('name', request('name'))->where('delivery_day', $delivery_day)->get()) == 0)
            {

                $newMilkbox = new MilkBox();
                // $newMilkbox->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newMilkbox->name = request('name');
                $newMilkbox->fruit_partner_id = request('fruit_partner_id');
                $newMilkbox->company_details_id = request('company_details_id');
                //$newMilkbox->route_id = request('company_data.route_id'); // Not using this for anything.
                $newMilkbox->delivery_week = request('first_delivery');
                $newMilkbox->frequency = request('frequency');
                $newMilkbox->week_in_month = request('week_in_month');
                $newMilkbox->delivery_day = $delivery_day;
                // Milk 2l
                $newMilkbox->semi_skimmed_2l = request('semi_skimmed_2l');
                $newMilkbox->skimmed_2l = request('skimmed_2l');
                $newMilkbox->whole_2l = request('whole_2l');
                // Milk 1l
                $newMilkbox->semi_skimmed_1l = request('semi_skimmed_1l');
                $newMilkbox->skimmed_1l = request('skimmed_1l');
                $newMilkbox->whole_1l = request('whole_1l');
                // Organic Milk 2l
                $newMilkbox->organic_semi_skimmed_2l = request('organic_semi_skimmed_2l');
                $newMilkbox->organic_skimmed_2l = request('organic_skimmed_2l');
                $newMilkbox->organic_whole_2l = request('organic_whole_2l');
                // Organic Milk 1l
                $newMilkbox->organic_semi_skimmed_1l = request('organic_semi_skimmed_1l');
                $newMilkbox->organic_skimmed_1l = request('organic_skimmed_1l');
                $newMilkbox->organic_whole_1l = request('organic_whole_1l');
                // Milk Alternatives
                $newMilkbox->milk_1l_alt_coconut = request('coconut_1l');
                $newMilkbox->milk_1l_alt_unsweetened_almond = request('unsweetened_almond_1l');
                $newMilkbox->milk_1l_alt_almond = request('almond_1l');
                // Milk Alternatives (Pt2)
                $newMilkbox->milk_1l_alt_unsweetened_soya = request('unsweetened_soya_1l');
                $newMilkbox->milk_1l_alt_soya = request('soya_1l');
                $newMilkbox->milk_1l_alt_oat = request('oat_1l');
                // Milk Alternatives (Pt3)
                $newMilkbox->milk_1l_alt_rice = request('rice_1l');
                $newMilkbox->milk_1l_alt_cashew = request('cashew_1l');
                $newMilkbox->milk_1l_alt_lactose_free_semi = request('lactose_free_semi_skimmed_1l');
                // Milk Alternatives (Pt4)
                $newMilkbox->milk_1l_alt_hazelnut = request('hazelnut_1l');
                $newMilkbox->milk_1l_alt_soya_chocolate = request('soya_chocolate_1l');
                $newMilkbox->save();

                // Find company details to use company (route) name in message slacked to laravel logs.
                $companyDetails = CompanyDetails::findOrFail(request('company_details_id'));
                $message = "Milkbox called " . request('name') . " for " . $companyDetails->route_name . " on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                // Find company details to use company (route) name in message slacked to laravel logs.
                $companyDetails = CompanyDetails::findOrFail(request('company_details_id'));
                // $message = "Milkbox for " . $companyDetails->route_name . " on $delivery_day saved."; // 10/01/20 - Odd this looks like it should say the opposite (i.e that it didn't save the milkbox)?!
                $message = "Milkbox called " . request('name') . " for " . $companyDetails->route_name . " on $delivery_day already found.";
                Log::channel('slack')->info($message);
            }

            // Once the Milkbox has been created we need to check for an existing route on the given delivery day, or create a new one for populating.
            if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0) {

                // If we're here, we need to create a new route
                $this->createNewRoute(request(), $delivery_day);

                // Let's hold this action for 1 seconds just to give the entry a chance to be created before we check for its existence.
                // I realise this is most likely an unnecessary step but I'll remove it another day :)
                sleep(1);

                // New routes need to be created before we have an id for them.  At this point we could then apply that route_id to the new milkbox entry if it has been created quickly enough.
                $newlyCreatedRoute = CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', $delivery_day)->get();

            } else {

                // We already have a route this delivery can go on - so we dont need to do anything else.
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

        // Edit 10-03-2020: Now that we've scrapped the old archive approach we can simplify all of the functions which created, edited or updated archive entries.  Lovely.
        
        //---------- Start of regular update process ----------//

        // Save the entry being updated into a variable.
        $milkboxForUpdating = MilkBox::find($id);

        // Apply update call to the variable.
        // Whatever else needs to be done, we need to make sure we update the entry, so let's get that sorted first.
        $milkboxForUpdating->update([
            // Box Details
            'is_active' => request('is_active'),
            'name' => request('name'),
            'fruit_partner_id' => request('fruit_partner_id'),
            'delivery_week' => request('delivery_week'), // Being able to change this is actually very important!
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
            // Milk Alternatives (Pt4)
            'milk_1l_alt_hazelnut' => request('milk_1l_alt_hazelnut'),
            'milk_1l_alt_soya_chocolate' => request('milk_1l_alt_soya_chocolate'),
        ]);

        //----- Now we need to configure the order change check -----//

        // We only want to check some fields for changes, as getChanges can't be filtered, we'll need to remove them afterwards.
        $fields_we_can_ignore = [
            'id',
            'is_active',
            'fruit_partner_id',
            'company_details_id',
            'previous_delivery',
            'delivery_week',
            'frequency',
            'week_in_month',
            'invoiced_at',
            'created_at',
            'updated_at',
            'order_changes',
            'date_changed'
        ];

            // So first let's get all the changes.
            $order_changes = $milkboxForUpdating->getChanges();

            // Then loop through them all, removing the changes we don't need to track.
            if ($order_changes) {
                foreach ($order_changes as $key => $order_change) {
                    if (in_array($key, $fields_we_can_ignore)) {
                        unset($order_changes[$key]);
                    }
                }

            }
            // With them removed, are there any changes left which we do want to track?
            if ($order_changes) {
                // If so let's grab the current time.
                $carbon_now = CarbonImmutable::now('Europe/London');
                // And save this info to the box.
                $milkboxForUpdating->update([
                    'order_changes' => $order_changes,
                    'date_changed' => $carbon_now,
                ]);
            }

        //----- End of configure the order change check -----//


        // Now we need to check that the milkbox still has a deliverable route, if for example we've just changed the delivery day.
        if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get())) {

            // We have nothing else we need to do.

        } elseif (request('fruit_partner_id') == 1) {

            // If we're here, a route wasn't found for the new delivery day, and we've confirmed it's an Office Pantry delivery, so we'd better make one.
            $this->createNewRoute(request());

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
        MilkBox::destroy($id);
    }
}
