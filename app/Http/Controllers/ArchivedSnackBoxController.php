<?php

namespace App\Http\Controllers;

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