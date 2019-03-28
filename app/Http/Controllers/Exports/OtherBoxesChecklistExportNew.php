<?php

namespace App\Http\Controllers\Exports;

use App\WeekStart;
use App\OtherBox;

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

class OtherBoxesChecklistExportNew implements
WithMultipleSheets
{
    public function __construct()
    {
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        $mon_tues = ['Monday', 'Tuesday']; 
        $wed_thur_fri = ['Wednesday', 'Thursday', 'Friday'];
        // Now we need to decide how many tabs we want to split the data onto.  
        // I'm thinking for the volume of orders, splitting it by the day of the week should be sufficient.
        
        if ($this->delivery_days == 'mon-tue') {
            
            foreach ($mon_tues as $day) {
                    // Each distinct assigned_to (route) calls the FruitboxPicklistCollection Class below.
                    $sheets[] = new OtherBoxChecklistCollection($day, $this->week_start);
            }
                return $sheets;
                
        } else {
            
            foreach ($wed_thur_fri as $day) {
                    // Each distinct assigned_to (route) calls the FruitboxPicklistCollection Class below.
                    $sheets[] = new OtherBoxChecklistCollection($day, $this->week_start);
            }
                return $sheets;
        }
    }

}

class OtherBoxChecklistCollection implements
FromView,
WithTitle,
ShouldAutoSize
// WithHeadings,
//WithEvents
{
    public function __construct($day, $week_start)
    {
        $this->day = $day;
        $this->week_start = $week_start;
        
        //dd($this->day);
    }
    
    public function view(): View
    {
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        $currentWeekStart = Weekstart::findOrFail(1);
        
        // Other than a valid week start, all we need to know is what day we're processing and that the orders are 'Active'.
        $otherboxes = OtherBox::where('is_active', 'Active')->where('next_delivery_week', $this->week_start)->where('delivery_day', $this->day)->get();
        
        // dd($otherboxes);
        // Rather than grouping the boxes by otherbox_id we actually want to know how many of a product we need to buy, so let's group them by product_id instead.
        $groupedByProductId = $otherboxes->groupBy('product_id');

        // dd($groupedByProductId);
        $products = [];
        
        foreach ($groupedByProductId as $otherbox) {

            $product = (object) [];
            $product->name = $otherbox[0]->name;
            $quantity = 0;
            
            foreach ($otherbox as $item) {
                $quantity += $item->quantity;
            }
            
            $product->quantity = $quantity;
            
            $products[] = $product;
        }
        
        // dd($products);

        return view('exports.new-otherbox-items-checklist', [
                    'week_start'            =>   $this->week_start,
                    'products'            =>  $products,
                    'out_for_delivery_day'  =>   $this->day
                ]);
    }
    
    public function title(): string
    {
        return $this->day;
    }
    
    public function registerEvents(): array
    {
    
    }
}