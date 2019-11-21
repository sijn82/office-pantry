<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Allergy;

class AllergyController extends Controller
{

    public function showAllergies() {
        $allergies = Allergy::select('allergy')->distinct()->get();
        // $allergies->toArray();
        return $allergies;
    }

    public function addAllergy(Request $request) {

        //dd(request());

        $allergy = new Allergy();
        $allergy->allergy = request('selected_allergens');
        $allergy->dietary_requirements = request('selected_dietary_requirements');
        $allergy->company_details_id = request('selected_company');
        $allergy->save();

        //return Allergy::where('company_details_id', request('selected_company'))->get(); //<-- Why am I doing this?
    }

    public function updateAllergies(Request $request, $id) {

        Allergy::find($id);
    }

    public function destroy($id) {

        Allergy::destroy($id);

    }
}
