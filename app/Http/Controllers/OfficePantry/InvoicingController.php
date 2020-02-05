<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

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
use Carbon\CarbonImmutable;

// Allow Log messages to Slack
use Illuminate\Support\Facades\Log;

class InvoicingController extends Controller
{
            //---------- Set some variables ----------// - 125
            //---------- Fruitbox ----------// - 224
            //---------- Milkbox ----------// - 1039
            //---------- Snackbox ----------// - 1326
            //---------- Drinkbox ----------//
            //---------- Otherbox ----------//

    public function __construct()
    {
        $week_start = WeekStart::findOrFail(1);
        $this->week_start = $week_start->current;

    }

    public function weekly_invoicing_export()
    {
        return \Excel::download(new Exports\WeeklyInvoicesExportV2(), 'weekly_invoicing-' . $this->week_start . '.csv');
    }


    // There's a tempation to have this function do more than just apply an invoice date but should it?
    // I'm thinking I could also make archived files 'Inactive' at this point so they can drop off subsequent searches, as we don't really need them for anything anymore.
    public function confirm_weekly_invoicing()
    {
        // This is the same query used in running the weekly invoices so we should be catching the exact same orders.
        // I will have to check to make sure this is as absolute as I think, and that any change made in one is also made in the other.
        // What could go wrong.


        $companies = CompanyDetails::where('is_active', 'Active')
                                    ->whereIn('branding_theme', ['BACS', 'GoCardless', 'Paypal (Stripe)', 'Weekly Standing Order', 'Eden Branding Theme']) // <-- Still need to get confirmation on these!
                                    ->with([
                                            'fruitbox' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
                                            },
                                            'fruitbox_archive' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
                                            },
                                            'milkbox' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
                                            },
                                            'milkbox_archive' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
                                            },
                                            'snackboxes' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            },
                                            'snackbox_archive' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            },
                                            'drinkboxes' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            },
                                            'drinkbox_archive' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            },
                                            'otherboxes' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            },
                                            'otherbox_archive' => function ($query) {
                                                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
                                            }
                                    ])->get();

            // dd($companies);

            //----- Grab current date for invoiced_at field -----//

            // Let's grab today's (relative to when invoice confirmation function is run) date without any formatting, to maximise its reuse.
            $date = CarbonImmutable::now('Europe/London');

            // Invoice date is just the day the invoice function is run.
            $invoice_date = $date->format('Y-m-d');

            //----- Loop through each company with qualifying orders -----//

            foreach ($companies as $company) {

                //----- Fruitboxes -----//
                dump($company->fruitbox);

                foreach ($company->fruitbox as $fruitbox) {
                    // As this is from the active fruitboxes (not archived), we might want to reuse this order for their next delivery - so let's just add an 'invoiced_at' date to the entry.
                    // $fruitbox->invoiced_at = $date; // <-- Why was I not using the invoice date for this?  I'm going to chnage them all to $invoice_Date but keeping this here as reference.
                    $fruitbox->invoiced_at = $invoice_date;
                    $fruitbox->save();

                    //dump($fruitbox);
                }

                //----- Archived Fruitboxes -----//

                foreach ($company->fruitbox_archive as $fruitbox_archive) {
                    // As this is from the archive, their current order has now changed.  In this case we can make the box inactive as well as adding the 'invoiced_at' date.
                    $fruitbox_archive->invoiced_at = $invoice_date;
                    $fruitbox_archive->is_active = 'Inactive';
                    $fruitbox_archive->save();

                    //dump($fruitbox_archive);
                }

                //----- Milkboxes -----//
                dump($company->milkbox);

                foreach ($company->milkbox as $milkbox) {
                    $milkbox->invoiced_at = $invoice_date;
                    $milkbox->save();

                    //dump($milkbox);
                }

                //----- Archived Milkboxes -----//
                //dd($company->milkbox_archive);

                foreach ($company->milkbox_archive as $milkbox_archive) {
                    $milkbox_archive->invoiced_at = $invoice_date;
                    $milkbox_archive->is_active = 'Inactive';
                    $milkbox_archive->save();

                    //dump($milkbox_archive);
                }

                //----- Snackboxes -----//

                foreach ($company->snackboxes->groupBy('snackbox_id') as $snackbox) {

                    // Do we want to add an invoice date to boxes that are effectively empty?
                    // These would be a $snackbox that has a single entry with a 'product_id' of 0.
                    // NOPE... well let's say no for now.
                    foreach ($snackbox as $snackbox_item) {
                        if ($snackbox_item->product_id === 0) {
                            // Then we can ignore it as there was nothing to invoice.
                            // 9/8/19 Edit: OR SHOULD WE DELETE IT TO PREVENT THESE ENTRIES BUILDING UP IN THE BACKGROUND?
                            // MIND YOU ARCHIVE AND EMPTY WILL STRIP THESE OUT WEEKLY ANYWAY? HMMN...
                        } else {
                            // Otherwise let's slap an 'invoiced_at' date on there and save it.
                            $snackbox_item->invoiced_at = $invoice_date;
                            $snackbox_item->save();

                        //    dump($snackbox_item);
                        }
                    }
                    // dump($snackbox);
                }

                //----- Archived Snackboxes -----//

                foreach ($company->snackbox_archive->groupBy('snackbox_id') as $snackbox_archive) {

                    // Do we want to add an invoice date to boxes that are effectively empty?
                    // These would be a $snackbox that has a single entry with a 'product_id' of 0.
                    // NOPE... well let's say no for now.
                    foreach ($snackbox_archive as $snackbox_archive_item) {
                        if ($snackbox_archive_item->product_id === 0) {
                            // Then we can ignore it as there was nothing to invoice.
                            // Nope if we do nothing with this one then the box will continue to appear in the archived boxes feed,
                            // which would serve no useful purpose that I can think of?
                            $snackbox_archive_item->is_active = 'Inactive';
                        } else {
                            // Otherwise let's slap an 'invoiced_at' date on there and save it.
                            $snackbox_archive_item->invoiced_at = $invoice_date;
                            $snackbox_archive_item->is_active = 'Inactive';
                            $snackbox_archive_item->save();

                        //    dump($snackbox_archive_item);
                        }
                    }
                    // dump($snackbox);
                }

                //----- Drinkboxes -----//

                foreach ($company->drinkboxes->groupBy('drinkbox_id') as $drinkbox) {
                    // dd($drinkbox);
                    foreach ($drinkbox as $drinkbox_item) {
                        // OK so for consistency I'm going add this for now but it's kinda pointless,
                        // but then adding an invoice date to items which haven't been invoiced is also WRONG!.
                        if ($drinkbox_item->product_id === 0) {
                            // Currently do nothing? Yeah, ok.
                        } else {
                            $drinkbox_item->invoiced_at = $invoice_date;
                            $drinkbox_item->save();
                        }
                    }
                }

                //----- Archived Drinkboxes -----//

                foreach ($company->drinkbox_archive->groupBy('drinkbox_id') as $drinkbox_archive) {
                    // dd($drinkbox_archive);
                    foreach ($drinkbox_archive as $drinkbox_archive_item) {
                        // OK so for consistency I'm going add this for now but it's kinda pointless,
                        // but then adding an invoice date to items which haven't been invoiced is also WRONG!.
                        if ($drinkbox_archive_item->product_id === 0) {
                            // Currently do nothing? Yeah alright let's deactivate them at least.
                            $drinkbox_archive_item->is_active = 'Inactive';
                        } else {
                            $drinkbox_archive_item->invoiced_at = $invoice_date;
                            $drinkbox_archive_item->is_active = 'Inactive';
                            $drinkbox_archive_item->save();
                        }
                    }
                }

                //----- Otherboxes -----//

                foreach ($company->otherboxes->groupBy('otherbox_id') as $otherbox) {
                    // dd($otherbox);
                    foreach ($otherbox as $otherbox_item) {
                        // OK so for consistency I'm going add this for now but it's kinda pointless,
                        // but then adding an invoice date to items which haven't been invoiced is also WRONG!.
                        if ($otherbox_item->product_id === 0) {
                            // Currently do nothing? Yeah, ok.
                        } else {
                            $otherbox_item->invoiced_at = $invoice_date;
                            $otherbox_item->save();
                        }
                    }
                }

                //----- Archived Otherboxes -----//

                foreach ($company->otherbox_archive->groupBy('otherbox_id') as $otherbox_archive) {
                    // dd($otherbox_archive);
                    foreach ($otherbox_archive as $otherbox_archive_item) {
                        // OK so for consistency I'm going add this for now but it's kinda pointless,
                        // but then adding an invoice date to items which haven't been invoiced is also WRONG!.
                        if ($otherbox_archive_item->product_id === 0) {
                            // Currently do nothing? Yeah alright let's deactivate them at least.
                            $otherbox_archive_item->is_active = 'Inactive';
                        } else {
                            $otherbox_archive_item->invoiced_at = $invoice_date;
                            $otherbox_archive_item->is_active = 'Inactive';
                            $otherbox_archive_item->save();
                        }
                    }
                }

            } // End of foreach ($companies as $company)
            return redirect('office');
    }

    // This weekly_invoicing function has been moved to Exports folder and now results in a csv file.
    // See function above. I.E (i think) weekly_invoicing_export() which is now the function above the function above. :)

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

            // Urghh, I still need to stop relying on hard coded values here!

            //----- OP Products -----//
                //----- Fruitbox Varibles -----//
                    $fruitbox_x1 = OfficePantryProducts::findOrFail(1);
                    $fruitbox_x2 = OfficePantryProducts::findOrFail(2);
                    $fruitbox_x3 = OfficePantryProducts::findOrFail(3);
                    $fruitbox_x4 = OfficePantryProducts::findOrFail(4);
                    $fruitbox_x7_plus = OfficePantryProducts::findOrFail(5);
                //----- Milkbox Variables -----//
                    $milk_1l = OfficePantryProducts::findOrFail(6);
                    $milk_2l = OfficePantryProducts::findOrFail(7);
                    $milk_alt = OfficePantryProducts::findOrFail(8);
                    $milk_1l_org = OfficePantryProducts::findOrFail(9);
                    $milk_2l_org = OfficePantryProducts::findOrFail(10);
                //----- Fruitbox Variables (Fruit Partner) -----//
                    $fruitbox_x1_fruit_partner = OfficePantryProducts::findOrFail(11);
                    $fruitbox_x2_fruit_partner = OfficePantryProducts::findOrFail(12);
                    $fruitbox_x3_fruit_partner = OfficePantryProducts::findOrFail(13);
                    $fruitbox_x4_fruit_partner = OfficePantryProducts::findOrFail(14);
                    $fruitbox_x7_plus_fruit_partner = OfficePantryProducts::findOrFail(15);
                //----- Milkbox Variables (Fruit Partner) -----//
                    $milk_1l_fruit_partner = OfficePantryProducts::findOrFail(16);
                    $milk_2l_fruit_partner = OfficePantryProducts::findOrFail(17);
                    $milk_alt_fruit_partner = OfficePantryProducts::findOrFail(18);
                    $milk_1l_org_fruit_partner = OfficePantryProducts::findOrFail(19);
                    $milk_2l_org_fruit_partner = OfficePantryProducts::findOrFail(20);
            //----- End of OP Products -----//

            //---------- Snackbox Functions ----------//

                function apply_deduction($item, $per_product_deduction) {
                    return $item = ( $item - $per_product_deduction );

                }

            //---------- End of Snackbox Functions ----------//



        // Now each $company should just have the orders which need invoicing attached.
        foreach ($companies as $company) {

            // Each box type needs to be processed slightly differently, so we need 5 dedicated foreach loops.

            //---------- Unset Some Variables ----------//

                unset($sales_invoices);
                unset($snackbox_invoice_pt1);
                unset($sales_invoice_group);
                unset($drinkbox_invoice_pt1);
                unset($otherbox_invoice_pt1);
                unset($snackboxes_ready_for_invoicing);
                unset($drinkboxes_ready_for_invoicing);
                unset($otherboxes_ready_for_invoicing);
                unset($grouped_invoice);
                unset($grouped_totals_ready_for_invoicing);


            //---------- End of Unset Some Variables ----------//

                                                            //---------- Set some variables ----------//

                //----- This'll hold all of our invoices into an array of custom built objects -----//
                    $sales_invoices = [];
                //----- End of - This'll hold all of our invoices into an array of custom built objects -----//

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
                    $sales_invoice_group = (object) [];
                //----- Snackbox Variables -----//

                //----- Drinkbox Variables -----//
                    $drinkbox_invoice_pt1 = (object) [];
                //----- Drinkbox Variables -----//

                //----- Otherbox Variables -----//
                    $otherbox_invoice_pt1 = (object) [];
                //----- Otherbox Variables -----//
                                                        //---------- End of - Set some variables ----------//
                                                        // dd($company->fruitbox);
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

            // At its most basic we just want to group the snackbox entries by snackbox_id
            $snackboxes = $company->snackboxes->groupBy('snackbox_id');
            // dd($snackboxes);

            // Grouping by snack cap would mean adding the various number of boxes up.
            // I also think the the current process totalling up the product values and dividing by the snack cap would need revising.
            // Probably better just finding a way to group the orders afterwards.
            // $snackboxes = $company->snackboxes->groupBy('snack_cap');

            //---------- Actually instead of grouping them by snackbox_id ----------//

                // What I could do for invoicing, is to group them up by 'snack_cap',
                // this could then result in 2 invoice rows (vat/zero rated),
                // for each snack_cap value and the quantity of boxes purchased.
                // This would work for mixed boxes anyway, but what would I do about wholesale that doin't have a snack_cap?
                // Goddamit, there's always a downside to any/all of these ideas?
                // Do we even want them listed like that vat/zero rated, or more like a drinks/other invoice?
                // Probably the latter, so I'll need to handle them differently anyway.

            //---------- End of Actually instead of grouping them by snackbox_id ----------//


            foreach ($snackboxes as $snackbox) {
                // I only need to get this value once, so moving it up one foreach statement.
                $snack_cap = $snackbox[0]->snack_cap;
                $no_of_boxes = $snackbox[0]->no_of_boxes;
                $type = $snackbox[0]->type;
                // Hmmn, are these actually just behaving the same as unsetting the variables each time?
                // I don't think I need them for their original use and if I only want them to unset, shouldn't I just do that to make it clearer?

                // $snackbox_items_total_inc_vat = [];
                // $snackbox_items_minus_vat = [];
                // $snackbox_items_vat = [];
                // $snackbox_items_zero_rated = [];
                // $all_snackbox_items = [];

                // Let's see if I still get the same results... EDIT: Yep.
                unset($snackbox_items_total_inc_vat);
                unset($snackbox_items_minus_vat);
                unset($snackbox_items_vat);
                unset($snackbox_items_zero_rated);
                unset($all_snackbox_items);
                unset($snackbox_items_total_inc_vat_with_snack_cap_deduction_applied);
                unset($snackbox_items_zero_rated_with_snack_cap_deduction_applied);

                $snackbox_items_total_inc_vat = [];
                $snackbox_items_minus_vat = [];
                $snackbox_items_vat = [];
                $snackbox_items_zero_rated = [];
                $all_snackbox_items = [];
                $snackbox_items_total_inc_vat_with_snack_cap_deduction_applied = [];
                $snackbox_items_zero_rated_with_snack_cap_deduction_applied = [];

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

                // How should I treat wholesale snackboxes?  Thinking the process should be the same except for snack cap deductions.
                // If I can contain all the changes to a simple if/else statement, it'll keep it more readable.

                // if ($snack_cap !== null) { // <-- Should i check snack_cap or type === 'wholesale'?
                if ($type !== strtolower('wholesale')) { // <-- Going to use type for now, as I haven't prevented misuse of snack_cap yet.

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

                    // So let's get the sum total(s)
                    $snacks_with_vat_and_discount_total = array_sum($snackbox_items_total_inc_vat_with_snack_cap_deduction_applied);
                    $snacks_zero_rated_total = array_sum($snackbox_items_zero_rated_with_snack_cap_deduction_applied);

                    // Ah, so fun story - don't forget to unset these arrays before the next order!!
                    // unset($snackbox_items_total_inc_vat_with_snack_cap_deduction_applied);
                    // unset($snackbox_items_zero_rated_with_snack_cap_deduction_applied);

                } else {
                    // These are exempt of Snack Caps, so we can just use the variables prior to the deductions.
                    // I'm allowing the now slightly misleading variable names, to keep the next 20 lines reusable.
                    // Or I could rename the variable to something more suitable?
                    $snacks_with_vat_and_discount_total = array_sum($snackbox_items_total_inc_vat);
                    $snacks_zero_rated_total = array_sum($snackbox_items_zero_rated);
                }

                    // and work out the total minus vat aka 'unit_amount'
                    $snacks_with_discount_minus_vat_total = ($snacks_with_vat_and_discount_total / 1.2);
                    // before grabbing the vat amount aka 'tax_amount'
                    $snacks_with_discount_vat_total = ($snacks_with_discount_minus_vat_total * 0.2);

                    // Create the 1st stage of the snackbox invoice
                    $snackbox_pt1 = new $snackbox_invoice_pt1();
                    $snackbox_pt1->snack_cap = $snack_cap;
                    $snackbox_pt1->no_of_boxes = $no_of_boxes;
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

                // EDIT: A SNACKCAP OF ZERO WOULD DISCOUNT THE SNACKBOX TO FREE, ZIP, NADA!  Not a good idea, unless we want to invoice them for a free box?
                // The if ($snack_cap !== null) should cover the wholesale side of things?  TESTING WILL PROVE DEFINITIVELY. <-- Already changed for if ($type !== 'wholesale')

            //---------- END OF WHAT TO DO ABOUT SNACK_CAP-LESS BOXES? END OF WHAT TO DO ABOUT WHOLESALE? ----------//

            //---------- WHEN TO APPLY THE BULK DISCOUNTS ----------//

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
                    $drinkbox_pt1->description = $product->name;
                    $drinkbox_pt1->sales_nominal = $product->sales_nominal;

                    if ($product->vat === 'Yes') {

                        $drinkbox_pt1->unit_amount = ($product->unit_price / 1.2);
                        $drinkbox_pt1->tax_amount = ($drinkbox_pt1->unit_amount * 0.2);

                    } elseif ($product->vat === 'No') {

                        $drinkbox_pt1->unit_amount = $product->unit_price;
                        $drinkbox_pt1->tax_amount = 0;

                    } else {
                        // Nothing should get here but I could log anything that does just in case.
                        $uh_oh_shit_happened = 'Drinkbox item vat status - ' . $drinkbox_item->vat . ' for ' . $drinkbox_item->name
                                                . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                        Log::channel('slack')->alert($uh_oh_shit_happened);
                    }

                    $drinkbox_pt1->vat = $product->vat;
                    $drinkbox_pt1->quantity = $drinkbox_item->quantity;
                    $drinkboxes_ready_for_invoicing[] = $drinkbox_pt1;
                }

            } // end of foreach ($company->drinkboxes as $drinkbox)

            // dd($drinkboxes_ready_for_invoicing);
            // dd($drinks_total);
            $drinks_grand_total = array_sum($drinks_total);
            // dd($drinks_grand_total);
            //----- End of Drinkbox -----//

            //----- Otherbox -----//

            foreach ($company->otherboxes as $otherbox_item) {
                if ($otherbox_item->product_id > 0) {

                    $product = Product::findOrFail($otherbox_item->product_id);
                    $other_total[] = ($product->unit_price * $otherbox_item->quantity);

                    $otherbox_pt1 = new $otherbox_invoice_pt1();
                    $otherbox_pt1->description = $product->name;
                    $otherbox_pt1->sales_nominal = $product->sales_nominal;

                    if ($product->vat === 'Yes') {

                        $otherbox_pt1->unit_amount = ($product->unit_price / 1.2);
                        $otherbox_pt1->tax_amount = ($otherbox_pt1->unit_amount * 0.2);

                    } elseif ($product->vat === 'No') {

                        $otherbox_pt1->unit_amount = $product->unit_price;
                        $otherbox_pt1->tax_amount = 0;

                    } else {
                        // Nothing should get here but I could log anything that does just in case.
                        $uh_oh_shit_happened = 'Otherbox item vat status - ' . $otherbox_pt1->vat . ' for ' . $otherbox_pt1->name
                                                . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                        Log::channel('slack')->alert($uh_oh_shit_happened);
                    }

                    $otherbox_pt1->vat = $product->vat;
                    $otherbox_pt1->quantity = $otherbox_item->quantity;
                    $otherboxes_ready_for_invoicing[] = $otherbox_pt1;
                }

            } // end of foreach ($company->otherbox as $otherbox)

            $other_grand_total = array_sum($other_total);
            // dd($other_grand_total);

            //----- End of Otherbox -----//

            //----- Final Checks For Qualifying Discounts -----//

            // Could move this to join the other variable declarations at the top, should I keep it.
            $company_invoice_discountable_total = 0;
            // $snackbox_grand_total = 0;

            foreach ($snackboxes_ready_for_invoicing as $snackbox) {

                $snackbox_grand_total_array[] = $snackbox->snackbox_total_cost;

            }

            $snackbox_grand_total = array_sum($snackbox_grand_total_array);

            if ($company->discount_snacks === 'True') {

                $company_invoice_discountable_total += $snackbox_grand_total;
            }
            if ($company->discount_drinks === 'True') {

                $company_invoice_discountable_total += $drinks_grand_total;
            }
            if ($company->discount_other === 'True') {

                $company_invoice_discountable_total += $other_grand_total;
            }

            // This should be holding the total of discountable orders this week
            // Checking this total is enough to qualify, is easy.
            // However we then need to make sure we only apply the discount to items that qualify.
            // I.e we must make sure not to discount otherbox items if they're not entitled to otherbox discounts.
            // dd($company_invoice_discountable_total);

            // I'm pretty sure this'll work but some testing will prove i'm right, hopefully. EDIT: Looks good!
            if ($company_invoice_discountable_total >= 20) { // <-- Will need to change this value to an actual discount threshold i.e £300(15%)

                // Then they're entitled to 15% off their orders which are eligible for discount.
                dump('15% discount!');
                //----- Snacks -----//
                if ($company->discount_snacks === 'True') {

                    foreach($snackboxes_ready_for_invoicing as $snackbox) {
                        // dd($snackbox);
                    //    dump($snackbox->snack_total_minus_vat);

                        $snackbox->snack_total_minus_vat = ($snackbox->snack_total_minus_vat * 0.85 );
                        $snackbox->snack_total_vat = ($snackbox->snack_total_vat * 0.85 );
                        $snackbox->snack_total_zero_registered = ($snackbox->snack_total_zero_registered * 0.85 );

                    //    dump($snackbox->snack_total_minus_vat);
                        // dd($snackbox);
                    }
                }
                //----- Drinks -----//
                if ($company->discount_drinks === 'True') {

                    foreach($drinkboxes_ready_for_invoicing as $drinkbox_item) {

                    //    dump($drinkbox_item);

                        //---------- This is where i'm up to - need to do do something with vat for vat registered drinks ----------//

                        // Ok before working out vat etc, we need to apply the 15% discount to get an updated total.
                        // The only two other details i'm pulling in here are, vat('Yes' or 'No') and quantity.
                        $drinkbox_item->unit_amount = ($drinkbox_item->unit_amount * 0.85 );

                        if ($drinkbox_item->vat === 'Yes') {

                            // We need to work out the vat deducted total of the discounted amount (per item).
                            $drinkbox_item->unit_amount = ($drinkbox_item->unit_amount / 1.2);
                            // And the total of vat incurred
                            $drinkbox_item->tax_amount = ($drinkbox_item->unit_amount * 0.2);
                            // $drinkbox_item->tax_type = '20% (VAT on Income)';

                        } elseif ($drinkbox_item->vat === 'No') {
                            // we don't actually need to work anything out here, just hard code the two fields invoicing wants instead.
                            $drinkbox_item->tax_amount = 0;
                            // $drinkbox_item->tax_type = 'Zero Rated Income';

                        } else {
                            // Nothing should get here but I could log anything that does just in case.
                            $uh_oh_shit_happened = 'Drinkbox item vat status - ' . $drinkbox_item->vat . ' for ' . $drinkbox_item->name
                                                    . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                            Log::channel('slack')->alert($uh_oh_shit_happened);
                        }
                    //    dump($drinkbox_item);
                    }
                }
                //----- Other -----//
                if ($company->discount_other === 'True') {

                    foreach($otherboxes_ready_for_invoicing as $otherbox_item) {

                //    dd($otherbox_item);

                        // Ok before working out vat etc, we need to apply the 15% discount to get an updated total.
                        // The only two other details i'm pulling in here are, vat('Yes' or 'No') and quantity.
                        $otherbox_item->unit_amount = ($otherbox_item->unit_amount * 0.85 );

                        if ($otherbox_item->vat === 'Yes') {

                            // We need to work out the vat deducted total of the discounted amount (per item).
                            $otherbox_item->unit_amount = ($otherbox_item->unit_amount / 1.2);
                            // And the total of vat incurred
                            $otherbox_item->tax_amount = ($otherbox_item->unit_amount * 0.2);
                            $otherbox_item->tax_type = '20% (VAT on Income)';

                        } elseif ($otherbox_item->vat === 'No') {
                            // we don't actually need to work anything out here, just hard code the two fields invoicing wants instead.
                            $otherbox_item->tax_amount = 0;
                            $otherbox_item->tax_type = 'Zero Rated Income';

                        } else {
                            // Nothing should get here but I could log anything that does just in case.
                            $uh_oh_shit_happened = 'Otherbox item vat status - ' . $otherbox_item->vat . ' for ' . $otherbox_item->name
                                                    . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                            Log::channel('slack')->alert($uh_oh_shit_happened);
                        }
                    //    dump($otherbox_item);
                    }
                }

            } elseif ($company_invoice_discountable_total >= 10) { // <-- Will need to change this value to an actual discount threshold i.e £100(10%)

                // Then they're entitled to 10% off their orders which are eligible for discount.
                dump('10% discount!');
                //----- Snacks -----//
                if ($company->discount_snacks === 'True') {

                    foreach($snackboxes_ready_for_invoicing as $snackbox) {
                        // dd($snackbox);
                    //    dump($snackbox->snack_total_minus_vat);

                        $snackbox->snack_total_minus_vat = ($snackbox->snack_total_minus_vat * 0.9 );
                        $snackbox->snack_total_vat = ($snackbox->snack_total_vat * 0.9 );
                        $snackbox->snack_total_zero_registered = ($snackbox->snack_total_zero_registered * 0.9 );

                    //    dump($snackbox->snack_total_minus_vat);
                        // dd($snackbox);
                    }
                }
                //----- Drinks -----//
                if ($company->discount_drinks === 'True') {

                    foreach($drinkboxes_ready_for_invoicing as $drinkbox_item) {

                    //    dump($drinkbox_item);

                        //---------- This is where i'm up to - need to do do something with vat for vat registered drinks ----------//

                        // Ok before working out vat etc, we need to apply the 15% discount to get an updated total.
                        // The only two other details i'm pulling in here are, vat('Yes' or 'No') and quantity.
                        $drinkbox_item->unit_amount = ($drinkbox_item->unit_amount * 0.9 );

                        if ($drinkbox_item->vat === 'Yes') {

                            // We need to work out the vat deducted total of the discounted amount (per item).
                            $drinkbox_item->unit_amount = ($drinkbox_item->unit_amount / 1.2);
                            // And the total of vat incurred
                            $drinkbox_item->tax_amount = ($drinkbox_item->unit_amount * 0.2);
                            // Actually, I shouldn't add the tax type here as this would only include the value on company invoices who are eligible for discount.
                            // $drinkbox_item->tax_type = '20% (VAT on Income)';

                        } elseif ($drinkbox_item->vat === 'No') {
                            // we don't actually need to work anything out here, just hard code the two fields invoicing wants instead.
                            $drinkbox_item->tax_amount = 0;
                            // Same reason as above here - commenting it out for now.
                            // $drinkbox_item->tax_type = 'Zero Rated Income';

                        } else {
                            // Nothing should get here but I could log anything that does just in case.
                            $uh_oh_shit_happened = 'Drinkbox item vat status - ' . $drinkbox_item->vat . ' for ' . $drinkbox_item->name
                                                    . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                            Log::channel('slack')->alert($uh_oh_shit_happened);
                        }
                    //    dump($drinkbox_item);
                    }
                }
                //----- Other -----//
                if ($company->discount_other === 'True') {

                    foreach($otherboxes_ready_for_invoicing as $otherbox_item) {

                    //    dump($otherbox_item);

                        // Ok before working out vat etc, we need to apply the 15% discount to get an updated total.
                        // The only two other details i'm pulling in here are, vat('Yes' or 'No') and quantity.
                        $otherbox_item->unit_amount = ($otherbox_item->unit_amount * 0.9 );

                        if ($otherbox_item->vat === 'Yes') {

                            // We need to work out the vat deducted total of the discounted amount (per item).
                            $otherbox_item->unit_amount = ($otherbox_item->unit_amount / 1.2);
                            // And the total of vat incurred
                            $otherbox_item->tax_amount = ($otherbox_item->unit_amount * 0.2);
                            $otherbox_item->tax_type = '20% (VAT on Income)';

                        } elseif ($otherbox_item->vat === 'No') {
                            // we don't actually need to work anything out here, just hard code the two fields invoicing wants instead.
                            $otherbox_item->tax_amount = 0;
                            $otherbox_item->tax_type = 'Zero Rated Income';

                        } else {
                            // Nothing should get here but I could log anything that does just in case.
                            $uh_oh_shit_happened = 'Otherbox item vat status - ' . $otherbox_item->vat . ' for ' . $otherbox_item->name
                                                    . ' was neither yes, or no? Company - ' . $company->invoice_name . ' invoicing screwed up.';

                            Log::channel('slack')->alert($uh_oh_shit_happened);
                        }
                    //    dump($otherbox_item);
                    }
                }
            } // End of - elseif ($company_invoice_discountable_total >= 10) & consequently end of - if ($company_invoice_discountable_total >= 20)

                // Final checks go here <-- EDIT: WHAT WAS I WANTING TO CHECK HERE? HAVE I DONE IT ALL NOW, OR IS IT A GENERIC FINAL CHECKS CALL?

            //----- End of Final Checks For Qualifying Discounts -----//

            //----- Merge orders for invoicing -----//

            // This foreach is to group the orders by their snack cap,
            // so I can re-evaluate their values ONE LAST TIME before committing them to an invoice.

            foreach ($snackboxes_ready_for_invoicing as $invoice) {

                $grouped_invoice[$invoice->snack_cap][] = $invoice;
            }


            foreach ($grouped_invoice as $key => $group) {

                // Let's ensure we reset the values for each snack cap group.
                $group_total = new $sales_invoice_group();
                $group_total->no_of_boxes = 0;
                $group_total->box_total = 0;
                $group_total->snack_total_minus_vat = 0;
                $group_total->snack_total_vat = 0;
                $group_total->snack_total_zero_registered = 0;

                // We need to know how many boxes there are of this snack cap (in total)
                // to work out each ratio of product values to get accurate accounts.

                foreach ($group as $invoice) {
                    $group_total->box_total += $invoice->no_of_boxes;
                }

                foreach ($group as $invoice) {
                    // By dividing the number of boxes with these products in, out of all the boxes (in this snack cap category),
                    // we can work out the ratio to multiply these numbers by.
                    $group_ratio = ($invoice->no_of_boxes / $group_total->box_total);
                    // Now we can use it to tweak the values again.
                    $group_total->snack_cap = $invoice->snack_cap;
                    $group_total->snack_total_minus_vat += ($invoice->snack_total_minus_vat * $group_ratio);
                    $group_total->snack_total_vat += ($invoice->snack_total_vat * $group_ratio);
                    $group_total->snack_total_zero_registered += ($invoice->snack_total_zero_registered * $group_ratio);
                    $group_total->no_of_boxes += $invoice->no_of_boxes;

                }
                $grouped_totals_ready_for_invoicing[] = $group_total;
            }

            // Now we have the invoices grouped by snack cap and the values have been ratioed by number of boxes with those product lines
            // We can create two new invoice lines ready for further details.  The first is vat inclusive items, the second one's for zero rated items.
            // Let's put them into invoices and never speak about snackboxes, snack caps and discounts ever again.

            foreach ($grouped_totals_ready_for_invoicing as $snackbox_group) {

                // OK, so for each of these entries I want to check whether they have zero rated &/or 20% VAT rated products,
                // creating an invoice entry for each if they do.

                // Snackboxes with vat registered products
                if ($snackbox_group->snack_total_minus_vat > 0) {

                    $sales_invoice = new $sales_invoice();

                    $sales_invoice->description = 'Snacks';
                    $sales_invoice->quantity = $snackbox_group->no_of_boxes;
                    $sales_invoice->account_code = '4010';
                    $sales_invoice->unit_amount = $snackbox_group->snack_total_minus_vat;
                    $sales_invoice->tax_amount = ($snackbox_group->snack_total_vat * $snackbox_group->no_of_boxes); // This converts the vat total from per item to grand total.
                    $sales_invoice->tax_type = '20% (VAT on Income)';

                    $sales_invoices[] = $sales_invoice;
                }
                // Snackboxes with zero rated products
                if ($snackbox_group->snack_total_zero_registered > 0) {

                    $sales_invoice = new $sales_invoice();

                    $sales_invoice->description = 'Snacks';
                    $sales_invoice->quantity = $snackbox_group->no_of_boxes;
                    $sales_invoice->account_code = '4010';
                    $sales_invoice->unit_amount = $snackbox_group->snack_total_zero_registered;
                    $sales_invoice->tax_amount = 0;
                    $sales_invoice->tax_type = 'Zero Rated Income';

                    $sales_invoices[] = $sales_invoice;
                }
            }

            // dd($drinkboxes_ready_for_invoicing);

            // Right, time for any last details which will vary between invoice lines for this company.
            if (isset($drinkboxes_ready_for_invoicing)) {
                foreach ($drinkboxes_ready_for_invoicing as $drinkbox_item) {

                    if ($drinkbox_item->vat === 'Yes') {
                        $drinkbox_item->tax_type = '20% (VAT on Income)';
                        $drinkbox_item->tax_amount = ($drinkbox_item->tax_amount * $drinkbox_item->quantity); // This converts the vat total from per item to grand total.

                    } elseif ($drinkbox_item->vat === 'No') {
                        $drinkbox_item->tax_type = 'Zero Rated Income';

                    } else {

                        $uh_oh_shit_happened = 'Now I\'ve really no idea how we got here? SHIT HAPPENED on line 1889 of the Invoicing Controller!';
                        Log::channel('slack')->alert($uh_oh_shit_happened);
                    }
                    // Mostly as a way for me to know what box type these items are supposed to be in.
                    $drinkbox_item->box_type = 'Drinkbox';
                    $sales_invoices[] = $drinkbox_item;
                }
            }

            if (isset($otherboxes_ready_for_invoicing)) {
                foreach ($otherboxes_ready_for_invoicing as $otherbox_item) {

                    if ($otherbox_item->vat === 'Yes') {
                        $otherbox_item->tax_type = '20% (VAT on Income)';
                        $drinkbox_item->tax_amount = ($drinkbox_item->tax_amount * $drinkbox_item->quantity); // This converts the vat total from per item to grand total.
                    //    dd($otherbox_item);

                    } elseif ($otherbox_item->vat === 'No') {
                        $otherbox_item->tax_type = 'Zero Rated Income';
                    } else {

                        $uh_oh_shit_happened = 'Now I\'ve really no idea how we got here? SHIT HAPPENED on line 1889 of the Invoicing Controller!';
                        Log::channel('slack')->alert($uh_oh_shit_happened);
                    }
                    // Mostly as a way for me to know what box type these items are supposed to be in.
                    $otherbox_item->box_type = 'Otherbox';
                    $sales_invoices[] = $otherbox_item;
                }
            }


            // This should now hold all the fruitbox, milkbox, snackbox, drinkbox and otherbox invoices,
            // for ONE company.  Now I can add the delivery information, branding theme etc which remains the same for any item sold to that company.

             // dd($sales_invoices);
            //  dd($company);

             // Could put this elsewhere but it's here for now to make it easy to find, while I sort it out.
             // $order_total = 0;

             foreach ($sales_invoices as $sales_invoice) {

                 if ($company->surcharge !== null) {
                    // dump($sales_invoice);
                    $order_total[] = (($sales_invoice->unit_amount + $sales_invoice->tax_amount) * $sales_invoice->quantity);

                 }

                 $sales_invoice->invoice_name = $company->invoice_name;
                 $sales_invoice->email_address = $company->primary_email; // <-- THIS NEEDS CHANGING FOR THE INVOICE EMAIL ADDRESS ONCE I'VE ADDED IT TO COMPANY DETAILS!

                 // Let's try to get an invoice address first, if this is null, then we'll just use the route address instead.
                 // If we don't have a route address then we probably shouldn't be invoicing them anyway!

                 if ($company->invoice_address_line_1 !== null) {

                     $sales_invoice->po_address_line_1 = $company->invoice_address_line_1;
                     $sales_invoice->po_address_line_2 = $company->invoice_address_line_2;
                     $sales_invoice->po_city = $company->invoice_city;
                     $sales_invoice->po_region = $company->invoice_region;
                     $sales_invoice->po_post_code = $company->invoice_postcode;

                 } else {

                     $sales_invoice->po_address_line_1 = $company->route_address_line_1;
                     $sales_invoice->po_address_line_2 = $company->route_address_line_2;
                     $sales_invoice->po_city = $company->route_city;
                     $sales_invoice->po_region = $company->route_region;
                     $sales_invoice->po_post_code = $company->route_postcode;

                 }

                 $sales_invoice->branding_theme = $company->branding_theme;

                 $completed_sales_invoices[] = $sales_invoice;
             }
             // NEED TO FIX THE LAST COMPANY INVOICE OVERWRITING ALL THE OTHERS FOR THE INVOICING INFO.

             unset($sales_invoices);

             // dd($completed_sales_invoices);
             // dd($order_total);

            //----- End of merge orders for invoicing -----//

            //----- Generate Outstanding Invoices For Snacks, Drinks & Other -----//

                // Generate final invoices here

            //----- End of Generate Outstanding Invoices For Snacks, Drinks & Other -----//

        } // end of foreach ($companies as $company)

        // dd($completed_sales_invoices);

        //----- Time To Loop Through Invoices Inserting Remaining Fields -----//

            // Now we (should) have all the invoices we need to process this week
            // All that remains is to add 'invoice_number', 'invoice_date' and 'due_date'
            // - Invoice Number is sequential, and effectively begins at yy-mm-dd-001 each time invoicing is run.
            // Which is why I figured I should get all the invoices made, to then loop through at the end - aka here.

            // Let's set the dates

            // Let's grab today's (relative to when invoice function is run) date without any formatting, to maximise its reuse.
            $date = CarbonImmutable::now('Europe/London');
            // The invoice date is the week start (monday) of the invoicing week.
            $week_start = $date->startOfWeek()->format('d/m/Y');
            // Invoice date is just the day the invoice function is run.
            $invoice_date = $date->format('ymd');
            // Date Xero will take the payments.
            $due_date = $date->addDay()->format('d/m/Y');
            // The counter starts every run of invoices at the invoice date + 001.
            $counter = 0;

            // The due date is typically the next day after uploading the invoice export to xero.
            // I will however be adding the ability to change this to a longer time period.  Either through a drop down of days or a numerical input.
            // For now though, I'm going to hardcode it to the day after the invoice date.

            dump($date->format('d/m/Y'));
            dump($invoice_date);
            dump($week_start);
            // dd($due_date);

            foreach ($completed_sales_invoices as $sales_invoice) {
            //    foreach ($sales_invoices as $sales_invoice) {
                    $counter++;
                    $sales_invoice->invoice_number = $invoice_date . str_pad($counter, 3, 0, STR_PAD_LEFT);
                    $sales_invoice->invoice_date = $week_start;
                    $sales_invoice->due_date = $due_date;
            //    }
            }

            // dd($completed_sales_invoices);
            //---------- Quick Fudge To Export The Results To A Template ----------//

                return view('exports.invoice-results', [
                    'invoices' => $completed_sales_invoices
                ]);

            //---------- End Of Quick Fudge To Export The Results To A Template ----------//

            // Loop through and insert the last 3 fields here

        //----- End of Time To Loop Through Invoices Inserting Remaining Fields -----//

    } // end of public function weekly_invoicing
} // end of class InvoicingController extends Controller
