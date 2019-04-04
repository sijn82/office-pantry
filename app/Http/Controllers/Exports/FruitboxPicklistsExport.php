<?php

namespace App\Http\Controllers\Exports;

use App\PickList;
use App\WeekStart;
use App\FruitBox;
use App\Route;
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

class FruitboxPicklistsExport implements
// FromCollection,
// WithHeadings,
WithMultipleSheets
{
    // use Exportable;
    private $picklistsolo;
    private $week_starting;

    protected $week_start;
    protected $delivery_days;

    public function __construct($week_starting)
    {
        $this->week_starting = $week_starting;

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
    public function sheets(): array
    {
        $sheets = [];
        
        switch ($this->delivery_days) {
            case 'mon-tue':
                $orderedRoutesAll = AssignedRoute::whereIn('delivery_day', ['Monday', 'Tuesday'])->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }
                break;
            case 'wed-thur-fri':
                $orderedRoutesAll = AssignedRoute::whereIn('delivery_day', ['Wednesday', 'Thursday', 'Friday'])->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }
                return $sheets;
                break;
            case 'mon':
                $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Monday')->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }    
                return $sheets;
                break;
            case 'tue':
                $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Tuesday')->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }    
                return $sheets;
                break;
            case 'wed':
                $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Wednesday')->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }    
                return $sheets;
                break;
            case 'thur':
                $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Thursday')->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }    
                return $sheets;
                break;
            case 'fri':
                $orderedRoutesAll = AssignedRoute::where('delivery_day', 'Friday')->orderBy('tab_order', 'desc')->get();
                foreach ($orderedRoutesAll as $route) {
                    $orderedRoutes[] = $route->name;
                }
                foreach ($orderedRoutes as $assigned_route) {
                    $sheets[] = new FruitboxPicklistCollection($assigned_route, $this->week_start);
                }    
                return $sheets;
                break;
        }  // End of switch statement

    }

} // End of FruitboxPicklistsExport class

