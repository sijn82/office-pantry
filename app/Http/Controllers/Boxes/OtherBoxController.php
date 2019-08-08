<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\OtherBox;
use App\OtherBoxArchive;
use App\CompanyRoute;
use App\CompanyDetails;
use App\WeekStart;
use App\AssignedRoute;

use App\Product;

class OtherBoxController extends Controller
{
    protected $week_start;
    protected $delivery_days;

    public function __construct()
    {
        $week_start = WeekStart::first();
        
        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }
    }
    // Not sure when I made this, or how complete the export file?
    public function download_otherbox_op_multicompany()
    {

        return \Excel::download(new Exports\OtherBoxesCompanyRouteExportNew, 'otherboxes-all' . $this->week_start . '.xlsx');
    }
    // However this one I'm about to make, so definitely used!
    public function download_otherbox_checklist_op()
    {
        return \Excel::download(new Exports\OtherBoxesChecklistExportNew, 'otherboxes-checklist' . $this->week_start . '.xlsx');
    }
    public function download_otherbox_checklist_weekly_total_op()
    {
        return \Excel::download(new Exports\OtherBoxesWeeklyTotalChecklistExportNew, 'otherboxes-checklist-total' . $this->week_start . '.xlsx');
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
        //
        // Because it generates a unique id based on the time we need to run this once per box only.
        $otherbox_id = request('details.company_details_id') . '-' . uniqid();

        if (!empty($request->order)) {
            
             //dd(request('details.company_details_id'));
            foreach (request('order') as $item) {

                $new_otherbox = new OtherBox();
                // These columns will be the same for each item created in this box
                $new_otherbox->otherbox_id = $otherbox_id;
                $new_otherbox->delivered_by_id = request('details.delivered_by_id');
                $new_otherbox->no_of_boxes = request('details.no_of_boxes');
                $new_otherbox->type = request('details.type');
                $new_otherbox->company_details_id = request('details.company_details_id');
                $new_otherbox->delivery_day = request('details.delivery_day');
                $new_otherbox->frequency = request('details.frequency');
                $new_otherbox->week_in_month = request('details.week_in_month');
                $new_otherbox->next_delivery_week = request('details.next_delivery_week');
                // Now we get to the elements which we want to loop through.
                $new_otherbox->product_id = $item['id'];
                $new_otherbox->code = $item['code'];
                $new_otherbox->name = $item['name'];
                $new_otherbox->quantity = $item['quantity'];
                $new_otherbox->unit_price = $item['unit_price'];
                // For now this is everything, so let's save the new entry to the db.
                $new_otherbox->save();
                
                // Looks I found a neat one liner to sort out reducing stock levels - I'm also guessing 'increment' will sort out returning stock too.
                // Just in case there's a problem saving the new product, we'll only worry about reducing the stock levels if we get this far without hitting an error.
                
                // Added 7/8/19 - look like I forgot to add stock adjustment to all the functions adding/removing stock
                // Suspect there may be more but I'm dating this to know how untested these additions might be.
                Product::find($item['id'])->decrement('stock_level', $item['quantity']);
            }
            
        } else {
            // Make an empty box to fill later on.
            $new_otherbox = new OtherBox();
            // These columns will be the same for each item created in this box
            $new_otherbox->otherbox_id = $otherbox_id;
            $new_otherbox->delivered_by_id = request('details.delivered_by_id');
            $new_otherbox->no_of_boxes = request('details.no_of_boxes');
            $new_otherbox->type = request('details.type');
            $new_otherbox->company_details_id = request('details.company_details_id');
            $new_otherbox->delivery_day = request('details.delivery_day');
            $new_otherbox->frequency = request('details.frequency');
            $new_otherbox->week_in_month = request('details.week_in_month');
            $new_otherbox->next_delivery_week = request('details.next_delivery_week');
            $new_otherbox->save();
        }

        // Only worry about creating a route for the updated day if Office Pantry are delivering the box personally.
        if (request('details.delivered_by_id') === 1) {

            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_details_id', request('details.company_details_id'))->where('delivery_day', request('details.delivery_day'))->get())) {

                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                $companyDetails = CompanyDetails::findOrFail(request('details.company_details_id'));
                
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
     * @param  \App\OtherBox  $otherBox
     * @return \Illuminate\Http\Response
     */
    public function show(OtherBox $otherBox)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OtherBox  $otherBox
     * @return \Illuminate\Http\Response
     */
    public function edit(OtherBox $otherBox)
    {
        //
    }
    
    public function archiveAndEmptyOtherBoxes () 
    {
        $otherboxes = OtherBox::where('is_active', 'Active')->get()->groupBy('otherbox_id');
        dump($otherboxes);
        foreach ($otherboxes as $otherbox) {
             dump($otherbox);
            if (count($otherbox) === 1) {     
            // we're probably looking at an empty box, so the product_id should be 0
             
                if ($otherbox[0]->product_id === 0) {
                // Then all is as expected.
                } else {
                // Something unexpected has happened, let's log it for review. 
                $message = 'Well, shhhiiiitttttt! Drinkbox ' . $otherbox[0]->otherbox_id 
                . ' only has one item in it and it\'s ' . $otherbox[0]->product_id 
                . ' rather than 0. You can find it at row ' . $otherbox[0]->id;
                
                Log::channel('slack')->info($message);    
                }
                
            } elseif (count($otherbox) > 1) {
            // we have a box which needs to be archived
                 
            //---------- Time to save the existing box as an archive ----------//
            
                // 1.(a) if the box has an invoiced_at date, we can save it as 'inactive'.
                if ($otherbox[0]->invoiced_at !== null) {
                    // We have a box that's already been invoiced, so we can save it to archives with an 'inactive' status.
                    foreach ($otherbox as $otherbox_item) {
                        // However if it's the first line in a box and lacks any product info, we don't really need it for invoicing.
                        if ($otherbox_item->product_id !== 0) {
                        
                            $otherbox_archive_entry = new OtherBoxArchive();
                            // Snackbox Info
                            $otherbox_archive_entry->is_active = 'Inactive';
                            $otherbox_archive_entry->otherbox_id = $otherbox_item->otherbox_id;
                            $otherbox_archive_entry->delivered_by_id = $otherbox_item->delivered_by_id;
                            $otherbox_archive_entry->type = $otherbox_item->type;
                            // Company Info
                            $otherbox_archive_entry->company_details_id = $otherbox_item->company_details_id;
                            $otherbox_archive_entry->delivery_day = $otherbox_item->delivery_day;
                            $otherbox_archive_entry->frequency = $otherbox_item->frequency;
                            $otherbox_archive_entry->week_in_month = $otherbox_item->week_in_month;
                            $otherbox_archive_entry->previous_delivery_week = $otherbox_item->previous_delivery_week;
                            $otherbox_archive_entry->next_delivery_week = $otherbox_item->next_delivery_week;
                            // Product Information
                            $otherbox_archive_entry->product_id = $otherbox_item->product_id;
                            $otherbox_archive_entry->code = $otherbox_item->code;
                            $otherbox_archive_entry->name = $otherbox_item->name;
                            $otherbox_archive_entry->quantity = $otherbox_item->quantity;
                            $otherbox_archive_entry->unit_price = $otherbox_item->unit_price;
                            $otherbox_archive_entry->case_price = $otherbox_item->case_price;
                            $otherbox_archive_entry->invoiced_at = $otherbox_item->invoiced_at;
                            $otherbox_archive_entry->save();
                            
                        } else {
                            // Maybe don't need this but we could log that this $drinkbox_item was skipped by logging it.
                            // Log::channel('slack')->info(''); <-- Yeah let's not bother for now.
                        }
                    }
                    
                } else {
                    // 1.(b) if it doesn't, we need to save it to archives as 'active' so it can be pulled into the next invoicing run.
                    foreach ($otherbox as $otherbox_item) {
                        // However if it's the first line in a box and lacks any product info, we don't really need it for invoicing.
                        if ($otherbox_item->product_id !== 0) {
                            
                            $otherbox_archive_entry = new OtherBoxArchive();
                            // Snackbox Info
                            $otherbox_archive_entry->is_active = 'Active';
                            $otherbox_archive_entry->otherbox_id = $otherbox_item->otherbox_id;
                            $otherbox_archive_entry->delivered_by_id = $otherbox_item->delivered_by_id;
                            $otherbox_archive_entry->type = $otherbox_item->type;
                            // Company Info
                            $otherbox_archive_entry->company_details_id = $otherbox_item->company_details_id;
                            $otherbox_archive_entry->delivery_day = $otherbox_item->delivery_day;
                            $otherbox_archive_entry->frequency = $otherbox_item->frequency;
                            $otherbox_archive_entry->week_in_month = $otherbox_item->week_in_month;
                            $otherbox_archive_entry->previous_delivery_week = $otherbox_item->previous_delivery_week;
                            $otherbox_archive_entry->next_delivery_week = $otherbox_item->next_delivery_week;
                            // Product Information
                            $otherbox_archive_entry->product_id = $otherbox_item->product_id;
                            $otherbox_archive_entry->code = $otherbox_item->code;
                            $otherbox_archive_entry->name = $otherbox_item->name;
                            $otherbox_archive_entry->quantity = $otherbox_item->quantity;
                            $otherbox_archive_entry->unit_price = $otherbox_item->unit_price;
                            $otherbox_archive_entry->case_price = $otherbox_item->case_price;
                            $otherbox_archive_entry->invoiced_at = $otherbox_item->invoiced_at;
                            $otherbox_archive_entry->save();
                            
                        } else {
                            // Just for symmetry but my current thinking is to scrap doing anything (and consequently needing) else.
                        }
                    }
                    
                }
                
                //---------- End of - Time to save the existing box as an archive ----------//
                     
                //---------- Now we can strip out the orders ready for adding new products ----------//
                
                // But first we need to grab any details we'll be reusing.
                $otherbox_id_recovered = $otherbox[0]->otherbox_id;
                $delivered_by_recovered = $otherbox[0]->delivered_by_id;
                $delivery_day_recovered = $otherbox[0]->delivery_day;
                $type_recovered = $otherbox[0]->type;
                $company_details_id_recovered = $otherbox[0]->company_details_id;
                $frequency_recovered = $otherbox[0]->frequency;
                $week_in_month_recovered = $otherbox[0]->week_in_month;
                $previous_delivery_week_recovered = $otherbox[0]->previous_delivery_week;
                $next_delivery_week_recovered = $otherbox[0]->next_delivery_week;
                
                // Now we can loop through each entry and delete them
                foreach ($otherbox as $other_item) {
                    // Don't worry, we've rescued all we need ;) ...probably.
                    OtherBox::destroy($other_item->id);
                }
                
                //---------- End of - Now we can strip out the orders ready for adding new products ----------//
                
                //---------- But we still need to recreate the empty box entry to repopulate with products later on. ----------//
                
                // 2. regardless of type, if the otherbox exists we strip out its orders, leaving only 1 entry with box details and a product id of 0, ready for the next mass/solo box update.
                
                $empty_otherbox = new OtherBox();
                // Snackbox Info
                // $new_snackbox->is_active <-- Is already set to 'Active' by default.
                $empty_otherbox->otherbox_id = $otherbox_id_recovered;
                $empty_otherbox->delivered_by_id = $delivered_by_recovered;
                $empty_otherbox->type = $type_recovered;
                // Company Info
                $empty_otherbox->company_details_id = $company_details_id_recovered;
                $empty_otherbox->delivery_day = $delivery_day_recovered;
                $empty_otherbox->frequency = $frequency_recovered;
                $empty_otherbox->week_in_month = $week_in_month_recovered;
                $empty_otherbox->previous_delivery_week = $previous_delivery_week_recovered;
                $empty_otherbox->next_delivery_week = $next_delivery_week_recovered;
                // Product Information
                $empty_otherbox->product_id = 0;
                $empty_otherbox->code = null;
                $empty_otherbox->name = null;
                $empty_otherbox->quantity = null;
                $empty_otherbox->unit_price = null;
                $empty_otherbox->case_price = null;
                $empty_otherbox->invoiced_at = null;
                $empty_otherbox->save();
                
                //---------- End of - But we still need to recreate the empty box entry to repopulate with products later on. ----------//
                     
            } // if (count($otherbox) === 1) & elseif (count($otherbox)) > 1)
        } // foreach ($otherboxes as $otherbox)
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OtherBox  $otherBox
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request)
     {
         //dd(request('snackbox_item_id'));
         OtherBox::where('id', request('otherbox_item_id'))->update([
             'quantity' => request('otherbox_item_quantity'),
         ]);
     }

     public function updateDetails(Request $request)
     {

         $otherbox = OtherBox::where('otherbox_id', request('otherbox_details.otherbox_id'))->get();

         foreach ($otherbox as $otherbox_entry ) {
             // dd($otherbox_entry);
             $otherbox_entry->update([
                 'is_active' => request('otherbox_details.is_active'),
                 'delivered_by_id' => request('otherbox_details.delivered_by_id'),
                 'no_of_boxes' => request('otherbox_details.no_of_boxes'),
                 'type' => request('otherbox_details.type'),
                 'delivery_day' => request('otherbox_details.delivery_day'),
                 'frequency' => request('otherbox_details.frequency'),
                 'week_in_month' => request('otherbox_details.week_in_month'),
                 'next_delivery_week' => request('otherbox_details.next_delivery_week'),
             ]);
         }

         // Only worry about creating a route for the updated day if Office Pantry are delivering the box personally.
         if (request('otherbox_details.delivered_by_id') === 1) {

             // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
             // If there is we're all done, if not, let's build a route.
             if (!count(CompanyRoute::where('company_details_id', request('otherbox_details.company_details_id'))->where('delivery_day', request('otherbox_details.delivery_day'))->get())) {

                 // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                 $companyDetails = CompanyDetails::findOrFail(request('otherbox_details.company_details_id'));
                 
                 $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                 $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                 $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                 $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                 $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();
                 
                 switch (request('otherbox_details.delivery_day')) {
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
                 $newRoute->company_details_id = request('otherbox_details.company_details_id');
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
                 $newRoute->delivery_day = request('otherbox_details.delivery_day');
                 $newRoute->save();


                 $message = "Route $newRoute->route_name on " . request('otherbox_details.delivery_day') . " saved.";
                 Log::channel('slack')->info($message);
             }
         } else {

             $message = "Route on " . request('otherbox_details.delivery_day') . " not necessary, delivered by " . request('otherbox_details.delivered_by_id');
             Log::channel('slack')->info($message);
         }
     }

     public function addProductToOtherbox (Request $request)
     {
         //dd(request('snackbox_details'));
         $addProduct = new OtherBox();
         $addProduct->otherbox_id = request('otherbox_details.otherbox_id');
         $addProduct->is_active = request('otherbox_details.is_active');
         $addProduct->delivered_by_id = request('otherbox_details.delivered_by_id');
         $addProduct->no_of_boxes = request('otherbox_details.no_of_boxes');
         $addProduct->type = request('otherbox_details.type');
         $addProduct->company_details_id = request('otherbox_details.company_details_id');
         $addProduct->delivery_day = request('otherbox_details.delivery_day');
         $addProduct->frequency = request('otherbox_details.frequency');
         $addProduct->week_in_month = request('otherbox_details.week_in_month');
         $addProduct->previous_delivery_week = request('otherbox_details.previous_delivery_week');
         $addProduct->next_delivery_week = request('otherbox_details.next_delivery_week');
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
     * @param  \App\OtherBox  $otherBox
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OtherBox::destroy($id);
    }
    
    //---------- These 2 functions below are currently just copied from the snackbox controller - in case you couldn't already guess! ----------//
    // I'll update it to work with otherboxes when I get to this point.
    
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
        $otherbox_total_items = OtherBox::where('otherbox_id', request('otherbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the snackbox entry...
        $otherbox_item = OtherBox::find(request('id'));
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        Product::find($otherbox_item->product_id)->increment('stock_level', $otherbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($otherbox_total_items) === 1) {
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            
            OtherBox::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            OtherBox::destroy($id);
        }
        
    }
    
    public function destroyBox(Request $request)
    {
        $otherbox = OtherBox::where('otherbox_id', request('otherbox_id'))->get();
        // dd($snackbox);
        foreach ($otherbox as $otherbox_item) {
            OtherBox::destroy($otherbox_item->id);
        }
    }
}
