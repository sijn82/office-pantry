<?php 

// //---------- Code pulled from FruitPartnerController function ----------//
// 
// // This will grab all fruit partners except for Office Pantry, so long as Office Pantry remains the 1st fruitpartner in the db.   
// // This is easy to guarantee, so long as I don't forget to add it during datbase refresh and setup!
// 
// $fruitpartners = FruitPartner::all()->whereNotIn('id', [1]);
// $week_start = WeekStart::first();
// // dd($week_start->current);
// 
// // Not sure why but I tried to use new \stdClass again and this time it worked fine?!?  Ah well, life is filled with surprises.
// $orders = new \stdClass;
// 
// foreach ($fruitpartners as $fruitpartner) {
// 
//     // These are all the boxes due for delivery this week.
//     $fruitboxes = $fruitpartner->fruitbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
//     $milkboxes = $fruitpartner->milkbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
// 
//     //---------- Archive Checks ----------//
// 
//     // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
//     if ($fruitpartner->fruitbox_archive) {
//         // These are the archived boxes, although I'm not sure how relevant they'll be as this function is run (weekly?) before orders have been delivered.
//         $archived_fruitboxes = $fruitpartner->fruitbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
//     }
//     // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
//     if ($fruitpartner->milkbox_archive) {
//         // Still not sure we'll actually be using them but all the more reason to make sure they don't throw errors.
//         $archived_milkboxes = $fruitpartner->milkbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
//     }
// 
//     //---------- End of Archive Checks ----------//
// 
//     // I probably don't need to worry about empty collections, so let's check that before adding to the orders.
//     if ($fruitboxes->isNotEmpty()) {
//         $orders->fruitboxes[$fruitpartner->name] = $fruitboxes;
//     }
// 
//     if ($milkboxes->isNotEmpty()) {
//         $orders->milkboxes[$fruitpartner->name] = $milkboxes;
//     }
// }
// // Cool(io) - $orders is now filled with orders.  Just orders, and the key used is the fruitpartner name as I'm sure that'll save some bother cometh the template.
// // However, do I really want to put/keep them together when they're going to different templates?
// 
// dd($orders);

//---------- End of code pulled from FruitPartnerController function ----------//

namespace App\Http\Controllers\Exports;

//----- Copied from another export function for speed but I should clean up and remove any not actually used -----//

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Sheet;

//----- End of Maatwebsite classes -----//

use App\FruitPartner;
use App\WeekStart;
use App\FruitBox;
use App\MilkBox;
use App\CompanyDetails;
use Illuminate\Support\Facades\Log;

class FruitPartnerPicklists implements
WithMultipleSheets
// FromView
{
    public function __construct($orders)
    {
        $this->fruitboxes = $orders->fruitboxes;
        $this->milkboxes = $orders->milkboxes;
    }
    
    public function sheets(): array
    {
    //     // OK, so how do I want to split up these tabs?
    //     // Each file is a different fruit partner
    //     // Each tab is a different day.
    // 
    //     //---------- Code pulled from FruitPartnerController function ----------//
    // 
    //     // This will grab all fruit partners except for Office Pantry, so long as Office Pantry remains the 1st fruitpartner in the db.   
    //     // This is easy to guarantee, so long as I don't forget to add it during datbase refresh and setup!
    // 
    //     // $fruitpartners = FruitPartner::all()->whereNotIn('id', [1]);
    //     // $week_start = WeekStart::first();
    //     // // dd($week_start->current);
    //     // 
    //     // // Not sure why but I tried to use new \stdClass again and this time it worked fine?!?  Ah well, life is filled with surprises.
    //     // $orders = new \stdClass;
    //     // 
    //     // foreach ($fruitpartners as $fruitpartner) {
    //     // 
    //     //     // These are all the boxes due for delivery this week.
    //     //     $fruitboxes = $fruitpartner->fruitbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
    //     //     $milkboxes = $fruitpartner->milkbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
    //     // 
    //     //     //---------- Archive Checks ----------//
    //     // 
    //     //     // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
    //     //     if ($fruitpartner->fruitbox_archive) {
    //     //         // These are the archived boxes, although I'm not sure how relevant they'll be as this function is run (weekly?) before orders have been delivered.
    //     //         $archived_fruitboxes = $fruitpartner->fruitbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
    //     //     }
    //     //     // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
    //     //     if ($fruitpartner->milkbox_archive) {
    //     //         // Still not sure we'll actually be using them but all the more reason to make sure they don't throw errors.
    //     //         $archived_milkboxes = $fruitpartner->milkbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
    //     //     }
    //     // 
    //     //     //---------- End of Archive Checks ----------//
    //     // 
    //     //     // I probably don't need to worry about empty collections, so let's check that before adding to the orders.
    //     //     if ($fruitboxes->isNotEmpty()) {
    //     //         $orders->fruitboxes[$fruitpartner->name] = $fruitboxes;
    //     //     } else {
    //     //         Log::channel('slack')->info('Fruit Partner: '$fruitpartner->name . ' has no Fruit Orders to be delivered this week.' )
    //     //     }
    //     // 
    //     //     if ($milkboxes->isNotEmpty()) {
    //     //         $orders->milkboxes[$fruitpartner->name] = $milkboxes;
    //     //     } else {
    //     //         Log::channel('slack')->info('Fruit Partner: '$fruitpartner->name . ' has no Milk Orders to be delivered this week.' )
    //     //     }
    //     // }
    //     // Cool(io) - $orders is now filled with orders.  Just orders, and the key used is the fruitpartner name as I'm sure that'll save some bother cometh the template.
    //     // However, do I really want to put/keep them together when they're going to different templates?
    // 
    //     // dd($orders);
    // 
    //     $sheets[] = new FruitPartnerFruitOrders($this->fruitboxes);
    //     $sheets[] = new FruitPartnerMilkOrders($this->fruitboxes);
    
        if (!empty($this->fruitboxes)) {
            $sheets[] = new FruitPartnerFruitOrders($this->fruitboxes);
        } else {
            $sheets[] = 'No fruit for this week.';
        }
        
        if (!empty($this->milkboxes)) {
            $sheets[] = new FruitPartnerMilkOrders($this->milkboxes);
        } else {
            $sheets[] = 'No milk for this week.';
        }
        
        if (!empty($this->fruitboxes) || !empty($this->milkboxes)) {
            $sheets[] = new FruitPartnerCombinedDetails($this->fruitboxes, $this->milkboxes);
        } else {
            $sheets[] = 'Nothing to deliver!';
        }
        // dd($sheets);
        return $sheets;
    }

}

