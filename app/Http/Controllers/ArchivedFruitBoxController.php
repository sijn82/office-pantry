<?php

namespace App\Http\Controllers;

use App\FruitBox;
use App\FruitBoxArchive;
// use App\Company;
use App\CompanyDetails;
use App\CompanyRoute;
use App\FruitPartner;
use App\WeekStart;
use App\AssignedRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Carbon\CarbonImmutable;

class ArchivedFruitBoxController extends Controller
{

    public function updateArchivedFruitBox(Request $request)
    {
        // Haha, oops I need to pass through the whole model not just the id!
        // dd(request('fruitbox_total')); // <-- actually it also needed the Request as well as $request passed into the function.

        FruitBoxArchive::where('id', request('id'))->update([
            // 'id' => request('id'), // this shouldn't change so I could delete it but nahh...
            'is_active' => request('is_active'),
            'fruit_partner_id' => request('fruit_partner_id'),
            'name' => request('name'),
            // 'company_details_id' => request('company_details_id'),
            // 'route_id' => request('route_id'),
            // 'type' => request('type'), // I'm not currently offering an update to box type, instead a new box should be created.
            'next_delivery' => request('next_delivery'), // Though for these purposes, as an archived box, it's the week delivered.
            'delivery_day' => request('delivery_day'),
            'frequency' => request('frequency'),
            'week_in_month' => request('week_in_month'),
            'fruitbox_total' => request('fruitbox_total'),
            'deliciously_red_apples' => request('deliciously_red_apples'),
            'pink_lady_apples' => request('pink_lady_apples'),
            'red_apples' => request('red_apples'),
            'green_apples' => request('green_apples'),
            'satsumas' => request('satsumas'),
            'pears' => request('pears'),
            'bananas' => request('bananas'),
            'nectarines' => request('nectarines'),
            'limes' => request('limes'),
            'lemons' => request('lemons'),
            'grapes' => request('grapes'),
            'seasonal_berries' => request('seasonal_berries'),
            'oranges' => request('oranges'),
            'cucumbers' => request('cucumbers'),
            'mint' => request('mint'),
            'organic_lemons' => request('organic_lemons'),
            'kiwis' => request('kiwis'),
            'grapefruits' => request('grapefruits'),
            'avocados' => request('avocados'),
            'root_ginger' => request('root_ginger'),
            'tailoring_fee' => request('tailoring_fee'),
            'discount_multiple' => request('discount_multiple'),
        ]);
    }

    public function deleteArchivedFruitBox($id)
    {
        dd('If I hadn\'t stopped it, fruitbox archive entry ' . $id . ' would have been deleted!');
        FruitBoxArchive::destroy($id);
    }

}
