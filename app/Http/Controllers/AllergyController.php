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
    
        dd($request['new_allergy']['name']);
    
        $allergy = new Allergy();
        $allergy->allergy = $request['new_allergy']['name'];
        $allergy->company_id = $request['new_allergy']['company'];
        $allergy->save();
        
        return Allergy::where('company_id', $request['new_allergy']['company'])->where('allergy', $request['new_allergy']['name'])->get();
    }
    
    public function destroy($id) {
        
        Allergy::destroy($id);
        
    }
}
