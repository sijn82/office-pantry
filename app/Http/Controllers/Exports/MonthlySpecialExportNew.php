<?php

namespace App\Http\Controllers\Exports;

use App\WeekStart;
// I will be needing these
use App\CompanyDetails;
use App\CompanyRoute;
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;

// But just in case
use App\AssignedRoute;
use App\FruitPartner;

// And these are just the excel thingies, 
// I may not be using all of them but quicker to copy and paste than evaluate each one.
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