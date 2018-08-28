<?php

namespace App\Http\Controllers;

use App\FruitOrderingDocument;
use Illuminate\Http\Request;

class FruitOrderingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $fods = FruitOrderingDocument::all();
        return $fods;
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
    // public function store(Request $request, $week_start = 60818)
        public function store(Request $request, $week_start = 270818)
    {
        // Upload Fruit Ordering Document Data

      // if (($handle = fopen(public_path() . '/fod/fod-' . $week_start . '-inc-zeros-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

      if (($handle = fopen(public_path() . '/fod/fod-' . $week_start . '-inc-zeros-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

        while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {


          $company_name_encoded = json_encode($data[1]);
          $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
          $company_name = json_decode($company_name_fixed);

          echo $company_name . ' is ' . strlen($company_name) . ' characters long. <br>';

          $fodData =  new FruitOrderingDocument();
          $fodData->week_start = $data[0];
          $fodData->company_name = trim($company_name);
          $fodData->company_supplier = $data[2];
          $fodData->pointless = $data[3];
          $fodData->delivery_notes = $data[4];
          $fodData->fruit_crates = $data[5];
          $fodData->fruit_boxes = $data[6];
          $fodData->deliciously_red_apples = $data[7];
          $fodData->pink_lady_apples = $data[8];
          $fodData->red_apples = $data[9];
          $fodData->green_apples = $data[10];
          $fodData->satsumas = $data[11];
          $fodData->pears = $data[12];
          $fodData->bananas = $data[13];
          $fodData->nectarines = $data[14];
          $fodData->limes = $data[15];
          $fodData->lemons = $data[16];
          $fodData->grapes = $data[17];
          $fodData->seasonal_berries = $data[18];
          $fodData->milk_1l_alt_coconut = $data[19];
          $fodData->milk_1l_alt_unsweetened_almond = $data[20];
          $fodData->milk_1l_alt_almond = $data[21];
          $fodData->milk_1l_alt_unsweetened_soya = $data[22];
          $fodData->milk_1l_alt_soya = $data[23];
          $fodData->milk_1l_alt_lactose_free_semi = $data[24];
          $fodData->filter_coffee_250g = $data[25];
          $fodData->expresso_coffee_250g = $data[26];
          $fodData->muesli = $data[27];
          $fodData->granola = $data[28];
          $fodData->still_water = $data[29];
          $fodData->sparkling_water = $data[30];
          $fodData->milk_2l_semi_skimmed = $data[31];
          $fodData->milk_2l_skimmed = $data[32];
          $fodData->milk_2l_whole = $data[33];
          $fodData->milk_1l_semi_skimmed = $data[34];
          $fodData->milk_1l_skimmed = $data[35];
          $fodData->milk_1l_whole = $data[36];
          $fodData->milk_pint_semi_skimmed = $data[37];
          $fodData->milk_pint_skimmed = $data[38];
          $fodData->milk_pint_whole = $data[39];
          $fodData->milk_1l_organic_semi_skimmed = $data[40];
          $fodData->milk_1l_organic_skimmed = $data[41];
          // $fodData->snack_boxes = $data[41];
          $fodData->delivery_day = $data[42];
          $fodData->save();

        }
        fclose ($handle);
      }
      // return redirect('routes');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
