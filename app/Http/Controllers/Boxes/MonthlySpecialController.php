<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

use App\WeekStart;

class MonthlySpecialController extends Controller 
{
    
    public function __construct()
    {
        // $this->week_start = 170918;
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];

    }
    
    // These currently hit an empty(ish) conroller missing the specified class. I'm also thinking for monthly specials we probably only need the weekly option.
    // I'm commenting this out for now but should we need to export via selected days, I can revisit this, creatin g afresh class or reusing the existing one with some extra checks.
    // I'll be commenting out the buttons in the vue component as well.
    
    // // Selected Delivery Day Exports
    // public function download_monthly_special_op()
    // {
    // session()->put('snackbox_courier', 'OP');
    // 
    // return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    // }
    // public function download_monthly_special_dpd()
    // {
    // session()->put('snackbox_courier', 'DPD');
    // 
    // return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    // }
    // public function download_monthly_special_apc()
    // {
    // session()->put('snackbox_courier', 'APC');
    // 
    // return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    // }

    // Full Week Exports
    public function download_monthly_special_op_weekly()
    {
    session()->put('snackbox_courier', 'OP');

    return \Excel::download(new Exports\MonthlySpecialWeeklyExportNew, 'monthly-special-' . $this->week_start . '.xlsx');
    }
    public function download_monthly_special_dpd_weekly()
    {
    session()->put('snackbox_courier', 'DPD');

    return \Excel::download(new Exports\MonthlySpecialWeeklyExportNew, 'monthly-special-' . $this->week_start . '.xlsx');
    }
    public function download_monthly_special_apc_weekly()
    {
    session()->put('snackbox_courier', 'APC');

    return \Excel::download(new Exports\MonthlySpecialWeeklyExportNew, 'monthly-special-' . $this->week_start . '.xlsx');
    }
}