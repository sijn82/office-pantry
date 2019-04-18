<?php

namespace App\Http\Controllers;

// Processing Week
use App\WeekStart;
// Products
use App\OfficePantryProducts;
use App\Product;

// Company Details
use App\CompanyDetails;
use App\CompanyRoute;

// Orders
use App\FruitBox;
use App\MilkBox;
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;

// Delivery Info
use App\AssignedRoute;
use App\FruitPartner;

// Date/Time Library
use Carbon\Carbon;

class InvoicingController extends Controller
{
            //---------- Set some variables ----------// - 122
            //---------- Fruitbox ----------// - 221
            //---------- Milkbox ----------// - 1036
            //---------- Snackbox ----------// - 1323
            //---------- Drinkbox ----------//
            //---------- Otherbox ----------//




    public function __construct()
    {
        $week_start = WeekStart::findOrFail(1);
        $this->week_start = $week_start->current;

    }

    public function weekly_invoicing()
    {
        // We want to run through each company, looking for orders processed this week.

        // First check we could make is whether the company is still registered as active.
        // This might help to speed up the processing, should the following steps prove a laboured process.
        // Though otherwise we want to check every company on record for orders.

        // Actually this is more like what I want, if I want to pull all connected orders together first.
        // It first checks that the company is active(ly receiving orders), then that the associated orders are also active and due to (or have) receive(d) an order this processing week.

        $companies = CompanyDetails::where('is_active', 'Active')->with([
            'fruitbox' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
            },
            // Now we need to check the fruitbox archives for any boxes that have been updated before invoicing,
            // their status will remain active as there is still work to be done with these box details.
            // If the status is inactive, this means the box was invoiced prior to being updated and is now only needed for our records.
            'fruitbox_archive' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
            },
            'milkbox' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
            },
            'milkbox_archive' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
            },
            // Milkbox archive check will go here...
            'snackboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            },
            // Snackbox archive will go here...
            'drinkboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            },
            // Drinkbox archive will go here...
            'otherboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            }
            // , and finally the otherbox archive check will go here.
            ])->get();

            // Moved this out of the foreach loop as we only need to set these variables once.

            //----- OP Products -----//
                //----- Milkbox Variables -----//
                    $milk_1l = OfficePantryProducts::findOrFail(1);
                    $milk_2l = OfficePantryProducts::findOrFail(2);
                    $milk_alt = OfficePantryProducts::findOrFail(3);
                    $milk_1l_org = OfficePantryProducts::findOrFail(4);
                    $milk_2l_org = OfficePantryProducts::findOrFail(5);
                //----- Fruitbox Varibles -----//
                    $fruitbox_x1 = OfficePantryProducts::findOrFail(6);
                    $fruitbox_x2 = OfficePantryProducts::findOrFail(7);
                    $fruitbox_x3 = OfficePantryProducts::findOrFail(8);
                    $fruitbox_x4 = OfficePantryProducts::findOrFail(9);
                    $fruitbox_x7_plus = OfficePantryProducts::findOrFail(10);
                //----- Milkbox Variables (Fruit Partner) -----//
                    $milk_1l_fruit_partner = OfficePantryProducts::findOrFail(11);
                    $milk_2l_fruit_partner = OfficePantryProducts::findOrFail(12);
                    $milk_alt_fruit_partner = OfficePantryProducts::findOrFail(13);
                    $milk_1l_org_fruit_partner = OfficePantryProducts::findOrFail(14);
                    $milk_2l_org_fruit_partner = OfficePantryProducts::findOrFail(15);
                //----- Fruitbox Variables (Fruit Partner) -----//
                    $fruitbox_x1_fruit_partner = OfficePantryProducts::findOrFail(16);
                    $fruitbox_x2_fruit_partner = OfficePantryProducts::findOrFail(17);
                    $fruitbox_x3_fruit_partner = OfficePantryProducts::findOrFail(18);
                    $fruitbox_x4_fruit_partner = OfficePantryProducts::findOrFail(19);
                    $fruitbox_x7_plus_fruit_partner = OfficePantryProducts::findOrFail(20);
            //----- End of OP Products -----//

            //----- This'll hold all of our invoices into an array of custom built objects -----//
                $sales_invoices = [];
            //----- End of - This'll hold all of our invoices into an array of custom built objects -----//

        // Now each $company should just have the orders which need invoicing attached.
        foreach ($companies as $company) {

            // Each box type needs to be processed slightly differently, so we need 5 dedicated foreach loops.

                                                            //---------- Set some variables ----------//

                //----- Fruitbox Variables -----//
                    //----- OP -----//
                        $monday_total_boxes = (object) [];
                        $tuesday_total_boxes = (object) [];
                        $wednesday_total_boxes = (object) [];
                        $thursday_total_boxes = (object) [];
                        $friday_total_boxes = (object) [];

                        $fruitbox_weekly_total = 0;
                        $punnets_weekly_total = 0;
                        $tailoring_fee_weekly_total = 0;
                        $total_boxes_without_discount = 0;
                    //----- End of OP -----//

                    //----- Fruit Partners -----//
                        $monday_total_boxes_fruit_partner = (object) [];
                        $tuesday_total_boxes_fruit_partner = (object) [];
                        $wednesday_total_boxes_fruit_partner = (object) [];
                        $thursday_total_boxes_fruit_partner = (object) [];
                        $friday_total_boxes_fruit_partner = (object) [];

                        $fruitbox_weekly_total_fruit_partner = 0;
                        $punnets_weekly_total_fruit_partner = 0;
                        $tailoring_fee_weekly_total_fruit_partner = 0;
                        $total_boxes_without_discount_fruit_partner = 0;
                    //----- End of Fruit Partners -----//
                //----- End of Fruitbox Variables -----//

                //----- Milk Total Variables  -----//

                    //----- OP -----//
                        // Current Orders
                        $milkbox_1l_order = 0;
                        $milkbox_2l_order = 0;
                        $milkbox_alt_order = 0;
                        $milk_organic_1l_order = 0;
                        $milk_organic_2l_order = 0;
                        // Archived (but not invoiced) Orders
                        $milkbox_archive_1l_order = 0;
                        $milkbox_archive_2l_order = 0;
                        $milkbox_archive_alt_order = 0;
                        $milk_archive_organic_1l_order = 0;
                        $milk_archive_organic_2l_order = 0;
                        // Total of both together
                        $milkbox_1l_total_week = (object) [];
                        $milkbox_2l_total_week = (object) [];
                        $milkbox_alt_total_week = (object) [];
                        $milk_organic_1l_total_week = (object) [];
                        $milk_organic_2l_total_week = (object) [];
                        // Add the relevant product pricing
                        $milkbox_1l_total_week->product = $milk_1l;
                        $milkbox_2l_total_week->product = $milk_2l;
                        $milkbox_alt_total_week->product = $milk_alt;
                        $milk_organic_1l_total_week->product = $milk_1l_org;
                        $milk_organic_2l_total_week->product = $milk_2l_org;
                        // Add quantity as a property set it to 0
                        $milkbox_1l_total_week->quantity = 0;
                        $milkbox_2l_total_week->quantity = 0;
                        $milkbox_alt_total_week->quantity = 0;
                        $milk_organic_1l_total_week->quantity = 0;
                        $milk_organic_2l_total_week->quantity = 0;
                    //----- Fruit Partners -----/
                        // Current Orders
                        $milkbox_1l_order_fruit_partner = 0;
                        $milkbox_2l_order_fruit_partner = 0;
                        $milkbox_alt_order_fruit_partner = 0;
                        $milk_organic_1l_order_fruit_partner = 0;
                        $milk_organic_2l_order_fruit_partner = 0;
                        // Archived (but not invoiced) Orders
                        $milkbox_archive_1l_order_fruit_partner = 0;
                        $milkbox_archive_2l_order_fruit_partner = 0;
                        $milkbox_archive_alt_order_fruit_partner = 0;
                        $milk_archive_organic_1l_order_fruit_partner = 0;
                        $milk_archive_organic_2l_order_fruit_partner = 0;
                        // Total of both together
                        $milkbox_1l_total_week_fruit_partner = (object) [];
                        $milkbox_2l_total_week_fruit_partner = (object) [];
                        $milkbox_alt_total_week_fruit_partner = (object) [];
                        $milk_organic_1l_total_week_fruit_partner = (object) [];
                        $milk_organic_2l_total_week_fruit_partner = (object) [];
                        // Add the relevant product pricing
                        $milkbox_1l_total_week_fruit_partner->product = $milk_1l_fruit_partner;
                        $milkbox_2l_total_week_fruit_partner->product = $milk_2l_fruit_partner;
                        $milkbox_alt_total_week_fruit_partner->product = $milk_alt_fruit_partner;
                        $milk_organic_1l_total_week_fruit_partner->product = $milk_1l_org_fruit_partner;
                        $milk_organic_2l_total_week_fruit_partner->product = $milk_2l_org_fruit_partner;
                        // Add quantity as a property set it to 0
                        $milkbox_1l_total_week_fruit_partner->quantity = 0;
                        $milkbox_2l_total_week_fruit_partner->quantity = 0;
                        $milkbox_alt_total_week_fruit_partner->quantity = 0;
                        $milk_organic_1l_total_week_fruit_partner->quantity = 0;
                        $milk_organic_2l_total_week_fruit_partner->quantity = 0;

                //----- End of Milk Total Variables  -----//

                //----- Snackbox Variables -----//
                    $snackbox_invoice_pt1 = (object) [];
                //----- Snackbox Variables -----//

                //----- Drinkbox Variables -----//
                    $drinkbox_invoice_pt1 = (object) [];
                //----- Drinkbox Variables -----//

                //----- Otherbox Variables -----//
                    $otherbox_invoice_pt1 = (object) [];
                //----- Otherbox Variables -----//
                                                        //---------- End of - Set some variables ----------//

                                                                //---------- Fruitbox ----------//

            foreach ($company->fruitbox as $fruitbox) {

                // If this works as intended, each $fruitbox total should build an accurate total for box types which can then be tallied up for potential discounts
                // The only remaining question is how to handle tailored and additional berry/grape punnets.

                // We want to separate drops made by us, with drops fillfilled by fruit partners, naming them slightly differently
                if ($fruitbox->fruit_partner_id === 1) {

                    // Punnets are priced and charged separately, a seasonal box contains 2 (charged at an additional £2.50 per punnet)
                    // and berry boxes are charged the same price for however many punnets they have.

                    if ( strtolower( $fruitbox->type ) === 'seasonal' ) {
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * 2 );
                    } elseif ( strtolower( $fruitbox->type ) === 'berry' ) {
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * $fruitbox->seasonal_berries );
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * $fruitbox->grapes );
                    }

                    // For each fruitbox we need to check for any additional charges under the tailoring_fee.
                    // I could specifically make this available for tailored boxes but its use is situational,
                    // and opening this fee up for all boxes increases its situational potential.
                    if (isset($fruitbox->tailoring_fee)) {
                        $tailoring_fee_weekly_total += ( $fruitbox->fruitbox_total * $fruitbox->tailoring_fee );
                    }

                    if ($fruitbox->discount_multiple === 'Yes') {
                        switch ($fruitbox->delivery_day) {
                            case 'Monday':
                                // I want the fruitbox types to be as dynamic as possible, so we can create new ones without worrying about hardcoded checks.
                                // This isset checks to see if the object contains the box type as a property, adding to the totals if it does.
                                if ( isset( $monday_total_boxes->{strtolower( $fruitbox->type )} )) {
                                    $monday_total_boxes->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    // Otherwise this is the first box of its type to be processed for this company, on this day.
                                    // Instead of adding to the total, we can set the inital value.
                                    $monday_total_boxes->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Tuesday':
                                if( isset( $tuesday_total_boxes->{strtolower( $fruitbox->type )} )) {
                                    $tuesday_total_boxes->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $tuesday_total_boxes->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Wednesday':
                                if( isset( $wednesday_total_boxes->{strtolower( $fruitbox->type )} )) {
                                    $wednesday_total_boxes->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $wednesday_total_boxes->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Thursday':
                                if( isset( $thursday_total_boxes->{strtolower( $fruitbox->type )} )) {
                                    $thursday_total_boxes->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $thursday_total_boxes->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Friday':
                                if( isset( $friday_total_boxes->{strtolower( $fruitbox->type )} )) {
                                    $friday_total_boxes->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $friday_total_boxes->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                        }
                    }  else { // end of if ($fruitbox->discount_multiple === 'Yes')

                        // So, fun story, we don't offer the multiple fruitbox discount for all boxes.
                        // Without rhyme or reason to when this happens, I've added a yes/no dropdown on the box information.
                        $total_boxes_without_discount += $fruitbox->fruitbox_total;
                    }
                } else {

                    // These fruitboxes are all handled by a fruit partner, they're categorised a little differently but processed the same.

                    if ( strtolower( $fruitbox->type ) === 'seasonal' ) {
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * 2 );
                    } elseif ( strtolower( $fruitbox->type ) === 'berry' ) {
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * $fruitbox->seasonal_berries );
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * $fruitbox->grapes );
                    }

                    // For each fruitbox we need to check for any additional charges under the tailoring_fee.
                    // I could specifically make this available for tailored boxes but its use is situational,
                    // and opening this fee up for all boxes increases its situational potential.
                    if (isset($fruitbox->tailoring_fee)) {
                        $tailoring_fee_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * $fruitbox->tailoring_fee );
                    }

                    if ($fruitbox->discount_multiple === 'Yes') {
                        switch ($fruitbox->delivery_day) {
                            case 'Monday':
                                // I want the fruitbox types to be as dynamic as possible, so we can create new ones without worrying about hardcoded checks.
                                // This isset checks to see if the object contains the box type as a property, adding to the totals if it does.
                                if ( isset( $monday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} )) {
                                    $monday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    // Otherwise this is the first box of its type to be processed for this company, on this day.
                                    // Instead of adding to the total, we can set the inital value.
                                    $monday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Tuesday':
                                if( isset( $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} )) {
                                    $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Wednesday':
                                if( isset( $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} )) {
                                    $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Thursday':
                                if( isset( $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} )) {
                                    $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                            case 'Friday':
                                if( isset( $friday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} )) {
                                    $friday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} += $fruitbox->fruitbox_total;
                                } else {
                                    $friday_total_boxes_fruit_partner->{strtolower( $fruitbox->type )} = $fruitbox->fruitbox_total;
                                }
                                break;
                        }
                    }  else { // end of if ($fruitbox->discount_multiple === 'Yes')

                        // So, fun story, we don't offer the multiple fruitbox discount for all boxes.
                        // Without rhyme or reason to when this happens, I've added a yes/no dropdown on the box information.
                        $total_boxes_without_discount_fruit_partner += $fruitbox->fruitbox_total;
                    }

                } // End of else ( fruit_partner_id != 1 )

            } // end of foreach ($company->fruitbox as $fruitbox)

            foreach ($company->fruitbox_archive as $fruitbox_archive) {
                // Now we repeat the process adding any potentially archived orders for the current week start to the totals.

                if ($fruitbox->fruit_partner_id === 1) {

                    if ( strtolower( $fruitbox->type ) === 'seasonal' ) {
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * 2 );
                    } elseif ( strtolower( $fruitbox->type ) === 'berry' ) {
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * $fruitbox->seasonal_berries );
                        $punnets_weekly_total += ( $fruitbox->fruitbox_total * $fruitbox->grapes );
                    }

                    if (isset($fruitbox->tailoring_fee)) {
                        $tailoring_fee_weekly_total += $fruitbox->tailoring_fee;
                    }

                    if ($fruitbox->discount_multiple === 'Yes') {
                        switch ($fruitbox_archive->delivery_day) {
                            case 'Monday':
                                if ( isset( $monday_total_boxes->{strtolower( $fruitbox_archive->type )} )) {
                                    $monday_total_boxes->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    // Otherwise this is the first box of its type to be processed for this company, on this day.
                                    // Instead of adding to the total, we can set the inital value.
                                    $monday_total_boxes->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Tuesday':
                                if( isset( $tuesday_total_boxes->{strtolower( $fruitbox_archive->type )} )) {
                                    $tuesday_total_boxes->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $tuesday_total_boxes->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Wednesday':
                                if( isset( $wednesday_total_boxes->{strtolower( $fruitbox_archive->type )} )) {
                                    $wednesday_total_boxes->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $wednesday_total_boxes->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Thursday':
                                if( isset( $thursday_total_boxes->{strtolower( $fruitbox_archive->type )} )) {
                                    $thursday_total_boxes->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $thursday_total_boxes->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Friday':
                                if( isset( $friday_total_boxes->{strtolower( $fruitbox_archive->type )} )) {
                                    $friday_total_boxes->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $friday_total_boxes->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                        }
                    } else { // end of if ($fruitbox->discount_multiple === 'Yes')

                        // So, fun story, we don't offer the multiple fruitbox discount for all boxes.
                        // Without rhyme or reason to when this happens, I've added a yes/no dropdown on the box information.
                        $total_boxes_without_discount += $fruitbox_archive->fruitbox_total;
                    }

                    // Same as above, archived boxes are just as likely to hold tailoring fees.
                    if (isset($fruitbox_archive->tailoring_fee)) {
                        $tailoring_fee_weekly_total += ( $fruitbox_archive->fruitbox_total * $fruitbox_archive->tailoring_fee );
                    }

                } else { // These orders are handles by a fruit partner

                    if ( strtolower( $fruitbox->type ) === 'seasonal' ) {
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * 2 );
                    } elseif ( strtolower( $fruitbox->type ) === 'berry' ) {
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * $fruitbox->seasonal_berries );
                        $punnets_weekly_total_fruit_partner += ( $fruitbox->fruitbox_total * $fruitbox->grapes );
                    }

                    if (isset($fruitbox->tailoring_fee)) {
                        $tailoring_fee_weekly_total_fruit_partner += $fruitbox->tailoring_fee;
                    }

                    if ($fruitbox->discount_multiple === 'Yes') {
                        switch ($fruitbox_archive->delivery_day) {
                            case 'Monday':
                                if ( isset( $monday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} )) {
                                    $monday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    // Otherwise this is the first box of its type to be processed for this company, on this day.
                                    // Instead of adding to the total, we can set the inital value.
                                    $monday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Tuesday':
                                if( isset( $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} )) {
                                    $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $tuesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Wednesday':
                                if( isset( $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} )) {
                                    $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $wednesday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Thursday':
                                if( isset( $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} )) {
                                    $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $thursday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                            case 'Friday':
                                if( isset( $friday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} )) {
                                    $friday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} += $fruitbox_archive->fruitbox_total;
                                } else {
                                    $friday_total_boxes_fruit_partner->{strtolower( $fruitbox_archive->type )} = $fruitbox_archive->fruitbox_total;
                                }
                                break;
                        }
                    } else { // end of if ($fruitbox->discount_multiple === 'Yes')

                        // So, fun story, we don't offer the multiple fruitbox discount for all boxes.
                        // Without rhyme or reason to when this happens, I've added a yes/no dropdown on the box information.
                        $total_boxes_without_discount_fruit_partner += $fruitbox_archive->fruitbox_total;
                    }

                    // Same as above, archived boxes are just as likely to hold tailoring fees.
                    if (isset($fruitbox_archive->tailoring_fee)) {
                        $tailoring_fee_weekly_total_fruit_partner += ( $fruitbox_archive->fruitbox_total * $fruitbox_archive->tailoring_fee );
                    }

                } // End of else ( fruit_partner_id != 1 )

            } // end of foreach ($company->fruitbox_archive as $fruitbox_archive)

            //----- These are the various Fruitbox totals, time to process any applicable discounts -----//

                $box_total_monday = 0;
                $box_total_tuesday = 0;
                $box_total_wednesday = 0;
                $box_total_thursday = 0;
                $box_total_friday = 0;

                // I don't actually need to declare the properties in the object and doing so actually adds extra unnecessary properties.
                // For now though it is a good reference of what I need.
                $sales_invoice = (object) [
                //     'contact_name',
                //     'email_address',
                //     'po_address_line_1',
                //     'po_address_line_2',
                //     'po_city',
                //     'po_region',
                //     'po_post_code',
                //     'invoice_number',
                //     'invoice_date',
                //     'due_date',
                //     'description',
                //     'quantity',
                //     'account_code',
                //     'unit_amount',
                //     'tax_amount',
                //     'tax_type',
                //     'branding_theme',
                ];

            // Fruitboxes grouped by day, minus the ones not applicable for discount.

                foreach($monday_total_boxes as $box_type) {
                    $box_total_monday += $box_type;
                };
                foreach($tuesday_total_boxes as $box_type) {
                    $box_total_tuesday += $box_type;
                };
                foreach($wednesday_total_boxes as $box_type) {
                    $box_total_wednesday += $box_type;
                };
                foreach($thursday_total_boxes as $box_type) {
                    $box_total_thursday += $box_type;
                };
                foreach($friday_total_boxes as $box_type) {
                    $box_total_friday += $box_type;
                };

                // Gather the (eligible for discount) fruitboxes ordered this week, tallied by day
                $box_totals = [$box_total_monday, $box_total_tuesday, $box_total_wednesday, $box_total_thursday, $box_total_friday];
                // Now count the values and see how many discounts are applicable
                $weekly_box_totals = array_count_values($box_totals);

                // Fruitboxes without discount
                if ($total_boxes_without_discount > 0) {
                    // Fruitboxes without discount are always treated like a single delivery
                    if (array_key_exists(1, $weekly_box_totals)) {
                        // If we already have some solo deliveries, we can add to the total
                        $weekly_box_totals[1] += $total_boxes_without_discount;
                    } else {
                        // Otherwise we need to add solo deliveries to their order.
                        $weekly_box_totals[1] = $total_boxes_without_discount;
                    }
                }

                // Now we have the orders grouped by discount we can create the invoice entry -
                // Depending on the amount of boxes delivered ($key), we want to apply the correct discount.

                // To save repetition, I could maybe add the company details later (like I plan to do with invoice number/due date)
                // but I still need this foreach/switch to handle fruitbox description/quantity etc.
                foreach ($weekly_box_totals as $key => $box_total) {

                    switch ($key) {
                        case 0:
                            // If we're here they're not getting a fruit delivery on this/these days, so we can just skip them.
                            break;
                        case 1:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x1->name;
                            $sales_invoice->quantity = ( $box_total * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x1->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x1->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 2:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x2->name;
                            $sales_invoice->quantity = ( $box_total * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x2->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x2->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 3:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x3->name;
                            $sales_invoice->quantity = ( $box_total * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x3->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x3->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 4:
                        case 5:
                        case 6:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x4->name;
                            $sales_invoice->quantity = ( $box_total * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x4->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x4->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        default:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x7_plus->name;
                            $sales_invoice->quantity = ( $box_total * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x7_plus->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x7_plus->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                    }
                }

            // Set the fruitbox totals for Fruit Partners

                $box_total_monday_fruit_partner = 0;
                $box_total_tuesday_fruit_partner = 0;
                $box_total_wednesday_fruit_partner = 0;
                $box_total_thursday_fruit_partner = 0;
                $box_total_friday_fruit_partner = 0;

            // Fruitboxes grouped by day, minus the ones not applicable for discount (Fruit Partners)

                foreach($monday_total_boxes_fruit_partner as $box_type) {
                    $box_total_monday_fruit_partner += $box_type;
                };
                foreach($tuesday_total_boxes_fruit_partner as $box_type) {
                    $box_total_tuesday_fruit_partner += $box_type;
                };
                foreach($wednesday_total_boxes_fruit_partner as $box_type) {
                    $box_total_wednesday_fruit_partner += $box_type;
                };
                foreach($thursday_total_boxes_fruit_partner as $box_type) {
                    $box_total_thursday_fruit_partner += $box_type;
                };
                foreach($friday_total_boxes_fruit_partner as $box_type) {
                    $box_total_friday_fruit_partner += $box_type;
                };

                $box_totals_fruit_partner = [   $box_total_monday_fruit_partner,
                                                $box_total_tuesday_fruit_partner,
                                                $box_total_wednesday_fruit_partner,
                                                $box_total_thursday_fruit_partner,
                                                $box_total_friday_fruit_partner
                            ];

                $weekly_box_totals_fruit_partner = array_count_values($box_totals_fruit_partner);

                // Fruitboxes without discount
                if ($total_boxes_without_discount_fruit_partner > 0) {

                    if (array_key_exists(1, $weekly_box_totals_fruit_partner)) {
                        $weekly_box_totals_fruit_partner[1] += $total_boxes_without_discount_fruit_partner;
                    } else {
                        $weekly_box_totals_fruit_partner[1] = $total_boxes_without_discount_fruit_partner;
                    }
                }

                // Just like before, let's make some invoices, this time for fruit partner orders.
                foreach ($weekly_box_totals_fruit_partner as $key => $box_total_fruit_partner) {

                    switch ($key) {
                        case 0:
                            // If we're here they're not getting a fruit delivery on this/these days, so we can just skip them.
                            break;
                        case 1:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x1_fruit_partner->name;
                            $sales_invoice->quantity = ( $box_total_fruit_partner * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x1_fruit_partner->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x1_fruit_partner->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 2:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x2_fruit_partner->name;
                            $sales_invoice->quantity = ( $box_total_fruit_partner * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x2_fruit_partner->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x2_fruit_partner->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 3:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x3_fruit_partner->name;
                            $sales_invoice->quantity = ( $box_total_fruit_partner * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x3_fruit_partner->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x3_fruit_partner->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        case 4:
                        case 5:
                        case 6:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x4_fruit_partner->name;
                            $sales_invoice->quantity = ( $box_total_fruit_partner * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x4_fruit_partner->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x4_fruit_partner->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                            break;
                        default:
                            $sales_invoice = new $sales_invoice();
                            $sales_invoice->invoice_name = $company->invoice_name;
                            $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                            // If the invoice details are empty, use the route info instead
                            $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                            $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                            $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                            $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                            $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                            // This is manually created following this pattern - yy-mm-dd-000.
                            // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                                // $sales_invoice->invoice_number =
                            // Week Start
                                // $sales_invoice->invoice_date =
                            // Today's date, unless the delay counter is populated (which I haven't made yet).
                                // $sales_invoice->due_date =
                            // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                            $sales_invoice->description = $fruitbox_x7_plus_fruit_partner->name;
                            $sales_invoice->quantity = ( $box_total_fruit_partner * $key ); // This is the number of boxes bought together * the number of occasions bought in the week.
                            $sales_invoice->account_code = $fruitbox_x7_plus_fruit_partner->sales_nominal;
                            $sales_invoice->unit_amount = $fruitbox_x7_plus_fruit_partner->price;
                            $sales_invoice->tax_amount = 0;
                            $sales_invoice->tax_type = 'Zero Rated Income';
                            $sales_invoice->branding_theme = $company->branding_theme;
                            $sales_invoices[] = $sales_invoice;
                    }
                }

                if ($tailoring_fee_weekly_total > 0) {
                    // Then we have some tailoring charges to add as an invoice entry.
                    $sales_invoice = new $sales_invoice();
                    $sales_invoice->invoice_name = $company->invoice_name;
                    $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                    // If the invoice details are empty, use the route info instead
                    $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                    $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                    $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                    $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                    $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                    // This is manually created following this pattern - yy-mm-dd-000.
                    // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                        // $sales_invoice->invoice_number =
                    // Week Start
                        // $sales_invoice->invoice_date =
                    // Today's date, unless the delay counter is populated (which I haven't made yet).
                        // $sales_invoice->due_date =
                    // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                    $sales_invoice->description = 'Tailored Fruit Box';
                    $sales_invoice->quantity = 1; // This is the number of boxes bought together * the number of occasions bought in the week.
                    $sales_invoice->account_code = '4040';
                    $sales_invoice->unit_amount = $tailoring_fee_weekly_total;
                    $sales_invoice->tax_amount = 0;
                    $sales_invoice->tax_type = 'Zero Rated Income';
                    $sales_invoice->branding_theme = $company->branding_theme;
                    $sales_invoices[] = $sales_invoice;
                }

                if ($tailoring_fee_weekly_total_fruit_partner > 0) {
                    // Then we have some tailoring charges to add as an invoice entry.
                    $sales_invoice = new $sales_invoice();
                    $sales_invoice->invoice_name = $company->invoice_name;
                    $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                    // If the invoice details are empty, use the route info instead
                    $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                    $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                    $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                    $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                    $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                    // This is manually created following this pattern - yy-mm-dd-000.
                    // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                        // $sales_invoice->invoice_number =
                    // Week Start
                        // $sales_invoice->invoice_date =
                    // Today's date, unless the delay counter is populated (which I haven't made yet).
                        // $sales_invoice->due_date =
                    // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                    $sales_invoice->description = 'Tailored Fruit Box P';
                    $sales_invoice->quantity = 1; // This is the number of boxes bought together * the number of occasions bought in the week.
                    $sales_invoice->account_code = '4050';
                    $sales_invoice->unit_amount = $tailoring_fee_weekly_total_fruit_partner;
                    $sales_invoice->tax_amount = 0;
                    $sales_invoice->tax_type = 'Zero Rated Income';
                    $sales_invoice->branding_theme = $company->branding_theme;
                    $sales_invoices[] = $sales_invoice;
                }


                if ($punnets_weekly_total > 0) {
                    // Then we have some punnets to charge for as a new invoice entry.
                    $sales_invoice = new $sales_invoice();
                    $sales_invoice->invoice_name = $company->invoice_name;
                    $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                    // If the invoice details are empty, use the route info instead
                    $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                    $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                    $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                    $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                    $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                    // This is manually created following this pattern - yy-mm-dd-000.
                    // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                        // $sales_invoice->invoice_number =
                    // Week Start
                        // $sales_invoice->invoice_date =
                    // Today's date, unless the delay counter is populated (which I haven't made yet).
                        // $sales_invoice->due_date =
                    // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                    $sales_invoice->description = 'Seasonal Fruit (Punnet)';
                    $sales_invoice->quantity = $punnets_weekly_total; // This is the number of boxes bought together * the number of occasions bought in the week.
                    $sales_invoice->account_code = '4040';
                    $sales_invoice->unit_amount = 2.5;
                    $sales_invoice->tax_amount = 0;
                    $sales_invoice->tax_type = 'Zero Rated Income';
                    $sales_invoice->branding_theme = $company->branding_theme;
                    $sales_invoices[] = $sales_invoice;
                }

                if ($punnets_weekly_total_fruit_partner > 0) {
                    // Then we have some punnets to charge for as a new invoice entry.
                    $sales_invoice = new $sales_invoice();
                    $sales_invoice->invoice_name = $company->invoice_name;
                    $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                    // If the invoice details are empty, use the route info instead
                    $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                    $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                    $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                    $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                    $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                    // This is manually created following this pattern - yy-mm-dd-000.
                    // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                        // $sales_invoice->invoice_number =
                    // Week Start
                        // $sales_invoice->invoice_date =
                    // Today's date, unless the delay counter is populated (which I haven't made yet).
                        // $sales_invoice->due_date =
                    // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                    $sales_invoice->description = 'Seasonal Fruit (Punnet) P';
                    $sales_invoice->quantity = $punnets_weekly_total_fruit_partner; // This is the number of boxes bought together * the number of occasions bought in the week.
                    $sales_invoice->account_code = '4050';
                    $sales_invoice->unit_amount = 2.5;
                    $sales_invoice->tax_amount = 0;
                    $sales_invoice->tax_type = 'Zero Rated Income';
                    $sales_invoice->branding_theme = $company->branding_theme;
                    $sales_invoices[] = $sales_invoice;
                }

            //----- End of various Fruitbox totals -----//

                // Now if I've thought of everything, this should mean all fruitboxes, ( current, archived and fruit partners ) have been processed and invoiced.
                // Any discounts, tailoring charges and additional punnets of grapes and berries should also have been added.

                // dd($sales_invoices);

                                                                //---------- End of Fruitbox ----------//

                                                                //---------- Milkbox ----------//

            foreach ($company->milkbox as $milkbox) {

                if ($milkbox->fruit_partner_id === 1) {

                    // There will only be one milkbox per day and no discounts are added so we can count up the milk and process the order at face value.

                    //----- 1l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_1l_order = $milkbox->semi_skimmed_1l +
                                            $milkbox->skimmed_1l +
                                            $milkbox->whole_1l;

                    //----- End of 1l Milk (Semi/Skimmed/Whole) -----//

                    //----- 2l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_2l_order = $milkbox->semi_skimmed_2l +
                                            $milkbox->skimmed_2l +
                                            $milkbox->whole_2l;

                    //----- End of 2l Milk (Semi/Skimmed/Whole) -----//

                    //----- 1l Milk Alternatives -----//
                        $milkbox_alt_order =    $milkbox->milk_1l_alt_coconut +
                                                $milkbox->milk_1l_alt_unsweetened_almond +
                                                $milkbox->milk_1l_alt_almond +
                                                $milkbox->milk_1l_alt_unsweetened_soya +
                                                $milkbox->milk_1l_alt_soya +
                                                $milkbox->milk_1l_alt_oat +
                                                $milkbox->milk_1l_alt_rice +
                                                $milkbox->milk_1l_alt_cashew +
                                                $milkbox->milk_1l_alt_lactose_free_semi;

                    //----- End of 1l Milk Alternatives -----//

                    //----- Organic Milks -----//
                        //----- 1l -----//
                        $milk_organic_1l_order =    $milkbox->organic_semi_skimmed_1l +
                                                    $milkbox->organic_skimmed_1l +
                                                    $milkbox->organic_whole_1l;

                        //----- End of 1l -----//
                        //----- 2l -----//
                        $milk_organic_2l_order =    $milkbox->organic_semi_skimmed_2l +
                                                    $milkbox->organic_skimmed_2l +
                                                    $milkbox->organic_whole_2l;

                        //----- End of 2l -----//
                    //----- End of Organic Milks -----//

                    //----- Milkbox Totals -----//


                    //----- End of Milkbox Totals -----//


                } else { // end of if ($milkbox->fruit_partner === 1)

                    //----- 1l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_1l_order_fruit_partner =   $milkbox->semi_skimmed_1l +
                                                            $milkbox->skimmed_1l +
                                                            $milkbox->whole_1l;

                    //----- End of 1l Milk (Semi/Skimmed/Whole) -----//

                    //----- 2l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_2l_order_fruit_partner =   $milkbox->semi_skimmed_2l +
                                                            $milkbox->skimmed_2l +
                                                            $milkbox->whole_2l;

                    //----- End of 2l Milk (Semi/Skimmed/Whole) -----//

                    //----- 1l Milk Alternatives -----//
                        $milkbox_alt_order_fruit_partner =      $milkbox->milk_1l_alt_coconut +
                                                                $milkbox->milk_1l_alt_unsweetened_almond +
                                                                $milkbox->milk_1l_alt_almond +
                                                                $milkbox->milk_1l_alt_unsweetened_soya +
                                                                $milkbox->milk_1l_alt_soya +
                                                                $milkbox->milk_1l_alt_oat +
                                                                $milkbox->milk_1l_alt_rice +
                                                                $milkbox->milk_1l_alt_cashew +
                                                                $milkbox->milk_1l_alt_lactose_free_semi;

                    //----- End of 1l Milk Alternatives -----//

                    //----- Organic Milks -----//
                        //----- 1l -----//
                        $milk_organic_1l_order_fruit_partner =  $milkbox->organic_semi_skimmed_1l +
                                                                $milkbox->organic_skimmed_1l +
                                                                $milkbox->organic_whole_1l;

                        //----- End of 1l -----//
                        //----- 2l -----//
                        $milk_organic_2l_order_fruit_partner =  $milkbox->organic_semi_skimmed_2l +
                                                                $milkbox->organic_skimmed_2l +
                                                                $milkbox->organic_whole_2l;

                        //----- End of 2l -----//
                    //----- End of Organic Milks -----//

                    //----- Milkbox Totals -----//

                        $milkbox_1l_order_fruit_partner;
                        $milkbox_2l_order_fruit_partner;
                        $milkbox_alt_order_fruit_partner;
                        $milk_organic_1l_order_fruit_partner;
                        $milk_organic_2l_order_fruit_partner;

                    //----- End of Milkbox Totals -----//

                }
            } // end of foreach ($company->milkbox as $milkbox)

            foreach ($company->milkbox_archive as $milkbox_archive) {
                if ($milkbox_archive->fruit_partner_id === 1) {

                    //----- 1l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_archive_1l_order = $milkbox_archive->semi_skimmed_1l +
                                                    $milkbox_archive->skimmed_1l +
                                                    $milkbox_archive->whole_1l;

                    //----- End of 1l Milk (Semi/Skimmed/Whole) -----//

                    //----- 2l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_archive_2l_order = $milkbox_archive->semi_skimmed_2l +
                                                    $milkbox_archive->skimmed_2l +
                                                    $milkbox_archive->whole_2l;

                    //----- End of 2l Milk (Semi/Skimmed/Whole) -----//

                    //----- 1l Milk Alternatives -----//
                        // Now check all the alternative milk values for the milkbox and add them together.
                        $milkbox_archive_alt_order =    $milkbox_archive->milk_1l_alt_coconut +
                                                        $milkbox_archive->milk_1l_alt_unsweetened_almond +
                                                        $milkbox_archive->milk_1l_alt_almond +
                                                        $milkbox_archive->milk_1l_alt_unsweetened_soya +
                                                        $milkbox_archive->milk_1l_alt_soya +
                                                        $milkbox_archive->milk_1l_alt_oat +
                                                        $milkbox_archive->milk_1l_alt_rice +
                                                        $milkbox_archive->milk_1l_alt_cashew +
                                                        $milkbox_archive->milk_1l_alt_lactose_free_semi;

                    //----- End of 1l Milk Alternatives -----//

                    //----- Organic Milks -----//
                        //----- 1l -----//
                        $milk_archive_organic_1l_order =        $milkbox_archive->organic_semi_skimmed_1l +
                                                                $milkbox_archive->organic_skimmed_1l +
                                                                $milkbox_archive->organic_whole_1l;

                        //----- End of 1l -----//
                        //----- 2l -----//
                        $milk_archive_organic_2l_order =        $milkbox_archive->organic_semi_skimmed_2l +
                                                                $milkbox_archive->organic_skimmed_2l +
                                                                $milkbox_archive->organic_whole_2l;

                        //----- End of 2l -----//
                    //----- End of Organic Milks -----//

                    //----- Milkbox Totals -----//


                    //----- End of Milkbox Totals -----//


                } else { // end of if ($milkbox->fruit_partner === 1)

                    //----- 1l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_archive_1l_order_fruit_partner =   $milkbox_archive->semi_skimmed_1l +
                                                                    $milkbox_archive->skimmed_1l +
                                                                    $milkbox_archive->whole_1l;

                    //----- End of 1l Milk (Semi/Skimmed/Whole) -----//

                    //----- 2l Milk (Semi/Skimmed/Whole) -----//
                        $milkbox_archive_2l_order_fruit_partner =   $milkbox_archive->semi_skimmed_2l +
                                                                    $milkbox_archive->skimmed_2l +
                                                                    $milkbox_archive->whole_2l;

                    //----- End of 2l Milk (Semi/Skimmed/Whole) -----//

                    //----- 1l Milk Alternatives -----//
                        // Now check all the alternative milk values for the milkbox and add them together.
                        $milkbox_archive_alt_order_fruit_partner =      $milkbox_archive->milk_1l_alt_coconut +
                                                                        $milkbox_archive->milk_1l_alt_unsweetened_almond +
                                                                        $milkbox_archive->milk_1l_alt_almond +
                                                                        $milkbox_archive->milk_1l_alt_unsweetened_soya +
                                                                        $milkbox_archive->milk_1l_alt_soya +
                                                                        $milkbox_archive->milk_1l_alt_oat +
                                                                        $milkbox_archive->milk_1l_alt_rice +
                                                                        $milkbox_archive->milk_1l_alt_cashew +
                                                                        $milkbox_archive->milk_1l_alt_lactose_free_semi;

                    //----- End of 1l Milk Alternatives -----//

                    //----- Organic Milks -----//
                        //----- 1l -----//
                        $milk_archive_organic_1l_order_fruit_partner =      $milkbox_archive->organic_semi_skimmed_1l +
                                                                            $milkbox_archive->organic_skimmed_1l +
                                                                            $milkbox_archive->organic_whole_1l;

                        //----- End of 1l -----//
                        //----- 2l -----//
                        $milk_archive_organic_2l_order_fruit_partner =   $milkbox_archive->organic_semi_skimmed_2l +
                                                                            $milkbox_archive->organic_skimmed_2l +
                                                                            $milkbox_archive->organic_whole_2l;

                        //----- End of 2l -----//
                    //----- End of Organic Milks -----//

                    //----- Milkbox Totals -----//


                    //----- End of Milkbox Totals -----//

                } // End of if/else ($milkbox_archive->fruit_partner_id === 1)
            } // End of foreach ($company->milkbox_archive as $milkbox_archive)

            // Now we need to combine the totals of current and archived (but un-invoiced) milkboxes for the week.

            //----- Milkbox OP -----//
            $milkbox_1l_total_week->quantity = $milkbox_1l_order + $milkbox_archive_1l_order;
            // dd($milkbox_1l_total_week);
            $milkbox_2l_total_week->quantity = $milkbox_2l_order + $milkbox_archive_2l_order;
            // dd($milkbox_2l_total_week);
            $milkbox_alt_total_week->quantity = $milkbox_alt_order + $milkbox_archive_alt_order;
            // dd($milkbox_alt_total_week);
            $milk_organic_1l_total_week->quantity = $milk_organic_1l_order + $milk_archive_organic_1l_order;
            $milk_organic_2l_total_week->quantity = $milk_organic_2l_order + $milk_archive_organic_2l_order;
            // dd($milkbox_archive_1l_order_fruit_partner);
            //----- Milkbox Fruit Partners -----//
            $milkbox_1l_total_week_fruit_partner->quantity = $milkbox_1l_order_fruit_partner + $milkbox_archive_1l_order_fruit_partner;
            $milkbox_2l_total_week_fruit_partner->quantity = $milkbox_2l_order_fruit_partner + $milkbox_archive_2l_order_fruit_partner;
            $milkbox_alt_total_week_fruit_partner->quantity = $milkbox_alt_order_fruit_partner + $milkbox_archive_alt_order_fruit_partner;
            $milk_organic_1l_total_week_fruit_partner->quantity = $milk_organic_1l_order_fruit_partner + $milk_archive_organic_1l_order_fruit_partner;
            $milk_organic_2l_total_week_fruit_partner->quantity = $milk_organic_2l_order_fruit_partner + $milk_archive_organic_2l_order_fruit_partner;

            // OK, time to process these totals into invoices.

            $all_milkboxes = [  $milkbox_1l_total_week,
                                $milkbox_2l_total_week,
                                $milkbox_alt_total_week,
                                $milk_organic_1l_total_week,
                                $milk_organic_2l_total_week,
                                $milkbox_1l_total_week_fruit_partner,
                                $milkbox_2l_total_week_fruit_partner,
                                $milkbox_alt_total_week_fruit_partner,
                                $milk_organic_1l_total_week_fruit_partner,
                                $milk_organic_2l_total_week_fruit_partner
                             ];

             foreach ($all_milkboxes as $milkbox_total) {
                 if ($milkbox_total->quantity > 0) {

                    // dd($milkbox_total);

                     $sales_invoice = new $sales_invoice();
                     $sales_invoice->invoice_name = $company->invoice_name;
                     $sales_invoice->email_address = $company->invoice_email; // Still need to add to company table
                     // If the invoice details are empty, use the route info instead
                     $sales_invoice->po_address_line_1 = isset($company->invoice_address_line_1) ? $company->invoice_address_line_1 : $company->route_address_line_1;
                     $sales_invoice->po_address_line_2 = isset($company->invoice_address_line_2) ? $company->invoice_address_line_2 : $company->route_address_line_2;
                     $sales_invoice->po_city = isset($company->invoice_city) ? $company->invoice_city : $company->route_city;
                     $sales_invoice->po_region = isset($company->invoice_region) ? $company->invoice_region : $company->route_region;
                     $sales_invoice->po_post_code = isset($company->invoice_postcode) ? $company->invoice_postcode : $company->route_postcode;
                     // This is manually created following this pattern - yy-mm-dd-000.
                     // EDIT: I might want to add these to the invoices when I have them all together (fruit, milk, snacks, drinks, other)
                         // $sales_invoice->invoice_number =
                     // Week Start
                         // $sales_invoice->invoice_date =
                     // Today's date, unless the delay counter is populated (which I haven't made yet).
                         // $sales_invoice->due_date =
                     // Currently the only field I need the switch case for, is this the best way?  Maybe give it (improvements) a little think once constructed.
                     $sales_invoice->description = $milkbox_total->product->name;
                     $sales_invoice->quantity = $milkbox_total->quantity;
                     $sales_invoice->account_code = $milkbox_total->product->sales_nominal;
                     $sales_invoice->unit_amount = $milkbox_total->product->price;
                     $sales_invoice->tax_amount = 0;
                     $sales_invoice->tax_type = 'Zero Rated Income';
                     $sales_invoice->branding_theme = $company->branding_theme;
                     $sales_invoices[] = $sales_invoice;
                 }
             }
             //dd($sales_invoices);
                                                                //---------- End of Milkbox -----------//

                                                                //---------- Snackbox ----------//

            //---------- Snackbox Functions ----------//

                function apply_deduction($item, $per_product_deduction) {
                    return $item = ( $item - $per_product_deduction );

                }
                //dd(apply_deduction(12, 4));

            //---------- End of Snackbox Functions ----------//



            // At its most basic we just want to group the snackbox entries by snackbox_id
            $snackboxes = $company->snackboxes->groupBy('snackbox_id');
            // dd($snackboxes);

            foreach ($snackboxes as $snackbox) {
                // I only need to get this value once, so moving it up one foreach statement.
                $snack_cap = $snackbox[0]->snack_cap;
                $snackbox_items_total_inc_vat = [];
                $snackbox_items_minus_vat = [];
                $snackbox_items_vat = [];
                $snackbox_items_zero_rated = [];
                $all_snackbox_items = [];

                // and add up all vat registered and all zero rated items into two totals,
                foreach ($snackbox as $snackbox_item) {
                    if ($snackbox_item->product_id != 0) {
                        // check snackbox item (product) code for whether it's zero rated, or charged vat
                        $product = Product::findOrFail($snackbox_item->product_id);

                        if ($product->vat === 'Yes') {
                            // if it is, then multiply the cost by the quantity, to get a total vat included value.
                            // Not sure why I'm using unit_cost here, commenting out and replacing with price but worth a relook in case I'm being dumb.
                            // $snackbox_item_total_inc_vat = $snackbox_item->unit_cost * $snackbox_item->quantity;
                            $snackbox_items_total_inc_vat[] = $snackbox_item->unit_price * $snackbox_item->quantity;

                        } else {
                            // These are zero rated items, so we don't need to worry about vat.
                            $snackbox_items_zero_rated[] = $snackbox_item->unit_price * $snackbox_item->quantity;
                        }

                    } // end of if ($snackbox_item->product_id != null)
                } // end of foreach ($snackbox as $snackbox_item)

                // Now we've made separate totals of vat registered and zero rated items, we need them back together for some of this.
                $all_snackbox_items = array_merge( $snackbox_items_zero_rated, $snackbox_items_total_inc_vat );
                // Now I need to work out the natural total of products (before snack_cap) to work out how much to deduct from each product.
                // So let's sum up the values of the two arrays.
                $snackbox_total_before_snack_cap = array_sum($all_snackbox_items);
                // And add up how many products are contained in each.
                $number_of_items_in_snackbox = count($all_snackbox_items);
                // Now we deduct the snack_cap limit to find out how much the box is over that value, dividing the total by the number of products, to get a per product deduction.
                $per_product_deduction = ( $snackbox_total_before_snack_cap - $snack_cap ) / $number_of_items_in_snackbox;

                // OK, now let's loop through the prices applying the discount
                foreach ($snackbox_items_total_inc_vat as $item) {

                    $snackbox_items_total_inc_vat_with_snack_cap_deduction_applied[] = apply_deduction($item, $per_product_deduction);
                }
                // and the same with zero rated items
                foreach ($snackbox_items_zero_rated as $item) {

                    $snackbox_items_zero_rated_with_snack_cap_deduction_applied[] = apply_deduction($item, $per_product_deduction);
                }

                // Now we have the snackbox items discounted to their snack_cap limit, we can work out the vat

                // So let's get the sum total
                $snacks_with_vat_and_discount_total = array_sum($snackbox_items_total_inc_vat_with_snack_cap_deduction_applied);
                // and work out the total minus vat aka 'unit_amount'
                $snacks_with_discount_minus_vat_total = ($snacks_with_vat_and_discount_total / 1.2);
                // before grabbing the vat amount aka 'tax_amount'
                $snacks_with_discount_vat_total = ($snacks_with_discount_minus_vat_total * 0.2);

                $snacks_zero_rated_total = array_sum($snackbox_items_zero_rated_with_snack_cap_deduction_applied);

                $snackbox_pt1 = new $snackbox_invoice_pt1();
                $snackbox_pt1->snack_total_minus_vat = $snacks_with_discount_minus_vat_total;
                $snackbox_pt1->snack_total_vat = $snacks_with_discount_vat_total;
                $snackbox_pt1->snack_total_zero_registered = $snacks_zero_rated_total;
                $snackbox_pt1->snackbox_total_cost = ($snacks_with_discount_minus_vat_total + $snacks_with_discount_vat_total + $snacks_zero_rated_total);
                $snackboxes_ready_for_invoicing[] = $snackbox_pt1;
            } // end of foreach ($company->snackboxes as $snackbox)

            // Ok, now I have an array of objects holding snackbox information.
            // Each object is a separate entry with a breakdown of vat registered products minus vat, the vat total and zero rated products total.
            // As well as a grand total I can access, to more easily determine whether the customer has spent enough for a further discount.

            // dd($snackboxes_ready_for_invoicing);

            //---------- WHAT TO DO ABOUT SNACK_CAP-LESS BOXES? WHAT TO DO ABOUT WHOLESALE? ----------//

                // Mixed snackboxes get a snack cap discount applied.  What would happen if the snack cap wasn't applied, would it default to 0 and run fine, or null and break?
                // Also what would happen if the snackbox is wholesale, does it need to be processed differently?

            //---------- WHAT TO DO ABOUT SNACK_CAP-LESS BOXES? END OF WHAT TO DO ABOUT WHOLESALE? ----------//
            //---------- END OF WHEN TO APPLY THE BULK DISCOUNTS ----------//

                // Right then, some customers will only be getting snackboxes, so I can't leave the £100 and £300 additional discounts to the drinkbox section,
                // (or otherbox for that matter), so I'm thinking I should go ahead and process the rest of their possible boxes before applying discounts and building invoices.

            //---------- END OF WHEN TO APPLY THE BULK DISCOUNTS ----------//

            //----- End of Snackbox -----//

            //----- Drinkbox -----//

            foreach ($company->drinkboxes as $drinkbox_item) {

                // It looks like drinks are processed differently to snacks.
                // I may be calling it a drinkbox and from a frontend perspective this is quite useful but for invoicing we treat each product as its own invoice line.
                // So long as I keep a running total for discounting purposes this should be fine.

                // And in fact, if I don't treat the drinkboxes as a box at all I can just run through the entries one by one, creating an invoice.
                // This if statement check could be != 0 but while null shouldn't find its way in (because default is 0), this will ensure it can't.

                if ($drinkbox_item->product_id > 0) {
                    // dd($drinkbox_item);
                    // Grab the product info using the drinkbox item product id, this holds more info (than drinkbox entry) such as vat registered or zero rated, etc.
                    $product = Product::findOrFail($drinkbox_item->product_id);
                    // Keep a running total of drinkbox items to work out potential discounts later on.
                    $drinks_total[] = ($product->unit_price * $drinkbox_item->quantity);
                    // Save each drinkbox entry as on object to process later, after we've applied all possible discounts to price.
                    $drinkbox_pt1 = new $drinkbox_invoice_pt1();
                    $drinkbox_pt1->unit_amount = $product->unit_price;
                    $drinkbox_pt1->vat = $product->vat;
                    $drinkbox_pt1->quantity = $drinkbox_item->quantity;
                    $drinkboxes_ready_for_invoicing[] = $drinkbox_pt1;
                }

            } // end of foreach ($company->drinkboxes as $drinkbox)

            // dd($drinkboxes_ready_for_invoicing);
            dd($drinks_total);
            $drinks_grand_total = array_sum($drinks_total);
            dd($drinks_grand_total);
            //----- End of Drinkbox -----//

            //----- Otherbox -----//

            foreach ($company->otherboxes as $otherbox_item) {
                if ($otherbox_item->product_id > 0) {

                    $product = Product::findOrFail($otherbox_item->product_id);
                    $other_total[] = ($product->unit_price * $otherbox_item->quantity);

                    $otherbox_pt1 = new $otherbox_invoice_pt1();
                    $otherbox_pt1->unit_amount = $product->unit_price;
                    $otherbox_pt1->vat = $product->vat;
                    $otherbox_pt1->quantity = $otherbox_item->quantity;
                    $otherboxes_ready_for_invoicing[] = $otherbox_pt1;
                }

                $other_grand_total = array_sum($other_total);
                dd($other_grand_total);
            } // end of foreach ($company->otherbox as $otherbox)

            //----- End of Otherbox -----//

            //----- Final Checks For Qualifying Discounts -----//

            //----- End of Final Checks For Qualifying Discounts -----//

            //----- Generate Outstanding Invoices For Snacks, Drinks & Other -----//

            //----- End of Generate Outstanding Invoices For Snacks, Drinks & Other -----//

        } // end of foreach ($companies as $company)

        //----- Time To Loop Through Invoices Inserting Remaining Fields -----//

            // Now we (should) have all the invoices we need to process this week
            // All that remains is to add 'invoice_number',

        //----- End of Time To Loop Through Invoices Inserting Remaining Fields -----//

    } // end of public function weekly_invoicing
} // end of class InvoicingController extends Controller
