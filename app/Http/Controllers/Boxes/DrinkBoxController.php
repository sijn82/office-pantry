<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DrinkBox;
use App\CompanyRoute;
use App\AssignedRoute;
use App\WeekStart;
// use App\Company;
use App\CompanyDetails;
use App\Product;

use App\DrinkBoxArchive;

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
                $new_drinkbox->delivery_week = request('details.delivery_week');
                // Now we get to the elements which we want to loop through.
                $new_drinkbox->product_id = $item['id'];
                $new_drinkbox->code = $item['code'];
                $new_drinkbox->name = $item['name'];
                $new_drinkbox->quantity = $item['quantity'];
                $new_drinkbox->unit_price = $item['unit_price'];
                // For now this is everything, so let's save the new entry to the db.
                $new_drinkbox->save();
                
                // Added 7/8/19 - look like I forgot to add stock adjustment to all the functions adding/removing stock
                // Suspect there may be more but I'm dating this to know how untested these additions might be.
                Product::find($item['id'])->decrement('stock_level', $item['quantity']);
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
            $new_drinkbox->delivery_week = request('details.delivery_week');
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
                $newRoute->address = implode(", ", array_filter([
                        $companyDetails->route_address_line_1, 
                        $companyDetails->route_address_line_2, 
                        $companyDetails->route_address_line_3, 
                        $companyDetails->route_city, 
                        $companyDetails->route_region
                    ]));
                
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
    
    public function archiveAndEmptyDrinkBoxes () 
    {
        $drinkboxes = DrinkBox::where('is_active', 'Active')->get()->groupBy('drinkbox_id');
        dump($drinkboxes);
        foreach ($drinkboxes as $drinkbox) {
             dump($drinkbox);
            if (count($drinkbox) === 1) {     
            // we're probably looking at an empty box, so the product_id should be 0
             
                if ($drinkbox[0]->product_id === 0) {
                // Then all is as expected.
                } else {
                // Something unexpected has happened, let's log it for review. 
                $message = 'Well, shhhiiiitttttt! Drinkbox ' . $drinkbox[0]->drinkbox_id 
                . ' only has one item in it and it\'s ' . $drinkbox[0]->product_id 
                . ' rather than 0. You can find it at row ' . $drinkbox[0]->id;
                
                Log::channel('slack')->info($message);    
                }
                
            } elseif (count($drinkbox) > 1) {
            // we have a box which needs to be archived
                 
            //---------- Time to save the existing box as an archive ----------//
            
                // 1.(a) if the box has an invoiced_at date, we can save it as 'inactive'.
                if ($drinkbox[0]->invoiced_at !== null) {
                    // We have a box that's already been invoiced, so we can save it to archives with an 'inactive' status.
                    foreach ($drinkbox as $drinkbox_item) {
                        // However if it's the first line in a box and lacks any product info, we don't really need it for invoicing.
                        if ($drinkbox_item->product_id !== 0) {
                        
                            $drinkbox_archive_entry = new DrinkBoxArchive();
                            // Snackbox Info
                            $drinkbox_archive_entry->is_active = 'Inactive';
                            $drinkbox_archive_entry->drinkbox_id = $drinkbox_item->drinkbox_id;
                            $drinkbox_archive_entry->delivered_by_id = $drinkbox_item->delivered_by_id;
                            $drinkbox_archive_entry->type = $drinkbox_item->type;
                            // Company Info
                            $drinkbox_archive_entry->company_details_id = $drinkbox_item->company_details_id;
                            $drinkbox_archive_entry->delivery_day = $drinkbox_item->delivery_day;
                            $drinkbox_archive_entry->frequency = $drinkbox_item->frequency;
                            $drinkbox_archive_entry->week_in_month = $drinkbox_item->week_in_month;
                            $drinkbox_archive_entry->previous_delivery_week = $drinkbox_item->previous_delivery_week;
                            $drinkbox_archive_entry->delivery_week = $drinkbox_item->delivery_week;
                            // Product Information
                            $drinkbox_archive_entry->product_id = $drinkbox_item->product_id;
                            $drinkbox_archive_entry->code = $drinkbox_item->code;
                            $drinkbox_archive_entry->name = $drinkbox_item->name;
                            $drinkbox_archive_entry->quantity = $drinkbox_item->quantity;
                            $drinkbox_archive_entry->unit_price = $drinkbox_item->unit_price;
                            $drinkbox_archive_entry->case_price = $drinkbox_item->case_price;
                            $drinkbox_archive_entry->invoiced_at = $drinkbox_item->invoiced_at;
                            $drinkbox_archive_entry->save();
                            
                        } else {
                            // Maybe don't need this but we could log that this $drinkbox_item was skipped by logging it.
                            // Log::channel('slack')->info(''); <-- Yeah let's not bother for now.
                        }
                    }
                    
                } else {
                    // 1.(b) if it doesn't, we need to save it to archives as 'active' so it can be pulled into the next invoicing run.
                    foreach ($drinkbox as $drinkbox_item) {
                        // However if it's the first line in a box and lacks any product info, we don't really need it for invoicing.
                        if ($drinkbox_item->product_id !== 0) {
                            
                            $drinkbox_archive_entry = new DrinkBoxArchive();
                            // Snackbox Info
                            $drinkbox_archive_entry->is_active = 'Active';
                            $drinkbox_archive_entry->drinkbox_id = $drinkbox_item->drinkbox_id;
                            $drinkbox_archive_entry->delivered_by_id = $drinkbox_item->delivered_by_id;
                            $drinkbox_archive_entry->type = $drinkbox_item->type;
                            // Company Info
                            $drinkbox_archive_entry->company_details_id = $drinkbox_item->company_details_id;
                            $drinkbox_archive_entry->delivery_day = $drinkbox_item->delivery_day;
                            $drinkbox_archive_entry->frequency = $drinkbox_item->frequency;
                            $drinkbox_archive_entry->week_in_month = $drinkbox_item->week_in_month;
                            $drinkbox_archive_entry->previous_delivery_week = $drinkbox_item->previous_delivery_week;
                            $drinkbox_archive_entry->delivery_week = $drinkbox_item->delivery_week;
                            // Product Information
                            $drinkbox_archive_entry->product_id = $drinkbox_item->product_id;
                            $drinkbox_archive_entry->code = $drinkbox_item->code;
                            $drinkbox_archive_entry->name = $drinkbox_item->name;
                            $drinkbox_archive_entry->quantity = $drinkbox_item->quantity;
                            $drinkbox_archive_entry->unit_price = $drinkbox_item->unit_price;
                            $drinkbox_archive_entry->case_price = $drinkbox_item->case_price;
                            $drinkbox_archive_entry->invoiced_at = $drinkbox_item->invoiced_at;
                            $drinkbox_archive_entry->save();
                            
                        } else {
                            // Just for symmetry but my current thinking is to scrap doing anything (and consequently needing) else.
                        }
                    }
                    
                }
                
                //---------- End of - Time to save the existing box as an archive ----------//
                     
                //---------- Now we can strip out the orders ready for adding new products ----------//
                
                // But first we need to grab any details we'll be reusing.
                $drinkbox_id_recovered = $drinkbox[0]->drinkbox_id;
                $delivered_by_recovered = $drinkbox[0]->delivered_by_id;
                $delivery_day_recovered = $drinkbox[0]->delivery_day;
                $type_recovered = $drinkbox[0]->type;
                $company_details_id_recovered = $drinkbox[0]->company_details_id;
                $frequency_recovered = $drinkbox[0]->frequency;
                $week_in_month_recovered = $drinkbox[0]->week_in_month;
                $previous_delivery_week_recovered = $drinkbox[0]->previous_delivery_week;
                $delivery_week_recovered = $drinkbox[0]->delivery_week;
                
                // Now we can loop through each entry and delete them
                foreach ($drinkbox as $drink_item) {
                    // Don't worry, we've rescued all we need ;) ...probably.
                    DrinkBox::destroy($drink_item->id);
                }
                
                //---------- End of - Now we can strip out the orders ready for adding new products ----------//
                
                //---------- But we still need to recreate the empty box entry to repopulate with products later on. ----------//
                
                // 2. regardless of type, if the snackbox exists we strip out its orders, leaving only 1 entry with box details and a product id of 0, ready for the next mass/solo box update.
                
                $empty_drinkbox = new DrinkBox();
                // Snackbox Info
                // $new_snackbox->is_active <-- Is already set to 'Active' by default.
                $empty_drinkbox->drinkbox_id = $drinkbox_id_recovered;
                $empty_drinkbox->delivered_by_id = $delivered_by_recovered;
                $empty_drinkbox->type = $type_recovered;
                // Company Info
                $empty_drinkbox->company_details_id = $company_details_id_recovered;
                $empty_drinkbox->delivery_day = $delivery_day_recovered;
                $empty_drinkbox->frequency = $frequency_recovered;
                $empty_drinkbox->week_in_month = $week_in_month_recovered;
                $empty_drinkbox->previous_delivery_week = $previous_delivery_week_recovered;
                $empty_drinkbox->delivery_week = $delivery_week_recovered;
                // Product Information
                $empty_drinkbox->product_id = 0;
                $empty_drinkbox->code = null;
                $empty_drinkbox->name = null;
                $empty_drinkbox->quantity = null;
                $empty_drinkbox->unit_price = null;
                $empty_drinkbox->case_price = null;
                $empty_drinkbox->invoiced_at = null;
                $empty_drinkbox->save();
                
                //---------- End of - But we still need to recreate the empty box entry to repopulate with products later on. ----------//
                     
            } // if (count($drinkbox) === 1) & elseif (count($drinkbox)) > 1)
        } // foreach ($drinkboxes as $drinkbox)
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
                 'delivery_week' => request('drinkbox_details.delivery_week'),
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
         $addProduct->delivery_week = request('drinkbox_details.delivery_week');
         $addProduct->product_id = request('product.id');
         $addProduct->code = request('product.code');
         $addProduct->name = request('product.name');
         $addProduct->quantity = request('product.quantity');
         $addProduct->unit_price = request('product.unit_price');
         $addProduct->save();
         
         // This currently looks to add products to a box but without reducing the stock level for the product!
         // I rather suspect there might be a few more of these to find.
         
         //---------- Adjust stock levels ----------//

             // Now we need to sort out the stock levels for these order items, keeping them in check and hopefully 100% accurate!
             //  If these order items get cancelled for any reason, we must remember to add them back in too!!

             // Looks I found a neat one liner to sort out reducing stock levels - I'm also guessing 'increment' will sort out returning stock too.
             // Just in case there's a problem saving the new product, we'll only worry about reducing the stock levels if we get this far without hitting an error.
             Product::find(request('product.id'))->decrement('stock_level', request('product.quantity'));

         //---------- End of Adjust stock levels ----------//
          
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
        $drinkbox_total_items = DrinkBox::where('drinkbox_id', request('drinkbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the snackbox entry...
        $drinkbox_item = DrinkBox::find(request('id'));
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        Product::find($drinkbox_item->product_id)->increment('stock_level', $drinkbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($drinkbox_total_items) === 1) {
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            
            DrinkBox::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
                'case_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            DrinkBox::destroy($id);
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
