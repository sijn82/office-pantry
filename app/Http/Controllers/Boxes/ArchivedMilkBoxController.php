<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use App\MilkBox;
use App\MilkBoxArchive;
// use App\Company;
use App\CompanyDetails;
use App\CompanyRoute;
use App\FruitPartner;
use App\WeekStart;
use App\AssignedRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Carbon\CarbonImmutable;

class ArchivedMilkBoxController extends Controller
{
    public function updateArchivedMilkBox(Request $request)
    {
        MilkBoxArchive::where('id', request('id'))->update([

            'is_active' => request('is_active'),
            'fruit_partner_id' => request('fruit_partner_id'),
            'company_details_id' => request('company_details_id'),
            'previous_delivery' => request('previous_delivery'),
            'next_delivery' => request('next_delivery'),
            'frequency' => request('frequency'),
            'week_in_month' => request('week_in_month'),
            'delivery_day' => request('delivery_day'), // Moved this data up to the initial check.
            // Milk 2l
            'semi_skimmed_2l' => request('semi_skimmed_2l'),
            'skimmed_2l' => request('skimmed_2l'),
            'whole_2l' => request('whole_2l'),
            // Milk 1l
            'semi_skimmed_1l' => request('semi_skimmed_1l'),
            'skimmed_1l' => request('skimmed_1l'),
            'whole_1l' => request('whole_1l'),
            // Organic Milk 2l
            'organic_semi_skimmed_2l' => request('organic_semi_skimmed_2l'),
            'organic_skimmed_2l' => request('organic_skimmed_2l'),
            'organic_whole_2l' => request('organic_whole_2l'),
            // Organic Milk 1l
            'organic_semi_skimmed_1l' => request('organic_semi_skimmed_1l'),
            'organic_skimmed_1l' => request('organic_skimmed_1l'),
            'organic_whole_1l' => request('organic_whole_1l'),
            // Milk Alternatives
            'milk_1l_alt_coconut' => request('milk_1l_alt_coconut'),
            'milk_1l_alt_unsweetened_almond' => request('milk_1l_alt_unsweetened_almond'),
            'milk_1l_alt_almond' => request('milk_1l_alt_almond'),
            // Milk Alternatives (Pt2)
            'milk_1l_alt_unsweetened_soya' => request('milk_1l_alt_unsweetened_soya'),
            'milk_1l_alt_soya' => request('milk_1l_alt_soya'),
            'milk_1l_alt_oat' => request('milk_1l_alt_oat'),
            // Milk Alternatives (Pt3)
            'milk_1l_alt_rice' => request('milk_1l_alt_rice'),
            'milk_1l_alt_cashew' => request('milk_1l_alt_cashew'),
            'milk_1l_alt_lactose_free_semi' => request('milk_1l_alt_lactose_free_semi'),
        ]);
    }
    
    public function deleteArchivedMilkBox($id)
    {
        dd('If I hadn\'t stopped it, milkbox archive entry ' . $id . ' would have been deleted!');
        MilkBoxArchive::destroy($id);
    }

}