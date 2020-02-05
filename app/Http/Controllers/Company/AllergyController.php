<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Allergy;

class AllergyController extends Controller
{

    // THESE ARE ALL FOR THE OLD APPROACH, THESE WILL ALL NEED REMOVING AND/OR REPLACING.

    public function showAllergies() {
        $allergies = Allergy::select('allergy')->distinct()->get();
        // $allergies->toArray();
        return $allergies;
    }

    // public function addAllergy(Request $request) {
    //
    //     //dd(request());
    //
    //     $allergy = new Allergy();
    //     $allergy->allergy = request('selected_allergens');
    //     $allergy->dietary_requirements = request('selected_dietary_requirements');
    //     $allergy->company_details_id = request('selected_company');
    //     $allergy->save();
    //
    //     //return Allergy::where('company_details_id', request('selected_company'))->get(); //<-- Why am I doing this?
    // }

    //Haha, this one isn't actually doing anything anyway!
    public function updateAllergies(Request $request, $id) {

        Allergy::find($id);
    }

    // Well this one will probably remain the same :)
    public function destroy($id) {

        Allergy::destroy($id);

    }

    // NEW ALLERGY APPROACH

    // So I can avoid making a crud right now, and to prevent postgresql messing up the sequence,
    // I'm going to pass the values into here directly to make the initial allergy list.
    public function multiStore()
    {

        $allergies = ['Celery', 'Gluten', 'Crustaceans', 'Eggs', 'Fish', 'Lupin', 'Milk', 'Molluscs', 'Mustard', 'Tree Nuts', 'Peanuts', 'Sesame', 'Soya', 'Sulphites'];

        foreach ($allergies as $allergy) {

            $newAllergy = new Allergy();
            $newAllergy->name = $allergy;
            $newAllergy->slug = Str::slug($allergy);
            $newAllergy->save();
        }


    }

}
