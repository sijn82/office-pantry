<?php

namespace App\Http\Controllers;

use App\FruitPartner;
use Illuminate\Http\Request;
use App\WeekStart;

class FruitPartnerController extends Controller
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
    
    public function listFruitPartners() {
        $fruit_partners = FruitPartner::all();
        // dd($fruit_partners);
        return $fruit_partners;
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
        // dd(request('fruit_partner'));
        
        $new_fruit_partner = new FruitPartner();
        $new_fruit_partner->name = request('fruit_partner.name');
        $new_fruit_partner->email = request('fruit_partner.email');
        $new_fruit_partner->telephone = request('fruit_partner.telephone');
        $new_fruit_partner->url = request('fruit_partner.url');
        $new_fruit_partner->primary_contact = request('fruit_partner.primary_contact');
        $new_fruit_partner->secondary_contact = request('fruit_partner.secondary_contact');
        $new_fruit_partner->alternative_telephone = request('fruit_partner.alt_phone');
        $new_fruit_partner->location = request('fruit_partner.location');
        $new_fruit_partner->coordinates = request('fruit_partner.coordinates');
        $new_fruit_partner->weekly_action = request('fruit_partner.weekly_action');
        $new_fruit_partner->changes_action = request('fruit_partner.changes_action');
        $new_fruit_partner->no_of_customers = request('fruit_partner.no_of_customers');
        $new_fruit_partner->additional_info = request('fruit_partner.additional_info');
        $new_fruit_partner->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function show(FruitPartner $fruitPartner, $id)
    {
        //
        $fruit_partner = FruitPartner::where('id', $id)->get();
        
        
        return $fruit_partner;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function edit(FruitPartner $fruitPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FruitPartner $fruitPartner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FruitPartner  $fruitPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(FruitPartner $fruitPartner)
    {
        //
    }

    
    // This has become a weird part of my process but moving this logic to the export folder now so I can break $orders up into exports, probably two.
    // AT SOME POINT I REALLY NEED TO DELETE SUPERFLUOUS CODE!!!
    public function groupOrdersByFruitPartner()
    {
        // This will grab all fruit partners except for Office Pantry, so long as Office Pantry remains the 1st fruitpartner in the db.   
        // This is easy to guarantee, so long as I don't forget to add it during datbase refresh and setup!
        
        $fruitpartners = FruitPartner::all()->whereNotIn('id', [1]);
        $week_start = WeekStart::first();
        // dd($week_start->current);
        
        // Not sure why but I tried to use new \stdClass again and this time it worked fine?!?  Ah well, life is filled with surprises.
        $orders = new \stdClass;
        
        foreach ($fruitpartners as $fruitpartner) {
            
            // These are all the boxes due for delivery this week.
            $fruitboxes = $fruitpartner->fruitbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            $milkboxes = $fruitpartner->milkbox->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            
            //---------- Archive Checks ----------//
            
            // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
            if ($fruitpartner->fruitbox_archive) {
                // These are the archived boxes, although I'm not sure how relevant they'll be as this function is run (weekly?) before orders have been delivered.
                $archived_fruitboxes = $fruitpartner->fruitbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            }
            // Quick check that this returns something before making the request more specific, 'cause otherwise there be errors.
            if ($fruitpartner->milkbox_archive) {
                // Still not sure we'll actually be using them but all the more reason to make sure they don't throw errors.
                $archived_milkboxes = $fruitpartner->milkbox_archive->where('next_delivery', $week_start->current)->where('is_active', 'Active');
            }
            
            //---------- End of Archive Checks ----------//

            // I probably don't need to worry about empty collections, so let's check that before adding to the orders.
            if ($fruitboxes->isNotEmpty()) {
                $orders->fruitboxes[$fruitpartner->name] = $fruitboxes;
            //    return \Excel::download(new Exports\FruitPartnerPicklists($fruitboxes), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
            } else {
                
                Log::channel('slack')->info('Fruit Partner: ' . $fruitpartner->name . ' has no Fruit Orders to be delivered this week.' );
            }
            
            if ($milkboxes->isNotEmpty()) {
                
                $orders->milkboxes[$fruitpartner->name] = $milkboxes;
            } else {
                
                Log::channel('slack')->info('Fruit Partner: ' . $fruitpartner->name . ' has no Milk Orders to be delivered this week.' );
            }
        }
        // Cool(io) - $orders is now filled with orders.  Just orders, and the key used is the fruitpartner name as I'm sure that'll save some bother cometh the template.
        // However, do I really want to put/keep them together when they're going to different templates?
        
        return \Excel::download(new Exports\FruitPartnerPicklists($orders), 'fruit-partner-' . $fruitpartner->name . '-orders-' . $week_start->current . '.xlsx');
        // dd($orders);
        
    }
}
