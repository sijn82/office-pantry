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

class OtherBoxesExportNew implements
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
                    $sheets[] = new OtherBoxPicklistCollection($day, $this->week_start);
            }
                return $sheets;
                
        } else {
            
            foreach ($wed_thur_fri as $day) {
                    // Each distinct assigned_to (route) calls the FruitboxPicklistCollection Class below.
                    $sheets[] = new OtherBoxPicklistCollection($day, $this->week_start);
            }
                return $sheets;
        }
    }

}

class OtherBoxPicklistCollection implements
FromView,
WithTitle,
ShouldAutoSize,
// WithHeadings,
WithEvents
{
    public function __construct($day)
    {
        $this->day = $day;
        
        dd($this->day);
    }
    
    public function view(): View
    {
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        $currentWeekStart = Weekstart::findOrFail(1);
        
        // Other than a valid week start, all we need to know is what day we're processing and that the orders are 'Active'.
        $otherboxes = OtherBox::where('is_active', 'Active')->where('next_delivery_week', $currentWeekStart->current)->where('delivery_day', $day)->get();
        
        foreach ($otherboxes as $otherbox) {
            
            // For each order we have a company ID but for the export file we need the actual name.
            $company_name = Company::where('id', $otherbox->company_id)
        }
    }
    
    public function title(): string
    {
    
    }
    
    public function registerEvents(): array
    {
    
    }
}