<?php

namespace App\Http\Controllers;

use App\OfficePantryProducts;
use Illuminate\Http\Request;

class OfficePantryProductsController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficePantryProducts  $officePantryProducts
     * @return \Illuminate\Http\Response
     */
    public function show(OfficePantryProducts $officePantryProducts)
    {
        //
        $office_pantry_products = OfficePantryProducts::all();
        
        return $office_pantry_products;
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OfficePantryProducts  $officePantryProducts
     * @return \Illuminate\Http\Response
     */
    public function edit(OfficePantryProducts $officePantryProducts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OfficePantryProducts  $officePantryProducts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // dd($request);
        OfficePantryProducts::where('id', $id)->update([
            'name' => request('name'),
            'price' => request('price'),
            'sales_nominal' => request('sales_nominal'),
            'vat' => request('vat'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OfficePantryProducts  $officePantryProducts
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficePantryProducts $officePantryProducts)
    {
        //
    }
}
