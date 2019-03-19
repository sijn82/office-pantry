<?php

namespace App\Http\Controllers;

use App\FruitBox;
use App\Company;
use App\CompanyRoute;
use App\FruitPartner;
use App\WeekStart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FruitBoxController extends Controller
{
    protected $week_start;
    protected $delivery_days;

    public function __construct()
    {
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];
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
            $route = CompanyRoute::where('company_id', $fruitbox->company_id)->where('delivery_day', $fruitbox->delivery_day)->get();
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

                echo 'No matching Route Id for ' . $fruitbox->name . ' ( ' . $fruitbox->company_id . ' ) on ' . $fruitbox->delivery_day . '<br/>';
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

        foreach ($request['company_data']['delivery_day'] as $delivery_day)
        {
            // Instead of creating a validation rule to check for unique name/day combo's we can make a quick db call, and skip creation if we already have a result.
            if (count(Fruitbox::where('name', $request['company_data']['name'])->Where('delivery_day', $delivery_day)->get()) == 0) // Going to change this !count when I'm testing this section again.
            {
                // dd($request);
                $newFruitbox = new Fruitbox();
                // $newFruitbox->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newFruitbox->fruit_partner_id = $request['company_data']['fruit_partner_id'];
                $newFruitbox->name = $request['company_data']['name'];
                $newFruitbox->company_id = $request['company_data']['company_id'];
                $newFruitbox->route_id = $request['company_data']['route_id'];
                $newFruitbox->type = $request['company_data']['type'];
                $newFruitbox->next_delivery = $request['company_data']['first_delivery'];
                $newFruitbox->frequency = $request['company_data']['frequency'];
                $newFruitbox->week_in_month = $request['company_data']['week_in_month'];
                $newFruitbox->delivery_day = $delivery_day;
                $newFruitbox->fruitbox_total = $request['company_data']['fruitbox_total'];
                $newFruitbox->deliciously_red_apples = $request['company_data']['deliciously_red_apples'];
                $newFruitbox->pink_lady_apples = $request['company_data']['pink_lady_apples'];
                $newFruitbox->red_apples = $request['company_data']['red_apples'];
                $newFruitbox->green_apples = $request['company_data']['green_apples'];
                $newFruitbox->satsumas = $request['company_data']['satsumas'];
                $newFruitbox->pears = $request['company_data']['pears'];
                $newFruitbox->bananas = $request['company_data']['bananas'];
                $newFruitbox->nectarines = $request['company_data']['nectarines'];
                $newFruitbox->limes = $request['company_data']['limes'];
                $newFruitbox->lemons = $request['company_data']['lemons'];
                $newFruitbox->grapes = $request['company_data']['grapes'];
                $newFruitbox->seasonal_berries = $request['company_data']['seasonal_berries'];
                $newFruitbox->oranges = $request['company_data']['oranges'];
                $newFruitbox->cucumbers = $request['company_data']['cucumbers'];
                $newFruitbox->mint = $request['company_data']['mint'];
                $newFruitbox->organic_lemons = $request['company_data']['organic_lemons'];
                $newFruitbox->kiwis = $request['company_data']['kiwis'];
                $newFruitbox->grapefruits = $request['company_data']['grapefruits'];
                $newFruitbox->avocados = $request['company_data']['avocados'];
                $newFruitbox->root_ginger = $request['company_data']['root_ginger'];
                $newFruitbox->save();

                $message = "Fruitbox $newFruitbox->name on $delivery_day saved.";
                Log::channel('slack')->info($message);

            } else {

                $box_name = $request['company_data']['name'];

                $message = "Fruitbox $box_name / $delivery_day combo already exists!";
                Log::channel('slack')->info($message);
            }


            // Once the Fruitbox has been created we need to check for an existing route on the given delivery day, or create a new one for populating.
            if (count(CompanyRoute::where('company_id', $request['company_data']['company_id'])->where('delivery_day', $delivery_day)->get()) == 0) {

                // Let's grab the current weekstart, we don't really need it but this will give us the week the company started with us.  Which might be nice to know.
                $currentWeekStart = Weekstart::findOrFail(1);

                // A route might not exist yet but when the company was set up a route name was inputted, so let's use that.
                $companyDetails = Company::findOrFail($request['company_data']['company_id']);

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                // $newRoute->week_start = $currentWeekStart->current;
                // $newRoute->previous_delivery_week_start = null // This doesn't need to be here as there will never be a previous delivery for a new route (obvs) but I'm noting all fields in new route table here, for now.
                // $newRoute->next_delivery_week_start = $request['company_data']['first_delivery'];
                $newRoute->company_id = $request['company_data']['company_id'];
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->postcode;
                $newRoute->address = $companyDetails->route_summary_address;
                $newRoute->delivery_information = $companyDetails->delivery_information;
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
        
        // Whatever happens, we want to update the fruitbox entry, so lets get that done first.
        
        FruitBox::where('id', $id)->update([
            // 'id' => request('id'), // this shouldn't change so I could delete it but nahh...
            'is_active' => request('is_active'),
            'fruit_partner_id' => request('fruit_partner_id'),
            'name' => request('name'),
            //'company_id' => request('company_id'),
            //'route_id' => request('route_id'),
            'type' => request('type'),
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
        ]);
        
        // When making an update to the fruitbox, we need to check there's still a route for its delivery.
        // If we've changed the delivery day for example this could easily lead us to having an order but not a route to deliver it on.

        // Grab the updated info delivery day, check we have a deliverable route in place.  If we do, we're all sorted.
        if (count(CompanyRoute::where('company_id', request('company_id'))->where('delivery_day', request('delivery_day'))->get())) {
            
            // We have nothing else we need to do. 
            
            // ^^^ Not true, we should be checking if the route is active already because if not we'll need to change that status to get pulled into the routes for exporting etc.
            $route = CompanyRoute::where('company_id', request('company_id'))->where('delivery_day', request('delivery_day'))->get();
            
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
            
            $companyDetails = Company::findOrFail($request['company_id']);

            // We need to create a new entry.
            $newRoute = new CompanyRoute();
            // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
            $newRoute->company_id = $request['company_id'];
            $newRoute->route_name = $companyDetails->route_name;
            $newRoute->postcode = $companyDetails->postcode;
            $newRoute->address = $companyDetails->route_summary_address;
            $newRoute->delivery_information = $companyDetails->delivery_information;
            $newRoute->delivery_day = $request['delivery_day'];
            $newRoute->save();


            $message = "Route $newRoute->company_name on " . $request['delivery_day'] . " saved.";
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