class FruitPartnerFruitOrders implements
FromView,
WithTitle,
ShouldAutoSize
{
    public function __construct($fruitpartner_fruitboxes)
    {
        $this->fruitpartner_fruitboxes = $fruitpartner_fruitboxes;
    }

    public function view(): View
    {
         // this is somewhat silly, I injected the fruitpartner's name as a key earlier, now I'm essentially just stripping it out.
         // It does mean I have it accessible for display, rather than using the id but once I know exactly what I'm doing with it, I may revise this.
         foreach ($this->fruitpartner_fruitboxes as $key => $fruitboxes) {
             foreach ($fruitboxes as $fruitbox) {
                 $company = CompanyDetails::find($fruitbox->company_details_id);
                 // This (invoice_name) may not be the best name to use as it could be the same for a couple of offices that share an umbrella payment company
                 // $company->invoice_name 
                 // Route name could suffer the same fate, however I think in practice this will be more flexible as it's not used by xero, so could be more easily fudged.
                 $fruitbox->company_name = $company->route_name;
             }
         }
         
        return view('exports.fruitpartner-fruitbox-picklists', [
            'picklists' => $fruitboxes
        ]);
    }
    
    public function title(): string
    {
        return 'Fruit Orders';
    }
}

class FruitPartnerMilkOrders implements
FromView,
WithTitle,
ShouldAutoSize
{
    public function __construct($fruitpartner_milkboxes)
    {
        $this->fruitpartner_milkboxes = $fruitpartner_milkboxes;
    }

    public function view(): View
    {
        foreach ($this->fruitpartner_milkboxes as $key => $milkboxes) {
            foreach ($milkboxes as $milkbox) {
                $company = CompanyDetails::find($milkbox->company_details_id);
                // This (invoice_name) may not be the best name to use as it could be the same for a couple of offices that share an umbrella payment company
                // $company->invoice_name 
                // Route name could suffer the same fate, however I think in practice this will be more flexible as it's not used by xero, so could be more easily fudged.
                $milkbox->company_name = $company->route_name;
            }
        }
        
        return view('exports.fruitpartner-milkbox-picklists', [
            'picklists' => $milkboxes
        ]);
    }
    
    
    public function title(): string
    {
        return 'Milk Orders';
    }
}

