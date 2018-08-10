<?php

// namespace App\Http\Controllers\Exports;
//
// use App\Route;
// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;
//
// class RoutesExport implements FromView
// {
//     public function view(): View
//     {
//         return view('display-routes', [
//             'routes' => Route::all()
//         ]);
//     }
// }

namespace App\Http\Controllers\Exports;


use App\Company;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// class CompaniesExport implements FromCollection, WithHeadings
class CompaniesExport implements FromView, WithHeadings, WithEvents
// ,ShouldAutoSize
{



    // use Exportable;
    public function headings(): array
    {
      return [
          'id',
          'is_active',
          'invoice_name',
          'route_name',
          'box_names',
          'primary_contact',
          'primary_email',
          'secondary_email',
          'delivery_information',
          'route_summary_address',
          'address_line_1',
          'address_line_2',
          'city',
          'region',
          'postcode',
          'branding_theme',
          'supplier',
          // 'delivery_monday',
          // 'delivery_tuesday',
          // 'delivery_wednesday',
          // 'delivery_thursday',
          // 'delivery_friday',
          // 'assigned_to_monday',
          // 'assigned_to_tuesday',
          // 'assigned_to_wednesday',
          // 'assigned_to_thursday',
          // 'assigned_to_friday',
          // 'created_at',
          // 'updated_at',
      ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {


        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // $styleArray = [
                //     'borders' => [
                //         'outline' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                //             'color' => ['argb' => 'FFFF0000'],
                //         ],
                //     ],
                //     'fill' => [
                //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //                 'color' => [
                //                   'argb' => 'FFA0A0A0'
                //                 ]
                //         // 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                //         // 'rotation' => 90,
                //         // 'startColor' => [
                //         //     'argb' => 'FFA0A0A0',
                //         // ],
                //         // 'endColor' => [
                //         //     'argb' => 'FFFFFFFF',
                //         // ],
                //     ],
                // ];

                $highestRow = $event->sheet->getHighestRow();
                $rowWidth = $event->sheet->getHighestColumn();

                // for ($row = 2; $row <= $highestRow; $row++) {
                //     var_dump($event->sheet->getDelegate()->getRowIterator()->position) . '<br>';
                // }

                foreach ($event->sheet->getRowIterator() as $row) {
                    // echo '<br>    Row number - ' . $row->getRowIndex() . '<br>';


                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                    foreach ($cellIterator as $cell) {
                        if ($cell !== null) {
                            // echo '        Cell - ' . $cell->getCoordinate() . ' - ' . $cell->getCalculatedValue();

                            if ($cell == 'OfficePantry') {
                                // echo '<br>    Row number - ' . $row->getRowIndex() . '<br>';
                                // echo '        Cell - ' . $cell->getCoordinate() . ' - ' . $cell->getCalculatedValue();
                                $selectedRow = $row->getRowIndex();
                                // $selectedCell = 'A' . $selectedRow . ':' . $cell->getCoordinate(); //P2
                                $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
                                $selectedRow = $row->getRowIndex();
                                $event->sheet->styleCells(
                                  $selectedCell, // Cell Range
                                  [ // Styles Array
                                      'borders' => [
                                          'outline' => [
                                              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                              'color' => ['argb' => 'AFFF46'],
                                          ],
                                      ],
                                      'fill' => [
                                                  'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                                  'color' => [
                                                    'argb' => '93DA38'
                                                  ]
                                      ]
                                  ]
                                );
                            }
                        } else {
                            continue;
                        }
                    }
                }

                // dd($event->sheet->getDelegate()->getRowIterator());
                $cellRange = 'A1:P1'; // All headers
                // $cellColumns = 'B2:Q200';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                // $event->spreadsheet->getActiveSheet()->getStyle($cellRange)->getFill()->getStartColor()->setARGB('FFFF0000');
                // $event->sheet->getStyle('B2:G8')->applyFromArray($styleArray);

                // $event->sheet->styleCells(
                //   'B2:G8', // Cell Range
                //   [ // Styles Array
                //       'borders' => [
                //           'outline' => [
                //               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                //               'color' => ['argb' => 'FFFF0000'],
                //           ],
                //       ],
                //       'fill' => [
                //                   'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //                   'color' => [
                //                     'argb' => 'FFA0A0A0'
                //                   ]
                //       ]
                //   ]
                // );

                // $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                // $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
                // $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
                // $conditional1->addCondition('Cancelled');
                // $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                // $conditional1->getStyle()->getFont()->setBold(true);
                //
                // $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                // $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
                // $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
                // $conditional2->addCondition('Active');
                // // $conditional2->getStyle($cellRange)->applyFromArray($styleArray);
                // $conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                // $conditional2->getStyle()->getFont()->setBold(true);
                //
                // $conditionalStyles = $event->sheet->getDelegate()->getStyle($cellColumns)->getConditionalStyles();
                // $conditionalStyles[] = $conditional1;
                // $conditionalStyles[] = $conditional2;
                //
                // // $event->sheet->getDelegate()->getStyle($cellColumns)->setConditionalStyles($conditionalStyles);
                // $event->sheet->getDelegate()->getStyle($cellColumns)->applyFromArray($styleArray);
            },


        ];



    }

    public function view(): View
   {
       return view('exports.companies', [
           'companies' => Company::all()
       ]);
   }

    // public function collection()
    // {
    //
    //     return Company::all()->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc');
    //
    // }



}
