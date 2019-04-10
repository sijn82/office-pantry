<?php

namespace App\Http\Controllers\Exports;

use App\WeekStart;
// I will be needing these
use App\CompanyDetails;
use App\CompanyRoute;
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;

// But just in case
use App\AssignedRoute;
use App\FruitPartner;
use App\Session;
// And these are just the excel thingies, 
// I may not be using all of them but quicker to copy and paste than evaluate each one.
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

class MonthlySpecialWeeklyExportNew implements 
FromView,
WithTitle,
ShouldAutoSize
{
    public function __construct()
    {
        $courier = session()->get('snackbox_courier');
        $this->courier = $courier;
        
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];
    }

    // This is the most important part of the FruitBox, Picklist Export Class,
    // it's where we determine how the multiple sheets are divided up.
    // Currently the sheets are seperated by their 'assigned_to' routes.

    /**
     * @return array
     */
    public function view(): View
    {
        // Ok let's start again...
        
        // Grab the Mystery Boxes
        
        $monthly_special_snacks = SnackBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();

        $monthly_special_drinks = DrinkBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();
    
        $monthly_special_other = OtherBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();
        
        // I can group each box type by its ID
        
        $monthly_special_snacks_grouped = $monthly_special_snacks->groupBy('snackbox_id');
        $monthly_special_drinks_grouped = $monthly_special_drinks->groupBy('drinkbox_id');
        $monthly_special_other_grouped = $monthly_special_other->groupBy('otherbox_id');
        
        // And shove it together as one multi-dimensional array
        
        $monthly_specials[] = [$monthly_special_snacks_grouped, $monthly_special_drinks_grouped, $monthly_special_other_grouped];
        
        // Now it's time to try and shuffle the data around into something we can loop through in the template
        // There's a lot of foreaching necessary to get down into the nitty gritty, where we can do something...
        
        foreach ($monthly_specials as $monthly_special) {
            foreach ($monthly_special as $order_group ) {
                foreach ($order_group as $key => $order) {
                    
                    // If I now explode the id before the '-', I can grab the associated company_details_id.
                    // I knew this would be useful eventually, but ... wow, I couldn't have done it without you.
                    $companydata = explode('-', $key);
                    // We now have the company id
                    $company_id = $companydata[0];
                    
                    // Now i'm getting most of what I want but the id also needs to be converted into the associated company details name.
                    $company = CompanyDetails::findOrFail($company_id);
                    $company_name = $company->route_name;
                    
                    foreach ($order as $item) {
                        //dd($item->delivery_day);
                        $route = CompanyRoute::where('company_details_id', $company_id)->where('delivery_day', $item->delivery_day)->get();
                        $assigned_route = AssignedRoute::where('id', $route[0]->assigned_route_id)->get();
                        $item->assigned_route_name = $assigned_route[0]->name;
                    }
                    
                    // If I put the name here, I can spit it out once and then list the products in the lines below.
                    $monthly_specials_all[$company_name][] = $order;
                    // dd($order);
                }
            }
        }
        
    
        return view('exports.monthly-special-picklists', [
            'monthly_specials_all' => $monthly_specials_all,
            'week_start' => $this->week_start
        ]);
    }
    
    public function title(): string
    {
        return 'OP Monthly Specials ' . $this->week_start;
    }
}