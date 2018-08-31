<?php

namespace App\Http\Controllers\Exports;

use App\PickList;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PicklistsExportFull implements
// FromView,
// WithTitle,
ShouldAutoSize,
// WithHeadings,
FromCollection
// WithEvents
{

    private $week_starting;

    public function __construct( $week_starting)
    {
        $this->week_starting = $week_starting;
    }

    public function collection()
    {
        return PickList::where('week_start', $this->week_starting)->orderBy('assigned_to', 'desc')->orderBy('position_on_route', 'asc')->get();
    }

    // This adds a named title to each worksheet tab.
    public function title(): string
    {
        return 'Picklist - Full';
    }

        // This isn't currently in use anymore as we're now using the view template to add headers but again, I'm keeping this here as an example of using headers this way.

        // public function headings(): array
        // {
        //   return [
        //       'ID',
        //       'Week Start',
        //       'Company Name',
        //       'Fruit Crates',
        //       'Fruit Boxes',
        //       'Deliciously Red Apples',
        //       'Pink Lady Apples',
        //       'Red Apples',
        //       'Green Apples',
        //       'Satsumas',
        //       'Pears',
        //       'Bananas',
        //       'Nectarines',
        //       'Limes',
        //       'Lemons',
        //       'Grapes',
        //       'Seasonal Berries',
        //       'Oranges',
        //       'Cucumbers',
        //       'Mint',
        //       'Assigned To',
        //       'Position On Route',
        //       'Delivery Day',
        //       'Created At',
        //       'Updated At',
        //   ];
        // }
}
