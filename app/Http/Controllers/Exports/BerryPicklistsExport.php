<?php

namespace App\Http\Controllers\Exports;

use App\PickList;
use App\WeekStart;

use App\FruitBox;
use App\CompanyRoute;
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
        // $picklists_with_berries = Picklist::where('week_start', $this->week_starting)->where('seasonal_berries', '>', 0)->where('delivery_day', $this->route_day)->orderBy('assigned_to', 'desc')->get();
        
        $fruitbox_with_berries = FruitBox::where('next_delivery', $this->week_starting)->where('is_active', 'Active')->where('fruit_partner_id', 1)->where('seasonal_berries', '>', 0)->where('delivery_day', $this->route_day)->get();
        //dd($fruitbox_with_berries);
        // Now that 'assigned_to' lives as an ID within the routes it needs to be merged together with assigned_routes,
        // before taking the assigned_to value and pulling it into the fruitbox collection.  
        // So first of all, we have to check each fruitbox for its associated route, then check that id for the associated route name.
        foreach ($fruitbox_with_berries as $fruitbox) {
            
            $route = CompanyRoute::where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)->get();
            // This is taken from the CompanyRoute table as an object property.
             if (!empty($route[0])) {
                
                $position_on_route = $route[0]->position_on_route;
                // This isn't using an object property but calling a relationship function declared in its model.
                $assigned_to = $route[0]->assigned_route;

                if ($assigned_to) {
                    // Now we need to push the route info into the fruitbox collection
                    $fruitbox->assigned_to = $assigned_to->name;
                    $fruitbox->position_on_route = $position_on_route;
                } else {
                    dd($route);
                    dd($assigned_to);
                }
            } else {
                dd($route);  
            }

            
            //dd($fruitbox);
        }
        
        // This groups the fruitboxes with berries into (an array of) their respective routes and orders each route by their position (on route).
        $groupedAndSortedBerries = $fruitbox_with_berries->groupBy('assigned_to')->map(function($group){
            return $group->sortBy('position_on_route');
        }); 
        
        // $sorted = $grouped
        
        // dd($groupedAndSorted);
        // dd($fruitbox_with_berries);
        
        // And replicate the 'order_by' assigned_to column to group them into routes.
        
        //$grouped_by_route_picklists_with_berries = $picklists_with_berries->groupBy('assigned_to', 'desc');

        return view('exports.new-berry-picklists', [
                    'week_start'         =>   $this->week_starting,
                    'routes'             =>   $groupedAndSortedBerries,
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
                // dd($event);
                // Set Cell Range Variables
                $cellRangeHeader    = 'A1:D1';  // Week Start & Delivery Day Titles (First Row)
                $cellRangeSub       = 'A2:B2'; // Week Start & Delivery Day Values (Second Row)
                $event->sheet->getDelegate()->getStyle('D2')->getAlignment()->setVertical('bottom');
                $event->sheet->getDelegate()->getStyle('D2')->getAlignment()->setHorizontal('center');
                $tableHeaders       = 'A5:E5'; // Table Headers of Route Name / Company Route (Picklist Box Name) / No. Of Berries.

                // Use Cell Range Variables to style headers on exported sheet.
                $event->sheet->getDelegate()->getStyle($cellRangeHeader)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRangeSub)->getFont()->setSize(18);
                $event->sheet->getDelegate()->getStyle($tableHeaders)->getFont()->setSize(18);
                // Increase the height for the specified Row
                $event->sheet->getDelegate()->getRowDimension('5')->setRowHeight(40);
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(40);
                // Set the cell text to be centered vertically
                $event->sheet->getDelegate()->getStyle($cellRangeHeader)->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle($cellRangeHeader)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRangeSub)->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle($cellRangeSub)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($tableHeaders)->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle($tableHeaders)->getAlignment()->setHorizontal('center');

                // These two variables find the highest column & row populated with data.
                // With this information I can dynamically style the tables.
                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();

                // As we know the tables start at A5 we can hardcode that part of the equation, $rowWidth and $highestRow determine the other coordinate dynamically.
                $selectedCells = 'A5' . ':' . $rowWidth . $highestRow;

                // At the moment this just outlines the table.
                $event->sheet->styleCells(
                      $selectedCells, // Cell Range
                      [ // Styles Array
                          'borders' => [
                              'outline' => [
                                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                  'color' => ['argb' => 'EF5CC3'],
                              ],
                          ], // end of borders
                          // 'fill' => [
                          //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                          //             'color' => [
                          //               'argb' => 'D05AAC'
                          //             ]
                          // ] // end of fill
                      ] // end of styles array
                  ); // end of styleCells function parameters.

                foreach ($event->sheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    // dd($cellIterator);
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $selectedRow = $row->getRowIndex();
                    $chosenCells = 'A' . $selectedRow . ':E' . $selectedRow;
                    $middleCells = 'B' . $selectedRow . ':C' . $selectedRow;
                    $numericalCells = 'C' . $selectedRow . ':E' . $selectedRow;
                    $bCells = 'B' . $selectedRow;
                    $cCells = 'C' . $selectedRow;
                    $dCells = 'D' . $selectedRow;
                    
                    // Styling the numerical values to be centered rather than pushed to one side, this makes them more readable and less likely to be mis-read.
                    $event->sheet->getDelegate()->getStyle($numericalCells)->getAlignment()->setHorizontal('center');
                      
                    // This could be cleaner. I'm sure there's a better way to handle the middle ranges, other than styling them individually like this but it works fine.   
                    $event->sheet->styleCells(
                            $bCells, // Cell Range
                            [ // Styles Array
                                'borders' => [
                                    'left' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                        'color' => ['argb' => 'EF5CC3'],
                                    ],
                                    'right' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                        'color' => ['argb' => 'EF5CC3'],
                                    ],
                                ], // end of borders
                            ] // end of styles array
                    ); // end of styleCells function parameters.
                    $event->sheet->styleCells(
                              $cCells, // Cell Range
                              [ // Styles Array
                                  'borders' => [
                                      'left' => [
                                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                          'color' => ['argb' => 'EF5CC3'],
                                      ],
                                      'right' => [
                                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                          'color' => ['argb' => 'EF5CC3'],
                                      ],
                                  ], // end of borders
                              ] // end of styles array
                      ); // end of styleCells function parameters.
                      $event->sheet->styleCells(
                                $dCells, // Cell Range
                                [ // Styles Array
                                    'borders' => [
                                        'left' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                            'color' => ['argb' => 'EF5CC3'],
                                        ],
                                        'right' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                            'color' => ['argb' => 'EF5CC3'],
                                        ],
                                    ], // end of borders
                                ] // end of styles array
                        ); // end of styleCells function parameters.
                        // End of middle column cells styling.

                        if ( $event->sheet->getDelegate()->getCell('A' . $selectedRow)->getValue() != NULL )
                        {
                             $event->sheet->styleCells(
                                   $chosenCells, // Cell Range
                                   [ // Styles Array
                                       'borders' => [
                                           'top' => [
                                               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                               'color' => ['argb' => 'EF5CC3'],
                                           ],
                                       ], // end of borders
                                   ] // end of styles array
                               ); // end of styleCells function parameters.
                         }

                }

            }]; // End of Aftersheet class & return statement.
    } // End of public function registerEvents()

}
