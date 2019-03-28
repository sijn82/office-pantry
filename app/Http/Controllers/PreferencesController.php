<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Preference;
// use App\Company;
use App\CompanyDetails;
use App\Allergy;
use App\AdditionalInfo;
// for use with random test function
use App\Product;

class PreferencesController extends Controller
{
    // using this random function to test sections of snackbox standard updater in isolation.
    public function random() {
        
        $company_details_id = 1;
        
        // ----- Select a random item from list of likes ----- //
        
        $likes = Preference::where('company_details_id', $company_details_id)->where('snackbox_likes', '!=', null)->pluck('snackbox_likes')->toArray();
        
        $key = array_rand($likes, 1);
        // 
        dd($likes[$key]);
        
        // ----- End of Select a random item from list of likes ----- //
        
        
        // ----- Remove any items from list of likes that are not in stock ----- //
        
        $likes = Preference::where('company_details_id', $company_details_id)->where('snackbox_likes', '!=', null)->pluck('snackbox_likes')->toArray();
        $dislikes = Preference::where('company_details_id', $company_details_id)->where('snackbox_dislikes', '!=', null)->pluck('snackbox_dislikes')->toArray();
        
         // dd($likes);
        
        // There's no point including any 'liked' items in this array if we don't have any of them in stock, 
        // so let's check the name in Products and see what the stock level looks like.
        foreach ($likes as $like) {
            // This will only return a countable $option if the item is in stock.
            $option = Product::where('name', $like)->where('stock_level', '>', 0)->get();
            // If $option count returns nothing, it's not in stock and can be removed from selectable products this time around.
            if (!count($option)) {
                // Search for the product in array of $likes and grab its positin (key) in array.
                $like_key = array_search($like, $likes);
                // Now use this key to unset (remove) the product from usable list of likes.
                unset($likes[$like_key]);
            }
        }
        // Likes now only contain products in stock.
        // dd($likes);
        
        // ----- End of Remove any items from list of likes that are not in stock ----- //
    }
    
    
    //
    public function store(Request $request)
    {
        // dd($request->preference)
        
        $preference = 'snackbox_' . $request['preference']['preference_category'];
        
        $newPreference = new Preference();
        $newPreference->company_details_id = $request['preference']['company_details_id'];
        $newPreference->$preference = $request['preference']['product_name'];
        
        if ($request['preference']['preference_category'] == 'essentials') {
            $newPreference->snackbox_essentials_quantity = $request['preference']['product_quantity'];
        }
        
        $newPreference->save();
        
        // Ok, now I need to work out how to return the right field each time, this'll either be as easy as my first idea, or a right bitch to do...
        return [ 'preference' => Preference::where('company_details_id', $request['preference']['company_details_id'])->where($preference, $request['preference']['product_name'])->get(), 'category' => $preference ]; 
        
    }
    
    public function show(Request $request)
    {
        // dd($request);
        $likes = [];
        $dislikes = [];
        $essentials = [];
        // $allergies = [];
        // $additional_notes = [];
        
        $preferences = Preference::where('company_details_id', $request->id)->get();
        $allergies = Allergy::where('company_details_id', $request->id)->get();
        $additional_notes = AdditionalInfo::where('company_details_id', $request->id)->get();
        
        foreach ( $preferences as $preference ) {
            
            if ( $preference->snackbox_likes != null ) {
                $likes[] = $preference;
                
            } elseif ( $preference->snackbox_dislikes != null ) {
                $dislikes[] = $preference;
                
            } elseif ( $preference->snackbox_essentials != null) {
                $essentials[] = $preference;
            }
            
        } // end of foreach preference
        
           
           
        if (empty($likes)) {
            $likes = 'None';
        }
        if (empty($dislikes)) {
           $dislikes = 'None';
        }
        if (empty($essentials)) {
           $essentials = 'None';
        }
        if (!count($allergies)) {
            $allergies = 'None';
        }
        if (!count($additional_notes)) {
            $additional_notes = 'None';
        }
         // dd($essentials);
        // dd($allergies);
         // dd($additional_notes);
        return ['likes' => $likes, 'dislikes' => $dislikes, 'essentials' => $essentials, 'allergies' => $allergies, 'additional_notes' => $additional_notes];
    }
    
    public function remove(Request $request)
    {
        dd($request);
    }
}
