<?php

namespace App\Http\Controllers\Exports;

use Session;
use App\WeekStart;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use App\Http\Controllers\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SnackboxOPSingleCompanyExport
 // implements WithMultipleSheets
  implements FromView, WithEvents, ShouldAutoSize //, WithMultipleSheets
{
    public function view(): View
   {
       $product_list = session()->get('snackbox_product_list');
       $snackboxes = session()->get('snackbox_OP_singlecompany');
       // dd($snackbox[0][0]);
       return view('exports.snackboxes-single-company', [
           'chunks'         =>   $snackboxes,
           'product_list'   =>   $product_list
       ]);
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
           },
       ];
   }

    // /**
    //  * @return array
    //  */
    // public function sheets(): array
    // {
    //     $snackbox_OP_multicompany = session()->get('snackbox_OP_multicompany');
    //     // $product_list = session()->get('snackbox_product_list');
    //     // dd($snackbox_OP_multicompany);
    //     $sheets = [];
    //     foreach ($snackbox_OP_multicompany as $snackboxes) {
    //
    //
    //         $this->snackboxes = $snackboxes;
    //         // dd($this->snackboxes);
    //
    //         session()->forget('snackbox_chunk');
    //         session()->put('snackbox_chunk', $this->snackboxes);
    //
    //
    //         $sheets[] = new SnackBoxCollection($this->snackboxes);
    //         // }
    //     }
    //     // dd($sheets);
    //     return $sheets;
    // }
}
    //
    // class SnackBoxCollection implements
    // FromView
    // {
    //     public function view(): View
    //    {
    //        $product_list = session()->get('snackbox_product_list');
    //        $snackboxes = session()->get('snackbox_OP_multicompany');
    //        // $snackboxes = session()->get('snackbox_chunk');
    //        // dd($snackbox[0][0]);
    //        return view('exports.snackboxes-multi-company', [
    //            'chunks'    =>   $snackboxes,
    //            'product_list'  =>   $product_list
    //        ]);
    //    }
    // }
