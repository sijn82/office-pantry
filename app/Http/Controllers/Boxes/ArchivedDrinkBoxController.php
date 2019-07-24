<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Session;

use Illuminate\Http\Request;
use App\DrinkBox;
use App\DrinkBoxArchive;
use App\WeekStart;
use App\Product;
use App\Preference;
// use App\Company;
use App\CompanyDetails;
use App\CompanyRoute;
use App\AssignedRoute;


class ArchivedDrinkBoxController extends Controller
{
    public function destroyItem($id, Request $request)
    {
        // We need some logic here to decide if the item to be deleted is the last item in the drinkbox.
        // Grab all the entries with the same drinkbox_id.
        $archived_drinkbox_total_items = DrinkBoxArchive::where('drinkbox_id', request('drinkbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the drinkbox entry...
        $archived_drinkbox_item = DrinkBoxArchive::find(request('id'));
        
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        // This will work if drinkboxes are treat a case of drinks as a unit of 1 i.e 1 case of (24x) drinks, not 24 units.
        // If we're taking this approach we need to multiply the value by the case size, or something.
        Product::find($archived_drinkbox_item->product_id)->increment('stock_level', $archived_drinkbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($archived_drinkbox_total_items) === 1) {
            
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            DrinkBoxArchive::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
                'case_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            DrinkBoxArchive::destroy($id);
        }
        
    }

    public function destroyBox(Request $request)
    {
        // dd($request);
        // I've just added the extra specificity of 'next_delivery_date' to try and prevent the destruction of all entries in the archive under that snackbox_id.
        // My thinking being that there could a situation where we need to delete a box (undelivered or whatever)
        // for one week while keeping the remaining outstanding invoices of successfully delivered boxes.
        // Especially for monthly invoicing but also so as not to lose archives for our records because of one mistake.
        $archived_drinkbox = DrinkBoxArchive::where('drinkbox_id', request('archived_drinkbox_id'))->where('next_delivery_week', request('archived_drinkbox_delivery_date'))->get();
        // dd($archived_drinkbox);
        foreach ($archived_drinkbox as $archived_drinkbox_item) {
             //dd($archived_drinkbox_item->id);
            // It does here though, if that's what you meant?
            DrinkBoxArchive::destroy($archived_drinkbox_item->id); // <-- WHY WONT THIS DELETE!!!! <-- it was because of the primary key declared in the model. 
            // DrinkBoxArchive::destroy(38);
        }
    }

}