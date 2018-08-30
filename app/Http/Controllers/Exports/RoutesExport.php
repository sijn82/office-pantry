<?php

namespace App\Http\Controllers\Exports;

use App\Route;
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

        public function __construct($week_starting)
        {
            $this->week_starting = $week_starting;
        }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $routescollection = Route::select('assigned_to')->distinct()->get();
        $sheets = [];

        foreach ($routescollection as $routesolo) {

            $sheets[] = new RoutesCollection($routesolo->assigned_to, $this->week_starting);
        }

        return $sheets;
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
       return 'Route - ' . $this->routesolo;
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
