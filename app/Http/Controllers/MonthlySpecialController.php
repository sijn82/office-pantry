<?php

namespace App\Http\Controllers;

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
    
    
    // Selected Delivery Day Exports
    public function download_monthly_special_op()
    {
    session()->put('snackbox_courier', 'OP');

    return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    }
    public function download_monthly_special_dpd()
    {
    session()->put('snackbox_courier', 'DPD');

    return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    }
    public function download_monthly_special_apc()
    {
    session()->put('snackbox_courier', 'APC');

    return \Excel::download(new Exports\MonthlySpecialExportNew, 'monthly-special-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
    }

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