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

class PicklistsExport implements
// FromCollection,
// WithHeadings,
WithMultipleSheets
{
    // use Exportable;
    private $picklistsolo;
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
        $correctOrderMonTue =   [
                                        '1.15 - Float',
                                        '1.19 - Float 3',
                                        '1.18 - East London',
                                        '1.17 - Canary Wharf 2',
                                        '1.16 - South London 2',
                                        '1.14 - Filling in',
                                        '1.13 - Thames Valley',
                                        '1.12 - West London',
                                        '1.11 - West Central',
                                        '1.10 - W1-W2',
                                        '1.09 - SW1',
                                        '1.08 - South London',
                                        '1.07 - City North',
                                        '1.06 - Canary Wharf',
                                        '1.05 - City South',
                                        '1.04 - M25 North',
                                        '1.03 - M25 South',
                                        '1.02 - Serviced Thames',
                                        '1.01 - Serviced London',
                                        '2.02 - Tue Multidrop',
                                        '2.01 - Tue Serviced',
                                        'TBC',
                                        '000 - Tuesday Route',
                                        '00 - Tuesday Route Serviced',
                                        '14 - Filling In Route',
                                        '13 - Thames Valley II',
                                        '12 - Thames Valley I',
                                        '11 - West Central',
                                        '10 - Michael',
                                        '09 - Gus',
                                        '08 - South London',
                                        '07 - Catalin',
                                        '06 - Stratford',
                                        '05 - City',
                                        '04 - M25 North',
                                        '03 - M25 South',
                                        '02 - Serviced II',
                                        '01 - Serviced I',

                                ];

        $correctOrderWedThurFri =   [
                                        '3.00 - Weds Float',
                                        '3.07 - Extra',
                                        '3.06 - Extra Float',
                                        '3.05 - Thames',
                                        '3.04 - South & North',
                                        '3.03 - West',
                                        '3.02 - City',
                                        '3.01 - Serviced London',
                                        '4.02 - Thu Multidrop',
                                        '4.01 - Thu Serviced',
                                        '4.00 - Thu Float',
                                        '5.02 - Fri Multidrop',
                                        '5.01 - Fri Serviced',
                                        'TBC',
                                        '26 - Friday Route 2',
                                        '25 - Friday Route',
                                        '24 - Thursday Route Gareth',
                                        '24.5 - Thursday Route Jordan',
                                        '23 - M25 Wednesday',
                                        '22 - Gareth',
                                        '21 - Piers',
                                        '20 - Pete',
                                        '19 - Serviced London',
                                    ];
        $picklistscollection = PickList::select('assigned_to')->distinct()->get();
        $sheets = [];

        // foreach ($picklistscollection as $picklistsolo) {
        //
        //     // Each distinct assigned_to (route) calls the PicklistCollection Class below.
        //     $sheets[] = new PicklistCollection($picklistsolo->assigned_to, $this->week_starting);
        // }

            if ($this->delivery_days == 'mon-tue') {

                foreach ($correctOrderMonTue as $picklistsolo) {

                    // Each distinct assigned_to (route) calls the PicklistCollection Class below.
                    $sheets[] = new PicklistCollection($picklistsolo, $this->week_starting);
                }

                return $sheets;

            } else {

                foreach ($correctOrderWedThurFri as $picklistsolo) {

                    // Each distinct assigned_to (route) calls the PicklistCollection Class below.
                    $sheets[] = new PicklistCollection($picklistsolo, $this->week_starting);
                }

                return $sheets;

            }
    }

        // Keeping this here as a backup incase of future issues and as a reminder of when the inner workings of laravel excel appeared to unravel.
        // and I had to add the '->get()' at the end to make the export work and not throw the error 'Call to undefined method Illuminate\Database\Eloquent\Builder::all()'

        // public function collection()
        // {
        //              return PickList::where('week_start', '270818')->where('delivery_day', 'Friday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc')->get();
        // }
}

class PicklistCollection implements
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
        return view('exports.picklists', [
           'picklists' => PickList::where('week_start', $this->week_starting)->where('assigned_to', $this->picklistsolo)->orderBy('seasonal_berries')->orderBy('position_on_route')->get()

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
