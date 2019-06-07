<?php

namespace App\Http\Controllers;

use App\Company; // this is also very out of date!!
use App\CompanyDetails;
use App\Order;
use App\FruitBox;
use App\MilkBox;
// looks like I only set this up to advance fruit and milk orders!!
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;
use App\WeekStart;
use App\CompanyRoute;
use App\Cron;
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
    
    public function showCronData() 
    {   
        // While we only have one cron this is fine.  If/when we add more, 
        // I need to decide if there will be a few different functions grabbing each one or, more likely, that I loop through them and use a more generic query.
        $cron_data_raw = Cron::where('command', 'advance:odd')->get(); 

        $cron_data_amended = (object)[];
        $cron_data_amended->command = $cron_data_raw[0]->command;
        $cron_data_amended->next_run = date('Y-m-d H:i:s', $cron_data_raw[0]->next_run);
        $cron_data_amended->last_run = date('Y-m-d H:i:s', $cron_data_raw[0]->last_run);
        
        return response()->json($cron_data_amended);
    }
    
    public function updateCronData(Request $request)
    {
        dump(request('next_run'));
        $edited_next_run_date = Carbon::parse(request('next_run'))->timestamp;
        dump($edited_next_run_date);
        
        dump(request('command'));
        Cron::where('command', request('command'))->update([
            'next_run' => $edited_next_run_date,
        ]);
    }
    
    // I think this 'public static function advanceNextOrderDeliveryDate()' might be the only function in here that's actually still in use.  
    // Which is quite funny as I have no idea whether it's still updating successfully since I don't actually know 
    // when it would ATTEMPT TO UPDATE THE ORDERS, OR WHAT HAPPENS WHEN IT CAN'T?!
    // But it's on the 'to do list', sob... EDIT: QUICK TEST TO AUTOMATE AGAIN ON 13/05/19 15:30, timezone changed to Europe/London.
    
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
        
        //----- Alternative approach for refactoring - thoughts... -----//
        
            // Instead of the initial query only taking one frequency, I could grab all of them, with an if clause
            // filtering by frequency to push the next_delivery_date a week/fortnight/monthly depending on requirements.
            
            // I've now refactored the function to reduce the repetition and make it easier to read as all the infomation for each section is now together.
            // It all appears to be working, however it will be worth keeping an eye on.
            // It could probably be condensed even further but this is readable enough for now.
        
        //----- End of Alternative approach for refactoring - thoughts... -----//
        

        // ---------- Fruitboxes ---------- //

            // I'm currently updating all weekly fruitboxes, however previously I also had 'where('is_active', 'Active')' as another filter
            // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
            $fruitboxes = FruitBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

            foreach ($fruitboxes as $fruitbox) {

                // If the 'next_delivery' date (value) has passed, we probably need to update it.
                // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.

                if ($fruitbox->next_delivery < Carbon::now()) {

                    // Add the old next_delivery_week to the last_delivery_week field.
                    $lastDelivery = $fruitbox->next_delivery;
                    
                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($fruitbox->frequency === 'Weekly') {
                        // Push the date forward a week
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(1);
                        
                    } elseif ($fruitbox->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(2);
                    
                    } elseif ($fruitbox->frequency === 'Monthly') {
                    
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
                        
                    } else {
                    
                        // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                    }

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    FruitBox::where('id', $fruitbox->id)->update([
                        'previous_delivery' => $lastDelivery,
                        'next_delivery' => $fruitbox->next_delivery,
                    ]);

                    // echo $fruitbox->next_delivery . "<br/>";
                } // end of if ($next_delivery < Carbon::now()), the else clause should only contain orders set to a time in the future, so we don't need to do anything.
            } // end of foreach ($fruitboxes as $fruitbox)

        // ---------- Milkboxes ---------- //
        
            // I'm currently updating all weekly milkboxes, however previously I also had 'where('is_active', 'Active')' as another filter
            // but this would mean any boxes frozen (even temporarily) would be at the wrong week start when advanced by a week as the code stands.
            $milkboxes = MilkBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();
        
            foreach ($milkboxes as $milkbox) {
        
                // If the 'next_delivery' date (value) has passed, we probably need to update it.
                // This check will also help to prevent the next_delivery date being increased before that (particular) delivery has been sent.
        
                if ($milkbox->next_delivery < Carbon::now()) {
        
                    // echo $milkbox->name . '\'s next delivery was outdated but has been changed from ' . $milkbox->next_delivery . " to ";
        
                    $lastDelivery = $milkbox->next_delivery;
        
                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($milkbox->frequency === 'Weekly') {
                        // Push the date forward a week
                        $milkbox->next_delivery = Carbon::parse($milkbox->next_delivery)->addWeek(1);
    
                    } elseif ($milkbox->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $milkbox->next_delivery = Carbon::parse($milkbox->next_delivery)->addWeek(2);
    
                    } elseif ($milkbox->frequency === 'Monthly') {
    
                        // This will hold either the value first, second, third, forth or last.
                         $week = $milkbox->week_in_month;
                        // This will check the month of the last delivery and then advance by one month,
                        // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                        $month = Carbon::parse($milkbox->next_delivery)->addMonth()->englishMonth;
                        // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                        $carbon = new Carbon;
                        // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                        // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                        // as it weighs more heavily on the last delivery date rather than when processes are run.
                        $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                        // Set the newly parsed delivery date.
                        $milkbox->next_delivery = $mondayOfMonth;
    
                    } else {
    
                        // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                    }
        
                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    MilkBox::where('id', $milkbox->id)->update([
                        'previous_delivery' => $lastDelivery,
                        'next_delivery' => $milkbox->next_delivery,
                    ]);
        
                } //  end of if ($milkbox->next_delivery < Carbon::now())
            } // foreach ($milkboxes as $milkbox)
        
        // ---------- Snackboxes ---------- //
        
            $snackboxes = SnackBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();
        
            // Do I want to group these snackboxes by their snackbox_id or as I'm only really concerned with advancing the next_delivery_date, should I just treat each entry on it's own?
            // Basically, what's the next delivery date (of entry), is that date prior to Carbon::now(), if so, the order (entry) is out of date and ready to be advanced.
            // If we've already stripped out the snackbox entries, then we'll only have one entry anyway.                
        
            foreach ($snackboxes as $snackbox_entry) {
        
                if ($snackbox_entry->next_delivery_week < Carbon::now()) {
        
                    $lastDelivery = $snackbox_entry->next_delivery_week;
        
                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($snackbox_entry->frequency === 'Weekly') {
                        // Push the date forward a week
                        $snackbox_entry->next_delivery_week = Carbon::parse($snackbox_entry->next_delivery_week)->addWeek(1);
        
                    } elseif ($snackbox_entry->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $snackbox_entry->next_delivery_week = Carbon::parse($snackbox_entry->next_delivery_week)->addWeek(2);
        
                    } elseif ($snackbox_entry->frequency === 'Monthly') {
        
                        // This will hold either the value first, second, third, forth or last.
                         $week = $snackbox_entry->week_in_month;
                        // This will check the month of the last delivery and then advance by one month,
                        // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                        $month = Carbon::parse($snackbox_entry->next_delivery_week)->addMonth()->englishMonth;
                        // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                        $carbon = new Carbon;
                        // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                        // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                        // as it weighs more heavily on the last delivery date rather than when processes are run.
                        $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                        // Set the newly parsed delivery date.
                        $snackbox_entry->next_delivery_week = $mondayOfMonth;
        
                    } else {
        
                        // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                    }
    
                    SnackBox::where('id', $snackbox_entry->id)->update([
                        'previous_delivery_week' => $lastDelivery,
                        'next_delivery_week' => $snackbox_entry->next_delivery_week,
                    ]);
        
                } //  end of if ($snackbox_entry->next_delivery_week < Carbon::now())    
            } // foreach ($snackboxes as $snackbox_entry)
        
        // ---------- Drinkboxes ---------- //
        
        $drinkboxes = DrinkBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();                
        
        foreach ($drinkboxes as $drinkbox_entry) {
        
            if ($drinkbox_entry->next_delivery_week < Carbon::now()) {
        
                $lastDelivery = $drinkbox_entry->next_delivery_week;
        
                // this is the only line of code which will differ depending on when the frequency selected
                if ($drinkbox_entry->frequency === 'Weekly') {
                    // Push the date forward a week
                    $drinkbox_entry->next_delivery_week = Carbon::parse($drinkbox_entry->next_delivery_week)->addWeek(1);
        
                } elseif ($drinkbox_entry->frequency === 'Fortnightly') {
                    // push the date forward two weeks
                    $drinkbox_entry->next_delivery_week = Carbon::parse($drinkbox_entry->next_delivery_week)->addWeek(2);
        
                } elseif ($drinkbox_entry->frequency === 'Monthly') {
        
                    // This will hold either the value first, second, third, forth or last.
                     $week = $drinkbox_entry->week_in_month;
                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($drinkbox_entry->next_delivery_week)->addMonth()->englishMonth;
                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;
                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                    // Set the newly parsed delivery date.
                    $drinkbox_entry->next_delivery_week = $mondayOfMonth;
        
                } else {
        
                    // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                }
        
                DrinkBox::where('id', $drinkbox_entry->id)->update([
                    'previous_delivery_week' => $lastDelivery,
                    'next_delivery_week' => $drinkbox_entry->next_delivery_week,
                ]);
        
            } // if ($drinkbox_entry->next_delivery_week < Carbon::now())
        } // foreach ($drinkboxes as $drinkbox_entry)
        
        // ---------- Otherboxes ---------- //
        
        $otherboxes = OtherBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();                
        
        foreach ($otherboxes as $otherbox_entry) {
        
            if ($otherbox_entry->next_delivery_week < Carbon::now()) {
        
                $lastDelivery = $otherbox_entry->next_delivery_week;
        
                // this is the only line of code which will differ depending on when the frequency selected
                if ($otherbox_entry->frequency === 'Weekly') {
                    // Push the date forward a week
                    $otherbox_entry->next_delivery_week = Carbon::parse($otherbox_entry->next_delivery_week)->addWeek(1);
        
                } elseif ($otherbox_entry->frequency === 'Fortnightly') {
                    // push the date forward two weeks
                    $otherbox_entry->next_delivery_week = Carbon::parse($otherbox_entry->next_delivery_week)->addWeek(2);
        
                } elseif ($otherbox_entry->frequency === 'Monthly') {
        
                    // This will hold either the value first, second, third, forth or last.
                     $week = $otherbox_entry->week_in_month;
                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($otherbox_entry->next_delivery_week)->addMonth()->englishMonth;
                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;
                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                    // Set the newly parsed delivery date.
                    $otherbox_entry->next_delivery_week = $mondayOfMonth;
        
                } else {
        
                    // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                }
        
                OtherBox::where('id', $otherbox_entry->id)->update([
                    'previous_delivery_week' => $lastDelivery,
                    'next_delivery_week' => $otherbox_entry->next_delivery_week,
                ]);
        
            } // if ($otherbox_entry->next_delivery_week < Carbon::now())
        } // foreach ($otherboxes as $otherbox_entry)
        
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
        $milkboxesForDelivery = MilkBox::where('next_delivery', $currentWeekStart->current)->where('is_active', 'Active')->get();

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
            $milkboxesForDelivery = MilkBox::where('company_details_id', $routeInfoSolo->company_details_id)->where('next_delivery', $currentWeekStart->current)
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
