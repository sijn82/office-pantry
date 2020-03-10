<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Exports;

use App\FruitBox;
use App\FruitBoxArchive;
use App\CompanyDetails;
use App\CompanyRoute;
use App\FruitPartner;
use App\WeekStart;
use App\AssignedRoute;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;

use App\Traits\Routes;

class FruitBoxController extends Controller
{
    use Routes;

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
        $fruitboxes = FruitBox::where('is_active', 'Active')->where('delivery_week', $this->week_start)->get();

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

        // $validatedData = $request->validate([
        //
        //     'fruit_partner_id' => 'required',
        //     'name' => 'required',
        //     'type' => 'required',
        //     'delivery_week' => 'required',
        //     'frequency' => 'required',
        //     'delivery_day' => 'required',
        //     'fruitbox_total' => 'required',
        //     'deliciously_red_apples' => 'required',
        //     'pink_lady_apples' => 'required',
        //     'red_apples' => 'required',
        //     'green_apples' => 'required',
        //     'satsumas' => 'required',
        //     'pears' => 'required',
        //     'bananas' => 'required',
        //     'nectarines' => 'required',
        //     'limes' => 'required',
        //     'lemons' => 'required',
        //     'grapes' => 'required',
        //     'seasonal_berries' => 'required',
        //     'oranges' => 'required',
        //     'cucumbers' => 'required',
        //     'mint' => 'required',
        //     'organic_lemons' => 'required',
        //     'kiwis' => 'required',
        //     'grapefruits' => 'required',
        //     'avocados' => 'required',
        //     'root_ginger' => 'required',
        //     'discount_multiple' => 'required',
        // ]);


        foreach (request('delivery_day') as $delivery_day)
        {
            // Instead of creating a validation rule to check for unique name/day combo's we can make a quick db call, and skip creation if we already have a result.
            if (count(Fruitbox::where('company_details_id', request('company_details_id'))->where('name', request('name'))->where('delivery_day', $delivery_day)->get()) == 0) // Going to change this !count when I'm testing this section again.
            {
                $newFruitbox = new Fruitbox();
                $newFruitbox->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newFruitbox->fruit_partner_id = request('fruit_partner_id');
                $newFruitbox->name = request('name');
                $newFruitbox->company_details_id = request('company_details_id');
                $newFruitbox->type = request('type');
                $newFruitbox->delivery_week = request('first_delivery');
                $newFruitbox->frequency = request('frequency');
                $newFruitbox->week_in_month = request('week_in_month');
                $newFruitbox->delivery_day = $delivery_day;
                $newFruitbox->fruitbox_total = request('fruitbox_total');
                $newFruitbox->deliciously_red_apples = request('deliciously_red_apples');
                $newFruitbox->pink_lady_apples = request('pink_lady_apples');
                $newFruitbox->red_apples = request('red_apples');
                $newFruitbox->green_apples = request('green_apples');
                $newFruitbox->satsumas = request('satsumas');
                $newFruitbox->pears = request('pears');
                $newFruitbox->bananas = request('bananas');
                $newFruitbox->nectarines = request('nectarines');
                $newFruitbox->limes = request('limes');
                $newFruitbox->lemons = request('lemons');
                $newFruitbox->grapes = request('grapes');
                $newFruitbox->seasonal_berries = request('seasonal_berries');
                $newFruitbox->oranges = request('oranges');
                $newFruitbox->cucumbers = request('cucumbers');
                $newFruitbox->mint = request('mint');
                $newFruitbox->organic_lemons = request('organic_lemons');
                $newFruitbox->kiwis = request('kiwis');
                $newFruitbox->grapefruits = request('grapefruits');
                $newFruitbox->avocados = request('avocados');
                $newFruitbox->root_ginger = request('root_ginger');
                $newFruitbox->tailoring_fee = request('tailoring_fee');
                $newFruitbox->discount_multiple = request('discount_multiple');
                $newFruitbox->save();

                $message = "Fruitbox $newFruitbox->name on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                $box_name = request('name');

                $message = "Fruitbox $box_name / $delivery_day combo already exists!";
                Log::channel('slack')->info($message);
            }


            // Once the Fruitbox has been created we need to check for an existing route on the given delivery day, or create a new one for populating.
            if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0) {

                // If we're here, we need to create a new route
                $this->createNewRoute(request(), $delivery_day);

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
    

        //----- Start of regular update process -----//

        // In order to use observables, it would seem I need to break up the db call, even if only retrieving 1 entry, with the update request.
        // Edit 10-03-2020: However I don't think I'm using observables afterall.  I'll leave this here though in case I do utilise them in the near future.

        // FruitBox::where('id', $id)->update([

        // Save the entry being updated into a variable.
        $fruitboxForUpdating = FruitBox::find($id);
        // Apply update call to the variable.
        $fruitboxForUpdating->update([
           // 'id' => request('id'), // this shouldn't change so I could delete it but nahh...
           'is_active' => request('is_active'),
           'fruit_partner_id' => request('fruit_partner_id'),
           'name' => request('name'),
           // 'company_details_id' => request('company_details_id'),
           'type' => request('type'),
           'delivery_week' => request('delivery_week'),
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

       //----- Using an observable was educational but also created an infinite loop! -----//

            // Putting the logic here and bypassing the observble.  If this works however it might be better to combine this into 1 update.

            // We only want to check some fields for changes, as getChanges can't be filtered, we'll need to remove them afterwards.
            $fields_we_can_ignore = [
                'id',
                'is_active',
                'fruit_partner_id',
                'name',
                'company_details_id',
                'type',
                'previous_delivery',
                'delivery_week',
                'frequency',
                'week_in_month',
                'tailoring_fee',
                'discount_multiple',
                'invoiced_at',
                'created_at',
                'updated_at',
                'order_changes',
                'date_changed'
            ];

                // So first let's get all the changes.
                $order_changes = $fruitboxForUpdating->getChanges();

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
                    $fruitboxForUpdating->update([
                        'order_changes' => $order_changes,
                        'date_changed' => $carbon_now,
                    ]);
                }

       //----- End of Using an observable was educational but also created and infinite loop! -----//

       // When making an update to the fruitbox, we need to check there's still a route for its delivery.
       // If we've changed the delivery day for example this could easily lead us to having an order but not a route to deliver it on.

       // Grab the updated info delivery day, check we have a deliverable route in place.  If we do, we're all sorted.
       if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get())) {

    
       } elseif (request('fruit_partner_id') == 1) {

           // If we're here, we need to create a new route
           $this->createNewRoute(request());

       } else { // Mind you would it hurt to create route entries for fruitpartner companies?  It would allow is to have different delivery info per day rather than per company address.

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
        FruitBox::destroy($id);
    }
}
