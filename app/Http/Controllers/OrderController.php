<?php

namespace App\Http\Controllers;

use App\Company;
use App\Order;
use App\FruitBox;
use App\MilkBox;
use App\WeekStart;
use App\CompanyRoute;
use Carbon\Carbon;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $active_orders = Company::find(55)->orders;

        // This calls the company model and finds the fruitbox function which stipulates the relationship between the two models.
        // By passing a hardcoded ID (for testing purposes) of 55 we select Twitch, which has two registered fruitboxes.
        $active_fruitbox_orders = Company::find(55)->fruitbox;
        // A second check looks for those boxes connected to the company with Active in the is_active column.  In this instance it only (correctly) retrieves one of the boxes.
        dd($active_fruitbox_orders->where('is_active', 'Active'));

        // $active_milk_orders = Company::find(55)->milkbox;
        // dd($active_milk_orders);
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
    
    public function createPicklists()
    {
        $fruitboxes = FruitBox::all();
        dd($fruitboxes);
        // if the fruitbox has a next delivery date matching the current week start variable, we need to create a picklist for it.
                
        
    }
    
    // I think this 'public static function advanceNextOrderDeliveryDate()' might be the only function in here that's actually still in use.  
    // Which is quite funny as I have no idea whether it's still updating successfully since I don't actually know 
    // when it would ATTEMPT TO UPDATE THE ORDERS, OR WHAT HAPPENS WHEN IT CAN'T?!
    // But it's on the 'to do list', sob...
    
    public static function advanceNextOrderDeliveryDate()
    {
        // ---------- Test Area ---------- //

            // $carbon = new Carbon; // 2019-01-09
            // $carbon->addWeeks(1); // 2019-01-16
            // $lastMondayOfMonth = $carbon::parse('last monday of next month'); // 2019-02-25
            // $secondMondayOfMonth = $carbon::parse('second monday of next month'); // 2019-02-11
            // // $secondMondayBeforeNextMonth = $carbon::parse('second monday after next month'); - neither 'before' nor 'after' work.
            //
            // $month = 'February';
            // $firstMondayOfMonth = $carbon::parse('first monday of ' . $month); // 2019-02-04
            // $firstMondayOfMonth->addMonth(); // 2019-03-04
            // $firstMondayOfMonth->addMonth(-1); // 2019-02-04 - You're right documentation, that was fun.
            //
            // dd($carbon);
            // dd($firstMondayOfMonth);
            // dd($secondMondayBeforeNextMonth);

        // ---------- End of Test Area ---------- //

        // ---------- Weekly ---------- //

            // Not actually using this for anything at the moment, identifying the weekstart variable is needed
            // for determining whether to put them on routes but not to advance their next delivery date.
            $weekStart = Weekstart::findOrFail(1);

            // ---------- Fruitboxes ---------- //

                // I'm currently updating all weekly fruitboxes, however previously I also had 'where('is_active', 'Active')' as another filter
                // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
                $fruitboxes = FruitBox::where('frequency', 'Weekly')->get();

                echo "<br/> Weekly <br/>";

                foreach ($fruitboxes as $fruitbox) {

                    // If the 'next_delivery' date (value) has passed, we probably need to update it.
                    // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                    if ($fruitbox->next_delivery < Carbon::now()) {

                        // echo $fruitbox->name . '\'s next delivery was outdated but has been changed from ' . $fruitbox->next_delivery . " to ";

                        $lastDelivery = $fruitbox->next_delivery;

                        // This will pull the current fruitbox next_delivery(_week_start) into Carbon where we can increase its value by 1 week.
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(1);

                        // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                        FruitBox::where('id', $fruitbox->id)->update([
                            'previous_delivery' => $lastDelivery,
                            'next_delivery' => $fruitbox->next_delivery,
                        ]);

                        // echo $fruitbox->next_delivery . "<br/>";
                    }
                }

            // ---------- Milkboxes ---------- //

                // I'm currently updating all weekly milkboxes, however previously I also had 'where('is_active', 'Active')' as another filter
                // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
                $milkboxes = MilkBox::where('frequency', 'Weekly')->get();

                echo "<br/> Weekly <br/>";

                foreach ($milkboxes as $milkbox) {

                    // If the 'next_delivery' date (value) has passed, we probably need to update it.
                    // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                    if ($milkbox->next_delivery_week_start < Carbon::now()) {

                        // echo $milkbox->name . '\'s next delivery was outdated but has been changed from ' . $milkbox->next_delivery_week_start . " to ";

                        $lastDelivery = $milkbox->next_delivery_week_start;

                        // This will pull the current fruitbox next_delivery(_week_start) into Carbon where we can increase its value by 1 week.
                        $milkbox->next_delivery_week_start = Carbon::parse($milkbox->next_delivery_week_start)->addWeek(1);

                        // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                        MilkBox::where('id', $milkbox->id)->update([
                            'previous_delivery_week_start' => $lastDelivery,
                            'next_delivery_week_start' => $milkbox->next_delivery_week_start,
                        ]);

                        // echo $milkbox->next_delivery_week_start . "<br/>";
                    }
                }

        // ---------- End of Weekly ---------- //

        // ---------- Fortnightly ---------- //

            // ---------- Fruitboxes ---------- //

                // I'm currently updating all weekly fruitboxes, however previously I also had 'where('is_active', 'Active')' as another filter
                // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
                $fruitboxes = FruitBox::where('frequency', 'Fortnightly')->get();

                echo "<br/> Fortnightly <br/>";

                foreach ($fruitboxes as $fruitbox) {

                    // If the 'next_delivery' date (value) has passed, we probably need to update it.
                    // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                    if ($fruitbox->next_delivery < Carbon::now()) {

                        // echo $fruitbox->name . '\'s next delivery was outdated but has been changed from ' . $fruitbox->next_delivery . " to ";

                        $lastDelivery = $fruitbox->next_delivery;

                        // This will pull the current fruitbox next_delivery(_week_start) into Carbon where we can increase its value by 2 week.
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(2);

                        // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                        FruitBox::where('id', $fruitbox->id)->update([
                            'previous_delivery' => $lastDelivery,
                            'next_delivery' => $fruitbox->next_delivery,
                        ]);

                        // echo $fruitbox->next_delivery . "<br/>";
                    }
                }

            // ---------- Milkboxes ---------- //

            // I'm currently updating all weekly milkboxes, however previously I also had 'where('is_active', 'Active')' as another filter
            // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
            $milkboxes = MilkBox::where('frequency', 'Fortnightly')->get();

            echo "<br/> Fortnightly <br/>";

            foreach ($milkboxes as $milkbox) {

                // If the 'next_delivery' date (value) has passed, we probably need to update it.
                // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                if ($milkbox->next_delivery_week_start < Carbon::now()) {

                    // echo $milkbox->name . '\'s next delivery was outdated but has been changed from ' . $milkbox->next_delivery_week_start . " to ";

                    $lastDelivery = $milkbox->next_delivery_week_start;

                    // This will pull the current milkbox next_delivery(_week_start) into Carbon where we can increase its value by 2 week.
                    $milkbox->next_delivery_week_start = Carbon::parse($milkbox->next_delivery_week_start)->addWeek(2);

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    MilkBox::where('id', $milkbox->id)->update([
                        'previous_delivery_week_start' => $lastDelivery,
                        'next_delivery_week_start' => $milkbox->next_delivery_week_start,
                    ]);

                    // echo $fruitbox->next_delivery_week_start . "<br/>";
                }
            }
        // ---------- End of Fortnightly ---------- //

        // ---------- 1st, 2nd, 3rd or 4th of Month ---------- //

            // ---------- Fruitboxes ---------- //

            // I'm currently updating all weekly fruitboxes, however previously I also had 'where('is_active', 'Active')' as another filter
            // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
            $fruitboxes = FruitBox::where('frequency', 'Monthly')->get();

            echo "<br/> Monthly <br/>";

            foreach ($fruitboxes as $fruitbox) {

                // If the 'next_delivery' date (value) has passed, we probably need to update it.
                // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                if ($fruitbox->next_delivery < Carbon::now()) {

                    // echo $fruitbox->name . '\'s next delivery was outdated but has been changed from ' . $fruitbox->next_delivery . " to ";

                    $lastDelivery = $fruitbox->next_delivery;

                    // This will pull the current fruitbox next_delivery(_week_start) into Carbon where we can increase its value by a given monday in the following month.
                    // Even if the monday remains the same this will still vary more than just progressing forward 4 weeks.

                    // This will hold either the value first, second, third, forth or last.
                     $week = $fruitbox->week_in_month;

                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($fruitbox->next_delivery)->addMonth()->englishMonth;

                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;

                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);

                    // Set the newly parsed delivery date.
                    $fruitbox->next_delivery = $mondayOfMonth;

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    FruitBox::where('id', $fruitbox->id)->update([
                        'previous_delivery' => $lastDelivery,
                        'next_delivery' => $fruitbox->next_delivery,
                    ]);

                    // echo $fruitbox->next_delivery . "<br/>";
                }
            }

            // ---------- Milkboxes ---------- //

            // I'm currently updating all weekly milkboxes, however previously I also had 'where('is_active', 'Active')' as another filter
            // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
            $milkboxes = MilkBox::where('frequency', 'Monthly')->get();

            echo "<br/> Monthly <br/>";

            foreach ($milkboxes as $milkbox) {

                // If the 'next_delivery' date (value) has passed, we probably need to update it.
                // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                if ($milkbox->next_delivery_week_start < Carbon::now()) {

                    // echo $milkbox->name . '\'s next delivery was outdated but has been changed from ' . $milkbox->next_delivery_week_start . " to ";

                    $lastDelivery = $milkbox->next_delivery_week_start;

                    // This will pull the current milkbox next_delivery(_week_start) into Carbon where we can increase its value by a given monday in the following month.
                    // Even if the monday remains the same this will still vary more than just progressing forward 4 weeks.

                    // This will hold either the value first, second, third, forth or last.
                     $week = $milkbox->week_in_month;

                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($milkbox->next_delivery_week_start)->addMonth()->englishMonth;

                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;

                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);

                    // Set the newly parsed delivery date.
                    $milkbox->next_delivery_week_start = $mondayOfMonth;

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    MilkBox::where('id', $milkbox->id)->update([
                        'previous_delivery_week_start' => $lastDelivery,
                        'next_delivery_week_start' => $milkbox->next_delivery_week_start,
                    ]);

                    // echo $milkbox->next_delivery_week_start . "<br/>";
                }
            }

        // ---------- Bespoke ---------- //

            // This may be easier to leave as a manual date field where we select a date when they need a delivery.
    }
    
    // This looks like the earlier form of a full routing function, I think when I created the CompanyRoutes Contoller I copied and continued this development there.
    
    public function addOrdersToRoutes()
    {

        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        $currentWeekStart = Weekstart::findOrFail(1);

        // If it matches, it's on for delivery this week.
        $fruitboxesForDelivery = FruitBox::where('next_delivery', $currentWeekStart->current)->where('is_active', 'Active')->get();

        // Now the same for milk, and yes I called the same field, with the same purpose something different each time.  I shouldn't be allowed to wield this much power.
        $milkboxesForDelivery = MilkBox::where('next_delivery_week_start', $currentWeekStart->current)->where('is_active', 'Active')->get();

        // Let's grab all the routes.
        $routeInfoAll = CompanyRoute::all();

        // We will want to build the routes based on the 'Assigned To' column, so let's grab that now.
        $assigned_route = CompanyRoute::select('assigned_to')->distinct()->get();

        foreach ($routeInfoAll as $routeInfoSolo)
        {
            // ---------- Fruit Deliveries ---------- //

            // For each route in the routes table, we check the associated Company ID for a FruitBox - that's Active, On Delivery For This Week and on this Delivery Day.
            $fruitboxesForDelivery = FruitBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery', $currentWeekStart->current)
                                                ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->get();
            // Set variable value.
            $fruitbox_totals = 0;

            // If there are more than one we need to generate a total for the route by adding the box totals together.
            if (count($fruitboxesForDelivery) > 1) {
                foreach($fruitboxesForDelivery as $fruitbox) {
                        $fruitbox_totals += $fruitbox->fruitbox_total;
                }
                // Then we overwrite the existing total attached to the route.
                $routeInfoSolo->fruit_boxes = $fruitbox_totals;
            }

            // If we didn't find any then we can add a string to search for in the template.  This was also for testing purposes and we could move this logic to the template.
            if (count($fruitboxesForDelivery) == 0) {
                // dd('None for this week!');
                $fruitboxesForDelivery = 'None for this week!';
            }

            // Now we can create a fruit entry into the route collection and add the fruitbox(es).
            $routeInfoSolo['fruit'] = $fruitboxesForDelivery;

            // ---------- Milk Deliveries ---------- //

            // For each route in the routes table, we check the associated Company ID for a MilkBox - that's Active, On Delivery For This Week and on this Delivery Day.
            $milkboxesForDelivery = MilkBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery_week_start', $currentWeekStart->current)
                                            ->where('delivery_day', $routeInfoSolo->delivery_day)->where('is_active', 'Active')->get();

            // Unlike FruitBoxes there shouldn't be any more than one entry, so totalling isn't necessary - however there may be no milk on the route.
            // If this is the case we need to set the milk totals to 0 for all the options, either here or in the template.  For now I'm going with another 'None For This Week!'.
            if (count($milkboxesForDelivery) == 0) {
                // dd('None for this week!');
                $milkboxesForDelivery = 'None for this week!';
            }

            // Same process with milk, create milk entry and add the information we have.
            $routeInfoSolo['milk'] = $milkboxesForDelivery;

            // A nice little way to check a specific result for testing purposes.  I can comment it out for now but may reuse again in the near future.
            // if ($routeInfoSolo->company_details_id == 1) {
            //     dd($routeInfoSolo);
            // }

            // Now we've added the entries we need to the route, we can build an array of collections and send it to the 'order-processing' template for outputting.
            // Although if they don't have anything scheduled for delivery we can ignore them this week.
            if ($routeInfoSolo->milk == 'None for this week!' && $routeInfoSolo->fruit == 'None for this week!') {
            } else {
                // Otherwise we can add them here.
                $routesAndOrders[] = $routeInfoSolo;
            }

            // dd($routeInfoAll);
            // dd($routesAndOrders);


        }
        return view ('exports.order-processing', ['routes' => $routesAndOrders, 'assigned_route' => $assigned_route]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // This is a possible approach to compile the various totals generated
        // in their own controllers.

        $orderData = new Order();
        $orderData->company_id = $data[0];
        $orderData->fruitbox_id = $data[1];
        $orderData->milkbox_id = $data[2]; // Milk currently has its own table.
        $orderData->snackbox_id = $data[3];
        $orderData->drinkbox_id = $data[4];
        $orderData->special_id = $data[5]; // Non Office Pantry Extras.
        $orderData->delivery_day = $data[6];
        $orderData-> save();

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
        //
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
