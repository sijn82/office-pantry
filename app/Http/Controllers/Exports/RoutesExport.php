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


use App\Route;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Events\AfterSheet;
// use Maatwebsite\Excel\Sheet;

// Using the multisheets wip.

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

// class RoutesExport implements FromCollection, WithHeadings
// class RoutesExport implements FromView
class RoutesExport implements WithMultipleSheets, WithEvents
{
    use Exportable;

    protected $week_starting;
    protected $routescollection;

        public function __construct($week_starting)
        {
            $this->week_starting = $week_starting;
            // dd($this->week_starting);
        }

    // public function headings(): array
    // {
    //   return [
    //       'id',
    //       'week_start',
    //       'company_name',
    //       'postcode',
    //       'address',
    //       'delivery_information',
    //       'fruit_crates',
    //       'fruit_boxes',
    //       'milk_2l_semi_skimmed',
    //       'milk_2l_skimmed',
    //       'milk_2l_whole',
    //       'milk_1l_semi_skimmed',
    //       'milk_1l_skimmed',
    //       'milk_1l_whole',
    //       'milk_1l_alt_coconut',
    //       'milk_1l_alt_unsweetened_almond',
    //       'milk_1l_alt_almond',
    //       'milk_1l_alt_unsweetened_soya',
    //       'milk_1l_alt_soya',
    //       'milk_1l_alt_oat',
    //       'milk_1l_alt_rice',
    //       'milk_1l_alt_cashew',
    //       'milk_1l_alt_lactose_free_semi',
    //       'drinks',
    //       'snacks',
    //       'assigned_to',
    //       'delivery_day',
    //       'position_on_route',
    //       'created_at',
    //       'updated_at',
    //   ];
    // }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $routescollection = Route::select('assigned_to')->distinct()->get();
        // dd($routescollection);
        $sheets = [];
        // dd($this->week_starting);
        foreach ($routescollection as $routesolo) {
            // dd($routesolo->assigned_to);
            $sheets[] = new RoutesCollection($routesolo->assigned_to, $this->week_starting);

        }

        // dd($sheets);

        return $sheets;
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

                            if ($cell == 'WHOLESALE') {
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
                $cellRange = 'A1:AA1'; // All headers
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

    public function collection()
    {
      //
      // $routes = Route::pluck('assigned_to')->distinct()->all();
      // foreach ($routes as $key => $route) {
      //   var_dump($routes);
      // }
      //
      // dd($routes);
      // I wonder how easily this could run through the assigned_to routes printing off a list of each, for each day?
        // return Route::where('assigned_to', 'Catalin')->where('delivery_day', 'Monday')->where('week_start', '90718');
        return Route::where('week_start', '300718')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc');
                    // ->orderBy('delivery_day', 'Tuesday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc')
                    // ->orderBy('delivery_day', 'Wednesday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc')
                    // ->orderBy('delivery_day', 'Thursday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc')
                    // ->orderBy('delivery_day', 'Friday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc');
    }
}


// class RoutesCollection implements FromQuery, WithTitle, WithHeadings
class RoutesCollection implements FromView, WithTitle, WithHeadings
{
    private $routesolo;
    // private $routecollection;
    private $week_starting;

    public function __construct($routesolo,
     // $routecollection,
                                $week_starting)
    {
        $this->routesolo = $routesolo;
        // dd($this->routesolo);
        // $this->routecollection  = $routecollection;
        $this->week_starting = $week_starting;
    }


    public function headings(): array
    {
      return [
          'id',
          'week_start',
          'company_name',
          'postcode',
          'address',
          'delivery_information',
          'fruit_crates',
          'fruit_boxes',
          'milk_2l_semi_skimmed',
          'milk_2l_skimmed',
          'milk_2l_whole',
          'milk_1l_semi_skimmed',
          'milk_1l_skimmed',
          'milk_1l_whole',
          'milk_1l_alt_coconut',
          'milk_1l_alt_unsweetened_almond',
          'milk_1l_alt_almond',
          'milk_1l_alt_unsweetened_soya',
          'milk_1l_alt_soya',
          'milk_1l_alt_oat',
          'milk_1l_alt_rice',
          'milk_1l_alt_cashew',
          'milk_1l_alt_lactose_free_semi',
          'drinks',
          'snacks',
          'other',
          'assigned_to',
          'delivery_day',
          'position_on_route',
          'created_at',
          'updated_at',
      ];
    }

     public function view(): View
    {


        return view('exports.routes', [
            'routes' => Route::all()->where('week_start', $this->week_starting)->where('assigned_to', $this->routesolo)->sortBy('position_on_route')
        ]);
    }

    /**
    * @return Builder
    */
   // public function query()
   // {
   //     // dd($this->$routesolo);
   //     return Route
   //         ::query()
   //         ->where('assigned_to', $this->routesolo)
   //         ->where('week_start', $this->week_starting)
   //         ->orderBy('position_on_route', 'asc');
   // }

   /**
    * @return string
    */
   public function title(): string
   {
       return 'Route ' . $this->routesolo;
   }

}
