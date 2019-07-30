<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Session;

use Illuminate\Http\Request;
use App\SnackBox;
use App\SnackBoxArchive;
use App\WeekStart;
use App\Product;
use App\Preference;
// use App\Company;
use App\CompanyDetails;
use App\CompanyRoute;
use App\AssignedRoute;


class ArchivedSnackBoxController extends Controller
{
    
    // This function is just used to update the snackbox company/delivery info - everything but the contents basically.
    // If the delivery day is changed then a check is made to see if we have a route for them already (on that day), creating it for them if not.
    public function updateDetails(Request $request)
    {
        // dd(request('snackbox_details'));
        $snackbox = SnackBoxArchive::where('snackbox_id', request('archived_snackbox_details.snackbox_id'))->get();

        foreach ($snackbox as $snackbox_entry ) {
            $snackbox_entry->update([
                'is_active' => request('archived_snackbox_details.is_active'),
                'delivered_by' => request('archived_snackbox_details.delivered_by'),
                'no_of_boxes' => request('archived_snackbox_details.no_of_boxes'),
                'snack_cap' => request('archived_snackbox_details.snack_cap'),
                'type' => request('archived_snackbox_details.type'),
                'delivery_day' => request('archived_snackbox_details.delivery_day'),
                'frequency' => request('archived_snackbox_details.frequency'),
                'week_in_month' => request('archived_snackbox_details.week_in_month'),
                'next_delivery_week' => request('archived_snackbox_details.next_delivery_week'),
            ]);
        }

        // We only want to create a route if Office Pantry are delivering it directly.
        if (request('archived_snackbox_details.delivered_by') === 'OP') {

            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_details_id', request('archived_snackbox_details.company_details_id'))->where('delivery_day', request('archived_snackbox_details.delivery_day'))->get())) {

                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                // $companyDetails = Company::findOrFail(request('snackbox_details.company_id'));
                $companyDetails = CompanyDetails::findOrFail(request('archived_snackbox_details.company_details_id'));

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
                $newRoute->company_details_id = request('archived_snackbox_details.company_details_id');
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
                $newRoute->delivery_day = request('archived_snackbox_details.delivery_day');
                $newRoute->save();


                $message = "Route $newRoute->route_name on " . request('archived_snackbox_details.delivery_day') . " saved.";
                Log::channel('slack')->info($message);
            }
        } else {

            $message = "Route on " . request('archived_snackbox_details.delivery_day') . " not necessary, delivered by " . request('archived_snackbox_details.delivered_by');
            Log::channel('slack')->info($message);
        }
    }
    
    
    public function destroyItem($id, Request $request)
    {
        // We need some logic here to decide if the item to be deleted is the last item in the snackbox.
        // Grab all the entries with the same snackbox_id.
        $archived_snackbox_total_items = SnackBoxArchive::where('snackbox_id', request('snackbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the snackbox entry...
        $archived_snackbox_item = SnackBoxArchive::find(request('id'));
        
        // New addition, if the snackbox is wholesale we need to multiply the quantity by case size in order to get an accurate number of units to return to stock.
        if (request('type') === 'wholesale') {
                // currently untested...
                $product_case_size = Product::where($archived_snackbox_item->product_id)->pluck('case_size')->first();
                $case_to_unit_adjustment = ($product_case_size * $archived_snackbox_item->quantity);
                Product::find($archived_snackbox_item->product_id)->increment('stock_level', $case_to_unit_adjustment);
        }
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        Product::find($archived_snackbox_item->product_id)->increment('stock_level', $archived_snackbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($archived_snackbox_total_items) === 1) {
            
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            SnackBoxArchive::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
                'case_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            SnackBoxArchive::destroy($id);
        }
        
    }

    public function destroyBox(Request $request)
    {
        // dd($request);
        // I've just added the extra specificity of 'next_delivery_date' to try and prevent the destruction of all entries in the archive under that snackbox_id.
        // My thinking being that there could a situation where we need to delete a box (undelivered or whatever)
        // for one week while keeping the remaining outstanding invoices of successfully delivered boxes.
        // Especially for monthly invoicing but also so as not to lose archives for our records because of one mistake.
        $archived_snackbox = SnackBoxArchive::where('snackbox_id', request('archived_snackbox_id'))->where('next_delivery_week', request('archived_snackbox_delivery_date'))->get();
        // dd($archived_snackbox);
        foreach ($archived_snackbox as $archived_snackbox_item) {
             //dd($archived_snackbox_item->id);
            // It does here though, if that's what you meant?
            SnackBoxArchive::destroy($archived_snackbox_item->id); // <-- WHY WONT THIS DELETE!!!! <-- it was because of the primary key declared in the model. 
            // SnackBoxArchive::destroy(38);
        }
    }

}