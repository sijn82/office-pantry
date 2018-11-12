<?php

namespace App\Http\Controllers\Exports;

use Session;
use Illuminate\Support\Facades\Log;
use App\WeekStart;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use App\Http\Controllers\Exports;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SnackboxDPDSingleCompanyExport
 // implements WithMultipleSheets
  implements FromView, WithEvents, ShouldAutoSize, WithTitle //, WithMultipleSheets
{
    public function view(): View
   {
       $product_list = session()->get('snackbox_product_list');
       $snackboxes = session()->get('snackbox_DPD_singlecompany');
       // dd($snackbox[0][0]);
       
       if ($snackboxes == 'None for this week') {
               
               Log::channel('slack')->info('Snackbox DPD Single Company - None for this week');
               return view('none-for-this-week');
          
       } else {
                 
           return view('exports.snackboxes-single-company', [
               'chunks'         =>   $snackboxes,
               'product_list'   =>   $product_list
           ]);
       }
   }
   
   public function title(): string
   {
       return 'DPD SingleCompany';
   }
   

      /**
       * @return array
       */
      public function registerEvents(): array
      {

          return [
              AfterSheet::class    => function(AfterSheet $event) {

                  $cellRange = 'A1:G1'; // All headers
                  $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(18);
                  $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                  $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
                  $event->sheet->getDelegate()->getPageSetup()->setOrientation('landscape');
                  $event->sheet->getDelegate()->getPageSetup()->setPaperSize('A3');

                  // Set Cell Range Variables
                  $cellRangeHeader    = 'A1:C1';  // Week Start & Delivery Day Titles (First Row)
                  $cellRangeSub       = 'A2:C2'; // Week Start & Delivery Day Values (Second Row)
                  $tableHeaders       = 'A5:G5'; // Table Headers of Route Name / Company Route (Picklist Box Name) / No. Of Berries.

                  // These two variables find the highest column & row populated with data.
                  // With this information I can dynamically style the tables.
                  $highestRow = $event->sheet->getHighestRow();
                  $rowWidth = $event->sheet->getHighestColumn();

                  // As we know the tables start at A5 we can hardcode that part of the equation, $rowWidth and $highestRow determine the other coordinate dynamically.
                  $selectedCells = 'A5' . ':' . $rowWidth . $highestRow;


                    foreach ($event->sheet->getRowIterator() as $row) {

                        $cellIterator = $row->getCellIterator();
                        // dd($cellIterator);
                        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                        $selectedRow = $row->getRowIndex();
                        $chosenCells = 'A' . $selectedRow . ':D' . $selectedRow;
                        $fullCellRange = 'A' . $selectedRow . ':E' . $selectedRow;
                        $centeredCellRange =  'B' . $selectedRow . ':E' . $selectedRow;
                        $middleCells = ['B' . $selectedRow, 'C' . $selectedRow, 'D' . $selectedRow];
                        // dd($chosenCells);

                        $chosenCellsArray = $event->sheet->getDelegate()
                            ->rangeToArray(
                                $chosenCells,     // The worksheet range that we want to retrieve
                                NULL,        // Value that should be returned for empty cells
                                TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                                TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                                TRUE         // Should the array be indexed by cell row and cell column
                            );
                            // if the value for Column A is 'Product Name' then we're styling a header row.
                            if ($event->sheet->getDelegate()->getCell('A' . $selectedRow)->getValue() == 'Product Name')
                            {
                                $event->sheet->getDelegate()->getRowDimension($selectedRow)->setRowHeight(80);
                                $event->sheet->getDelegate()->getStyle($centeredCellRange)->getAlignment()->setHorizontal('center');
                                $event->sheet->getDelegate()->getStyle($fullCellRange)->getAlignment()->setVertical('center');
                                $event->sheet->getDelegate()->getStyle($fullCellRange)->getFont()->setSize(36);
                            }
                            // if the value for Column A is 'Packed By' then we're styling a nother uniquely displayed row.
                            if ($event->sheet->getDelegate()->getCell('B' . $selectedRow)->getValue() == 'Packed By: .....................')
                            {
                                $event->sheet->getDelegate()->getRowDimension($selectedRow)->setRowHeight(80);
                                $event->sheet->getDelegate()->getStyle($fullCellRange)->getFont()->setSize(36);

                                $event->sheet->getDelegate()->getRowDimension($selectedRow - 1)->setRowHeight(80);
                                $event->sheet->getDelegate()->getStyle('A' . ($selectedRow - 1))->getFont()->setSize(36);
                                $event->sheet->getDelegate()->getStyle('A' . ($selectedRow - 1 . ':' . 'A' . $selectedRow))->getAlignment()->setHorizontal('center');
                                $event->sheet->getDelegate()->getStyle('A' . ($selectedRow - 1 . ':' . 'A' . $selectedRow))->getAlignment()->setVertical('center');

                                // Look in this cell for delivered_by info, based on which of the 3 values it is, style the cell accordingly.  P.s Finally had a use case for a switch statement over if/else!
                                $delivered_by = $event->sheet->getDelegate()->getCell('A' . $selectedRow)->getValue();
                                // dd($delivered_by);
                                switch ($delivered_by) {

                                    case "OP":
                                    $event->sheet->styleCells(
                                          'A' . $selectedRow, // Cell Range
                                          [ // Styles Array
                                              'fill' => [
                                                          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                          'color' => [
                                                            'argb' => 'e619e5' // desaturated fuchsia
                                                          ]
                                              ] // end of fill
                                          ] // end of styles array
                                      ); // end of styleCells function parameters.
                                      break;
                                      case "DPD":
                                      $event->sheet->styleCells(
                                            'A' . $selectedRow, // Cell Range
                                            [ // Styles Array
                                                'fill' => [
                                                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                            'color' => [
                                                              'argb' => '0b0f54' // desaturated navy
                                                            ]
                                                ] // end of fill
                                            ] // end of styles array
                                        ); // end of styleCells function parameters.
                                        break;
                                        case "APC":
                                        $event->sheet->styleCells(
                                              'A' . $selectedRow, // Cell Range
                                              [ // Styles Array
                                                  'fill' => [
                                                              'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                              'color' => [
                                                                'argb' => '7fffd4' // aquamarine
                                                              ]
                                                  ] // end of fill
                                              ] // end of styles array
                                          ); // end of styleCells function parameters.
                                          break;
                                }
                            }
                            // if it's neither of those and not an empty row, then it's a snack/drink item and company order; now it's time for some additional conditional styling.
                             if ( $event->sheet->getDelegate()->getCell('A' . $selectedRow)->getValue() != NULL
                               && $event->sheet->getDelegate()->getCell('B' . $selectedRow)->getValue() != 'Packed By: .....................'
                               && $event->sheet->getDelegate()->getCell('B' . ($selectedRow + 1))->getValue() != 'Packed By: .....................'
                               && $event->sheet->getDelegate()->getCell('A' . $selectedRow)->getValue() != 'Product Name'
                               && $selectedRow > 1)

                             {
                                 $event->sheet->getDelegate()->getStyle($centeredCellRange)->getAlignment()->setHorizontal('center');
                                 $event->sheet->getDelegate()->getStyle($fullCellRange)->getFont()->setSize(24);
                                 $event->sheet->getDelegate()->getRowDimension($fullCellRange)->setRowHeight(40);

                                 // This foreach will create the grey lines defining/seperating the columns.
                                 foreach ($middleCells as $selectedCell) {
                                         // dd($selectedCell);
                                         $event->sheet->styleCells(
                                               $selectedCell, // Cell Range
                                               [ // Styles Array
                                                   'borders' => [
                                                       'left' => [
                                                           'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                                           'color' => ['argb' => '959595'],
                                                       ],
                                                       'right' => [
                                                           'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                                           'color' => ['argb' => '959595'],
                                                       ],
                                                   ], // end of borders
                                               ] // end of styles array
                                           ); // end of styleCells function parameters.
                                 }
                                 // if the row is an even row, and so can be divided by 2 without remainders.
                                 if ($selectedRow % 5 == 0) {
                                     $event->sheet->styleCells(
                                           $chosenCells, // Cell Range
                                           [ // Styles Array
                                               'fill' => [
                                                           'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                           'color' => [
                                                             // 'argb' => 'ffd240'
                                                             'argb' => 'd5deff'
                                                         ]
                                               ] // end of fill
                                           ] // end of styles array
                                       ); // end of styleCells function parameters.
                               // if the row is odd and can be divided by 5 without remainders.

                                 } elseif ($selectedRow % 2 == 0) {
                                       $event->sheet->styleCells(
                                             $chosenCells, // Cell Range
                                             [ // Styles Array
                                                 'fill' => [
                                                             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                             'color' => [
                                                               'argb' => 'f5ddee'
                                                             ]
                                                 ] // end of fill
                                             ] // end of styles array
                                         ); // end of styleCells function parameters.
                                 // if the row is an even row, and so can be divided by 10 without remainders but not 5.

                               } else {
                                         $event->sheet->styleCells(
                                               $chosenCells, // Cell Range
                                               [ // Styles Array
                                                   'fill' => [
                                                               'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                               'color' => [
                                                                 'argb' => 'b5eac5'
                                                               ]
                                                   ] // end of fill
                                               ] // end of styles array
                                           ); // end of styleCells function parameters.
                                 }

                             }

                    }
              },
          ];
      }

   }
