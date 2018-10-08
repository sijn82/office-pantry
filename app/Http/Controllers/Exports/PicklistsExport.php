<?php

namespace App\Http\Controllers\Exports;

use App\PickList;

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

    public function __construct( $week_starting)
    {
        $this->week_starting = $week_starting;
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
                                        '13 - Sunday Route',
                                        '12 - Thames Valley II',
                                        '11 - Thames Valley I',
                                        '10 - West Central',
                                        '09 - Michael',
                                        '08 - Gus',
                                        '07 - South London',
                                        '06 - Catalin',
                                        '05 - City',
                                        '04 - M25 North',
                                        '03 - M25 South',
                                        '02 - Serviced II',
                                        '01 - Serviced I',
                                        '00 - Tuesday Route Serviced',
                                        '000 - Tuesday Route',
                                        'TBC'
                                ];

        $correctOrderWedThurFri =   [
                                        '20 - Pete',
                                        '21 - Piers',
                                        '22 - Gareth',
                                        '23 - M25 Wednesday',
                                        '24 - Thursday Route',
                                        '25 - Friday Route',
                                        'TBC'
                                    ];

        $picklistscollection = PickList::select('assigned_to')->distinct()->get();
        $sheets = [];

        // foreach ($picklistscollection as $picklistsolo) {
        //
        //     // Each distinct assigned_to (route) calls the PicklistCollection Class below.
        //     $sheets[] = new PicklistCollection($picklistsolo->assigned_to, $this->week_starting);
        // }
        foreach ($correctOrderWedThurFri as $picklistsolo) {

            // Each distinct assigned_to (route) calls the PicklistCollection Class below.
            $sheets[] = new PicklistCollection($picklistsolo, $this->week_starting);
        }

        return $sheets;
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
