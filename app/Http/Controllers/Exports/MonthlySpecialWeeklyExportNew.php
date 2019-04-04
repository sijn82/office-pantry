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
        
        // I can group each box type by its ID
        
        // Then if I explode the id before the '-', I can grab the associated company_details_id.
        
        
        
        
        //$monthly_specials = [];
        
        $monthly_special_snacks = SnackBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();
        //dd($monthly_special_snacks);
        $monthly_special_drinks = DrinkBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();
    
        $monthly_special_other = OtherBox::where('next_delivery_week', $this->week_start)->where('type', 'monthly-special')->where('product_id', '!=', 0)->get();
    
        // Hmmn... actually let's think about this:
        
        // This combines the collection into an array of 3 arrays.  I'm not sure this is the right format to process them?
        // $monthly_specials_all = array_merge($monthly_specials, [$monthly_special_snacks, $monthly_special_drinks, $monthly_special_other]);
        
        $monthly_special_snacks_grouped = $monthly_special_snacks->groupBy('snackbox_id');
        //dd($monthly_special_snacks_grouped);
        
        // foreach ($monthly_special_snacks_grouped as $key => $monthly_special_snackbox) {
        //     $companydata = explode('-', $key);
        //     $company_id = $companydata[0];
        //     dd($monthly_special_snackbox);
        // }
        
        $monthly_special_drinks_grouped = $monthly_special_drinks->groupBy('drinkbox_id');
        $monthly_special_other_grouped = $monthly_special_other->groupBy('otherbox_id');
        
        $monthly_specials[] = [$monthly_special_snacks_grouped, $monthly_special_drinks_grouped, $monthly_special_other_grouped];
        
        // dd($monthly_specials);
        
        foreach ($monthly_specials as $monthly_special) {
            foreach ($monthly_special as $key => $order_group ) {
                //dd($order_group);
                foreach ($order_group as $key => $order) {
                    $companydata = explode('-', $key);
                    // We now have the company id
                    $company_id = $companydata[0];
                    //dd($company_id);
                    $company_orders[$company_id][] = $order;
                    //dd($order);
                }
            }
        }
        
        dd($company_orders);
        
        // dd($monthly_special_snacks_grouped);
        
        $monthly_specials_all = array_merge($monthly_specials, [$monthly_special_snacks_grouped, $monthly_special_drinks_grouped, $monthly_special_other_grouped]);
        
        dd($monthly_specials_all);
        //$monthly_specials_grouped_by_company_details_id = [];
        
        if (isset($monthly_special_box_type[0]->snackbox_id)) {
            $monthly_special_box_type_grouped_by_id = $monthly_special_box_type->groupBy('snackbox_id');
        
        //    dd($monthly_special_box_type_grouped_by_id);
        } elseif (isset($monthly_special_box_type[0]->drinkbox_id)) {
            $monthly_special_box_type_grouped_by_id = $monthly_special_box_type->groupBy('drinkbox_id');
        //    dd($monthly_special_box_type_grouped_by_id);
        } elseif (isset($monthly_special_box_type[0]->otherbox_id)) {
            $monthly_special_box_type_grouped_by_id = $monthly_special_box_type->groupBy('otherbox_id');
        //    dd($monthly_special_box_type_grouped_by_id);
        } else {
            dd('this check isn\'t going to work.');
        }
        
        
        foreach ($monthly_specials_all as $monthly_special_box_type) {
            
            $monthly_specials_grouped_by_company_details_id[] = $monthly_special_box_type->groupBy('company_details_id');
            
            //dd($monthly_special_box_type->groupBy('company_details_id'));
            

        }
        
        foreach ($monthly_specials_grouped_by_company_details_id as $group) {
            $monthly_specials_combined = array_merge($monthly_specials_combined, $group);
        }
        
        dd($monthly_specials_combined);
        dd($monthly_specials_grouped_by_company_details_id);
        
        $monthly_special_snacks_group_company_id = $monthly_special_snacks->groupBy('company_details_id');
    
        //dd($monthly_special_snacks_group_company_id);
    
        $monthly_special_drinks_group_company_id = $monthly_special_drinks->groupBy('company_details_id');
        
        dd($monthly_special_drinks_group_company_id);
        
        $monthly_special_other_group_company_id = $monthly_special_other->groupBy('company_details_id');
    
        // I want to check each model for orders, there may be more than one but typically at least one of these variables will be empty. 
    
    
    
        return view('exports.monthly-special-picklists', [
            'picklists' => $monthly_specials_all
        ]);
    }
    
    public function title(): string
    {
        return 'OP Drinks - ' . $this->week_start;
    }
}