class FruitboxPicklistCollection implements
FromView,
WithTitle,
ShouldAutoSize,
// WithHeadings,
WithEvents
{
    private $picklistsolo;
    private $week_starting;

    public function __construct($picklistsolo, $week_starting)
    {
        $this->picklistsolo = $picklistsolo;
        $this->week_start = $week_starting;
    }
    
    // This is the old way, when the picklists were their own table in the db rather than several relational tables.

    // public function view(): View
    // {
    //     return view('exports.fruitbox-picklists', [
    //        'picklists' => PickList::where('week_start', $this->week_starting)->where('assigned_to', $this->picklistsolo)->orderBy('seasonal_berries')->orderBy('position_on_route')->get()
    // 
    //    ]);
    // }
    
    public function view(): View
    {
        // ---------- Fruit Deliveries ---------- //
        
        // First we need to check for orders that are due for delivery this week.  We can compare their next delivery date with the current week start date.
        // $currentWeekStart = Weekstart::findOrFail(1);

        // If it matches, it's on for delivery this week and delivered/packed by office pantry.
        $fruitboxesForDelivery = FruitBox::where('next_delivery', $this->week_start)->where('is_active', 'Active')->where('fruit_partner_id', 1)->get();

        foreach ($fruitboxesForDelivery as $fruitbox)
        {
            // dd($fruitbox);
            
        // -----  This needs changing to replace the 'Route' check with a 'CompanyRoute' check and to replace the '->where('assigned_to' $this->picklistsolo)' reliance. ----- //
            
            // Ok, time to change the '->where('assigned_to' $this->picklistsolo)', to look for the id instead after translating the $this->picklistsolo name from AssignedRoutes.
            $assigned_route = AssignedRoute::where('name', $this->picklistsolo)->get();
            // For each route in the routes table, we check the associated Company ID for a FruitBox - that's Active, On Delivery For This Week and on this Delivery Day.
            $routesForFruitbox = CompanyRoute::where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)
                                             ->where('is_active', 'Active')->where('assigned_route_id', $assigned_route[0]->id)->get();

            if (count($routesForFruitbox)) {
                $fruitbox['assigned_to'] = $routesForFruitbox[0]->assigned_to;
                $fruitbox['position_on_route'] = $routesForFruitbox[0]->position_on_route;
                
                $routesAndOrders[] = $fruitbox;
                
            } else {
                // The fruitbox isn't for this particular route, so on this tab we can forget all about it.  
            }
            
        // ----- End of This needs changing to replace the 'Route' check with a 'CompanyRoute' check and to replace the '->where('assigned_to' $this->picklist)' reliance. ----- //
            
            // A nice little way to check a specific result for testing purposes.  I can comment it out for now but may reuse again in the near future.
            // if ($routeInfoSolo->company_details_id == 1) {
            //     dd($routeInfoSolo);
            // }
            
        }

        if (empty($routesAndOrders)) {
            
            $routesAndOrders = [];
        } else {
            
            // This function is being delared before using it in the usort function(s) in a moment.
            $reorder_by_position = function($a, $b) 
            {
                // This is using the new and sexy spaceship operator to compare company string names and return them in alphabetical order.
                // Reorder everything by position on route, don't worry about seasonal berries just yet.
                $outcome = $a->position_on_route <=> $b->position_on_route;
                return $outcome;
            };
            
            // Combined with usort, some background php magic will return the (alpabetically, or in this case numerically prior) item.
            usort($routesAndOrders, $reorder_by_position);
            
            // ----- For the record what happens next feels overly complicated and I wouldn't be at all surprised to find a simpler solution! ----- //
            
            // Now we create a second array only containing picklists with seasonal berries.
            foreach ($routesAndOrders as $picklist) {
                if ($picklist->seasonal_berries > 0) {
                    $seasonal_berries[] = $picklist;
                }
            }
            
            if (empty($seasonal_berries)) {
                $seasonal_berries = [];
            }
            
            // So we can compare the two arrays and return a new array without any of the seasonal berries included.
            $PicklistsWithoutSeasonalBerries = array_diff($routesAndOrders, $seasonal_berries);
            // Next let's reset the array keys before adding the berries back later.
            $ReorderedPicklistsWithoutSeasonalBerries = array_values($PicklistsWithoutSeasonalBerries);
            // Time to reorder the seasonal berries by position on route
            usort($seasonal_berries, $reorder_by_position);
            // And add them back to the original picklist array.
            foreach($seasonal_berries as $picklist) {
                 $ReorderedPicklistsWithoutSeasonalBerries[] = $picklist;
            }
            
            // This is just because the variable name is misleading once it contains seasonal berries!
            // Oh and actually so that it doesn't break the whole function should there not be any picklists for a particular route,
            // although this is very unlikely to really happen.
            $routesAndOrders = $ReorderedPicklistsWithoutSeasonalBerries;
            
        }
        
        return view('exports.fruitbox-picklists', [
            'picklists' => $routesAndOrders
        ]);
    }
    

    // This adds a named title to each worksheet tab.
    public function title(): string
    {
        return $this->picklistsolo;
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
                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();

                foreach ($event->sheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $selectedRow = $row->getRowIndex();
                    $chosenCells = 'E' . $selectedRow . ':S' . $selectedRow;
                    $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                    $chosenCellsArray = $event->sheet->getDelegate()
                        ->rangeToArray(
                            $chosenCells,     // The worksheet range that we want to retrieve
                            NULL,        // Value that should be returned for empty cells
                            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                            TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                            TRUE         // Should the array be indexed by cell row and cell column
                        );

                    foreach ($cellIterator as $cell) {
                        if ($cell !== null && ($selectedRow > 1 && $selectedRow != $highestRow)) {

                            if (    $chosenCellsArray[$selectedRow]['G'] == 6
                                    && $chosenCellsArray[$selectedRow]['H'] == 3
                                    && $chosenCellsArray[$selectedRow]['I'] == 10
                                    && $chosenCellsArray[$selectedRow]['J'] == 3
                                    && $chosenCellsArray[$selectedRow]['K'] == 16
                                    && $chosenCellsArray[$selectedRow]['L'] == 12)
                            {

                                    if (    $chosenCellsArray[$selectedRow]['E'] != 0
                                            || $chosenCellsArray[$selectedRow]['F'] != 0
                                            || $chosenCellsArray[$selectedRow]['M'] != 0
                                            || $chosenCellsArray[$selectedRow]['N'] != 0
                                            || $chosenCellsArray[$selectedRow]['O'] != 0
                                            || $chosenCellsArray[$selectedRow]['P'] != 0
                                            || $chosenCellsArray[$selectedRow]['Q'] != 0
                                            || $chosenCellsArray[$selectedRow]['R'] != 0
                                            || $chosenCellsArray[$selectedRow]['S'] != 0)
                                    {
                                        // styleCells() is a custom built Macro and registered
                                        $event->sheet->styleCells(
                                              $selectedCell, // Cell Range
                                              [ // Styles Array
                                                  'borders' => [
                                                      'outline' => [
                                                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                                          'color' => ['argb' => 'EF5CC3'],
                                                      ],
                                                  ], // end of borders
                                                  'fill' => [
                                                              'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                              'color' => [
                                                                'argb' => 'D05AAC'
                                                              ]
                                                  ] // end of fill
                                              ] // end of styles array
                                          ); // end of styleCells function parameters.

                                      } else {

                                            // $selectedRow = $row->getRowIndex();
                                            $event->sheet->styleCells(
                                                  $selectedCell, // Cell Range
                                                  [ // Styles Array
                                                      'borders' => [
                                                          'outline' => [
                                                              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                                              'color' => ['argb' => 'AFFF46'],
                                                          ],
                                                      ], // end of borders
                                                      'fill' => [
                                                                  'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                                  'color' => [
                                                                    'argb' => '93DA38'
                                                                  ]
                                                      ] // end of fill
                                                  ] // end of styles array
                                              ); // end of styleCells function parameters.
                                      } // end of if ($cell == TBC)
                        } else {
                                $event->sheet->styleCells(
                                      $selectedCell, // Cell Range
                                      [ // Styles Array
                                          'borders' => [
                                              'outline' => [
                                                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                                  'color' => ['argb' => '689BE9'],
                                              ],
                                          ], // end of borders
                                          'fill' => [
                                                      'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                      'color' => [
                                                        'argb' => '567EBC'
                                                      ]
                                          ] // end of fill
                                      ] // end of styles array
                                ); // end of styleCells function parameters.
                        }
                    }
                } // end of foreach statement
            }
                $cellRange = 'A1:AA1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            }
        ];
    }
}
