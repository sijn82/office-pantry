<?php

namespace App\Http\Controllers\Exports;


use App\PickList;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\Exportable;

class PicklistsExport implements FromCollection, WithHeadings
{
    // use Exportable;
    public function headings(): array
    {
      return [
          'id',
          'week_start',
          'company_name',
          'fruit_crates',
          'fruit_boxes',
          'deliciously_red_apples',
          'pink_lady_apples',
          'red_apples',
          'green_apples',
          'satsumas',
          'pears',
          'bananas',
          'nectarines',
          'limes',
          'lemons',
          'grapes',
          'seasonal_berries',
          'oranges',
          'cucumbers',
          'mint',
          'assigned_to',
          'position_on_route',
          'delivery_day',
          'created_at',
          'updated_at',
      ];
    }

    public function collection()
    {
        return PickList::where('week_start', '200818')->where('delivery_day', 'Friday')->orderBy('assigned_to', 'asc')->orderBy('position_on_route', 'asc');
        // I want this functionality (somehow) to ignore these columns when exporting = ->except(['id', 'created_at', 'updated_at'])
        // I've found a solution but can't believe Laravel doesn't have a built in sytem to handle this (typical) request?
    }

    // public function thamesValley()
    // {
    //     return PickList::all()->where('assigned_to', 'Thames Valley');
    // }


}
