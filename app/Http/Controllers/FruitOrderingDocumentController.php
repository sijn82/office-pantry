<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\FruitOrderingDocument;
use App\WeekStart;
use Illuminate\Http\Request;

// use League\Csv\Reader;
// use League\Csv\Writer;

// require '../vendor/league/csv/autoload.php';

class FruitOrderingDocumentController extends Controller
{

    protected $week_start;


    public function __construct()
    {
        // $this->week_start = 170918;
        $week_start = WeekStart::all()->toArray();
        $this->week_start = $week_start[0]['current'];

    }

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

    }

    public function reset()
    {
        // This function is to strip out all the existing values from the FOD.  We have no need to keep them after processing with the current system,
        // and now the site is live we should be more concerned with using unnecessary rows in the database.

        $message = 'FOD entries deleted.';

        $fods = FruitOrderingDocument::all();
        // dd($routes);
        foreach ($fods as $fod)
        {
            $fod->delete();
        }

        Log::channel('slack')->info($message);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request, $this->week_start = 60818)i
        public function upload(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function
        // $delivery_days = $request->delivery_days ? 'wed-thur-fri' : '';
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->fod_csv);
        // now we can decode the remainder of the encoded data string
        $requestcsv = base64_decode($request_mime_fix);
        // however it now has some unwated unicode characters i.e the 'no break space' - (U+00A0) and use json_encode to make them visible
        $csv_data_with_unicode_characters = json_encode($requestcsv);
        // now they're no longer hidden characters, we can strip them out of the data
        $csv_data_fixed = str_replace('\u00a0', ' ', $csv_data_with_unicode_characters);
        // and return the file ready for storage
        $ready_csv = json_decode($csv_data_fixed);

        // this is how we determine where to put the file, these variables are populated with the $week_start variable at the top of this class
        // and the request parameters attached to the form on submission.
        Storage::put('public/fod/fod-' . $this->week_start . '-inc-zeros-' . $delivery_days . '-noheaders-utf8-nobom.csv', $ready_csv);
        $message = '';
        // i've decided to place this here, processing the request immediately after storing a copy
        // because i can't think of a reason why the file would be uploaded without processing them straight away.
        if (($handle = fopen('../storage/app/public/fod/fod-' . $this->week_start . '-inc-zeros-' . $delivery_days . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
               while (($data = fgetcsv ($handle,',')) !== FALSE) {

                   // this is just to test the data a little before we risk putting stuff automatically into the database.
                   echo $data[0] . ' is ' . strlen($data[0]) . ' characters long and ' . json_encode($data[1]) . ' is also ' . strlen($data[1]) . '<br>';

                    $company_route_name_exceptions =    [
                                                            'Legal and General London (FAO Simon Chong)' => 'Legal and General London',
                                                            'London Business School (FAO Victoria Gilbert)' => 'London Business School',
                                                            'JP Morgan (FAO Sara Cordwell 15th Floor)' => 'JP Morgan',
                                                            'JP Morgan II (FAO Sara Cordwell 15th Floor)' => 'JP Morgan II',
                                                            'TI Media Limited (FAO Ruth Stanley)' => 'TI Media Limited',
                                                            'Lloyds (Gatwick - FAO Katie Artlett)' => 'Lloyds (Gatwick)',
                                                            'Lloyds (London - London Wall - FAO Elaine Charlery)' => 'Lloyds (London - London Wall)',
                                                            'Lloyds (London - 10 Gresham Street – FAO Marytn Shone / Alex Linnell)' => 'Lloyds (London - 10 Gresham Street)',
                                                            'Lloyds (London - 25 Gresham Street - FAO Massir Thomson / Martyn Shone / Debbie Cattrell)' => 'Lloyds (London - 25 Gresham Street)',
                                                            'Lloyds (London - Old Broad Street - FAO Jamie Mcreesh / Daniel Lee / Parul Patel)' => 'Lloyds (London - Old Broad Street)',
                                                            'BNP Paribas Basingstoke (FAO Stacy Scott/Jessica Howarth)' => 'BNP Paribas Basingstoke',
                                                            'Gu (Noble Foods - FAO Ali Heal)' => 'Gu (Noble Foods)',
                                                            'Paddle (FAO Yago Cano)' => 'Paddle',
                                                            'Charlotte Tilbury - We Work (FAO Sophie Kendrick)' => 'Charlotte Tilbury - We Work',
                                                            'Charlotte Tilbury (FAO Sophie Kendrick)' => 'Charlotte Tilbury',
                                                            'Juro (FAO Adrienne)' => 'Juro',
                                                            'Home Office Eaton House (FAO Mike Jarrett)' => 'Home Office Eaton House',

                                                       ];

                         // If $newRoute->company_name doesn't match a Company route_name, check to see if this value matches a Company route_name exception.
                         // These are some of the rare cases where the route name is tailored for the delivery with an FAO attached.
                    if (array_search($data[1], $company_route_name_exceptions)) {
                               // if it finds a matching value, it returns the associated key.
                               $data[1] = array_search($data[1], $company_route_name_exceptions);
                    }

                   // when I'm feeling a little braver, it'll look just like this...

                   $message .= 'Saving FOD entry for ' . $data[1] . ' on ' . $data[42] . "\n";


                   $fodData =  new FruitOrderingDocument();
                   $fodData->week_start = $data[0];
                   $fodData->company_name = $data[1];
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
               Log::channel('slack')->info($message);
        }
    }

    // This function was the temporary measure before creating the upload one above.  It is now subservient but a good backup in case the bells and whistles fall off.

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

        public function store(Request $request)
    {
        // Upload Fruit Ordering Document Data

      // if (($handle = fopen(public_path() . '/fod/fod-' . $this->week_start . '-inc-zeros-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

      // if (($handle = fopen(public_path() . '/fod/fod-' . $this->week_start . '-inc-zeros-wed-thur-fri-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {
      //
      //   while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {
      //
      //       $company_route_name_exceptions =    [
      //                                              'Legal and General London (FAO Simon Chong)' => 'Legal and General London',
      //                                              'London Business School (FAO Victoria Gilbert)' => 'London Business School',
      //                                              'JP Morgan (FAO Sara Cordwell 15th Floor)' => 'JP Morgan',
      //                                              'JP Morgan II (FAO Sara Cordwell 15th Floor)' => 'JP Morgan II',
      //                                              'TI Media Limited (FAO Ruth Stanley)' => 'TI Media Limited',
      //                                              'Lloyds (Gatwick - FAO Katie Artlett)' => 'Lloyds (Gatwick)',
      //                                              'Lloyds (London - London Wall - FAO Elaine Charlery)' => 'Lloyds (London - London Wall)',
      //                                              'Lloyds (London - 10 Gresham Street – FAO Marytn Shone / Ben Pryce)' => 'Lloyds (London - 10 Gresham Street)',
      //                                              'Lloyds (London - 25 Gresham Street - FAO James Gamble / Maryn Shone / Ben Pryce)' => 'Lloyds (London - 25 Gresham Street)',
      //                                              'Lloyds (London - Old Broad Street - FAO Jamie Mcreesh / Daniel Lee / Parul Patel)' => 'Lloyds (London - Old Broad Street)',
      //                                              'BNP Paribas Basingstoke (FAO Stacy Scott/Jessica Howarth)' => 'BNP Paribas Basingstoke',
      //                                              'Gu (Noble Foods - FAO Ali Heal)' => 'Gu (Noble Foods)',
      //                                              'Paddle (FAO Yago Cano)' => 'Paddle',
      //                                              'Charlotte Tilbury (FAO Sophie Kendrick)' => 'Charlotte Tilbury - We Work',
      //                                          ];
      //
      //            // If $newRoute->company_name doesn't match a Company route_name, check to see if this value matches a Company route_name exception.
      //            // These are some of the rare cases where the route name is tailored for the delivery with an FAO attached.
      //       if (array_search($data[1], $company_route_name_exceptions)) {
      //                  // if it finds a matching value, it returns the associated key.
      //                  $data[1] = array_search($data[1], $company_route_name_exceptions);
      //       }
      //
      //       $company_name_encoded = json_encode($data[1]);
      //       $company_name_fixed = str_replace('\u00a0', ' ', $company_name_encoded);
      //       $company_name = json_decode($company_name_fixed);
      //
      //     echo $company_name . ' is ' . strlen($company_name) . ' characters long. <br>';
      //
      //     $fodData =  new FruitOrderingDocument();
      //     $fodData->week_start = $data[0];
      //     $fodData->company_name = trim($company_name);
      //     $fodData->company_supplier = $data[2];
      //     $fodData->pointless = $data[3];
      //     $fodData->delivery_notes = $data[4];
      //     $fodData->fruit_crates = $data[5];
      //     $fodData->fruit_boxes = $data[6];
      //     $fodData->deliciously_red_apples = $data[7];
      //     $fodData->pink_lady_apples = $data[8];
      //     $fodData->red_apples = $data[9];
      //     $fodData->green_apples = $data[10];
      //     $fodData->satsumas = $data[11];
      //     $fodData->pears = $data[12];
      //     $fodData->bananas = $data[13];
      //     $fodData->nectarines = $data[14];
      //     $fodData->limes = $data[15];
      //     $fodData->lemons = $data[16];
      //     $fodData->grapes = $data[17];
      //     $fodData->seasonal_berries = $data[18];
      //     $fodData->milk_1l_alt_coconut = $data[19];
      //     $fodData->milk_1l_alt_unsweetened_almond = $data[20];
      //     $fodData->milk_1l_alt_almond = $data[21];
      //     $fodData->milk_1l_alt_unsweetened_soya = $data[22];
      //     $fodData->milk_1l_alt_soya = $data[23];
      //     $fodData->milk_1l_alt_lactose_free_semi = $data[24];
      //     $fodData->filter_coffee_250g = $data[25];
      //     $fodData->expresso_coffee_250g = $data[26];
      //     $fodData->muesli = $data[27];
      //     $fodData->granola = $data[28];
      //     $fodData->still_water = $data[29];
      //     $fodData->sparkling_water = $data[30];
      //     $fodData->milk_2l_semi_skimmed = $data[31];
      //     $fodData->milk_2l_skimmed = $data[32];
      //     $fodData->milk_2l_whole = $data[33];
      //     $fodData->milk_1l_semi_skimmed = $data[34];
      //     $fodData->milk_1l_skimmed = $data[35];
      //     $fodData->milk_1l_whole = $data[36];
      //     $fodData->milk_pint_semi_skimmed = $data[37];
      //     $fodData->milk_pint_skimmed = $data[38];
      //     $fodData->milk_pint_whole = $data[39];
      //     $fodData->milk_1l_organic_semi_skimmed = $data[40];
      //     $fodData->milk_1l_organic_skimmed = $data[41];
      //     // $fodData->snack_boxes = $data[41];
      //     $fodData->delivery_day = $data[42];
      //     $fodData->save();
      //
      //   }
      //   fclose ($handle);
      // }
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
