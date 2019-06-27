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

class FruitPartnerPicklists implements
WithMultipleSheets
FromView
{
    public function __construct($orders)
    {
        $this->fruitboxes = $orders->fruitboxes;
        $this->milkboxes = $orders->milkboxes;
    }
    
    // public function sheets(): array
    // {
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
    // }

    public function view(): View
    {
        // dd($this->fruitboxes);
    
        return view('exports.fruitpartner-fruitbox-picklists', [
            'picklists' => $this->fruitboxes
        ]);
    
    }


}

class FruitPartnerFruitOrders implements
FromView
{
    public function __construct($fruitboxes)
    {
        $this->fruitboxes = $fruitboxes;
    }

    public function view(): View
    {
        dd($this->fruitboxes);
        return view('exports.fruitpartner-fruitbox-picklists', [
            'picklists' => $this->fruitboxes
        ]);
    }
}

class FruitPartnerMilkOrders implements
FromView
{
    public function __construct($milkboxes)
    {
        $this->$milkboxes = $milkboxes;
    }

    public function view(): View
    {
        dd($this->$milkboxes);
        return view('exports.fruitpartner-milkbox-picklists', [
            'picklists' => $this->$milkboxes
        ]);
    }
}




