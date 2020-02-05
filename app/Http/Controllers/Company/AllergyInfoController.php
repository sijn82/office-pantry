<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\CompanyDetails;
use App\AllergyInfo;
use App\Product;
use App\Allergy;

class AllergyInfoController extends Controller
{
    // Not sure if I really need a specific controller for this but
    // let's make the new allergy info from products (allergy info) replacement function here.

    public function buildAllergiesFromProductList()
    {
        $products = Product::all();

        foreach ($products as $product) {
            //dd($product->allergen_info);
            foreach ($product->allergen_info as $allergen) {

                // This model Allergy will become a short list of the possible allergens.  I guess this also means we could add some more, if needed.
                // They're currently saved as lower case hyphenated words, since there aren't many allergies to input I'm going to include a column
                // called 'slug' which will have the matching formatting.

                $allergy = Allergy::where('slug', $allergen)->first();

                $newProductAllergenInfo = new AllergyInfo();
                $newProductAllergenInfo->allergy_id = $allergy->id;
                $newProductAllergenInfo->connection_id = $product->id;
                $newProductAllergenInfo->connection_type = 'App\\Product';
                $newProductAllergenInfo->save();
            }
        }
    }

    public function addCompanyAllergies(Request $request)
    {
        DB::transaction(function () {
            // We need to get/use the company details id and assign it to the connection_id
            $company = CompanyDetails::findOrFail(request('selected_company'));
            // After calling $company->allergy_info we'll connection_type and I guess the connection_id,
            // so all we need is to save the allergy_id.

            foreach (request('selected_allergens') as $selected_allergy) {

                //dd($selected_allergy);

                $allergy = Allergy::where('slug', $selected_allergy)->first();
                // Due to the relationship already established, the connection_id and connection_type are automatically added.
                // This just leaves the allergy_id which I think has to be added by grabbing the matching allergy entry and saving it below.
                //dd($allergy);
                $company->allergy_info()->create([
                    'allergy_id' => $allergy->id,
                ]);
            }
        });
    }

    public function updateCompanyAllergies(Request $request)
    {
        DB::transaction(function () {
            $company = CompanyDetails::findOrFail(request('selected_company'));
            $selected_allergens = request('selected_allergens');

            // This will check all held allergies for this company, removing them from the requested allergies to submit
            // i.e request('selected_allergies').  If the allergy isn't in the array, we're going to remove it from the held values.
            foreach ($company->allergy_info as $info) {

                if (in_array($info->allergy->slug, $selected_allergens)) {
                    // Then we already have a record of this allergy/company combo
                    $key = array_search($info->allergy->slug, $selected_allergens);
                    // Get the key for this item in the array and remove it (from consideration).
                    unset($selected_allergens[$key]);
                } else {
                    // We have a record of the Company/Allergy but this no longer seems to be the case.
                    // Perhaps it was added by mistake, or the employee susceptible to the allergy has moved elsewhere.
                    AllergyInfo::destroy($info->id);
                }
            }

            // Now we can add the remaining allergies from $selected_allergens as they're not currently associated with the company.
            foreach ($selected_allergens as $selected_allergy) {
                $allergy = Allergy::where('slug', $selected_allergy)->first();
                // Due to the relationship already established, the connection_id and connection_type are automatically added.
                // This just leaves the allergy_id which I think has to be added by grabbing the matching allergy entry and saving it below.
                //dd($allergy);
                $company->allergy_info()->create([
                    'allergy_id' => $allergy->id,
                ]);
            }
        });
    }

}
