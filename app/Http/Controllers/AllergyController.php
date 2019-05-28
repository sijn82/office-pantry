<?php

namespace App\Http\Controllers;

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
    
        //dd($request['new_allergy']['name']);
    
        $allergy = new Allergy();
        $allergy->allergy = $request['new_allergy']['name'];
        $allergy->company_details_id = $request['new_allergy']['company_details_id'];
        $allergy->save();
        
        return Allergy::where('company_details_id', $request['new_allergy']['company_details_id'])->where('allergy', $request['new_allergy']['name'])->get();
    }
    
    public function destroy($id) {
        
        Allergy::destroy($id);
        
    }
}
