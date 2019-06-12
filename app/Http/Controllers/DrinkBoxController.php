<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DrinkBox;
use App\CompanyRoute;
use App\AssignedRoute;
use App\WeekStart;
// use App\Company;
use App\CompanyDetails;

class DrinkBoxController extends Controller
{
    protected $week_start;

    public function __construct()
    {
        $week_start = WeekStart::first();
        
        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }

    }
    
    public function download_drinkbox_wholesale_op_multicompany()
    {
        session()->put('drinkbox_courier', '1');

        return \Excel::download(new Exports\DrinkboxWholesaleExport, 'drinkboxesWholesaleOPMultiCompany' . $this->week_start . '.xlsx');
    }
    
    public function download_drinkbox_wholesale_weekly_op_multicompany()
    {
        session()->put('drinkbox_courier', '1');

        return \Excel::download(new Exports\DrinkboxWholesaleWeekExport, 'drinkboxes-Weekly-WholesaleOPMultiCompany' . $this->week_start . '.xlsx');
    }
    
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
        // dd($request);
    
        // Because it generates a unique id based on the time we need to run this once per box only.
        $drinkbox_id = request('details.company_details_id') . '-' . uniqid();
        
        if (!empty($request->order)) {
        
            // dd(request('details.company_details_id'));
            foreach (request('order') as $item) {
        
                $new_drinkbox = new DrinkBox();
                // These columns will be the same for each item created in this box
                $new_drinkbox->drinkbox_id = $drinkbox_id;
                $new_drinkbox->delivered_by_id = request('details.delivered_by_id');
                $new_drinkbox->type = request('details.type');
                $new_drinkbox->company_details_id = request('details.company_details_id');
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
        
        } else {
            // Make an empty box with all the necessary details.
            $new_drinkbox = new DrinkBox();
            // These columns will be the same for each item created in this box
            $new_drinkbox->drinkbox_id = $drinkbox_id;
            $new_drinkbox->delivered_by_id = request('details.delivered_by_id');
            $new_drinkbox->type = request('details.type');
            $new_drinkbox->company_details_id = request('details.company_details_id');
            $new_drinkbox->delivery_day = request('details.delivery_day');
            $new_drinkbox->frequency = request('details.frequency');
            $new_drinkbox->week_in_month = request('details.week_in_month');
            $new_drinkbox->next_delivery_week = request('details.next_delivery_week');
            $new_drinkbox->save();
        }
        
        // We only want to create a route if Office Pantry are delivering it directly.
        if (request('details.delivered_by_id') === 1) {
        
            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_details_id', request('details.company_details_id'))->where('delivery_day', request('details.delivery_day'))->get())) {
                
                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                // $companyDetails = Company::findOrFail(request('details.company_id'));
                $companyDetails = CompanyDetails::findOrFail(request('details.company_details_id'));
                
                $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();
                
                switch (request('details.delivery_day')) {
                    case 'Monday':
                        $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                        break;
                    case 'Tuesday':
                        $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                        break;
                    case 'Wednesday':
                        $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                        break;
                    case 'Thursday':
                        $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                        break;
                    case 'Friday':
                        $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                        break;
                }
                
                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_details_id = request('details.company_details_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->route_postcode;
                
                //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                // $newRoute->address = $companyDetails->route_summary_address;
                
                // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
                $newRoute->address = $companyDetails->route_address_line_1 . ', '
                                    . $companyDetails->route_address_line_2 . ', '
                                    . $companyDetails->route_address_line_3 . ', '
                                    . $companyDetails->route_city . ', '
                                    . $companyDetails->route_region . ', '
                                    . $companyDetails->route_postcode;
                
                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->assigned_route_id = $assigned_route_id;
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
                 'type' => request('drinkbox_details.type'),
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
             if (!count(CompanyRoute::where('company_details_id', request('drinkbox_details.company_details_id'))->where('delivery_day', request('drinkbox_details.delivery_day'))->get())) {
                 
                 // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                 // $companyDetails = Company::findOrFail(request('drinkbox_details.company_id'));
                 $companyDetails = CompanyDetails::findOrFail(request('drinkbox_details.company_details_id'));
                 
                 $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                 $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                 $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                 $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                 $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();
                 
                 switch (request('delivery_day')) {
                     case 'Monday':
                         $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                         break;
                     case 'Tuesday':
                         $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                         break;
                     case 'Wednesday':
                         $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                         break;
                     case 'Thursday':
                         $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                         break;
                     case 'Friday':
                         $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                         break;
                 }
                 
                 // We need to create a new entry.
                 $newRoute = new CompanyRoute();
                 // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                 $newRoute->company_details_id = request('drinkbox_details.company_details_id');
                 $newRoute->route_name = $companyDetails->route_name;
                 $newRoute->postcode = $companyDetails->route_postcode;
                 
                 //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                 // $newRoute->address = $companyDetails->route_summary_address;
                 
                 // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
                 $newRoute->address = $companyDetails->route_address_line_1 . ', '
                                     . $companyDetails->route_address_line_2 . ', '
                                     . $companyDetails->route_address_line_3 . ', '
                                     . $companyDetails->route_city . ', '
                                     . $companyDetails->route_region . ', '
                                     . $companyDetails->route_postcode;
                 
                 $newRoute->delivery_information = $companyDetails->delivery_information;
                 $newRoute->assigned_route_id = $assigned_route_id;
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
         $addProduct->type = request('drinkbox_details.type');
         $addProduct->company_details_id = request('drinkbox_details.company_details_id');
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
        DrinkBox::destroy($id); // I don't think I'm using this function and now I've split it into two different roles.
    }
    
    //---------- These 2 functions below are currently just copied from the snackbox controller - in case you couldn't already guess! ----------//
    // I'll update it to work with drinkboxes when I get to this point.
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyItem($id, Request $request)
    {
        // We need some logic here to decide if the item to be deleted is the last item in the snackbox.
        // Grab all the entries with the same snackbox_id.
        $snackbox_total_items = SnackBox::where('snackbox_id', request('snackbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the snackbox entry...
        $snackbox_item = SnackBox::find(request('id'));
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        Product::find($snackbox_item->product_id)->increment('stock_level', $snackbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($snackbox_total_items) === 1) {
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            
            SnackBox::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            SnackBox::destroy($id);
        }
        
    }
    
    public function destroyBox(Request $request)
    {
        $drinkbox = DrinkBox::where('drinkbox_id', request('drinkbox_id'))->get();
        // dd($snackbox);
        foreach ($drinkbox as $drinkbox_item) {
            DrinkBox::destroy($drinkbox_item->id);
        }
    }
    
}
