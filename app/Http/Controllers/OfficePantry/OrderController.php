<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

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
use Carbon\CarbonImmutable; // for some reason this doesn't seem to be finding its target.

// Now before the order gets moved onto its next delivery date it creates an archive entry if one doesn't already exist.
use App\FruitBoxArchive;
use App\MilkBoxArchive;
use App\SnackBoxArchive;
use App\DrinkBoxArchive;
use App\OtherBoxArchive;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class OrderController extends Controller
{

        public function __construct()
        {
            $weekstart = WeekStart::first();
            $this->weekstart = $weekstart->current;
        }

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
    {-
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


    public function createNextDeliveryDate($box)
    {
        // Add the old delivery_week to the last_delivery_week field.
        $lastDelivery = $box->delivery_week;

        // this is the only line of code which will differ depending on when the frequency selected
        if ($box->frequency === 'Weekly') {
            // Push the date forward a week
            $box->delivery_week = Carbon::parse($box->delivery_week)->addWeek(1);

        } elseif ($box->frequency === 'Fortnightly') {
            // push the date forward two weeks
            $box->delivery_week = Carbon::parse($box->delivery_week)->addWeek(2);

        } elseif ($box->frequency === 'Monthly') {

            // This will hold either the value first, second, third, fourth or last.
            $week = $box->week_in_month;

            // This will check the month of the last delivery and then advance by one month,
            // before saving that month as a string to be parsed later in $mondayOfMonth variable.
            $month = Carbon::parse($box->delivery_week)->addMonthNoOverflow()->englishMonth;

            // Create new instance of Carbon to use as the primer for $carbon::parse() below.
            $carbon = new Carbon;
            // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
            // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
            // as it weighs more heavily on the last delivery date rather than when processes are run.
            $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);

            if ($lastDelivery > $mondayOfMonth) {
                //dd('Happy New Year');
                // This only happens when we advance the month from december to january.
                // The parse doesn't know to advance the year so we actually go back in time.
                // To tackle this issue, we add a year to the delivery_week date
                // THERE MUST BE A BETTER WAY TO DO THIS BUT MEH, OTHER THINGS TO DO.
                $year = Carbon::parse($box->delivery_week)->addYearNoOverflow()->year;
                // Then recalculate the parsed date to use the new year calendar and not the previous!
                $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month . ' ' . $year);
            }

            // Set the newly parsed delivery date.
            $box->delivery_week = $mondayOfMonth;

        } else {
            // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
        }

    }

    // So the new backend means a new approach to order advancement.  Lets see how this goes.  
    public function newFangledOrderAdvancement()
    {
        // As we're not reusing the same box each week anymore, we no longer need to worry about archives 
        // but we do need to worry about making unnecessary/unwanted boxes by creating a fresh entry for every box each week.

        // To tackle this we're only interested in the boxes dated for this weekstart. 
        // Any set in the future can wait until we're processing that week.
        // Conversely, any set in the past are to be considered archive entries.

        // We can ignore any boxes with a 'Paused' or 'Inactive' status.
        // We can also ignore any boxes where the frequency isn't weekly, fortnightly or monthly, i.e bespoke.
        // Active boxes need recreating with a new delivery date, based on the previous delivery and the associated frequency of that fruitbox.

        //----- FruitBoxes -----//

        $active_fruitboxes = FruitBox::where('delivery_week', $this->weekstart)->where('is_active', 'Active')->whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($active_fruitboxes as $fruitbox) {

            $previous_delivery_week = $fruitbox->delivery_week;
            // Now we have a function to update the next delivery_week value for any fruit, milk, snack, drink, or other box.  
            $this->createNextDeliveryDate($fruitbox);
            // dd($previous_delivery_week . ' updated to ' . $fruitbox->delivery_week->format('Y-m-d'));

            // $fruitbox->delivery_week now holds the new delivery_date so we can go ahead and create the new FruitBox entry.

            $new_fruitbox = new FruitBox();
            $new_fruitbox->is_active = 'Active'; // Currently hard coded but this is also the default.
            $new_fruitbox->fruit_partner_id = $fruitbox->fruit_partner_id;
            $new_fruitbox->name = $fruitbox->name;
            $new_fruitbox->company_details_id = $fruitbox->company_details_id;
            $new_fruitbox->type = $fruitbox->type;
            $new_fruitbox->previous_delivery_week = $previous_delivery_week; // Previous delivery date. If a box gets paused this will allow us to see for how long that has been the case.
            $new_fruitbox->delivery_week =  $fruitbox->delivery_week; // New delivery date created by passing the $fruitbox through createNextDeliveryDate().
            $new_fruitbox->frequency = $fruitbox->frequency;
            $new_fruitbox->week_in_month = $fruitbox->week_in_month;
            $new_fruitbox->delivery_day = $fruitbox->delivery_day;
            $new_fruitbox->fruitbox_total = $fruitbox->fruitbox_total;
            $new_fruitbox->deliciously_red_apples = $fruitbox->deliciously_red_apples;
            $new_fruitbox->pink_lady_apples = $fruitbox->pink_lady_apples;
            $new_fruitbox->red_apples = $fruitbox->red_apples;
            $new_fruitbox->green_apples = $fruitbox->green_apples;
            $new_fruitbox->satsumas = $fruitbox->satsumas;
            $new_fruitbox->pears = $fruitbox->pears;
            $new_fruitbox->bananas = $fruitbox->bananas;
            $new_fruitbox->nectarines = $fruitbox->nectarines;
            $new_fruitbox->limes = $fruitbox->limes;
            $new_fruitbox->lemons = $fruitbox->lemons;
            $new_fruitbox->grapes = $fruitbox->grapes;
            $new_fruitbox->seasonal_berries = $fruitbox->seasonal_berries;
            $new_fruitbox->oranges = $fruitbox->oranges;
            $new_fruitbox->cucumbers = $fruitbox->cucumbers;
            $new_fruitbox->mint = $fruitbox->mint;
            $new_fruitbox->organic_lemons = $fruitbox->organic_lemons;
            $new_fruitbox->kiwis = $fruitbox->kiwis;
            $new_fruitbox->grapefruits = $fruitbox->grapefruits;
            $new_fruitbox->avocados = $fruitbox->avocados;
            $new_fruitbox->root_ginger = $fruitbox->root_ginger;
            $new_fruitbox->tailoring_fee = $fruitbox->tailoring_fee;
            $new_fruitbox->discount_multiple = $fruitbox->discount_multiple;
            $new_fruitbox->save();
        }

        //----- Milkboxes -----//

        $active_milkboxes = MilkBox::where('delivery_week', $this->weekstart)->where('is_active', 'Active')->whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($active_milkboxes as $milkbox) {

            $previous_delivery_week = $milkbox->delivery_week;

            $this->createNextDeliveryDate($milkbox);

            $new_milkbox = new MilkBox();
            $new_milkbox->is_active = $milkbox->is_active;
            $new_milkbox->name = $milkbox->name;
            $new_milkbox->fruit_partner_id = $milkbox->fruit_partner_id;
            $new_milkbox->company_details_id = $milkbox->company_details_id;
            $new_milkbox->previous_delivery_week = $previous_delivery_week;
            $new_milkbox->delivery_week = $milkbox->delivery_week;
            $new_milkbox->frequency = $milkbox->frequency;
            $new_milkbox->week_in_month = $milkbox->week_in_month;
            $new_milkbox->delivery_day = $milkbox->delivery_day;
            // Regular 2l
            $new_milkbox->semi_skimmed_2l = $milkbox->semi_skimmed_2l;
            $new_milkbox->skimmed_2l = $milkbox->skimmed_2l;
            $new_milkbox->whole_2l = $milkbox->whole_2l;
            // Regular 1l
            $new_milkbox->semi_skimmed_1l = $milkbox->semi_skimmed_1l;
            $new_milkbox->skimmed_1l = $milkbox->skimmed_1l;
            $new_milkbox->whole_1l = $milkbox->whole_1l;
            // Organic 1l
            $new_milkbox->organic_semi_skimmed_1l = $milkbox->organic_semi_skimmed_1l;
            $new_milkbox->organic_skimmed_1l = $milkbox->organic_skimmed_1l;
            $new_milkbox->organic_whole_1l = $milkbox->organic_whole_1l;
            // Organic 2l
            $new_milkbox->organic_semi_skimmed_2l = $milkbox->organic_semi_skimmed_2l;
            $new_milkbox->organic_skimmed_2l = $milkbox->organic_skimmed_2l;
            $new_milkbox->organic_whole_2l = $milkbox->organic_whole_2l;
            // Alternative 1l options
            $new_milkbox->milk_1l_alt_coconut = $milkbox->milk_1l_alt_coconut;
            $new_milkbox->milk_1l_alt_unsweetened_almond = $milkbox->milk_1l_alt_unsweetened_almond;
            $new_milkbox->milk_1l_alt_almond = $milkbox->milk_1l_alt_almond;
            // Alt pt2
            $new_milkbox->milk_1l_alt_unsweetened_soya = $milkbox->milk_1l_alt_unsweetened_soya;
            $new_milkbox->milk_1l_alt_soya = $milkbox->milk_1l_alt_soya;
            $new_milkbox->milk_1l_alt_oat = $milkbox->milk_1l_alt_oat;
            // Alt pt3
            $new_milkbox->milk_1l_alt_rice = $milkbox->milk_1l_alt_rice;
            $new_milkbox->milk_1l_alt_cashew = $milkbox->milk_1l_alt_cashew;
            $new_milkbox->milk_1l_alt_lactose_free_semi = $milkbox->milk_1l_alt_lactose_free_semi;
            // Alt pt4 (new 09/01/20)
            $new_milkbox->milk_1l_alt_hazelnut = $milkbox->milk_1l_alt_hazelnut;
            $new_milkbox->milk_1l_alt_soya_chocolate = $milkbox->milk_1l_alt_soya_chocolate;
            $new_milkbox->save();     
        }

        // Snackboxes

        $active_snackboxes = SnackBox::where('delivery_week', $this->weekstart)->where('is_active', 'Active')->whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($active_snackboxes as $snackbox) {

            $previous_delivery_week = $snackbox->delivery_week;
  
            $this->createNextDeliveryDate($snackbox);

            $new_snackbox = new SnackBox();
            $new_snackbox->is_active = 'Active';
            $new_snackbox->name = $snackbox->name;
            $new_snackbox->delivered_by = $snackbox->delivered_by; // Maybe this should be uniform too - is it supplier id, fruit_partner_id or delivered_by?  Choose and stick with it!
            $new_snackbox->no_of_boxes = $snackbox->no_of_boxes;
            $new_snackbox->snack_cap = $snackbox->snack_cap; // Is this staying or going, if going we need to have the set box prices entered and pulled into invoicing.  A price id attached to the box would make life easier.
            $new_snackbox->type = $snackbox->type;
            $new_snackbox->company_details_id = $snackbox->company_details_id;
            $new_snackbox->delivery_day = $snackbox->delivery_day;
            $new_snackbox->frequency = $snackbox->frequency;
            $new_snackbox->week_in_month = $snackbox->week_in_month;
            $new_snackbox->previous_delivery_week = $previous_delivery_week;
            $new_snackbox->delivery_week = $snackbox->delivery_week;
            $new_snackbox->save();
        }
        // Drinkboxes

        $active_drinkboxes = DrinkBox::where('delivery_week', $this->weekstart)->where('is_active', 'Active')->whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($active_drinkboxes as $drinkbox) {

            $previous_delivery_week = $drinkbox->delivery_week;
  
            $this->createNextDeliveryDate($drinkbox);

            $new_drinkbox = new DrinkBox();
            $new_drinkbox->is_active = 'Active';
            $new_drinkbox->name = $drinkbox->name;
            $new_drinkbox->delivered_by = $drinkbox->delivered_by;
            $new_drinkbox->type = $drinkbox->type;
            $new_drinkbox->company_details_id = $drinkbox->company_details_id;
            $new_drinkbox->delivery_day = $drinkbox->delivery_day;
            $new_drinkbox->frequency = $drinkbox->frequency;
            $new_drinkbox->week_in_month = $drinkbox->week_in_month;
            $new_drinkbox->previous_delivery_week = $previous_delivery_week;
            $new_drinkbox->delivery_week = $drinkbox->delivery_week;
            $new_drinkbox->save();
        }


        // Otherboxes

        $active_otherboxes = OtherBox::where('delivery_week', $this->weekstart)->where('is_active', 'Active')->whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($active_otherboxes as $otherbox) {

            $previous_delivery_week = $otherbox->delivery_week;
  
            $this->createNextDeliveryDate($otherbox);

            $new_otherbox = new OtherBox();
            $new_otherbox->is_active = 'Active';
            $new_otherbox->name = $otherbox->name;
            $new_otherbox->delivered_by = $otherbox->delivered_by;
            $new_otherbox->type = $otherbox->type;
            $new_otherbox->company_details_id = $otherbox->company_details_id;
            $new_otherbox->delivery_day = $otherbox->delivery_day;
            $new_otherbox->frequency = $otherbox->frequency;
            $new_otherbox->week_in_month = $otherbox->week_in_month;
            $new_otherbox->previous_delivery_week = $previous_delivery_week;
            $new_otherbox->delivery_week = $otherbox->delivery_week;
            $new_otherbox->save();
        }
    }








    // THIS IS A TEMPORARY SHORTCUT TO THE ORDER ADVANCEMENT FUNCTION
    // - SOME CHANGES MADE TO THIS NEED TO BE APPLIED TO THE FULL FUNCTION BUT I'M STILL TESTING HOW IT HANDLES THE SWITCH ONTO THE NEW YEAR BEFORE IMPLEMENTING THE REWORK.
    // SUMMARY OF CHANGES SO FAR
    // - ADD 'NO OVERFLOW' TO MONTHS AND YEARS TO PREVENT 31 DAYS AND I SUSPECT LEAP YEARS ADVANCING BY AN ADDITIONAL MONTH (OR YEAR) BECAUSE OF A SURPLUS DAY.
    public function fudgeOrderAdvancement()
    {
        //----- Fruitboxes -----//
        $fruitboxes = FruitBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($fruitboxes as $fruitbox) {

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


                    //dump($fruitbox->next_delivery);
                    // This will hold either the value first, second, third, fourth or last.
                    $week = $fruitbox->week_in_month;
                    //dump($week);

                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($fruitbox->next_delivery)->addMonthNoOverflow()->englishMonth;
                    //dump($month);
                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;
                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);

                    if ($lastDelivery > $mondayOfMonth) {
                        //dd('Happy New Year');
                        // This only happens when we advance the month from december to january.
                        // The parse doesn't know to advance the year so we actually go back in time.
                        // To tackle this issue, we add a year to the next_delivery date
                        // THERE MUST BE A BETTER WAY TO DO THIS BUT MEH, OTHER THINGS TO DO.
                        $year = Carbon::parse($fruitbox->next_delivery)->addYearNoOverflow()->year;
                        // Then recalculate the parsed date to use the new year calendar and not the previous!
                        $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month . ' ' . $year);
                        //dump($mondayOfMonth);
                    }

                    // dd($mondayOfMonth);
                    // Set the newly parsed delivery date.
                    $fruitbox->next_delivery = $mondayOfMonth;

                } else {
                    // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                }

                //----- Advance the Fruitbox Orders -----//

                FruitBox::where('id', $fruitbox->id)->update([
                    'previous_delivery' => $lastDelivery,
                    'next_delivery' => $fruitbox->next_delivery,
                    // 'invoiced_at' => null, nothing will have been invoiced so let's not worry about that.
                ]);

                //----- End of Advance the Fruitbox Orders -----//
            }
        }

        //----- End of Fruitboxes -----//

        //----- Milkboxes -----//
        $milkboxes = MilkBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get();

        foreach ($milkboxes as $milkbox) {

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

                    // This will hold either the value first, second, third, fourth or last.
                    $week = $milkbox->week_in_month;
                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($milkbox->next_delivery)->addMonthNoOverflow()->englishMonth;
                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;
                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);

                    if ($lastDelivery > $mondayOfMonth) {

                        // Same issue/solution as fruit (see fruit for related comments)
                        $year = Carbon::parse($milkbox->next_delivery)->addYearNoOverflow()->year;
                        // Then recalculate the parsed date to use the new year calendar and not the previous!
                        $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month . ' ' . $year);

                    }

                    // Set the newly parsed delivery date.
                    $milkbox->next_delivery = $mondayOfMonth;


                } else {

                    // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                }

                //----- Advance the Milkbox Orders -----//

                MilkBox::where('id', $milkbox->id)->update([
                    'previous_delivery' => $lastDelivery,
                    'next_delivery' => $milkbox->next_delivery,
                    // 'invoiced_at' => null, nothing will have been invoiced so let's not worry about that.
                ]);

                //----- End of Advance the Milkbox Orders -----//
            }

        //----- End of Milkboxes -----//
        }
    }



    // I think this 'public static function advanceNextOrderDeliveryDate()' might be the only function in here that's actually still in use.
    // Which is quite funny as I have no idea whether it's still updating successfully since I don't actually know
    // when it would ATTEMPT TO UPDATE THE ORDERS, OR WHAT HAPPENS WHEN IT CAN'T?!
    // But it's on the 'to do list', sob... EDIT: QUICK TEST TO AUTOMATE AGAIN ON 13/05/19 15:30, timezone changed to Europe/London.

    public static function advanceNextOrderDeliveryDate()
    {
        // dd('*Rolls Up Sleeves* ~ "You want me to do this or what?!"');

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

        //$dateTest = CarbonImmutable::parse('2019-08-19');
        //dd($dateTest);
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

                    // Bit of repetition but atleast the names will be very clear... :)
                    $archived_entry_next_delivery = $fruitbox->next_delivery;
                    $archived_entry_previous_delivery = $fruitbox->previous_delivery;

                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($fruitbox->frequency === 'Weekly') {
                        // Push the date forward a week
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(1);

                    } elseif ($fruitbox->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $fruitbox->next_delivery = Carbon::parse($fruitbox->next_delivery)->addWeek(2);

                    } elseif ($fruitbox->frequency === 'Monthly') {

                        // This will hold either the value first, second, third, fourth or last.
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

                    // Now before we update the entries we should create an archive of the order we're effectively replacing.
                    // If the order has a matching invoice_at/created_at date we can go ahead and create an 'inactive' archive entry,
                    // otherwise we need to create an active archive to get pulled into the next invoicing run for that branding theme.

                    // First let's make sure the two dates are in the same format for comparison
                    $converted_invoiced_at_date = new CarbonImmutable($fruitbox->invoiced_at);
                    //dump($converted_invoiced_at_date);

                    $converted_updated_at_date = new CarbonImmutable($fruitbox->updated_at);
                    //dump($converted_updated_at_date);

                    //dd($converted_updated_at_date);

                    if ($fruitbox->is_active === 'Active') {

                        // Now check the dates to see if they match the same day.
                        // EDIT: OR NOT, I HAVE A NEW PLAN...
                        // if ($converted_invoiced_at_date->format('ymd') == $converted_updated_at_date->format('ymd')) {
                        // NOW WE JUST CHECK FOR AN INVOICE DATE, IF WE GOT ONE, WE CAN ARCHIVE THIS BOX AS INACTIVE
                        if ($fruitbox->invoiced_at !== null) {

                            Log::channel('slack')->info(
                                $fruitbox->invoiced_at . ' doesn\'t equal null, so fingers are firmly crossed this box ('
                                . $fruitbox->id . ' on ' . $archived_entry_next_delivery . '('. $fruitbox->delivery_day
                                .') can be saved as an Inactive archive'
                            );

                            // Then we can create an inactive archive
                            FruitBoxArchive::updateOrInsert(
                                [ // Check the values contained in this array for a matching record.  If we find it, update the record, otherwise add a new entry.
                                    'fruitbox_id' => $fruitbox->id,
                                    'next_delivery' => $archived_entry_next_delivery
                                ],
                                [
                                    'is_active' => 'Inactive',
                                    'fruit_partner_id' => $fruitbox->fruit_partner_id,
                                    'name' => $fruitbox->name,
                                    'company_details_id' => $fruitbox->company_details_id,
                                    'type' => $fruitbox->type,
                                    'previous_delivery' => $archived_entry_previous_delivery,
                                    'frequency' => $fruitbox->frequency,
                                    'week_in_month' => $fruitbox->week_in_month,
                                    'delivery_day' => $fruitbox->delivery_day,
                                    'fruitbox_total' => $fruitbox->fruitbox_total,
                                    'deliciously_red_apples' => $fruitbox->deliciously_red_apples,
                                    'pink_lady_apples' => $fruitbox->pink_lady_apples,
                                    'red_apples' => $fruitbox->red_apples,
                                    'green_apples' => $fruitbox->green_apples,
                                    'satsumas' => $fruitbox->satsumas,
                                    'pears' => $fruitbox->pears,
                                    'bananas' => $fruitbox->bananas,
                                    'nectarines' => $fruitbox->nectarines,
                                    'limes' => $fruitbox->limes,
                                    'lemons' => $fruitbox->lemons,
                                    'grapes' => $fruitbox->grapes,
                                    'seasonal_berries' => $fruitbox->seasonal_berries,
                                    'oranges' => $fruitbox->oranges,
                                    'cucumbers' => $fruitbox->cucumbers,
                                    'mint' => $fruitbox->mint,
                                    'organic_lemons' => $fruitbox->organic_lemons,
                                    'kiwis' => $fruitbox->kiwis,
                                    'grapefruits' => $fruitbox->grapefruits,
                                    'avocados' => $fruitbox->avocados,
                                    'root_ginger' => $fruitbox->root_ginger,
                                    'tailoring_fee' => $fruitbox->tailoring_fee,
                                    'discount_multiple' => $fruitbox->discount_multiple,
                                    'invoiced_at' => $fruitbox->invoiced_at, // This might prove a useful field, to sense check at least, that the order was indeed invoiced.  Although the due date will be different.
                                    'created_at' => $fruitbox->created_at,
                                    'updated_at' => $fruitbox->updated_at // this may not be worth updating as it'll be changed on creation?
                                ]
                            );

                        } else {

                            Log::channel('slack')->info(
                                'Invoiced_at (value) (' . $fruitbox->invoiced_at . ') equals null, we need to save fruitbox (id) ' . $fruitbox->id
                                . ' on ' . $archived_entry_next_delivery . ' (' . $fruitbox->delivery_day .') as Active.'
                            );

                            // we need to create an active archive
                            FruitBoxArchive::updateOrInsert(
                                [ // Check the values contained in this array for a matching record.  If we find it, update the record, otherwise add a new entry.
                                    'fruitbox_id' => $fruitbox->id,
                                    'next_delivery' => $archived_entry_next_delivery
                                ],
                                [
                                    'is_active' => 'Active',
                                    'fruit_partner_id' => $fruitbox->fruit_partner_id,
                                    'name' => $fruitbox->name,
                                    'company_details_id' => $fruitbox->company_details_id,
                                    'type' => $fruitbox->type,
                                    'previous_delivery' => $archived_entry_previous_delivery,
                                    'frequency' => $fruitbox->frequency,
                                    'week_in_month' => $fruitbox->week_in_month,
                                    'delivery_day' => $fruitbox->delivery_day,
                                    'fruitbox_total' => $fruitbox->fruitbox_total,
                                    'deliciously_red_apples' => $fruitbox->deliciously_red_apples,
                                    'pink_lady_apples' => $fruitbox->pink_lady_apples,
                                    'red_apples' => $fruitbox->red_apples,
                                    'green_apples' => $fruitbox->green_apples,
                                    'satsumas' => $fruitbox->satsumas,
                                    'pears' => $fruitbox->pears,
                                    'bananas' => $fruitbox->bananas,
                                    'nectarines' => $fruitbox->nectarines,
                                    'limes' => $fruitbox->limes,
                                    'lemons' => $fruitbox->lemons,
                                    'grapes' => $fruitbox->grapes,
                                    'seasonal_berries' => $fruitbox->seasonal_berries,
                                    'oranges' => $fruitbox->oranges,
                                    'cucumbers' => $fruitbox->cucumbers,
                                    'mint' => $fruitbox->mint,
                                    'organic_lemons' => $fruitbox->organic_lemons,
                                    'kiwis' => $fruitbox->kiwis,
                                    'grapefruits' => $fruitbox->grapefruits,
                                    'avocados' => $fruitbox->avocados,
                                    'root_ginger' => $fruitbox->root_ginger,
                                    'tailoring_fee' => $fruitbox->tailoring_fee,
                                    'discount_multiple' => $fruitbox->discount_multiple,
                                    'invoiced_at' => $fruitbox->invoiced_at,
                                    'created_at' => $fruitbox->created_at,
                                    'updated_at' => $fruitbox->updated_at // this may not be worth updating as it'll be changed on creation?
                                ]
                            );
                        }
                    }

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    // EDIT: 22/08/19 I'M DEBATING WHETHER TO DESTROY AND REBUILD THE BOX OR JUST TO ADD INVOICED_AT => null
                    // Let's start with just adding null to the update for now.
                    FruitBox::where('id', $fruitbox->id)->update([
                        'previous_delivery' => $lastDelivery,
                        'next_delivery' => $fruitbox->next_delivery,
                        'invoiced_at' => null,
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

                    // Bit of repetition but atleast the names will be very clear... :)
                    $archived_entry_next_delivery = $milkbox->next_delivery;
                    $archived_entry_previous_delivery = $milkbox->previous_delivery;

                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($milkbox->frequency === 'Weekly') {
                        // Push the date forward a week
                        $milkbox->next_delivery = Carbon::parse($milkbox->next_delivery)->addWeek(1);

                    } elseif ($milkbox->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $milkbox->next_delivery = Carbon::parse($milkbox->next_delivery)->addWeek(2);

                    } elseif ($milkbox->frequency === 'Monthly') {

                        // This will hold either the value first, second, third, fourth or last.
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

                    // Now before we update the entries we should create an archive of the order we're effectively replacing.
                    // If the order has a matching invoice_at/created_at date we can go ahead and create an 'inactive' archive entry,
                    // otherwise we need to create an active archive to get pulled into the next invoicing run for that branding theme.

                    // First let's make sure the two dates are in the same format for comparison
                    $converted_invoiced_at_date = new CarbonImmutable($milkbox->invoiced_at);
                    $converted_updated_at_date = new CarbonImmutable($milkbox->updated_at);

                    if ($milkbox->is_active === 'Active') {
                        // Now check the dates to see if they match the same day.
                        if ($milkbox->invoiced_at !== null) {

                            Log::channel('slack')->info(
                                $milkbox->invoiced_at . ' doesn\'t equal null, so fingers are firmly crossed this box ('
                                . $milkbox->id . ' on ' . $archived_entry_next_delivery . '('. $milkbox->delivery_day
                                .') can be saved as an Inactive archive'
                            );

                            MilkBoxArchive::updateOrInsert(
                            [
                                // Technically, milkbox_id + next_delivery_week should be all that's needed but testing will probably prove me wrong.
                                'milkbox_id' => $milkbox->id,
                                'next_delivery' => $archived_entry_next_delivery,
                            ],
                            [
                                'is_active' => 'Inactive',
                                'company_details_id' => $milkbox->company_details_id,
                                'fruit_partner_id' => $milkbox->fruit_partner_id,
                                'frequency' => $milkbox->frequency,
                                'delivery_day' => $milkbox->delivery_day,
                                'week_in_month' => $milkbox->week_in_month,
                                'previous_delivery' => $archived_entry_previous_delivery,
                                // Milk 2l
                                'semi_skimmed_2l' => $milkbox->semi_skimmed_2l,
                                'skimmed_2l' => $milkbox->skimmed_2l,
                                'whole_2l' => $milkbox->whole_2l,
                                // Milk 1l
                                'semi_skimmed_1l' => $milkbox->semi_skimmed_1l,
                                'skimmed_1l' => $milkbox->skimmed_1l,
                                'whole_1l' => $milkbox->whole_1l,
                                // Organic Milk 2l
                                'organic_semi_skimmed_2l' => $milkbox->organic_semi_skimmed_2l,
                                'organic_skimmed_2l' => $milkbox->organic_skimmed_2l,
                                'organic_whole_2l' => $milkbox->organic_whole_2l,
                                // Organic Milk 1l
                                'organic_semi_skimmed_1l' => $milkbox->organic_semi_skimmed_1l,
                                'organic_skimmed_1l' => $milkbox->organic_skimmed_1l,
                                'organic_whole_1l' => $milkbox->organic_whole_1l,
                                // Milk Alternatives
                                'milk_1l_alt_coconut' => $milkbox->milk_1l_alt_coconut,
                                'milk_1l_alt_unsweetened_almond' => $milkbox->milk_1l_alt_unsweetened_almond,
                                'milk_1l_alt_almond' => $milkbox->milk_1l_alt_almond,
                                // Milk Alternatives (Pt2)
                                'milk_1l_alt_unsweetened_soya' => $milkbox->milk_1l_alt_unsweetened_soya,
                                'milk_1l_alt_soya' => $milkbox->milk_1l_alt_soya,
                                'milk_1l_alt_oat' => $milkbox->milk_1l_alt_oat,
                                // Milk Alternatives (Pt3)
                                'milk_1l_alt_rice' => $milkbox->milk_1l_alt_rice,
                                'milk_1l_alt_cashew' => $milkbox->milk_1l_alt_cashew,
                                'milk_1l_alt_lactose_free_semi' => $milkbox->milk_1l_alt_lactose_free_semi,
                                // Invoiced At Date
                                'invoiced_at' => $milkbox->invoiced_at,
                            ]);

                        } else {

                            Log::channel('slack')->info(
                                'Invoiced_at (value) (' . $milkbox->invoiced_at . ') equals null, we need to save fruitbox (id) ' . $milkbox->id
                                . ' on ' . $archived_entry_next_delivery . ' (' . $milkbox->delivery_day .') as Active.'
                            );

                            // we need to create an active archive
                            MilkBoxArchive::updateOrInsert(
                            [
                                // Technically, milkbox_id + next_delivery_week should be all that's needed but testing will probably prove me wrong.
                                'milkbox_id' => $milkbox->id,
                                'next_delivery' => $archived_entry_next_delivery,
                            ],
                            [
                                'is_active' => 'Active',
                                'company_details_id' => $milkbox->company_details_id,
                                'fruit_partner_id' => $milkbox->fruit_partner_id,
                                'frequency' => $milkbox->frequency,
                                'delivery_day' => $milkbox->delivery_day,
                                'week_in_month' => $milkbox->week_in_month,
                                'previous_delivery' => $archived_entry_previous_delivery,
                                // Milk 2l
                                'semi_skimmed_2l' => $milkbox->semi_skimmed_2l,
                                'skimmed_2l' => $milkbox->skimmed_2l,
                                'whole_2l' => $milkbox->whole_2l,
                                // Milk 1l
                                'semi_skimmed_1l' => $milkbox->semi_skimmed_1l,
                                'skimmed_1l' => $milkbox->skimmed_1l,
                                'whole_1l' => $milkbox->whole_1l,
                                // Organic Milk 2l
                                'organic_semi_skimmed_2l' => $milkbox->organic_semi_skimmed_2l,
                                'organic_skimmed_2l' => $milkbox->organic_skimmed_2l,
                                'organic_whole_2l' => $milkbox->organic_whole_2l,
                                // Organic Milk 1l
                                'organic_semi_skimmed_1l' => $milkbox->organic_semi_skimmed_1l,
                                'organic_skimmed_1l' => $milkbox->organic_skimmed_1l,
                                'organic_whole_1l' => $milkbox->organic_whole_1l,
                                // Milk Alternatives
                                'milk_1l_alt_coconut' => $milkbox->milk_1l_alt_coconut,
                                'milk_1l_alt_unsweetened_almond' => $milkbox->milk_1l_alt_unsweetened_almond,
                                'milk_1l_alt_almond' => $milkbox->milk_1l_alt_almond,
                                // Milk Alternatives (Pt2)
                                'milk_1l_alt_unsweetened_soya' => $milkbox->milk_1l_alt_unsweetened_soya,
                                'milk_1l_alt_soya' => $milkbox->milk_1l_alt_soya,
                                'milk_1l_alt_oat' => $milkbox->milk_1l_alt_oat,
                                // Milk Alternatives (Pt3)
                                'milk_1l_alt_rice' => $milkbox->milk_1l_alt_rice,
                                'milk_1l_alt_cashew' => $milkbox->milk_1l_alt_cashew,
                                'milk_1l_alt_lactose_free_semi' => $milkbox->milk_1l_alt_lactose_free_semi,
                                // Invoiced At Date
                                'invoiced_at' => $milkbox->invoiced_at,
                            ]);

                        }
                    } else {
                        // We can assume this is an order that wasn't used this week so no achive is necessary.
                    }

                    // Now we can update the next_delivery_week_value, using the id to identify the correct entry.
                    // And as we're advancing the date, this box needs the invoice date (if stamped), stripped out.
                    // Even if it's already null, it doesn't hurt to redeclare it and the exisitng order to invoice should have been added as an active archive.

                    MilkBox::where('id', $milkbox->id)->update([
                        'previous_delivery' => $lastDelivery,
                        'next_delivery' => $milkbox->next_delivery,
                        'invoiced_at' => null,
                    ]);

                } //  end of if ($milkbox->next_delivery < Carbon::now())
            } // foreach ($milkboxes as $milkbox)

        // ---------- Snackboxes ---------- //

            $snackboxes = SnackBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly', 'Bespoke'])->get()->groupBy('snackbox_id'); // <-- I think bespoke was a temporary test and should be removed, as I can't automate bespoke!

            // Do I want to group these snackboxes by their snackbox_id or as I'm only really concerned with advancing the next_delivery_date, should I just treat each entry on it's own?
            // Basically, what's the next delivery date (of entry), is that date prior to Carbon::now(), if so, the order (entry) is out of date and ready to be advanced.
            // If we've already stripped out the snackbox entries, then we'll only have one entry anyway.

            foreach ($snackboxes as $snackbox) {

                $snackbox_status_recovered = $snackbox[0]->is_active;
                $snackbox_id_recovered = $snackbox[0]->snackbox_id;
                $delivered_by_recovered = $snackbox[0]->delivered_by;
                $delivery_day_recovered = $snackbox[0]->delivery_day;
                $no_of_boxes_recovered = $snackbox[0]->no_of_boxes;
                $snack_cap_recovered = $snackbox[0]->snack_cap;
                $type_recovered = $snackbox[0]->type;
                $company_details_id_recovered = $snackbox[0]->company_details_id;
                $frequency_recovered = $snackbox[0]->frequency;
                $week_in_month_recovered = $snackbox[0]->week_in_month;

                // while the next delivery date is calculated below I'm going to do it here (either as well, or use this to replace the Carbon::parsing below).

                if ($frequency_recovered === 'Weekly') {
                    // Push the date forward a week
                    $advanced_next_delivery_week = Carbon::parse($snackbox[0]->next_delivery_week)->addWeek(1)->format('Y-m-d'); // Added format('Y-m-d') ...

                } elseif ($frequency_recovered === 'Fortnightly') {
                    // push the date forward two weeks
                    $advanced_next_delivery_week = Carbon::parse($snackbox[0]->next_delivery_week)->addWeek(2)->format('Y-m-d'); // ... In the hope of fixing the ...

                } elseif ($frequency_recovered === 'Monthly') {

                    // This will hold either the value first, second, third, fourth or last.
                    $week = $snackbox[0]->week_in_month;
                    // This will check the month of the last delivery and then advance by one month,
                    // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                    $month = Carbon::parse($snackbox[0]->next_delivery_week)->addMonth()->englishMonth;
                    // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                    $carbon = new Carbon;
                    // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                    // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                    // as it weighs more heavily on the last delivery date rather than when processes are run.
                    $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month)->format('Y-m-d'); // ... Inconsistent date format! 23/09/19
                    // Set the newly parsed delivery date.
                    $advanced_next_delivery_week = $mondayOfMonth;

                } else {

                    // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly,
                    // fortnightly, and monthly orders
                }

                foreach ($snackbox as $snackbox_entry) {

                    if ($snackbox_entry->next_delivery_week < Carbon::now()) {

                        $lastDelivery = $snackbox_entry->next_delivery_week;

                        // Bit of repetition but atleast the names will be very clear... :)
                        $archived_entry_next_delivery = $snackbox_entry->next_delivery_week;
                        $archived_entry_previous_delivery = $snackbox_entry->previous_delivery_week;

                        // this is the only line of code which will differ depending on when the frequency selected
                        if ($snackbox_entry->frequency === 'Weekly') {
                            // Push the date forward a week
                            $snackbox_entry->next_delivery_week = Carbon::parse($snackbox_entry->next_delivery_week)->addWeek(1)->format('Y-m-d'); // <-- Why not use $advanced_next_delivery_week?

                        } elseif ($snackbox_entry->frequency === 'Fortnightly') {
                            // push the date forward two weeks
                            $snackbox_entry->next_delivery_week = Carbon::parse($snackbox_entry->next_delivery_week)->addWeek(2)->format('Y-m-d');

                        } elseif ($snackbox_entry->frequency === 'Monthly') {

                            // This will hold either the value first, second, third, fourth or last.
                            $week = $snackbox_entry->week_in_month;
                            // This will check the month of the last delivery and then advance by one month,
                            // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                            $month = Carbon::parse($snackbox_entry->next_delivery_week)->addMonth()->englishMonth;
                            // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                            $carbon = new Carbon;
                            // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                            // and allow it to use the carbon date of when the function is run however I'm currently prefering this approach
                            // as it weighs more heavily on the last delivery date rather than when processes are run.
                            $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                            // Set the newly parsed delivery date.
                            $snackbox_entry->next_delivery_week = $mondayOfMonth->format('Y-m-d');

                        } else {

                            // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
                        }

                        dump($snackbox_entry->next_delivery_week);

                        //----- Oops looks like some superfluous code -----//

                        // Oh right, I think I put these here to use, before deciding as we destroy the box every time, we actually just needed to check if there's an invoice date or not.
                        $converted_invoiced_at_date = new CarbonImmutable($snackbox_entry->invoiced_at);
                        $converted_updated_at_date = new CarbonImmutable($snackbox_entry->updated_at);

                        //----- End of Oops looks like some superfluous code -----//

                        if ($snackbox_entry->is_active) {
                            // As snackboxes are effectively destroyed and rebuilt empty each week, we only need to check that the invoiced_at date isn't null
                            if ($snackbox_entry->invoiced_at !== null) {
                                // if we have an invoice date, this box has been processed but not emptied yet.
                                // if that's the case we just need to create an inactive archive holding all the previous orders.
                                // at least so long as the archive hasn't been created already

                                // Unlike the previous 2 categories (fruitbox/milkbox) the snackbox_id will have multiple entries for the same next_delivery_week,
                                // however if we also combine the product_id, the entries should be unique.

                                SnackBoxArchive::updateOrInsert(
                                [
                                    'snackbox_id' => $snackbox_entry->snackbox_id,
                                    'next_delivery_week' => $archived_entry_next_delivery,
                                    'product_id' => $snackbox_entry->product_id,
                                ],
                                [
                                    'is_active' => 'Inactive',
                                    // 'id' => $snackbox_entry->id, // EDIT: DON'T ADD THIS TO ENTRIES, SERVES NO PURPOSE AND CAN THROW DUPLICATE ID ERRORS!
                                    'delivered_by' => $snackbox_entry->delivered_by,
                                    'no_of_boxes' => $snackbox_entry->no_of_boxes,
                                    'snack_cap' => $snackbox_entry->snack_cap,
                                    'type' => $snackbox_entry->type,
                                    'company_details_id' => $snackbox_entry->company_details_id,
                                    'delivery_day' => $snackbox_entry->delivery_day,
                                    'frequency' => $snackbox_entry->frequency,
                                    'week_in_month' => $snackbox_entry->week_in_month,
                                    'previous_delivery_week' => $archived_entry_previous_delivery,
                                    'code' => $snackbox_entry->code,
                                    'name' => $snackbox_entry->name,
                                    'quantity' => $snackbox_entry->quantity,
                                    'unit_price' => $snackbox_entry->unit_price,
                                    'case_price' => $snackbox_entry->case_price,
                                    'invoiced_at' => $snackbox_entry->invoiced_at,
                                    'created_at' => $snackbox_entry->created_at,
                                    'updated_at' => $snackbox_entry->updated_at,
                                ]);

                            } else {
                                // Same again if it hasn't been invoiced only this time we save it as active so it can be pulled into the next invoicing run for that branding theme.
                                SnackBoxArchive::updateOrInsert(
                                [
                                    'snackbox_id' => $snackbox_entry->snackbox_id,
                                    'next_delivery_week' => $archived_entry_next_delivery,
                                    'product_id' => $snackbox_entry->product_id,
                                ],
                                [
                                    'is_active' => 'Active',
                                    // 'id' => $snackbox_entry->id, // EDIT: DON'T ADD THIS TO ENTRIES, SERVES NO PURPOSE AND CAN THROW DUPLICATE ID ERRORS!
                                    'delivered_by' => $snackbox_entry->delivered_by,
                                    'no_of_boxes' => $snackbox_entry->no_of_boxes,
                                    'snack_cap' => $snackbox_entry->snack_cap,
                                    'type' => $snackbox_entry->type,
                                    'company_details_id' => $snackbox_entry->company_details_id,
                                    'delivery_day' => $snackbox_entry->delivery_day,
                                    'frequency' => $snackbox_entry->frequency,
                                    'week_in_month' => $snackbox_entry->week_in_month,
                                    'previous_delivery_week' => $archived_entry_previous_delivery,
                                    'code' => $snackbox_entry->code,
                                    'name' => $snackbox_entry->name,
                                    'quantity' => $snackbox_entry->quantity,
                                    'unit_price' => $snackbox_entry->unit_price,
                                    'case_price' => $snackbox_entry->case_price,
                                    'invoiced_at' => $snackbox_entry->invoiced_at,
                                    'created_at' => $snackbox_entry->created_at,
                                    'updated_at' => $snackbox_entry->updated_at,
                                ]);
                            }


                        } else {
                         // then (it's inactive &) we don't need to worry about it.
                        }

                        // Updating the box to the next week start is still a good (essential) thing to do
                        // but as the orders don't need to be kept, we should also empty the box, ready to be refilled.

                        // We can't rely on an entry having a product id of 0, this only happens if we built the box without adding any products initially.
                        // So what is the best way to empty this box?  Should we just delete all the entries and build a new box, or edit one entry and delete the rest?


                        // So instead of updating a box like this...
                        // SnackBox::where('id', $snackbox_entry->id)->update([
                        //     'previous_delivery_week' => $lastDelivery,
                        //     'next_delivery_week' => $snackbox_entry->next_delivery_week,
                        // ]);
                        // we could destroy the box entries like this, now that we have the archive created.
                        Snackbox::destroy($snackbox_entry->id);

                    } // end of if ($snackbox_entry->next_delivery_week < Carbon::now())

                } // end of foreach ($snackbox as $snackbox_entry)

                // Right so now the box has been backed up as an archive and deleted (!), let's recreate it again ready for reuse (phew) :)
                $rebuilt_snackbox = new SnackBox();
                $rebuilt_snackbox->is_active = $snackbox_status_recovered;
                $rebuilt_snackbox->snackbox_id = $snackbox_id_recovered;
                $rebuilt_snackbox->delivered_by = $delivered_by_recovered;
                $rebuilt_snackbox->delivery_day = $delivery_day_recovered;
                $rebuilt_snackbox->no_of_boxes = $no_of_boxes_recovered;
                $rebuilt_snackbox->snack_cap = $snack_cap_recovered;
                $rebuilt_snackbox->type = $type_recovered;
                $rebuilt_snackbox->company_details_id = $company_details_id_recovered;
                $rebuilt_snackbox->frequency = $frequency_recovered;
                $rebuilt_snackbox->week_in_month = $week_in_month_recovered;
                $rebuilt_snackbox->previous_delivery_week = $snackbox[0]->next_delivery_week;
                $rebuilt_snackbox->next_delivery_week = $advanced_next_delivery_week;
                $rebuilt_snackbox->product_id = 0;
                $rebuilt_snackbox->code = null;
                $rebuilt_snackbox->name = null;
                $rebuilt_snackbox->quantity = null;
                $rebuilt_snackbox->unit_price = null;
                $rebuilt_snackbox->case_price = null;
                $rebuilt_snackbox->invoiced_at = null; // We could keep a record of the last time this box was invoiced or return it to null.  It looks like I'm going with null... subject to change.
                $rebuilt_snackbox->save();

            } // end of foreach ($snackboxes as $snackbox)

        // ---------- Drinkboxes ---------- //

        $drinkboxes = DrinkBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get()->groupBy('drinkbox_id');

        foreach ($drinkboxes as $drinkbox) {

            // dd($drinkbox);

            $drinkbox_status_recovered = $drinkbox[0]->is_active;
            $drinkbox_id_recovered = $drinkbox[0]->drinkbox_id;
            $delivered_by_recovered = $drinkbox[0]->delivered_by_id;
            $delivery_day_recovered = $drinkbox[0]->delivery_day;
            $type_recovered = $drinkbox[0]->type;
            $company_details_id_recovered = $drinkbox[0]->company_details_id;
            $frequency_recovered = $drinkbox[0]->frequency;
            $week_in_month_recovered = $drinkbox[0]->week_in_month;

            // while the next delivery date is calculated below I'm going to do it here (either as well, or use this to replace the Carbon::parsing below).

            if ($frequency_recovered === 'Weekly') {
                // Push the date forward a week
                $advanced_next_delivery_week = Carbon::parse($drinkbox[0]->next_delivery_week)->addWeek(1);

            } elseif ($frequency_recovered === 'Fortnightly') {
                // push the date forward two weeks
                $advanced_next_delivery_week = Carbon::parse($drinkbox[0]->next_delivery_week)->addWeek(2);

            } elseif ($frequency_recovered === 'Monthly') {

                // This will hold either the value first, second, third, fourth or last.
                $week = $drinkbox[0]->week_in_month;
                // This will check the month of the last delivery and then advance by one month,
                // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                $month = Carbon::parse($drinkbox[0]->next_delivery_week)->addMonth()->englishMonth;
                // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                $carbon = new Carbon;
                // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                // as it weighs more heavily on the last delivery date rather than when processes are run.
                $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                // Set the newly parsed delivery date.
                $advanced_next_delivery_week = $mondayOfMonth;

            } else {

                // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
            }

            foreach ($drinkbox as $drinkbox_entry) {

                if ($drinkbox_entry->next_delivery_week < Carbon::now()) {

                    $lastDelivery = $drinkbox_entry->next_delivery_week;

                    // Bit of repetition but atleast the names will be very clear... :)
                    $archived_entry_next_delivery = $drinkbox_entry->next_delivery_week;
                    $archived_entry_previous_delivery = $drinkbox_entry->previous_delivery_week;

                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($drinkbox_entry->frequency === 'Weekly') {
                        // Push the date forward a week
                        $drinkbox_entry->next_delivery_week = Carbon::parse($drinkbox_entry->next_delivery_week)->addWeek(1);

                    } elseif ($drinkbox_entry->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $drinkbox_entry->next_delivery_week = Carbon::parse($drinkbox_entry->next_delivery_week)->addWeek(2);

                    } elseif ($drinkbox_entry->frequency === 'Monthly') {

                        // This will hold either the value first, second, third, fourth or last.
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

                    $converted_invoiced_at_date = new CarbonImmutable($drinkbox_entry->invoiced_at);
                    $converted_updated_at_date = new CarbonImmutable($drinkbox_entry->updated_at);

                    if ($drinkbox_entry->is_active) {
                        // As snackboxes are effectively destroyed and rebuilt empty each week, we only need to check that the invoiced_at date isn't null
                        if ($drinkbox_entry->invoiced_at !== null) {
                            // if we have an invoice date, this box has been processed but not emptied yet.
                            // if that's the case we just need to create an inactive archive holding all the previous orders.
                            // at least so long as the archive hasn't been created already

                            // Unlike the previous 2 categories (fruitbox/milkbox) the snackbox_id will have multiple entries for the same next_delivery_week,
                            // however if we also combine the product_id, the entries should be unique.

                            DrinkBoxArchive::updateOrInsert(
                            [
                                'drinkbox_id' => $drinkbox_entry->drinkbox_id,
                                'next_delivery_week' => $archived_entry_next_delivery,
                                'product_id' => $drinkbox_entry->product_id,
                            ],
                            [
                                'is_active' => 'Inactive',
                                // 'id' => $drinkbox_entry->id, // For the same reasons as stated in snackboxes.
                                'delivered_by_id' => $drinkbox_entry->delivered_by_id,
                                'type' => $drinkbox_entry->type,
                                'company_details_id' => $drinkbox_entry->company_details_id,
                                'delivery_day' => $drinkbox_entry->delivery_day,
                                'frequency' => $drinkbox_entry->frequency,
                                'week_in_month' => $drinkbox_entry->week_in_month,
                                'previous_delivery_week' => $archived_entry_previous_delivery,
                                'code' => $drinkbox_entry->code,
                                'name' => $drinkbox_entry->name,
                                'quantity' => $drinkbox_entry->quantity,
                                'unit_price' => $drinkbox_entry->unit_price,
                                'case_price' => $drinkbox_entry->case_price,
                                'invoiced_at' => $drinkbox_entry->invoiced_at,
                                'created_at' => $drinkbox_entry->created_at,
                                'updated_at' => $drinkbox_entry->updated_at,
                            ]);

                        } else {
                            // Same again if it hasn't been invoiced only this time we save it as active so it can be pulled into the next invoicing run for that branding theme.
                            DrinkBoxArchive::updateOrInsert(
                            [
                                'drinkbox_id' => $drinkbox_entry->drinkbox_id,
                                'next_delivery_week' => $archived_entry_next_delivery,
                                'product_id' => $drinkbox_entry->product_id,
                            ],
                            [
                                'is_active' => 'Active',
                                // 'id' => $drinkbox_entry->id, // For the same reasons as stated in snackboxes.
                                'delivered_by_id' => $drinkbox_entry->delivered_by_id,
                                'type' => $drinkbox_entry->type,
                                'company_details_id' => $drinkbox_entry->company_details_id,
                                'delivery_day' => $drinkbox_entry->delivery_day,
                                'frequency' => $drinkbox_entry->frequency,
                                'week_in_month' => $drinkbox_entry->week_in_month,
                                'previous_delivery_week' => $archived_entry_previous_delivery,
                                'code' => $drinkbox_entry->code,
                                'name' => $drinkbox_entry->name,
                                'quantity' => $drinkbox_entry->quantity,
                                'unit_price' => $drinkbox_entry->unit_price,
                                'case_price' => $drinkbox_entry->case_price,
                                'invoiced_at' => $drinkbox_entry->invoiced_at,
                                'created_at' => $drinkbox_entry->created_at,
                                'updated_at' => $drinkbox_entry->updated_at,
                            ]);
                        }


                    } else {
                     // then we don't need to worry about it.
                    }


                    DrinkBox::destroy($drinkbox_entry->id);

                } // if ($drinkbox_entry->next_delivery_week < Carbon::now())
            } // foreach ($drinkbox as $drinkbox_entry)

            // Right so now the box has been backed up as an archive and deleted (!), let's recreate it again ready for reuse (phew) :)
            $rebuilt_drinkbox = new DrinkBox();
            $rebuilt_drinkbox->is_active = $drinkbox_status_recovered;
            $rebuilt_drinkbox->drinkbox_id = $drinkbox_id_recovered;
            $rebuilt_drinkbox->delivered_by_id = $delivered_by_recovered;
            $rebuilt_drinkbox->delivery_day = $delivery_day_recovered;
            $rebuilt_drinkbox->type = $type_recovered;
            $rebuilt_drinkbox->company_details_id = $company_details_id_recovered;
            $rebuilt_drinkbox->frequency = $frequency_recovered;
            $rebuilt_drinkbox->week_in_month = $week_in_month_recovered;
            $rebuilt_drinkbox->previous_delivery_week = $drinkbox[0]->next_delivery_week;
            $rebuilt_drinkbox->next_delivery_week = $advanced_next_delivery_week;
            $rebuilt_drinkbox->product_id = 0;
            $rebuilt_drinkbox->code = null;
            $rebuilt_drinkbox->name = null;
            $rebuilt_drinkbox->quantity = null;
            $rebuilt_drinkbox->unit_price = null;
            $rebuilt_drinkbox->case_price = null;
            $rebuilt_drinkbox->invoiced_at = null; // We could keep a record of the last time this box was invoiced or return it to null.  It looks like I'm going with null... subject to change.
            $rebuilt_drinkbox->save();

        } // foreach ($drinkboxes as $drinkbox)


        // ---------- Otherboxes ---------- //

        $otherboxes = OtherBox::whereIn('frequency', ['Weekly', 'Fortnightly', 'Monthly'])->get()->groupBy('otherbox_id');

        foreach ($otherboxes as $otherbox) {

            $otherbox_status_recovered = $otherbox[0]->is_active;
            $otherbox_id_recovered = $otherbox[0]->otherbox_id;
            $delivered_by_recovered = $otherbox[0]->delivered_by_id;
            $delivery_day_recovered = $otherbox[0]->delivery_day;
            $no_of_boxes_recovered = $otherbox[0]->no_of_boxes;
            $type_recovered = $otherbox[0]->type;
            $company_details_id_recovered = $otherbox[0]->company_details_id;
            $frequency_recovered = $otherbox[0]->frequency;
            $week_in_month_recovered = $otherbox[0]->week_in_month;

            // while the next delivery date is calculated below I'm going to do it here (either as well, or use this to replace the Carbon::parsing below).

            if ($frequency_recovered === 'Weekly') {
                // Push the date forward a week
                $advanced_next_delivery_week = Carbon::parse($otherbox[0]->next_delivery_week)->addWeek(1);

            } elseif ($frequency_recovered === 'Fortnightly') {
                // push the date forward two weeks
                $advanced_next_delivery_week = Carbon::parse($otherbox[0]->next_delivery_week)->addWeek(2);

            } elseif ($frequency_recovered === 'Monthly') {

                // This will hold either the value first, second, third, fourth or last.
                $week = $otherbox[0]->week_in_month;
                // This will check the month of the last delivery and then advance by one month,
                // before saving that month as a string to be parsed later in $mondayOfMonth variable.
                $month = Carbon::parse($otherbox[0]->next_delivery_week)->addMonth()->englishMonth;
                // Create new instance of Carbon to use as the primer for $carbon::parse() below.
                $carbon = new Carbon;
                // An alternative to setting the month above and parsing below would be to parse the phrase '$week . ' monday of NEXT month'
                // and allow it use the carbon date of when the function is run however I'm currently prefering this approach
                // as it weighs more heavily on the last delivery date rather than when processes are run.
                $mondayOfMonth = $carbon::parse($week . ' monday of ' . $month);
                // Set the newly parsed delivery date.
                $advanced_next_delivery_week = $mondayOfMonth;

            } else {

                // Nothing should get here as the frequency is a drop down (selection) of options, and we specifically grabbed only weekly, fortnightly, and monthly orders
            }



            foreach ($otherbox as $otherbox_entry) {

                if ($otherbox_entry->next_delivery_week < Carbon::now()) {

                    $lastDelivery = $otherbox_entry->next_delivery_week;

                    // Bit of repetition but atleast the names will be very clear... :)
                    $archived_entry_next_delivery = $otherbox_entry->next_delivery_week;
                    $archived_entry_previous_delivery = $otherbox_entry->previous_delivery_week;

                    // this is the only line of code which will differ depending on when the frequency selected
                    if ($otherbox_entry->frequency === 'Weekly') {
                        // Push the date forward a week
                        $otherbox_entry->next_delivery_week = Carbon::parse($otherbox_entry->next_delivery_week)->addWeek(1);

                    } elseif ($otherbox_entry->frequency === 'Fortnightly') {
                        // push the date forward two weeks
                        $otherbox_entry->next_delivery_week = Carbon::parse($otherbox_entry->next_delivery_week)->addWeek(2);

                    } elseif ($otherbox_entry->frequency === 'Monthly') {

                        // This will hold either the value first, second, third, fourth or last.
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

                    $converted_invoiced_at_date = new CarbonImmutable($otherbox_entry->invoiced_at);
                    $converted_updated_at_date = new CarbonImmutable($otherbox_entry->updated_at);

                    if ($otherbox_entry->is_active) {
                        // As snackboxes are effectively destroyed and rebuilt empty each week, we only need to check that the invoiced_at date isn't null
                        if ($otherbox_entry->invoiced_at !== null) {
                            // if we have an invoice date, this box has been processed but not emptied yet.
                            // if that's the case we just need to create an inactive archive holding all the previous orders.
                            // at least so long as the archive hasn't been created already

                            // Unlike the previous 2 categories (fruitbox/milkbox) the snackbox_id will have multiple entries for the same next_delivery_week,
                            // however if we also combine the product_id, the entries should be unique.

                            OtherBoxArchive::updateOrInsert(
                            [
                                'otherbox_id' => $otherbox_entry->otherbox_id,
                                'next_delivery_week' => $archived_entry_next_delivery,
                                'product_id' => $otherbox_entry->product_id,
                            ],
                            [
                                'is_active' => 'Inactive',
                                // 'id' => $otherbox_entry->id, // Yup let's get rid of this too.
                                'delivered_by_id' => $otherbox_entry->delivered_by_id,
                                //'no_of_boxes' => $otherbox_entry->no_of_boxes,
                                'type' => $otherbox_entry->type,
                                'company_details_id' => $otherbox_entry->company_details_id,
                                'delivery_day' => $otherbox_entry->delivery_day,
                                'frequency' => $otherbox_entry->frequency,
                                'week_in_month' => $otherbox_entry->week_in_month,
                                'previous_delivery_week' => $archived_entry_previous_delivery,
                                'code' => $otherbox_entry->code,
                                'name' => $otherbox_entry->name,
                                'quantity' => $otherbox_entry->quantity,
                                'unit_price' => $otherbox_entry->unit_price,
                                'case_price' => $otherbox_entry->case_price,
                                'invoiced_at' => $otherbox_entry->invoiced_at,
                                'created_at' => $otherbox_entry->created_at,
                                'updated_at' => $otherbox_entry->updated_at,
                            ]);

                        } else {
                            // Same again if it hasn't been invoiced only this time we save it as active so it can be pulled into the next invoicing run for that branding theme.
                            OtherBoxArchive::updateOrInsert(
                            [
                                'otherbox_id' => $otherbox_entry->otherbox_id,
                                'next_delivery_week' => $archived_entry_next_delivery,
                                'product_id' => $otherbox_entry->product_id,
                            ],
                            [
                                'is_active' => 'Active',
                                // 'id' => $otherbox_entry->id, // Yup let's get rid of this too.
                                'delivered_by_id' => $otherbox_entry->delivered_by_id,
                                //'no_of_boxes' => $otherbox_entry->no_of_boxes,
                                'type' => $otherbox_entry->type,
                                'company_details_id' => $otherbox_entry->company_details_id,
                                'delivery_day' => $otherbox_entry->delivery_day,
                                'frequency' => $otherbox_entry->frequency,
                                'week_in_month' => $otherbox_entry->week_in_month,
                                'previous_delivery_week' => $archived_entry_previous_delivery,
                                'code' => $otherbox_entry->code,
                                'name' => $otherbox_entry->name,
                                'quantity' => $otherbox_entry->quantity,
                                'unit_price' => $otherbox_entry->unit_price,
                                'case_price' => $otherbox_entry->case_price,
                                'invoiced_at' => $otherbox_entry->invoiced_at,
                                'created_at' => $otherbox_entry->created_at,
                                'updated_at' => $otherbox_entry->updated_at,
                            ]);
                        }

                    } else {
                     // then we don't need to worry about it.
                    }

                    OtherBox::destroy($otherbox_entry->id);

                } // if ($otherbox_entry->next_delivery_week < Carbon::now())
            } // foreach ($otherbox as $otherbox_entry)

            // Right so now the box has been backed up as an archive and deleted (!), let's recreate it again ready for reuse (phew) :)
            $rebuilt_otherbox = new OtherBox();
            $rebuilt_otherbox->is_active = $otherbox_status_recovered;
            $rebuilt_otherbox->otherbox_id = $otherbox_id_recovered;
            $rebuilt_otherbox->delivered_by_id = $delivered_by_recovered;
            $rebuilt_otherbox->delivery_day = $delivery_day_recovered;
            // $rebuilt_otherbox->no_of_boxes = $no_of_boxes_recovered;
            $rebuilt_otherbox->type = $type_recovered;
            $rebuilt_otherbox->company_details_id = $company_details_id_recovered;
            $rebuilt_otherbox->frequency = $frequency_recovered;
            $rebuilt_otherbox->week_in_month = $week_in_month_recovered;
            $rebuilt_otherbox->previous_delivery_week = $otherbox[0]->next_delivery_week;
            $rebuilt_otherbox->next_delivery_week = $advanced_next_delivery_week;
            $rebuilt_otherbox->product_id = 0;
            $rebuilt_otherbox->code = null;
            $rebuilt_otherbox->name = null;
            $rebuilt_otherbox->quantity = null;
            $rebuilt_otherbox->unit_price = null;
            $rebuilt_otherbox->case_price = null;
            $rebuilt_otherbox->invoiced_at = null;
            $rebuilt_otherbox->save();


        } // foreach ($otherboxes as $otherbox)

        // ---------- Bespoke ---------- //

            // This may be easier to leave as a manual date field where we select a date when they need a delivery.

            return back(); // <-- This took me back to the homepage, not to the previous page... curious.

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

        // ALTHOUGH IT WAS AN OLD IDEA AND NOT IMPLEMENTED.  HOWEVER WHY HAS 'IDEA' GOT SYNTAX HIGHLIGHTING?

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
