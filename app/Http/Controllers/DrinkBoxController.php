<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DrinkBox;
use App\CompanyRoute;
use App\Company;

class DrinkBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        // Because it generates a unique id based on the time we need to run this once per box only.
        $drinkbox_id = request('details.company_id') . '-' . uniqid();
        
        // dd(request('details.company_id'));
        foreach (request('order') as $item) {
    
            $new_drinkbox = new DrinkBox();
            // These columns will be the same for each item created in this box
            $new_drinkbox->drinkbox_id = $drinkbox_id;
            $new_drinkbox->delivered_by_id = request('details.delivered_by_id');
            $new_drinkbox->no_of_boxes = request('details.no_of_boxes');
            $new_drinkbox->company_id = request('details.company_id');
            $new_drinkbox->delivery_day = request('details.delivery_day');
            $new_drinkbox->frequency = request('details.frequency');
            $new_drinkbox->week_in_month = request('details.week_in_month');
            $new_drinkbox->next_delivery_week = request('details.next_delivery_week');
            // Now we get to the elements which we want to loop through.
            $new_drinkbox->product_id = $item['id'];
            $new_drinkbox->code = $item['code'];
            $new_drinkbox->name = $item['name'];
            $new_drinkbox->quantity = $item['quantity'];
            $new_drinkbox->unit_price = $item['unit_price'];
            // For now this is everything, so let's save the new entry to the db.
            $new_drinkbox->save();
        }
        
        // We only want to create a route if Office Pantry are delivering it directly.
        if (request('details.delivered_by_id') === 1) {
        
            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_id', request('details.company_id'))->where('delivery_day', request('details.delivery_day'))->get())) {
                
                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                $companyDetails = Company::findOrFail(request('details.company_id'));

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_id = request('details.company_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->postcode;
                $newRoute->address = $companyDetails->route_summary_address;
                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->delivery_day = request('details.delivery_day');
                $newRoute->save();

                $message = "Route $newRoute->route_name on " . request('details.delivery_day') . " saved.";
                Log::channel('slack')->info($message);
            }
            
        } else {
        
            $message = "Route on " . request('details.delivery_day') . " not necessary, delivered by " . request('details.delivered_by_id');
            Log::channel('slack')->info($message);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request)
     {
         DrinkBox::where('id', request('drinkbox_item_id'))->update([
             'quantity' => request('drinkbox_item_quantity'),
         ]);
     }
     
     public function updateDetails(Request $request)
     {
         //dd(request('snackbox_details'));
         $drinkbox = DrinkBox::where('drinkbox_id', request('drinkbox_details.drinkbox_id'))->get();
         
         foreach ($drinkbox as $drinkbox_entry ) {
             // dd(request('drinkbox_details.delivered_by_id'));
             // dd($drinkbox_entry);
             $drinkbox_entry->update([
                 'is_active' => request('drinkbox_details.is_active'),
                 'delivered_by_id' => request('drinkbox_details.delivered_by_id'),
                 'no_of_boxes' => request('drinkbox_details.no_of_boxes'),
                 'delivery_day' => request('drinkbox_details.delivery_day'),
                 'frequency' => request('drinkbox_details.frequency'),
                 'week_in_month' => request('drinkbox_details.week_in_month'),
                 'next_delivery_week' => request('drinkbox_details.next_delivery_week'),
             ]);
         }
         
         // Only worry about creating a route for the updated day if Office Pantry are delivering the box personally.
         if (request('drinkbox_details.delivered_by_id') === 1) {
         
             // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
             // If there is we're all done, if not, let's build a route.
             if (!count(CompanyRoute::where('company_id', request('drinkbox_details.company_id'))->where('delivery_day', request('drinkbox_details.delivery_day'))->get())) {
                 
                 // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                 $companyDetails = Company::findOrFail(request('drinkbox_details.company_id'));

                 // We need to create a new entry.
                 $newRoute = new CompanyRoute();
                 // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                 $newRoute->company_id = request('drinkbox_details.company_id');
                 $newRoute->route_name = $companyDetails->route_name;
                 $newRoute->postcode = $companyDetails->postcode;
                 $newRoute->address = $companyDetails->route_summary_address;
                 $newRoute->delivery_information = $companyDetails->delivery_information;
                 $newRoute->delivery_day = request('drinkbox_details.delivery_day');
                 $newRoute->save();

                 $message = "Route $newRoute->route_name on " . request('drinkbox_details.delivery_day') . " saved.";
                 Log::channel('slack')->info($message);
             }
             
         } else {
             
             $message = "Route on " . request('drinkbox_details.delivery_day') . " not necessary, delivered by " . request('drinkbox_details.delivered_by');
             Log::channel('slack')->info($message);
         }
     }
     
     public function addProductToDrinkbox (Request $request)
     {
         //dd(request('snackbox_details'));
         $addProduct = new DrinkBox();
         $addProduct->drinkbox_id = request('drinkbox_details.drinkbox_id');
         $addProduct->is_active = request('drinkbox_details.is_active');
         $addProduct->delivered_by_id = request('drinkbox_details.delivered_by_id');
         $addProduct->no_of_boxes = request('drinkbox_details.no_of_boxes');
         $addProduct->company_id = request('drinkbox_details.company_id');
         $addProduct->delivery_day = request('drinkbox_details.delivery_day');
         $addProduct->frequency = request('drinkbox_details.frequency');
         $addProduct->week_in_month = request('drinkbox_details.week_in_month');
         $addProduct->previous_delivery_week = request('drinkbox_details.previous_delivery_week');
         $addProduct->next_delivery_week = request('drinkbox_details.next_delivery_week');
         $addProduct->product_id = request('product.id');
         $addProduct->code = request('product.code');
         $addProduct->name = request('product.name');
         $addProduct->quantity = request('product.quantity');
         $addProduct->unit_price = request('product.unit_price');
         $addProduct->save();
     }
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DrinkBox::destroy($id);
    }
}
