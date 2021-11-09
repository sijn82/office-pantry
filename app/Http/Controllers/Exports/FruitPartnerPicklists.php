<?php

namespace App\Http\Controllers\Exports;
ini_set('max_execution_time', 180); //3 minutes

//----- Copied from another export function for speed but I should clean up and remove any not actually used -----//

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

//----- End of Maatwebsite classes -----//

use App\FruitPartner;
use App\WeekStart;
use App\FruitBox;
use App\MilkBox;
use App\CompanyDetails;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonImmutable;

class FruitPartnerPicklists implements
WithMultipleSheets
{
    public $timeout = 60;

    public function __construct($orders)
    {
        $this->fruitboxes = $orders->fruitboxes;
        $this->milkboxes = $orders->milkboxes;
    }

    public function sheets(): array
    {
        //----- Combined Details Tab -----//

        if (!empty($this->fruitboxes) || !empty($this->milkboxes)) {
            $sheets[] = new FruitPartnerCombinedDetails($this->fruitboxes, $this->milkboxes);
        } else {
            $sheets[] = 'Nothing to deliver!';
        }

        //----- End of Combined Details Tab -----//

        //----- Fruitbox Picklists Tab -----//

        if (!empty($this->fruitboxes)) {
            // $this->fruitboxes looks like - [ Array Fruit Partner { Object Collection [ Fruitboxes { Fruitbox Attributes }]}] -
            //dd($this->fruitboxes);
            //$byDay = $this->fruitboxes->orderBy('delivery_day');

            // Just using this foreach to move into the fruitbox data, rather than the outer array, so I can group the fruitboxes by day.
            foreach ($this->fruitboxes as $fruitboxes) {
                // This is fine at sorting by day, however we need to order the groups by mon - fri, not a - z (fri - wed) - why isn't this a built in function?!

                $monToFri = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

                $ordersByMonToFri = $fruitboxes->sortBy( function ($fruitboxes) use ($monToFri) {
                    return array_search($fruitboxes->delivery_day, $monToFri);
                });
            }

            $sheets[] = new FruitPartnerFruitOrders($ordersByMonToFri);

        } else {
            $sheets[] = 'No fruit for this week.';
        }

        //----- End of Fruitbox Picklists Tab -----//

        //----- Milk is sufficiently handled by the combined class (of fruitbox total , milk breakdown and delivery details) -----//
        //----- So we're going to scrap it for now -----//

        // if (!empty($this->milkboxes)) {
        //
        //     foreach ($this->milkboxes as $milkboxes) {
        //         $byDay = $milkboxes->groupBy('delivery_day');
        //     }
        //     foreach ($byDay as $key => $day) {
        //         $sheets[] = new FruitPartnerMilkOrders($key, $day);
        //     }
        //
        // } else {
        //     $sheets[] = 'No milk for this week.';
        // }

        //----- End of milk logic and call to FruitPartnerMilkOrders -----//

        // Don't forget to return $sheets! ... again.
        return $sheets;

    } // End of public function sheets()

} // End of FruitPartnerPicklists class

