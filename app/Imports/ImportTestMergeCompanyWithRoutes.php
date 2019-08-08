<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithEvents; // Not sure we need any events for imports.

use App\CompanyDetails;
use App\Company;
use App\Route;

class ImportedCompanyDetails implements
toModel,
// WithEvents,
WithHeadingRow
{

    public function model(array $row)
    {
        dd($row);
        
        // Can I do some magic here to combine the two tables together, where we merge all the info we want, if it's successful.
        // But how do we skip the rows we don't want?
        // I have a feeling this will take too long to sort out for a one off fucntion?
        
        
        return new CompanyDetails([
            
            
        ]);
        
    }