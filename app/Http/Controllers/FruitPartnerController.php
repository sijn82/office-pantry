<?php

namespace App\Http\Controllers;

use App\FruitPartner;
use Illuminate\Http\Request;

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
}