class FruitPartnerFruitOrders implements
FromView,
WithTitle,
ShouldAutoSize,
WithEvents
{
    public function __construct($fruitpartner_fruitboxes)
    {
        //dd($key);
        //$this->key = $key;
        $this->fruitpartner_fruitboxes = $fruitpartner_fruitboxes;
        //dd($this->fruitpartner_fruitboxes);
    }

    public function view(): View
    {
        // Let's set some variables to generate a running total for the export.
        $deliciously_red_apples_running_total = 0;
        $pink_lady_apples_running_total = 0;
        $red_apples_running_total = 0;
        $green_apples_running_total = 0;
        $satsumas_running_total = 0;

        $pears_running_total = 0;
        $bananas_running_total = 0;
        $nectarines_running_total = 0;
        $limes_running_total = 0;
        $lemons_running_total = 0;

        $grapes_running_total = 0;
        $seasonal_berries_running_total = 0;
        $oranges_running_total = 0;
        $cucumbers_running_total = 0;
        $mint_running_total = 0;

        $organic_lemons_running_total = 0;
        $kiwis_running_total = 0;
        $grapefruits_running_total = 0;
        $avocados_running_total = 0;
        $root_ginger_running_total = 0;
        $order_changes = false;


        $now = CarbonImmutable::now();
        $last_week = $now->subWeek();


         // this is somewhat silly, I injected the fruitpartner's name as a key earlier, now I'm essentially just stripping it out.
         // It does mean I have it accessible for display, rather than using the id but once I know exactly what I'm doing with it, I may revise this.
         foreach ($this->fruitpartner_fruitboxes as $key => $fruitboxes) {
            // dd($fruitboxes);
            // foreach ($fruitboxes as $fruitbox) {

                 $company = CompanyDetails::find($fruitboxes->company_details_id);

                  //dd($company);

                 // This (invoice_name) may not be the best name to use as it could be the same for a couple of offices that share an umbrella payment company
                 // $company->invoice_name
                 // Route name could suffer the same fate, however I think in practice this will be more flexible as it's not used by xero, so could be more easily fudged.
                 $fruitboxes->company_name = $company->route_name;

                 //----- This is probably the best place to pull in the change information if I'm keeping it in it's own table -----//
                 //----- However I think it will be quicker just to add two new columns to the boxes, then the information is already stored where I need it -----//

            // }

            $deliciously_red_apples_running_total += ($fruitboxes->deliciously_red_apples * $fruitboxes->fruitbox_total);
            $pink_lady_apples_running_total += ($fruitboxes->pink_lady_apples * $fruitboxes->fruitbox_total);
            $red_apples_running_total += ($fruitboxes->red_apples * $fruitboxes->fruitbox_total);
            $green_apples_running_total += ($fruitboxes->green_apples * $fruitboxes->fruitbox_total);
            $satsumas_running_total += ($fruitboxes->satsumas * $fruitboxes->fruitbox_total);

            $pears_running_total += ($fruitboxes->pears * $fruitboxes->fruitbox_total);
            $bananas_running_total += ($fruitboxes->bananas * $fruitboxes->fruitbox_total);
            $nectarines_running_total += ($fruitboxes->nectarines * $fruitboxes->fruitbox_total);
            $limes_running_total += ($fruitboxes->limes * $fruitboxes->fruitbox_total);
            $lemons_running_total += ($fruitboxes->lemons * $fruitboxes->fruitbox_total);

            $grapes_running_total += ($fruitboxes->grapes * $fruitboxes->fruitbox_total);
            $seasonal_berries_running_total += ($fruitboxes->seasonal_berries * $fruitboxes->fruitbox_total);
            $oranges_running_total += ($fruitboxes->oranges * $fruitboxes->fruitbox_total);
            $cucumbers_running_total += ($fruitboxes->cucumbers * $fruitboxes->fruitbox_total);
            $mint_running_total += ($fruitboxes->mint * $fruitboxes->fruitbox_total);

            $organic_lemons_running_total += ($fruitboxes->organic_lemons * $fruitboxes->fruitbox_total);
            $kiwis_running_total += ($fruitboxes->kiwis * $fruitboxes->fruitbox_total);
            $grapefruits_running_total += ($fruitboxes->grapefruits * $fruitboxes->fruitbox_total);
            $avocados_running_total += ($fruitboxes->avocados * $fruitboxes->fruitbox_total);
            $root_ginger_running_total += ($fruitboxes->root_ginger * $fruitboxes->fruitbox_total);

            // Instead of just checking a week back for all order changes, for some we can tap into their frequency.
            // However if orders are only updated in the week building up to their delivery,
            // this will just add unnecessary complexity to the check.
            // -- For now, i'm going to leave it and see what happens in use.

            if ($fruitboxes->date_changed > $last_week ) {
                $order_changes = true;
            }
         }

         // See above and below for the new solution - it appears to work fine now. Fingers crossed.
         $deliciously_red_apples_total = $deliciously_red_apples_running_total;
         $pink_lady_apples_total = $pink_lady_apples_running_total;
         $red_apples_total = $red_apples_running_total;
         $green_apples_total = $green_apples_running_total;
         $satsumas_total = $satsumas_running_total;
         $pears_total = $pears_running_total;
         $bananas_total = $bananas_running_total;
         $nectarines_total = $nectarines_running_total;
         $limes_total = $limes_running_total;
         $lemons_total = $lemons_running_total;
         $grapes_total = $grapes_running_total;
         $seasonal_berries_total = $seasonal_berries_running_total;
         $oranges_total = $oranges_running_total;
         $cucumbers_total = $cucumbers_running_total;
         $mint_total = $mint_running_total;
         $organic_lemons_total = $organic_lemons_running_total;
         $kiwis_total = $kiwis_running_total;
         $grapefruits_total = $grapefruits_running_total;
         $avocados_total = $avocados_running_total;
         $root_ginger_total = $root_ginger_running_total;

        return view('exports.fruitpartner-fruitbox-picklists', [
            'picklists' => $this->fruitpartner_fruitboxes,
            'deliciously_red_apples_total' => $deliciously_red_apples_total,
            'pink_lady_apples_total' => $pink_lady_apples_total,
            'red_apples_total' => $red_apples_total,
            'green_apples_total' => $green_apples_total,
            'satsumas_total' => $satsumas_total,
            'pears_total' => $pears_total,
            'bananas_total' => $bananas_total,
            'nectarines_total' => $nectarines_total,
            'limes_total' => $limes_total,
            'lemons_total' => $lemons_total,
            'grapes_total' => $grapes_total,
            'seasonal_berries_total' => $seasonal_berries_total,
            'oranges_total' => $oranges_total,
            'cucumbers_total' => $cucumbers_total,
            'mint_total' => $mint_total,
            'organic_lemons_total' => $organic_lemons_total,
            'kiwis_total' => $kiwis_total,
            'grapefruits_total' => $grapefruits_total,
            'avocados_total' => $avocados_total,
            'root_ginger_total' => $root_ginger_total,
            'order_changes' => $order_changes, // is this just to check if we need the 'Updated This Week?' column? Need to check. <-- Yep, it is. Good!
        ]);
    }

    public function title(): string
    {
        if ($this->fruitpartner_fruitboxes == 'No fruit for this week.') {
            return 'No Fruit Orders This Week';
        } else {
            return 'Fruit Orders';
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // dd($event);
                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getColumnDimension('D')->setWidth(50);
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setVertical('center');

                $totals_border = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ],
                ];
                // dd($rowWidth . $highestRow);
                $event->sheet->getDelegate()->getStyle('A' . $highestRow . ':' . $rowWidth . $highestRow)->applyFromArray($totals_border);

                $columns = [
                    'A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                     'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];

                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                foreach ($event->sheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $selectedRow = $row->getRowIndex();
                    $previousRow = ($row->getRowIndex() - 1);
                    //
                    $event->sheet->getStyle('C' . $selectedRow . ':D' . $selectedRow)->getAlignment()->setWrapText(true);

                    //----- If 'Updated' column exists and this row has undated written in the row, make the company name yellow. -----//

                        if ($event->sheet->getCell($rowWidth . $selectedRow) == 'Yes') {

                            $companyName = 'A' . $selectedRow;

                            $event->sheet->styleCells(
                                $companyName,
                                [
                                    'fill' =>   [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                    'argb' => 'ffff66'
                                                    ]
                                                ]
                                ]);
                        }

                    //----- End of if update column exists -----//

                    //----- Potential to apply a border to seperate the orders by week day -----//

                        // This might be a good place to check if the value of the current row held in the cell of column 'b',
                        // is different to the value held in the same cell of the row above.  This might allow is to apply a top border to seperate the week days?

                        $previousRowData = $event->sheet->getCell('B' . $previousRow);
                        $currentRowData = $event->sheet->getCell('B' . $selectedRow);

                        if ($currentRowData->getValue() !== $previousRowData->getValue() && $selectedRow > 2 ) {
                            // dd($row);
                            $selectedRowCoordinates = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow;

                            $event->sheet->styleCells(
                              $selectedRowCoordinates, // Cell Range
                              [ // Styles Array
                                  'borders' => [
                                      'top' => [
                                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                          'color' => ['argb' => '000'],
                                      ],
                                  ], // end of borders

                              ] // end of styles array
                          ); // end of styleCells function parameters.
                        }

                    //----- End of Potential to apply a border to seperate the orders by week day -----//


                    foreach ($cellIterator as $cell) {
                        if ($cell !== null) {

                            if ($cell == 'Company Name') {

                                $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                                // $selectedRow = $row->getRowIndex();
                                $event->sheet->styleCells(
                                  $selectedCell, // Cell Range
                                  [ // Styles Array
                                      // 'borders' => [
                                      //     'bottom' => [
                                      //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                      //         'color' => ['argb' => '000'],
                                      //     ],
                                      // ], // end of borders
                                      'font' => [
                                          'color' => [
                                              'argb' => 'ffffff'
                                          ]
                                      ],
                                      // With no guarantee about colour printers, I'm making this black and white friendly.
                                      'fill' => [
                                                  'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                  'color' => [
                                                    'argb' => '000000'
                                                  ]
                                      ] // end of fill
                                      // With no guarantee about colour printers, I'm making this black and white friendly.
                                      // 'fill' => [
                                      //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                      //             'color' => [
                                      //               'argb' => '93DA38'
                                      //             ]
                                      // ] // end of fill
                                  ] // end of styles array
                              ); // end of styleCells function parameters.
                            } // end of if ($cell == 'Company Name')

                        } else {
                            continue;
                        }
                    } // foreach ($cellIterator as $cell)
                } // end of foreach ($event->sheet->getRowIterator() as $row)

                // This is nice but the increase in font size also increases the column width, which may be unwanted?
                $cellRange = 'A1:AA1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            }, // End of AfterSheet class function
        ];
    }
}