class FruitPartnerCombinedDetails implements
FromView,
WithTitle,
ShouldAutoSize
{
    public function __construct($fruitboxes, $milkboxes)
    {
        $this->fruitboxes = $fruitboxes;
        $this->milkboxes = $milkboxes;
    }
    
    public function view(): View
    {
        if (!empty($this->fruitboxes)) {
            foreach ($this->fruitboxes as $fruitboxes) {
                // $companies_getting_fruit = $fruitboxes->pluck('company_details_id')->all(); <-- This needs to be more company/delivery day specific if it's going to work.
                // dd($companies_getting_fruit);
                foreach ($fruitboxes as $fruitbox) {
                    // dd($fruitbox);
                    $company_details = CompanyDetails::find($fruitbox->company_details_id);
                    $fruitbox->company_details_route_name = $company_details->route_name;
                    $fruitbox->company_details_delivery_information = $company_details->delivery_information;
                    $fruitbox->company_details_postcode = $company_details->route_postcode;
                    // This approach will grab all populated address lines, ignoring any empty (null) fields (array_filter)
                    // and separating (implode) them with a comma.
                    // I need to upgrade other sections using the summary address with this approach.
                    $fruitbox->company_details_summary_address = implode(", ", array_filter([
                            $company_details->route_address_line_1, 
                            $company_details->route_address_line_2, 
                            $company_details->route_address_line_3, 
                            $company_details->route_city, 
                            $company_details->route_region
                        ]));
                        
                    // dd($fruitbox);
                    
                    // I could do something here, instead of adding a new foreach clause stipulating the same thing.
                    
                }            
            }
        }
        
        if (!empty($this->milkboxes)) {
            foreach ($this->milkboxes as $milkboxes) {
                $companies_getting_milk = $milkboxes->pluck('company_details_id')->all();
                // dd($companies_getting_milk);
                foreach ($milkboxes as $milkbox) {
                    $company_details = CompanyDetails::find($milkbox->company_details_id);
                    $milkbox->company_details_route_name = $company_details->route_name;
                    $milkbox->company_details_delivery_information = $company_details->delivery_information;
                    $milkbox->company_details_postcode = $company_details->route_postcode;
                    // This approach will grab all populated address lines, ignoring any empty (null) fields
                    // and separating (implode) them with a comma.
                    // I need to upgrade other sections using the summary address with this approach.
                    $milkbox->company_details_summary_address = implode(", ", array_filter([
                            $company_details->route_address_line_1, 
                            $company_details->route_address_line_2, 
                            $company_details->route_address_line_3, 
                            $company_details->route_city, 
                            $company_details->route_region
                        ]));
                        
                    //    dd($milkbox);
                }
            }
        }
        
        // Before passing the data to the template I need to sort through what we have, grouping fruit and milk totals into one route/delivery entry if applicable.
        // However should they only be getting one or the other (fruit or milk), I need to be able to know that and populate the remaining fields with zero's.
        
        // Step 1
        
        //----- Test One -----//
        
        // // This collect() turns the array into a collection, allowing the use of (Laravel's) collection methods.
        // $companies_getting_milk_collection = collect($companies_getting_milk);
        // // Milk only orders are rare, however they do occur.  In these instances we want just want to put 0 in the fruit totals column and otherwise treat it the same as the others.
        //  $milk_only_orders = $companies_getting_milk_collection->diff($companies_getting_fruit);
        
        //----- Test One Results -----//
        
        // This approach doesn't work because we're checking for the week, not the delivery day.  
        // There actually is a milk only delivery which didn't show as unique because their company_details_id was featured elsewhere in the week.
        // dd($milk_only_orders);
        
        //----- End of Test One (Results) -----//
        
        //----- Test Two -----//
        
        // Create a new object which will get populated one way or another depending on whether it contains fruit/milk or both.
        $delivery_entry = new \stdClass;
        
        // OK, so let's just run through all the fruit orders, see if they have a corresponding milk entry and worry about the rest later.
        foreach ($fruitboxes as $fruitbox) {
            $additional_milk = $milkboxes->where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)->all();
            dd($additional_milk);
            
            if (!empty($additional_milk)) {
                // Then if the check is working properly, we have a corresponding milkbox entry to add to the delivery route.
                $delivery_entry->company_details_route_name = $additional_milk->company_details_route_name;
                $delivery_entry->company_details_delivery_information = $additional_milk->company_details_delivery_information;
                $delivery_entry->company_details_postcode = $additional_milk->company_details_postcode;
                $delivery_entry->company_details_summary_address = $additional_milk->company_details_summary_address;
                $delivery_entry->delivery_day = $additional_milk->delivery_day;
                $delivery_entry->
                $delivery_entry->
            } else {
                // Then this hopefully means we have a fruit only delivery.
                
            }
        }
        
        //----- Test Two Results -----//
        
        //----- End of Test Two (Results) -----//
        
        
        // return view('exports.fruitpartner-combined-details', [
        //     ''
        // ])
        
        // dd($company_details);
    }
    
    public function title(): string
    {
        return 'Delivery Details';
    }
}





