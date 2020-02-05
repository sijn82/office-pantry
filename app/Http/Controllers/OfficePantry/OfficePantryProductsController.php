<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

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
        $new_op_product = new OfficePantryProducts;
        $new_op_product->name = request('name');
        $new_op_product->price = request('price');
        $new_op_product->sales_nominal = request('sales_nominal');
        $new_op_product->vat = request('vat');
        $new_op_product->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OfficePantryProducts  $officePantryProducts
     * @return \Illuminate\Http\Response
     */
    public function show(OfficePantryProducts $officePantryProducts)
    {
        // This could do with some better ordering on show.
        $office_pantry_products = OfficePantryProducts::where('sales_nominal', '4040')->orderBy('id', 'asc')->get();
        $fruit_partner_products = OfficePantryProducts::where('sales_nominal', '4050')->orderBy('id', 'asc')->get();

        // This now returns an array of 2 lists.  Split back into two variables in the component mount.
        // The only differenece between the two lists are the soles nominal, the lists are rendered side by side for now.
            return [$office_pantry_products, $fruit_partner_products];

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
