<?php

namespace App\Http\Controllers\Exports;

use App\PickList;
use App\WeekStart;

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

class BerryPicklistsExport implements
// FromCollection,
// WithHeadings,
WithMultipleSheets
{
    // use Exportable;
    private $route_day;
    private $week_starting;
    
    protected $week_start;
    protected $delivery_days;

    public function __construct( $week_starting)
    {
        $this->week_starting = $week_starting;
        
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];
        $this->delivery_days = $week_start[0]['delivery_days'];
    }

    // This is the most important part of the Picklist Export Class,
    // it's where we determine how the multiple sheets are divided up.
    // Currently the sheets are seperated by their 'assigned_to' routes.

    /**
     * @return array
     */
    public function sheets(): array
    {

        $monTues = ['Monday', 'Tuesday'];
        $wedThurFri = ['Wednesday', 'Thursday', 'Friday'];
        $sheets = [];

        
            if ($this->delivery_days == 'mon-tue') {
                
                foreach ($monTues as $route_day) {

                    // Each distinct assigned_to (route) calls the PicklistCollection Class below.
                    $sheets[] = new BerryPicklistCollection($route_day, $this->week_starting);
                }

                return $sheets;
                
            } else {
                
                foreach ($wedThurFri as $route_day) {

                    // Each distinct assigned_to (route) calls the PicklistCollection Class below.
                    $sheets[] = new BerryPicklistCollection($route_day, $this->week_starting);
                }

                return $sheets;
        
            }
    }

}

class BerryPicklistCollection implements
FromView,
WithTitle,
ShouldAutoSize,
// WithHeadings,
WithEvents
{
    private $route_day;
    private $week_starting;

    public function __construct($route_day, $week_starting)
    {
        $this->route_day = $route_day;
        $this->week_starting = $week_starting;
    }

    // This is using a view (blade table template) to shape how the exported excel file will look.
    // public function view(): View
    // {
    //     return view('exports.picklists', [
    //        'picklists' => PickList::all()->where('week_start', $this->week_starting)->where('assigned_to', $this->picklistsolo)->sortBy('position_on_route')
    //    ]);
    // }

    public function view(): View
    {
        $picklists_with_berries = Picklist::where('week_start', $this->week_starting)->where('seasonal_berries', '>', 0)->where('delivery_day', $this->route_day)->orderBy('assigned_to', 'desc')->get();
        $grouped_by_route_picklists_with_berries = $picklists_with_berries->groupBy('assigned_to', 'desc');
        
        return view('exports.berry-picklists', [
                    'week_start'         =>   $this->week_starting,
                    'routes'             =>   $grouped_by_route_picklists_with_berries,
                    'route_day'          =>   $this->route_day
                ]);
    }

    // This adds a named title to each worksheet tab.
    public function title(): string
    {
        return $this->route_day;
    }

    // This is where all the styling magic happens.  This is ultilising the PHPSpreadsheet classes which lie beneath the Laravel-Excel module.
    /**
     * @return array
     */
    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRangeHeader = 'A1:C1';
                $cellRangeSub = 'A2:B2'; // All headers
                $tableHeaders = 'A5:C5';
                $event->sheet->getDelegate()->getStyle($cellRangeHeader)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRangeSub)->getFont()->setSize(16);
                $event->sheet->getDelegate()->getStyle($tableHeaders)->getFont()->setSize(16);
            }];
            }

}
