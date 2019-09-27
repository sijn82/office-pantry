<?php

namespace App\Http\Controllers\Exports;

use App\Route;
use App\WeekStart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;


class RoutesExport implements WithMultipleSheets
// , WithEvents
{
    // use Exportable;

    protected $week_starting;
    protected $routescollection;

    protected $week_start;
    protected $delivery_days;

        public function __construct($week_starting)
        {
            $this->week_starting = $week_starting;

            $week_start = WeekStart::all()->toArray();
            $this->week_start = $week_start[0]['current'];
            $this->delivery_days = $week_start[0]['delivery_days'];
        }

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
                                        '2.03 - Tue Float',
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

        // This $routecollection isn't currently used, even though it's more reliable at pulling through all the routes for the week
        // because by hardcoding an order for the routes I can select which ones to output and in which order.
        // However if a route is added or has a name change it won't get pulled through.

        // I should find a way to manipulate $routecollection in the same way (to get the best of both worlds) but for now this will work with manual code changes.

        $routescollection = Route::select('assigned_to')->distinct()->get()->toArray();
        $sheets = [];

        if ($this->delivery_days == 'mon-tue') {

            foreach ($correctOrderMonTue as $routesolo) {

                $sheets[] = new RoutesCollection($routesolo, $this->week_starting);
            }
            // dd($sheets);
            return $sheets;

        } else {

            foreach ($correctOrderWedThurFri as $routesolo) {

                $sheets[] = new RoutesCollection($routesolo, $this->week_starting);
            }

            return $sheets;

        }



        // dd($routescollection);

        // $reorderedRoutesCollection = array_replace($correctOrder, $routescollection);
        // dd($reorderedRoutesCollection);


        // foreach ($routescollection as $routesolo) {
        //
        //     $sheets[] = new RoutesCollection($routesolo->assigned_to, $this->week_starting);
        // }

        // foreach ($correctOrderMonTue as $routesolo) {
        //
        //     $sheets[] = new RoutesCollection($routesolo, $this->week_starting);
        // }
        //
        // return $sheets;
    }

    // public function collection()
    // {
    //     // I wonder how easily this could run through the assigned_to routes printing off a list of each, for each day?
    //     // return Route::where('assigned_to', 'Catalin')->where('delivery_day', 'Monday')->where('week_start', '90718');
    //     return Route::where('week_start', '300718')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc');
    // }

} // End of - class RoutesExport implements WithMultipleSheets

class RoutesCollection implements
FromView,
WithTitle,
WithHeadings,
WithEvents
{
    // use Exportable;

    private $routesolo;
    private $week_starting;

    public function __construct($routesolo, $week_starting)
    {
        $this->routesolo = $routesolo;
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
    * @return string
    */
   public function title(): string
   {
       return $this->routesolo;
   }

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
                   foreach ($cellIterator as $cell) {
                       if ($cell !== null) {

                           if ($cell == 'Week Start') {

                               $selectedRow = $row->getRowIndex();

                               $selectedCell = 'A' . $selectedRow . ':' . $rowWidth . $selectedRow; //P2
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
                           continue;
                       }
                   }
               } // end of foreach statement
               $cellRange = 'A1:AA1'; // All headers
               $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
           },
       ];
   }

       /**
       * @return Builder
       */

       // Keeping this here purely as an example of using query() with laravel excel as this was working before I opted for View() instead.

      // public function query()
      // {
      //     // dd($this->$routesolo);
      //     return Route
      //         ::query()
      //         ->where('assigned_to', $this->routesolo)
      //         ->where('week_start', $this->week_starting)
      //         ->orderBy('position_on_route', 'asc');
      // }

}