class FruitPartnerMilkOrders implements
FromView,
WithTitle,
ShouldAutoSize,
WithEvents
{
    public function __construct($key, $fruitpartner_milkboxes)
    {
        $this->key = $key;
        $this->fruitpartner_milkboxes = $fruitpartner_milkboxes;
    }

    public function view(): View
    {
        foreach ($this->fruitpartner_milkboxes as $key => $milkboxes) {
            //foreach ($milkboxes as $milkbox) {
                $company = CompanyDetails::find($milkboxes->company_details_id);
                // This (invoice_name) may not be the best name to use as it could be the same for a couple of offices that share an umbrella payment company
                // $company->invoice_name
                // Route name could suffer the same fate, however I think in practice this will be more flexible as it's not used by xero, so could be more easily fudged.
                $milkboxes->company_name = $company->route_name;
            //}
        }

        // Generate totals prior to going into template, which allows us to omit columns that would otherwise total 0.
        // Regular Milk
        $semi_skimmed_2l_total = $this->fruitpartner_milkboxes->pluck('semi_skimmed_2l')->sum();
        $skimmed_2l_total = $this->fruitpartner_milkboxes->pluck('skimmed_2l')->sum();
        $whole_2l_total = $this->fruitpartner_milkboxes->pluck('whole_2l')->sum();
        $semi_skimmed_1l_total = $this->fruitpartner_milkboxes->pluck('semi_skimmed_1l')->sum();
        $skimmed_1l_total = $this->fruitpartner_milkboxes->pluck('skimmed_1l')->sum();
        $whole_1l_total = $this->fruitpartner_milkboxes->pluck('whole_1l')->sum();
        // Organic Milk
        $organic_semi_skimmed_2l_total = $this->fruitpartner_milkboxes->pluck('organic_semi_skimmed_2l')->sum();
        $organic_skimmed_2l_total = $this->fruitpartner_milkboxes->pluck('organic_skimmed_2l')->sum();
        $organic_whole_2l_total = $this->fruitpartner_milkboxes->pluck('organic_whole_2l')->sum();
        $organic_semi_skimmed_1l_total = $this->fruitpartner_milkboxes->pluck('organic_semi_skimmed_1l')->sum();
        $organic_skimmed_1l_total = $this->fruitpartner_milkboxes->pluck('organic_skimmed_1l')->sum();
        $organic_whole_1l_total = $this->fruitpartner_milkboxes->pluck('organic_whole_1l')->sum();
        // Alternative Milk
        $alt_coconut_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_coconut')->sum();
        $alt_unsweetened_almond_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_unsweetened_almond')->sum();
        $alt_almond_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_almond')->sum();
        $alt_unsweetened_soya_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_unsweetened_soya')->sum();
        $alt_soya_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_soya')->sum();
        $alt_oat_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_oat')->sum();
        $alt_rice_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_rice')->sum();
        $alt_cashew_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_cashew')->sum();
        $alt_lactose_free_semi_total = $this->fruitpartner_milkboxes->pluck('milk_1l_alt_lactose_free_semi')->sum();

        return view('exports.fruitpartner-milkbox-picklists', [
            'picklists' => $this->fruitpartner_milkboxes,
            'semi_skimmed_2l_total' => $semi_skimmed_2l_total,
            'skimmed_2l_total' => $skimmed_2l_total,
            'whole_2l_total' => $whole_2l_total,
            'semi_skimmed_1l_total' => $semi_skimmed_1l_total,
            'skimmed_1l_total' => $skimmed_1l_total,
            'organic_whole_1l_total' => $organic_whole_1l_total,
            'organic_semi_skimmed_2l_total' => $organic_semi_skimmed_2l_total,
            'organic_skimmed_2l_total' => $organic_skimmed_2l_total,
            'organic_whole_2l_total' => $organic_whole_2l_total,
            'organic_semi_skimmed_1l_total' => $organic_semi_skimmed_1l_total,
            'organic_skimmed_1l_total' => $organic_skimmed_1l_total,
            'whole_1l_total' => $whole_1l_total,
            'alt_coconut_total' => $alt_coconut_total,
            'alt_unsweetened_almond_total' => $alt_unsweetened_almond_total,
            'alt_almond_total' => $alt_almond_total,
            'alt_unsweetened_soya_total' => $alt_unsweetened_soya_total,
            'alt_soya_total' => $alt_soya_total,
            'alt_oat_total' => $alt_oat_total,
            'alt_rice_total' => $alt_rice_total,
            'alt_cashew_total' => $alt_cashew_total,
            'alt_lactose_free_semi_total' => $alt_lactose_free_semi_total,
        ]);
    }


    public function title(): string
    {
        return 'Milk Orders -' . $this->key;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // dd($event);
                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getColumnDimension('D')->setWidth(50);
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setVertical('center');

                $totals_border = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ],
                ];
                // dd($rowWidth . $highestRow);
                $event->sheet->getDelegate()->getStyle('A' . $highestRow . ':' . $rowWidth . $highestRow)->applyFromArray($totals_border);

                // Not sure why the columns array goes white from 'V' onwards, it doesn't seem to affect the excel results.
                $columns = [
                    'A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                     'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];

                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                foreach ($event->sheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $selectedRow = $row->getRowIndex();


                    //
                    $event->sheet->getStyle('C' . $selectedRow . ':D' . $selectedRow)->getAlignment()->setWrapText(true);
                    foreach ($cellIterator as $cell) {
                        if ($cell !== null) {

                            if ($cell == 'Company Name') {

                                $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                                // $selectedRow = $row->getRowIndex();
                                $event->sheet->styleCells(
                                  $selectedCell, // Cell Range
                                  [ // Styles Array
                                      'borders' => [
                                          'bottom' => [
                                              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                              'color' => ['argb' => '000'],
                                          ],
                                      ], // end of borders
                                      // With no guarantee about colour printers, I'm making this black and white friendly.
                                      // 'fill' => [
                                      //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                      //             'color' => [
                                      //               'argb' => '93DA38'
                                      //             ]
                                      // ] // end of fill
                                  ] // end of styles array
                              ); // end of styleCells function parameters.
                            } // end of if ($cell == 'Company Name')

                        } else {
                            continue;
                        }
                    } // foreach ($cellIterator as $cell)
                } // end of foreach ($event->sheet->getRowIterator() as $row)

                // This is nice but the increase in font size also increases the column width, which may be unwanted?
                $cellRange = 'A1:AA1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            }, // End of AfterSheet class function
        ];
    }
}

