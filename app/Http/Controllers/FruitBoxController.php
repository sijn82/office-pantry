<?php

namespace App\Http\Controllers;

use App\FruitBox;
use Illuminate\Http\Request;

class FruitBoxController extends Controller
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
        if (($handle = fopen(public_path() . '/fruitbox-import-test-noheaders.csv', 'r')) !== FALSE) {

          while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            $fruitBoxData = new FruitBox();
            $fruitBoxData->is_active = $data[0];
            $fruitBoxData->name = $data[1];
            $fruitBoxData->deliciously_red_apples = $data[2];
            $fruitBoxData->pink_lady_apples = $data[3];
            $fruitBoxData->red_apples = $data[4];
            $fruitBoxData->green_apples = $data[5];
            $fruitBoxData->satsumas = $data[6];
            $fruitBoxData->pears = $data[7];
            $fruitBoxData->bananas = $data[8];
            $fruitBoxData->nectarines = $data[9];
            $fruitBoxData->limes = $data[10];
            $fruitBoxData->lemons = $data[11];
            $fruitBoxData->grapes = $data[12];
            $fruitBoxData->seasonal_berries = $data[13];
            $fruitBoxData->save();

        }
        fclose ($handle);
      }
        return redirect('/');
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
