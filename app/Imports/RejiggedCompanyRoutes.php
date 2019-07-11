<?php

namespace App\Imports;

use App\CompanyRoute;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;

class RejiggedCompanyRoutes implements
// ToModel,
WithEvents,
WithHeadingRow // This means we can upload the file with headers, allowing the columns to link to values, by title ($row['Company Route ID']) rather than position ($row[0]).
{
    // // Auto generated function but I don't like the name model, so making my own rather than editing (for my records/curiosity at) the default generation.
    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */
    // public function model(array $row)
    // {
    //     return new CompanyRoute([
    //         //
    //     ]);
    // }

    public function __construct()
    {
        $this->sheetNames = [];

    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->sheetNames[] = $event->getSheet()->getTitle();
            }
        ];
    }

    public function import(Request $request)
    {
        //dd($request);
        // $rejiggedRoutes = Excel::toCollection(new RejiggedCompanyRoutes(), $request->file('rejigged_routes'));
        // foreach ($rejiggedRoutes[0] as $rejiggedRoute) {
        //
        // }
    }
}