class FruitPartnerCombinedDetails implements
FromView,
WithTitle,
// ShouldAutoSize,
WithEvents
{
    public function __construct($fruitboxes, $milkboxes)
    {
        // Maybe I should change the name of these as they are passed into the Class, as I want to be able differentiate between the $fruitboxes described here and those in the foreach statements.
        // It may not make much difference to how the function runs but it would at least be clearer.
        // EDIT: HUH, JUST USED '$milkboxes' BEFORE DECLARING THEM IN THE FOREACH AND IT CAME BACK UNDEFINED
        // - at least I now know, '$fruitboxes' & '$milkboxes' passed into the construct are stuck here.
        $this->fruitboxes = $fruitboxes;
        $this->milkboxes = $milkboxes;
    }

    public function view(): View
    {
        // Create a new object which will get populated one way or another depending on whether it contains fruit/milk or both.
        // $delivery_entry = new \stdClass;

        // We only really need to set this once, so let's keep it out of the loop.
        $now = CarbonImmutable::now();
        $last_week = $now->subWeek();
        $week_start = WeekStart::first();
        // Hmmmn, do I want to do a group check, sending '$order_changes = true' if any are updated, or check them on an individual basis in the template?
        $order_changes = false;

        if (!empty($this->fruitboxes)) {
            foreach ($this->fruitboxes as $key => $fruitboxes) {
                // dd($fruitboxes);
                foreach ($fruitboxes as $fruitbox) {

                    // Each time we process a new order we need to redeclare the variable.
                    $delivery_entry = new \stdClass;
                    // dd($fruitbox);
                    $company_details = CompanyDetails::find($fruitbox->company_details_id);

                    //
                    $fruitbox->company_details_id = $company_details->id;
                    $fruitbox->company_details_route_name = $company_details->route_name;
                    $fruitbox->company_details_delivery_information = $company_details->delivery_information;
                    $fruitbox->company_details_postcode = $company_details->route_postcode;
                    // This approach will grab all populated address lines, ignoring any empty (null) fields (array_filter)
                    // and separating (implode) them with a comma.
                    // I need to upgrade other sections using the summary address with this approach.
                    $fruitbox->company_details_summary_address = implode(", ", array_filter([
                            $company_details->route_address_line_1,
                            $company_details->route_address_line_2,
                            $company_details->route_address_line_3,
                            $company_details->route_city,
                            $company_details->route_region
                        ]));

                    // dd($fruitbox);

                    // I could do something here, instead of adding a new foreach clause stipulating the same thing.

                    //----- Moved logic up to here to handle all fruit & milk and just fruit deliveries -----//
                    $milkboxes = isset($this->milkboxes[$key]) ? $this->milkboxes[$key] : null; // This is now throwing an error, not sure why but maybe this will bypass it 09/11/2021 
                    // dump($milkboxes);
                    $milkboxes ? $additional_milk = $milkboxes->where('next_delivery', $week_start->current)->where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)->first() : $additional_milk = $milkboxes;
                    // $additional_milk = $milkboxes->where('company_details_id', $fruitbox->company_details_id)->where('delivery_day', $fruitbox->delivery_day)->first();
                    // dd($additional_milk);

                    if (!empty($additional_milk)) {


                        // Then if the check is working properly, we have a corresponding milkbox entry to add to the delivery route.
                        // First let's grab the delivery data pushed into the fruitbox (this could equally be taken from the milkbox data)
                        $delivery_entry->company_details_id = $fruitbox->company_details_id;
                        $delivery_entry->company_details_route_name = $fruitbox->company_details_route_name;
                        $delivery_entry->company_details_delivery_information = $fruitbox->company_details_delivery_information;
                        $delivery_entry->company_details_postcode = $fruitbox->company_details_postcode;
                        $delivery_entry->company_details_summary_address = $fruitbox->company_details_summary_address;
                        $delivery_entry->delivery_day = $fruitbox->delivery_day;
                        $delivery_entry->fruitbox_total = $fruitbox->fruitbox_total;
                        // Now let's grab all the milk related data from the $additional_milk entry.
                        // Regular Milk Order
                        $delivery_entry->semi_skimmed_2l = $additional_milk->semi_skimmed_2l;
                        $delivery_entry->skimmed_2l = $additional_milk->skimmed_2l;
                        $delivery_entry->whole_2l = $additional_milk->whole_2l;
                        $delivery_entry->semi_skimmed_1l = $additional_milk->semi_skimmed_1l;
                        $delivery_entry->skimmed_1l = $additional_milk->skimmed_1l;
                        $delivery_entry->whole_1l = $additional_milk->whole_1l;
                        // Organic Milk
                        $delivery_entry->organic_semi_skimmed_1l = $additional_milk->organic_semi_skimmed_1l;
                        $delivery_entry->organic_skimmed_1l = $additional_milk->organic_skimmed_1l;
                        $delivery_entry->organic_whole_1l = $additional_milk->organic_whole_1l;
                        $delivery_entry->organic_semi_skimmed_2l = $additional_milk->organic_semi_skimmed_2l;
                        $delivery_entry->organic_skimmed_2l = $additional_milk->organic_skimmed_2l;
                        $delivery_entry->organic_whole_2l = $additional_milk->organic_whole_2l;
                        // Milk Alternatives
                        // Pt 1
                        $delivery_entry->milk_1l_alt_coconut = $additional_milk->milk_1l_alt_coconut;
                        $delivery_entry->milk_1l_alt_unsweetened_almond = $additional_milk->milk_1l_alt_unsweetened_almond;
                        $delivery_entry->milk_1l_alt_almond = $additional_milk->milk_1l_alt_almond;
                        // Pt 2
                        $delivery_entry->milk_1l_alt_unsweetened_soya = $additional_milk->milk_1l_alt_unsweetened_soya;
                        $delivery_entry->milk_1l_alt_soya = $additional_milk->milk_1l_alt_soya;
                        $delivery_entry->milk_1l_alt_oat = $additional_milk->milk_1l_alt_oat;
                        // Pt 3
                        $delivery_entry->milk_1l_alt_rice = $additional_milk->milk_1l_alt_rice;
                        $delivery_entry->milk_1l_alt_cashew = $additional_milk->milk_1l_alt_cashew;
                        $delivery_entry->milk_1l_alt_lactose_free_semi = $additional_milk->milk_1l_alt_lactose_free_semi;

                        $delivery_entry->order_changes = false; // <-- Before we check for changes we need to set this in case their aren't any.

                        //----- We need to check for milk and fruit order updates plus company detail changes -----//

                        if ($fruitbox->date_changed > $last_week) {
                            // dd($fruitbox); //
                            // Individual check
                            // $delivery_entry->fruit_changes = true; <-- why over complicate things? Let's leave this for now.
                            $delivery_entry->order_changes = true;
                            // General check
                            $order_changes = true;
                        }

                        if ($additional_milk->date_changed > $last_week) {
                            // Individual check
                            // $delivery_entry->milk_changes = true; <-- why over complicate things? Let's leave this for now.
                            $delivery_entry->order_changes = true;
                            // General check
                            $order_changes = true;
                        }

                        if ($company_details->date_changed > $last_week) {
                            // Individual check
                            // $delivery_details->company_details_changes = true; <-- why over complicate things? Let's leave this for now.
                            $delivery_entry->order_changes = true;
                            // General check
                            $order_changes = true;
                        }

                        // Additonal debugging property.
                        $delivery_entry->status = 'Includes additional milk';

                    } else {
                        // Then this hopefully means we have a fruit only delivery.
                        $delivery_entry->company_details_id = $fruitbox->company_details_id;
                        $delivery_entry->company_details_route_name = $fruitbox->company_details_route_name;
                        $delivery_entry->company_details_delivery_information = $fruitbox->company_details_delivery_information;
                        $delivery_entry->company_details_postcode = $fruitbox->company_details_postcode;
                        $delivery_entry->company_details_summary_address = $fruitbox->company_details_summary_address;
                        $delivery_entry->delivery_day = $fruitbox->delivery_day;
                        $delivery_entry->fruitbox_total = $fruitbox->fruitbox_total;
                        // As there isn't any additional milk to add to this order we need to set the values as 0.
                        // Regular Milk
                        $delivery_entry->semi_skimmed_2l = 0;
                        $delivery_entry->skimmed_2l = 0;
                        $delivery_entry->whole_2l = 0;
                        $delivery_entry->semi_skimmed_1l = 0;
                        $delivery_entry->skimmed_1l = 0;
                        $delivery_entry->whole_1l = 0;
                        // Organic Milk
                        $delivery_entry->organic_semi_skimmed_1l = 0;
                        $delivery_entry->organic_skimmed_1l = 0;
                        $delivery_entry->organic_whole_1l = 0;
                        $delivery_entry->organic_semi_skimmed_2l = 0;
                        $delivery_entry->organic_skimmed_2l = 0;
                        $delivery_entry->organic_whole_2l = 0;
                        // Alternative Milk
                        // Pt 1
                        $delivery_entry->milk_1l_alt_coconut = 0;
                        $delivery_entry->milk_1l_alt_unsweetened_almond = 0;
                        $delivery_entry->milk_1l_alt_almond = 0;
                        // Pt 2
                        $delivery_entry->milk_1l_alt_unsweetened_soya = 0;
                        $delivery_entry->milk_1l_alt_soya = 0;
                        $delivery_entry->milk_1l_alt_oat = 0;
                        // Pt 3
                        $delivery_entry->milk_1l_alt_rice = 0;
                        $delivery_entry->milk_1l_alt_cashew = 0;
                        $delivery_entry->milk_1l_alt_lactose_free_semi = 0;

                        $delivery_entry->order_changes = false; // <-- Before we check for changes we need to set this in case their aren't any.

                        //----- No milk order, still need to check for fruit and company detail changes though -----//

                            if ($fruitbox->date_changed > $last_week) {
                                // dd($fruitbox);
                                // Individual check
                                // $delivery_entry->fruit_changes = true; <-- Ignoring this for now.
                                $delivery_entry->order_changes = true;
                                // General check
                                $order_changes = true;
                            }

                            if ($company_details->date_changed > $last_week) {
                                // Individual check
                                // $delivery_details->company_details_changes = true; <-- Ignoring this for now.
                                $delivery_entry->order_changes = true;
                                // General check
                                $order_changes = true;
                            }

                        //----- End of No milk order, still need to check for fruit and company detail changes though -----//

                        // Additional debugging property
                        $delivery_entry->status = 'No additional milk';
                    }

                    $orders[] = $delivery_entry;
                    unset($delivery_entry); // <-- Not we've passed the data on we can unset it ready for the next entry
                } // End of foreach ($fruitboxes as $fruitbox)
            } // End of foreach ($this->fruitboxes as $key => $fruitboxes)
        } // End of if (!empty($this->fruitboxes))

        if (!empty($this->milkboxes)) {
            foreach ($this->milkboxes as $key => $milkboxes) {
                foreach ($milkboxes as $milkbox) {

                    // Ok, so each time we run a new order we need to unset and redefine the variable again or the results all end up the same as the last entry.
                    // I stil feel there's a better solution to this but it's fine for now.

                    $delivery_entry = new \stdClass;
                    // Added but untested, however I did the same but with milk to the fruitboxes so it should be fine?
                    $fruitboxes = isset($this->fruitboxes[$key]) ? $this->fruitboxes[$key] : null; // This is now throwing an error, not sure why but maybe this will bypass it 09/11/2021 
                    // dump($fruitboxes);
                    // Call to a member function where() on null, throws error - needs fixing!!
                    $fruitboxes ? $additional_fruit = $fruitboxes->where('company_details_id', $milkbox->company_details_id)->where('delivery_day', $milkbox->delivery_day)->first() : $additional_fruit = $fruitboxes;

                    if (empty($additional_fruit)) {

                        // Then this is a milk only office

                        //----- Do some milky magic, add a template and then some more entries to test this code -----//

                        $company_details = CompanyDetails::find($milkbox->company_details_id);

                        // This approach will grab all populated address lines, ignoring any empty (null) fields
                        // and separating (implode) them with a comma.
                        // I need to upgrade other sections using the summary address with this approach.
                        $milkbox->company_details_summary_address = implode(", ", array_filter([
                                $company_details->route_address_line_1,
                                $company_details->route_address_line_2,
                                $company_details->route_address_line_3,
                                $company_details->route_city,
                                $company_details->route_region
                            ]));

                            // Grab delivery information
                            $delivery_entry->company_details_id = $company_details->id;
                            $delivery_entry->company_details_route_name = $company_details->route_name;
                            $delivery_entry->company_details_delivery_information = $company_details->delivery_information;
                            $delivery_entry->company_details_postcode = $company_details->route_postcode;
                            $delivery_entry->company_details_summary_address = $milkbox->company_details_summary_address;
                            $delivery_entry->delivery_day = $milkbox->delivery_day;
                            $delivery_entry->fruitbox_total = 0;
                            // Now let's grab all the milk related data from the $additional milk entry.
                            // Regular Milk
                            $delivery_entry->semi_skimmed_2l = $milkbox->semi_skimmed_2l;
                            $delivery_entry->skimmed_2l = $milkbox->skimmed_2l;
                            $delivery_entry->whole_2l = $milkbox->whole_2l;
                            $delivery_entry->semi_skimmed_1l = $milkbox->semi_skimmed_1l;
                            $delivery_entry->skimmed_1l = $milkbox->skimmed_1l;
                            $delivery_entry->whole_1l = $milkbox->whole_1l;
                            // Organic Milk
                            $delivery_entry->organic_semi_skimmed_1l = $milkbox->organic_semi_skimmed_1l;
                            $delivery_entry->organic_skimmed_1l = $milkbox->organic_skimmed_1l;
                            $delivery_entry->organic_whole_1l = $milkbox->organic_whole_1l;
                            $delivery_entry->organic_semi_skimmed_2l = $milkbox->organic_semi_skimmed_2l;
                            $delivery_entry->organic_skimmed_2l = $milkbox->organic_skimmed_2l;
                            $delivery_entry->organic_whole_2l = $milkbox->organic_whole_2l;
                            // Alternative Milk
                            // Pt 1
                            $delivery_entry->milk_1l_alt_coconut = $milkbox->milk_1l_alt_coconut;
                            $delivery_entry->milk_1l_alt_unsweetened_almond = $milkbox->milk_1l_alt_unsweetened_almond;
                            $delivery_entry->milk_1l_alt_almond = $milkbox->milk_1l_alt_almond;
                            // Pt 2
                            $delivery_entry->milk_1l_alt_unsweetened_soya = $milkbox->milk_1l_alt_unsweetened_soya;
                            $delivery_entry->milk_1l_alt_soya = $milkbox->milk_1l_alt_soya;
                            $delivery_entry->milk_1l_alt_oat = $milkbox->milk_1l_alt_oat;
                            // Pt 3
                            $delivery_entry->milk_1l_alt_rice = $milkbox->milk_1l_alt_rice;
                            $delivery_entry->milk_1l_alt_cashew = $milkbox->milk_1l_alt_cashew;
                            $delivery_entry->milk_1l_alt_lactose_free_semi = $milkbox->milk_1l_alt_lactose_free_semi;

                            $delivery_entry->order_changes = false; // <-- Before we check for changes we need to set this in case their aren't any.

                            //----- No fruit order, still need to check for milk and company detail changes though -----//

                                if ($milkbox->date_changed > $last_week) {
                                    // Individual check
                                    // $delivery_entry->fruit_changes = true; <-- Ignoring this for now.
                                    $delivery_entry->order_changes = true;
                                    // General check
                                    $order_changes = true;
                                }

                                if ($company_details->date_changed > $last_week) {
                                    // Individual check
                                    // $delivery_details->company_details_changes = true; <-- Ignoring this for now.
                                    $delivery_entry->order_changes = true;
                                    // General check
                                    $order_changes = true;
                                }

                            //----- End of No milk order, still need to check for fruit and company detail changes though -----//

                            // Additional debugging property
                            $delivery_entry->status = 'No additional fruit';

                            $orders[] = $delivery_entry; // <-- Now this only gets added if it contains a milk only order.
                                                         // This is to prevent a fruit order being added twice because the details were not updated prior to this.
                             unset($delivery_entry); // <-- Same as above, unset ready to redeclared at the top of the foreach.
                    } // End of if (empty($additional_fruit))
                    // Add the 'milk only' orders to the 'fruit only' and 'fruit and milk' orders
                    // $orders[] = $delivery_entry; <-- Moving this inside of the if clause to ensure we only add it again if there was a milk order to include.
                } // End of foreach ($milkboxes as $milkbox)
            } // End of foreach ($this->milkboxes as $milkboxes)
        } // End of if (!empty($this->milkboxes))

         // dd($orders); // <-- This should now hold everything we need to take to the template.

        // In order (huh) to use the laravel collection methods we need to turn $orders into a collection using collect.
        //$orders_collect = collect($orders); // I'm not really utilising anything from having turned it into a collection at this point.  Maybe I wait until after stripping out unneccessary results?

         //----- Now we have address details added to all the orders, we need to check if there are any orders which need combining into 1 delivery -----//

         foreach ($orders as $order) {
             // Sort orders by company, then delivery day so we can look for and combine fruitboxes to the same location.
             $orders_by_company_details_id[$order->company_details_id][$order->delivery_day][] = $order;
         }
         //dd($orders_by_company_details_id);

         // Now we need to burrow down into the nested arrays, first looping through each company
         foreach ($orders_by_company_details_id as $orders_by_id) {
             // And then through their orders day by day
             foreach ($orders_by_id as $orders_by_day) {
                 // Now we can count to see if there's more than one delivery to the same location any day
                 if (count($orders_by_day) > 1) {
                     // If so, we can loop through the orders
                     foreach ($orders_by_day as $key => $order) {
                         // after the first one, so as not to duplicate the initial order
                         if($key > 0) {
                             // Adding the additional fruit totals to the 1st entry which we're going to keep
                             $orders_by_day[0]->fruitbox_total += $order->fruitbox_total;
                             // dump($orders_by_day[$key]);
                             // unset($orders_by_day[$key]);
                             // Before unsetting the rest from the original array at the top of this nested chaos - apologies, I'm sure there was a better way to this!
                             unset($orders_by_company_details_id[$order->company_details_id][$order->delivery_day][$key]);
                         }
                     }
                 }
             }
         }
         // Now we can turn this into a collection and use flatten to undo the madness.
         $reordered_deliveries = collect($orders_by_company_details_id);
         // Flatten(2) removes the data out of the 2 nested arrays into its original format, minus the duplicate deliveries.
         $flattened_back_to_orders_collection = $reordered_deliveries->flatten(2);
         //dd($flattened_back_to_orders_collection);

         //----- End of reducing orders into one drop per location per day. -----//

         // Now we can pluck the values a product at a time and add them up, saving the total ahead of reaching the blade template.
         // This means we can determine whether to show a column or not before creating the rows for each company order.
         // Regular Milk
         $semi_skimmed_2l_total = $flattened_back_to_orders_collection->pluck('semi_skimmed_2l')->sum();
         $skimmed_2l_total = $flattened_back_to_orders_collection->pluck('skimmed_2l')->sum();
         $whole_2l_total = $flattened_back_to_orders_collection->pluck('whole_2l')->sum();
         $semi_skimmed_1l_total = $flattened_back_to_orders_collection->pluck('semi_skimmed_1l')->sum();
         $skimmed_1l_total = $flattened_back_to_orders_collection->pluck('skimmed_1l')->sum();
         $whole_1l_total = $flattened_back_to_orders_collection->pluck('whole_1l')->sum();
         // Organic Milk
         $organic_semi_skimmed_2l_total = $flattened_back_to_orders_collection->pluck('organic_semi_skimmed_2l')->sum();
         $organic_skimmed_2l_total = $flattened_back_to_orders_collection->pluck('organic_skimmed_2l')->sum();
         $organic_whole_2l_total = $flattened_back_to_orders_collection->pluck('organic_whole_2l')->sum();
         $organic_semi_skimmed_1l_total = $flattened_back_to_orders_collection->pluck('organic_semi_skimmed_1l')->sum();
         $organic_skimmed_1l_total = $flattened_back_to_orders_collection->pluck('organic_skimmed_1l')->sum();
         $organic_whole_1l_total = $flattened_back_to_orders_collection->pluck('organic_whole_1l')->sum();
         // Alternative Milk
         $alt_coconut_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_coconut')->sum();
         $alt_unsweetened_almond_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_unsweetened_almond')->sum();
         $alt_almond_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_almond')->sum();
         $alt_unsweetened_soya_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_unsweetened_soya')->sum();
         $alt_soya_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_soya')->sum();
         $alt_oat_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_oat')->sum();
         $alt_rice_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_rice')->sum();
         $alt_cashew_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_cashew')->sum();
         $alt_lactose_free_semi_total = $flattened_back_to_orders_collection->pluck('milk_1l_alt_lactose_free_semi')->sum();


         $monToFri = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

         $ordersByMonToFri = $flattened_back_to_orders_collection->sortBy( function ($order) use ($monToFri) {
              return array_search($order->delivery_day, $monToFri);
         });

         // dd($ordersByMonToFri);

        return view('exports.fruitpartner-combined-details', [
            // 'deliveries' => $orders,
            'deliveries' => $ordersByMonToFri,
            'semi_skimmed_2l_total' => $semi_skimmed_2l_total,
            'skimmed_2l_total' => $skimmed_2l_total,
            'whole_2l_total' => $whole_2l_total,
            'semi_skimmed_1l_total' => $semi_skimmed_1l_total,
            'skimmed_1l_total' => $skimmed_1l_total,
            'organic_whole_1l_total' => $organic_whole_1l_total,
            'organic_semi_skimmed_2l_total' => $organic_semi_skimmed_2l_total,
            'organic_skimmed_2l_total' => $organic_skimmed_2l_total,
            'organic_whole_2l_total' => $organic_whole_2l_total,
            'organic_semi_skimmed_1l_total' => $organic_semi_skimmed_1l_total,
            'organic_skimmed_1l_total' => $organic_skimmed_1l_total,
            'whole_1l_total' => $whole_1l_total,
            'alt_coconut_total' => $alt_coconut_total,
            'alt_unsweetened_almond_total' => $alt_unsweetened_almond_total,
            'alt_almond_total' => $alt_almond_total,
            'alt_unsweetened_soya_total' => $alt_unsweetened_soya_total,
            'alt_soya_total' => $alt_soya_total,
            'alt_oat_total' => $alt_oat_total,
            'alt_rice_total' => $alt_rice_total,
            'alt_cashew_total' => $alt_cashew_total,
            'alt_lactose_free_semi_total' => $alt_lactose_free_semi_total,
            'order_changes' => $order_changes,
        ]);

        // dd($company_details);
    }

    public function title(): string
    {
        return 'Delivery Details';
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // dd($event);
                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getColumnDimension('D')->setWidth(50);
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A1:' . $rowWidth . $highestRow)->getAlignment()->setVertical('center');

                $totals_border = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ],
                ];
                // dd($rowWidth . $highestRow);
                $event->sheet->getDelegate()->getStyle('A' . $highestRow . ':' . $rowWidth . $highestRow)->applyFromArray($totals_border);

                // Not sure why the columns array goes white from 'V' onwards, it doesn't seem to affect the excel results.
                $columns = [
                    'A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                     'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];

                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                foreach ($event->sheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    $selectedRow = $row->getRowIndex();
                    $previousRow = ($row->getRowIndex() - 1);

                    // dump($selectedRow);
                    // dump($previousRow);

                    //----- If 'Updated' column exists and this row has undated written in the row, make the company name yellow. -----//

                        if ($event->sheet->getCell($rowWidth . $selectedRow) == 'Yes') {

                            $companyName = 'A' . $selectedRow;

                            $event->sheet->styleCells(
                                $companyName,
                                [
                                    'fill' =>   [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                    'argb' => 'ffff66'
                                                    ]
                                                ]
                                ]);
                        }

                    //----- End of if update column exists -----//

                    //----- Potential to apply a border to seperate the orders by week day -----//

                        // This might be a good place to check if the value of the current row held in the cell of column 'b',
                        // is different to the value held in the same cell of the row above.  This might allow is to apply a top border to seperate the week days?

                        // dump($event->sheet->getCell('B' . $selectedRow));
                        // dump($event->sheet->getCell('B' . $previousRow));

                        $previousRowData = $event->sheet->getCell('B' . $previousRow);
                        $currentRowData = $event->sheet->getCell('B' . $selectedRow);
                        if ($currentRowData->getValue() !== $previousRowData->getValue() && $selectedRow > 2 ) {
                            // dd($row);
                            $selectedRowCoordinates = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow;

                            $event->sheet->styleCells(
                              $selectedRowCoordinates, // Cell Range
                              [ // Styles Array
                                  'borders' => [
                                      'top' => [
                                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                          'color' => ['argb' => '000'],
                                      ],
                                  ], // end of borders

                              ] // end of styles array
                          ); // end of styleCells function parameters.
                        }

                    //----- End of Potential to apply a border to seperate the orders by week day -----//

                    $event->sheet->getStyle('C' . $selectedRow . ':D' . $selectedRow)->getAlignment()->setWrapText(true);

                    // Styles applied on a cell by cell basis are particularly taxing and slow the process down.
                    foreach ($cellIterator as $cell) {
                        if ($cell !== null) {
                            // dump($cell->getCoordinate());
                            $currentCell = $cell->getCoordinate();
                            // Right, now this might get confusing so let's think about it for a sec...
                            // 1. If the cell has a value, it has a column header.
                            // 2. The header will always be the same column as the cell, and in the first row.
                            // 3.

                            // Magic Regex to split the string on the first numerical value creating an array [0 => letters, 1 => numbers]
                            $experiment = preg_split('/(?=\d)/', $cell->getParent()->getCurrentCoordinate(), 2);

                            // Using the letters from $experiment to find the header row of the current cell.
                            // The header will always be on row 1 so we can hardcode that part.
                            $cellHeaderCoordinates = $experiment[0].'1';

                            // Using those coordinates we can grab the value of that cell, the column header.
                            $cellHeaderValue = $event->sheet->getDelegate()->getCell($cellHeaderCoordinates);
                            // dump($cellHeaderValue);
                            // If that value matches either of these 3 headers, it's for semi skimmed milk - so let's give it a green background.
                            if (    $cellHeaderValue == 'Milk 2l Semi-Skimmed' ||
                                    $cellHeaderValue == 'Milk 1l Semi-Skimmed' ||
                                    $cellHeaderValue == 'Organic 1l Semi Skimmed') {

                                // Cool, after a bunch of fiddling, this does what it should!
                                // However I'm thinking it would be quit nice to give the headers the same treatment.
                                if ( is_numeric($cell->getValue()) // ||
                                    // $cell->getValue() == 'Milk 2l Semi-Skimmed' ||
                                    // $cell->getValue() == 'Milk 1l Semi-Skimmed' ||
                                    // $cell->getValue() == 'Organic 1l Semi Skimmed'
                                ) {

                                    $event->sheet->styleCells($currentCell, [
                                        'fill' => [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                        'argb' => 'b3ffcc'
                                                    ]
                                        ] // end of fill
                                    ]);
                                }
                            // Now let's do the same with skimmed, giving it a red background.
                            } elseif (  $cellHeaderValue == 'Milk 2l Skimmed' ||
                                        $cellHeaderValue == 'Milk 1l Skimmed' ||
                                        $cellHeaderValue == 'Organic 1l Skimmed') {

                                if ( is_numeric($cell->getValue()) // ||
                                    // $cell->getValue() == 'Milk 2l Skimmed' ||
                                    // $cell->getValue() == 'Milk 1l Skimmed' ||
                                    // $cell->getValue() == 'Organic 1l Skimmed'
                                ) {

                                    $event->sheet->styleCells($currentCell, [
                                        'fill' => [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                        'argb' => 'ff9999'
                                                    ]
                                        ] // end of fill
                                    ]);
                                }
                            // And blue for whole milk
                            } elseif (  $cellHeaderValue == 'Milk 2l Whole' ||
                                        $cellHeaderValue == 'Milk 1l Whole' ||
                                        $cellHeaderValue == 'Organic 1l Whole') {

                                if ( is_numeric($cell->getValue()) // ||
                                    // $cell->getValue() == 'Milk 2l Whole' ||
                                    // $cell->getValue() == 'Milk 1l Whole' ||
                                    // $cell->getValue() == 'Organic 1l Whole'
                                ) {

                                    $event->sheet->styleCells($currentCell, [
                                        'fill' => [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                        'argb' => '99ccff'
                                                    ]
                                        ] // end of fill
                                    ]);
                                }
                            // The rest are all alternative milks, so let's give it ummm, *exhales*... yellow?
                            } elseif (  $cellHeaderValue == 'Milk 1l Alt Coconut' ||
                                        $cellHeaderValue == 'Milk 1l Alt Unsweetened Almond' ||
                                        $cellHeaderValue == 'Milk 1l Alt Almond' ||
                                        $cellHeaderValue == 'Milk 1l Alt Unsweetened Soya' ||
                                        $cellHeaderValue == 'Milk 1l Alt Soya' ||
                                        $cellHeaderValue == 'Milk 1l Alt Oat' ||
                                        $cellHeaderValue == 'Milk 1l Alt Rice' ||
                                        $cellHeaderValue == 'Milk 1l Alt Cashew' ||
                                        $cellHeaderValue == 'Milk 1l Alt Lactose Free Semi') {

                                if ( is_numeric($cell->getValue()) // ||
                                    // $cell->getValue() == 'Milk 1l Alt Coconut' ||
                                    // $cell->getValue() == 'Milk 1l Alt Unsweetened Almond' ||
                                    // $cell->getValue() == 'Milk 1l Alt Almond' ||
                                    // $cell->getValue() == 'Milk 1l Alt Unsweetened Soya' ||
                                    // $cell->getValue() == 'Milk 1l Alt Soya' ||
                                    // $cell->getValue() == 'Milk 1l Alt Oat' ||
                                    // $cell->getValue() == 'Milk 1l Alt Rice' ||
                                    // $cell->getValue() == 'Milk 1l Alt Cashew' ||
                                    // $cell->getValue() == 'Milk 1l Alt Lactose Free Semi'
                                ) {

                                    $event->sheet->styleCells($currentCell, [
                                        'fill' => [
                                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                    'color' => [
                                                        'argb' => 'ffffb3'
                                                    ]
                                        ] // end of fill
                                    ]);
                                }

                            }


                            if ($cell == 'Company Name') {

                                $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                                // $selectedRow = $row->getRowIndex();
                                $event->sheet->styleCells(
                                  $selectedCell, // Cell Range
                                  [ // Styles Array
                                      // 'borders' => [
                                      //     'bottom' => [
                                      //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                      //         'color' => ['argb' => '000'],
                                      //     ],
                                      //], // end of borders
                                      'font' => [
                                          'color' => [
                                              'argb' => 'ffffff'
                                          ]
                                      ],
                                      // With no guarantee about colour printers, I'm making this black and white friendly.
                                      'fill' => [
                                                  'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                  'color' => [
                                                    'argb' => '000000'
                                                  ]
                                      ] // end of fill
                                  ] // end of styles array
                              ); // end of styleCells function parameters.
                            } // end of if ($cell == 'Company Name')

                        } else {
                            continue;
                        }
                    } // foreach ($cellIterator as $cell)
                } // end of foreach ($event->sheet->getRowIterator() as $row)

                // This is nice but the increase in font size also increases the column width, which may be unwanted?
                $cellRange = 'A1:AA1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ]; // End of Register Events return array
    } // End of Register Events(): array function
}
