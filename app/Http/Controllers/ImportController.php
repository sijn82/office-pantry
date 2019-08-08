<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\ImportedCompanyDetails;
use App\Imports\ImportedFruitPartners;

use App\CompanyDetails;
use App\FruitPartner;

// Temporary for table merge test

use App\Company;
use App\Route;

class ImportController extends Controller
{

    public function importCompanyDetails(Request $request)
    {
        //dd($request);

        $importedCompanyDetails = Excel::import(new ImportedCompanyDetails(), $request->file('imported-company-details-file'));

    }

    public function importFruitPartners(Request $request)
    {
        $importedFruitPartners = Excel::import(new ImportedFruitPartners(), $request->file('imported-fruit-partners-file'));

    }
    
    // Temporary for table merge test
    
    public function mergeCompanyWithRoutes()
    {
        
    }
    
}
