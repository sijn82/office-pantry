<?php

namespace App\Http\Controllers\Exports;

use App\WeekStart;
use App\OtherBox;
use App\CompanyRoute;
use App\CompanyDetails;
use App\AssignedRoute;

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

class OtherBoxesCompanyRouteExportNew implements
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
                foreach ($mon_tues as $route_day) {
                    $sheets[] = new OtherBoxCompanyRouteCollection($route_day, $this->week_start);
                }
                break;
            case 'wed-thur-fri':
                foreach ($wed_thur_fri as $route_day) {
                    $sheets[] = new OtherBoxCompanyRouteCollection($route_day, $this->week_start);
                }
                return $sheets;
                break;
            case 'mon':
                $sheets[] = new OtherBoxCompanyRouteCollection('Monday', $this->week_start);
                return $sheets;
                break;
            case 'tue':
                $sheets[] = new OtherBoxCompanyRouteCollection('Tuesday', $this->week_start);
                return $sheets;
                break;
            case 'wed':
                $sheets[] = new OtherBoxCompanyRouteCollection('Wednesday', $this->week_start);
                return $sheets;
                break;
            case 'thur':
                $sheets[] = new OtherBoxCompanyRouteCollection('Thursday', $this->week_start);
                return $sheets;
                break;
            case 'fri':
                $sheets[] = new OtherBoxCompanyRouteCollection('Friday', $this->week_start);
                return $sheets;
                break;
        }
    }
}

class OtherBoxCompanyRouteCollection implements
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
    }
    
    public function view(): View
    {
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        //$currentWeekStart = Weekstart::findOrFail(1); <-- Replaced by passing the variable from OtherBoxesCompanyRouteExportNew class and constructing it.
        
        // Other than a valid week start, all we need to know is what day we're processing and that the orders are 'Active'.
        $otherboxes = OtherBox::where('is_active', 'Active')->where('next_delivery_week', $this->week_start)->where('delivery_day', $this->day)->where('delivered_by_id', 1)->get();
        
        // dd($otherboxes);
        // Rather than grouping the boxes by otherbox_id we actually want to know how many of a product we need to buy, so let's group them by product_id instead.
        $groupedByOtherboxId = $otherboxes->groupBy('otherbox_id');

        // dd($groupedByProductId);
        
        foreach ($groupedByOtherboxId as $otherbox) {
            //dd($otherbox);
            foreach ($otherbox as $item) {
            
                $route = CompanyRoute::where('company_details_id', $item->company_details_id)->where('delivery_day', $item->delivery_day)->get();
                $company = CompanyDetails::findOrFail($item->company_details_id);
                
                 if (isset($route[0]->assigned_route_id)) {
                    $assigned_route_id = $route[0]->assigned_route_id;
                    $assigned_route = AssignedRoute::findOrFail($assigned_route_id);
                 } else {
                     dd($item);
                 }
                
                $item->company_name = $company->route_name;
                $item->route = $assigned_route->name;
            }
        }
        //dd($groupedByOtherboxId);
        
        // dd($products);

        return view('exports.new-otherbox-items-and-routes', [
                    'week_start'            =>  $this->week_start,
                    'otherboxes'            =>  $otherboxes,
                    'out_for_delivery_day'  =>  $this->day
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