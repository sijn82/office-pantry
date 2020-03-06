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

// I'm not sure I'm even using these Export Classes?
// I think it's been replaced, before even finishing it with the OtherBoxesCompanyRouteExportNew
// I'll keep it here for now, as it's quicker than looking into it!
// Hint: The foreach loop is empty and the export template file doesn't exist...

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
        // Set variables for combined days and our exportable $sheets array.
        $mon_tues = ['Monday', 'Tuesday']; 
        $wed_thur_fri = ['Wednesday', 'Thursday', 'Friday'];
        $sheets = [];
        
        // Switch statement to handle day selection exports, passing through a string into the other class still registers as the expected $route_day.
        switch ($this->delivery_days) {
            case 'mon-tue':
                foreach ($monTues as $route_day) {
                    $sheets[] = new OtherBoxPicklistCollection($route_day, $this->week_start);
                }
                break;
            case 'wed-thur-fri':
                foreach ($wedThurFri as $route_day) {
                    $sheets[] = new OtherBoxPicklistCollection($route_day, $this->week_start);
                }
                return $sheets;
                break;
            case 'mon':
                $sheets[] = new OtherBoxPicklistCollection('Monday', $this->week_start);
                return $sheets;
                break;
            case 'tue':
                $sheets[] = new OtherBoxPicklistCollection('Tuesday', $this->week_start);
                return $sheets;
                break;
            case 'wed':
                $sheets[] = new OtherBoxPicklistCollection('Wednesday', $this->week_start);
                return $sheets;
                break;
            case 'thur':
                $sheets[] = new OtherBoxPicklistCollection('Thursday', $this->week_start);
                return $sheets;
                break;
            case 'fri':
                $sheets[] = new OtherBoxPicklistCollection('Friday', $this->week_start);
                return $sheets;
                break;
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
    public function __construct($day, $week_start)
    {
        $this->day = $day;
        $this->week_start = $week_start;
        
        //dd($this->day);
    }
    
    public function view(): View
    {
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        //$currentWeekStart = Weekstart::findOrFail(1);
        
        // Other than a valid week start, all we need to know is what day we're processing and that the orders are 'Active'.
        $otherboxes = OtherBox::where('is_active', 'Active')->where('delivery_week', $this->week_start)->where('delivery_day', $this->day)->get();
        
        foreach ($otherboxes as $otherbox_items) {
        
            // For each order we have a company ID but for the export file we need the actual name.
            // $company_name = CompanyDetails::where('id', $otherbox->company_details_id)
        }
        
        return view('exports.otherbox-items', [
                    'week_start'            =>   $this->week_start,
                    'other_items'            =>  $otherboxes,
                    'out_for_delivery_day'  =>   $this->day
                ]);
    }
    
    public function title(): string
    {
    
    }
    
    public function registerEvents(): array
    {
    
    }
}