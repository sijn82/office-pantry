<?php

// Edited to use sub folder 'Boxes' where the box controllers have now been moved to.
namespace App\Http\Controllers\Boxes;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

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
use App\Allergy;
use App\OrderItem;

use App\Traits\Orders;



set_time_limit(0);
class SnackBoxController extends Controller
{

    use Orders;

        protected $week_start;

        public function __construct()
        {
            $week_start = WeekStart::first();

            if ($week_start !== null) {
                $this->week_start = $week_start->current;
                $this->delivery_days = $week_start->delivery_days;
            }

        }

        // public function snackbox_test () {
        //     // dd('well this worked fine? what\'s going on?!!');
        //     session()->put('snackbox_courier', 'OP');
        //
        //     return \Excel::download(new Exports\SnackboxSingleCompanyExportNew, 'snackboxesOPSingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
        // }

        // There are a couple of options here, use the same function with a switch statement value based on the button pressed, or as I'm going to do for now, create several functions
        // one to handle each scenario.

        //----- Single Company, Multiple Boxes -----//

            // - Weekly Export Results

            public function download_snackbox_weekly_op_singlecompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxSingleCompanyWeeklyExportNew, 'snackboxesOPSingleCompany-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_weekly_dpd_singlecompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxSingleCompanyWeeklyExportNew, 'snackboxesDPDSingleCompany-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_weekly_apc_singlecompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxSingleCompanyWeeklyExportNew, 'snackboxesAPCSingleCompany-' . $this->week_start . '.xlsx');
            }

            // Selected Day(s) Export Results

            public function download_snackbox_op_singlecompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxSingleCompanyExportNew, 'snackboxesOPSingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_dpd_singlecompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxSingleCompanyExportNew, 'snackboxesDPDSingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_apc_singlecompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxSingleCompanyExportNew, 'snackboxesAPCSingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }

        //----- End of Single Company, Multiple Boxes -----//

        //----- Multiple Companies, Single Box -----//

            // - Weekly Export Results

            public function download_snackbox_weekly_op_multicompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxMultiCompanyWeeklyExportNew, 'snackboxesOPMultiCompany-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_weekly_dpd_multicompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxMultiCompanyWeeklyExportNew, 'snackboxesDPDMultiCompany-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_weekly_apc_multicompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxMultiCompanyWeeklyExportNew, 'snackboxesAPCMultiCompany-' . $this->week_start . '.xlsx');
            }

            // Selected Day(s) Export Results

            public function download_snackbox_op_multicompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesOPMultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_dpd_multicompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesDPDMultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_apc_multicompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesAPCMultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }

        //----- End of Multiple Companies, Single Box -----//

        //----- Unique Box, Multiple Companies -----//

            // These are used for companies who receive unique items held in stock which need a picklist creating such as peanut butter, himalayan salt and cereal etc.
            // The following 3 functions are for companies receiving one box.
            public function download_snackbox_unique_op_multicompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxUniqueMultiCompanyExportNew, 'snackboxes-Unique-OP-MultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_unique_dpd_multicompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxUniqueMultiCompanyExportNew, 'snackboxes-Unique-DPD-MultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_unique_apc_multicompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxUniqueMultiCompanyExportNew, 'snackboxes-Unique-APC-MultiCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }

            // Whereas these 3 are for companies receiving more than one box - they will be used rarely but need to be possible if needed.

            public function download_snackbox_unique_op_singlecompany()
            {
                session()->put('snackbox_courier', 'OP');

                return \Excel::download(new Exports\SnackboxUniqueSingleCompanyExportNew, 'snackboxes-Unique-OP-SingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_unique_dpd_singlecompany()
            {
                session()->put('snackbox_courier', 'DPD');

                return \Excel::download(new Exports\SnackboxUniqueSingleCompanyExportNew, 'snackboxes-Unique-DPD-SingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }
            public function download_snackbox_unique_apc_singlecompany()
            {
                session()->put('snackbox_courier', 'APC');

                return \Excel::download(new Exports\SnackboxUniqueSingleCompanyExportNew, 'snackboxes-Unique-APC-SingleCompany-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
            }

            // These will no longer be used in the new system - replaced by drinkbox.

                // public function download_snackbox_op_unique()
                // {
                //     session()->put('snackbox_courier', 'OP');
                //
                //     return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesOPUnique' . $this->week_start . '.xlsx');
                // }
                // public function download_snackbox_dpd_unique()
                // {
                //     session()->put('snackbox_courier', 'DPD');
                //
                //     return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesDPDUnique' . $this->week_start . '.xlsx');
                // }
                // public function download_snackbox_apc_unique()
                // {
                //     session()->put('snackbox_courier', 'APC');
                //
                //     return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesAPCUnique' . $this->week_start . '.xlsx');
                // }

            // End of ones replaced by drinkbox

        //----- End of Unique Box, Multiple Companies -----//

        // Snackbox Wholesale Exports
        public function download_snackbox_wholesale_op_singlecompany()
        {
            session()->put('snackbox_courier', 'OP');

            return \Excel::download(new Exports\SnackboxWholesaleExport, 'snackboxesWholesaleOP-' . $this->delivery_days . '-' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_wholesale_weekly_op_singlecompany()
        {
            session()->put('snackbox_courier', 'OP');

            return \Excel::download(new Exports\SnackboxWeeklyWholesaleSingleCompanyExportNew, 'snackboxes-weekly-WholesaleOPSingleCompany' . $this->week_start . '.xlsx');
        }

    // This is an attempt to send the data for snacks and drinks to the templates without troubling a database for anything.
    // This is to allow the product list to be dynamic and not hard coded, otherwise weekly code changes would be required and the database table rebuilt each time.

    public function upload_products_and_codes(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function
        // $delivery_days = $request->delivery_days ? 'wed-thur-fri' : '';
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->products_and_codes);
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
        Storage::put('public/snackboxes/productcodes-' . $this->week_start . '-' . $delivery_days . '.csv', $ready_csv);

        $message = 'Uploaded latest product codes as of ' . $this->week_start . ' for delivery on ' . $delivery_days;

        Log::channel('slack')->info($message);
    }

    public function upload_snackbox_orders(Request $request)
    {
        // these are the additional parameters we'll use to save the file in the right place and with the right name, which is built further down in this function
        // $delivery_days = $request->delivery_days ? 'wed-thur-fri' : '';
        $delivery_days = $request->delivery_days;

        // strip out the automatic base encoding with wrong mime after file upload form
        $request_mime_fix = str_replace('data:application/vnd.ms-excel;base64,','',$request->snackbox_orders);
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
        Storage::put('public/snackboxes/snackbox_orders-' . $this->week_start . '-' . $delivery_days . '.csv', $ready_csv);

        $message = 'Uploaded latest snackbox orders as of ' . $this->week_start . ' for delivery on ' . $delivery_days;

        Log::channel('slack')->info($message);
    }

    // This function primarily uploads the snackbox/drinks orders and latest product lines, seperating them into groups and saving the collections to session variables.
    public function auto_process_snackboxes()
    {
                // this will pull in the product name/code information so long as the file exists to be read.
                // it is then saved to a $product_list variable and passed to the template.

              if (($handle = fopen('../storage/app/public/snackboxes/productcodes-' . $this->week_start . '-' . $this->delivery_days . '.csv', 'r')) !== FALSE) {

                    while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                        $product_list[$data[0]] = trim($data[1]);
                    }

                fclose ($handle);

                // dd($product_list);
              }

                // Next we need to do the same with this week's snackbox data which needs to be processed and spat out into the appropriate templates.

                if (($handle = fopen('../storage/app/public/snackboxes/snackbox_orders-' . $this->week_start . '-' . $this->delivery_days . '.csv', 'r')) !== FALSE) {

                      while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

                            $company_order = $data;

                            // I don't like how anti DRY this seems but neither do I think a switch case will work or be more readable?

                            // if delivered by Office Pantry
                            if      ($company_order[0] == 'OP' && $company_order[1] > 1) { $snd_OP_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'OP' && $company_order[1] == 1) { $snd_OP_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'OP' && $company_order[1] == 0) { $snd_OP_uniqueBoxes[] = $company_order; }

                            // if delivered by DPD
                            if      ($company_order[0] == 'DPD' && $company_order[1] > 1) { $snd_DPD_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'DPD' && $company_order[1] == 1) { $snd_DPD_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'DPD' && $company_order[1] == 0) { $snd_DPD_uniqueBoxes[] = $company_order; }

                            // if delivered by APC
                            if      ($company_order[0] == 'APC' && $company_order[1] > 1) { $snd_APC_multipleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'APC' && $company_order[1] == 1) { $snd_APC_singleBoxes[] = $company_order; }
                            elseif  ($company_order[0] == 'APC' && $company_order[1] == 0) { $snd_APC_uniqueBoxes[] = $company_order; }
                      }
                  fclose ($handle);
                }

              // $chunks = [];

              // Now due to popular demand these company orders will be grouped into alphabetical order.

              $alphabetise = function($a, $b)
              {
                  // This is using the new and sexy spaceship operator to compare company string names and return them in alphabetical order.
                  $outcome = $a[2] <=> $b[2];
                  // Combined with usort, some background php magic will return the (alpabetically prior) item.
                  return $outcome;
              };

              // So long as the array isn't empty, let's alphabetise them.
              if (!empty($snd_OP_multipleBoxes)) { usort($snd_OP_multipleBoxes, $alphabetise); };
              if (!empty($snd_OP_singleBoxes)) { usort($snd_OP_singleBoxes, $alphabetise); };
              if (!empty($snd_OP_uniqueBoxes)) { usort($snd_OP_uniqueBoxes, $alphabetise); };

              if (!empty($snd_DPD_multipleBoxes)) { usort($snd_DPD_multipleBoxes, $alphabetise); };
              if (!empty($snd_DPD_singleBoxes)) { usort($snd_DPD_singleBoxes, $alphabetise); };
              if (!empty($snd_DPD_uniqueBoxes)) { usort($snd_DPD_uniqueBoxes, $alphabetise); };

              if (!empty($snd_APC_multipleBoxes)) { usort($snd_APC_multipleBoxes, $alphabetise); };
              if (!empty($snd_APC_singleBoxes)) { usort($snd_APC_singleBoxes, $alphabetise); };
              if (!empty($snd_APC_uniqueBoxes)) { usort($snd_APC_uniqueBoxes, $alphabetise); };

              // These 3 arrays need chunking into groups of four, so we can loop through them outputting 4 company orders per template.
              $snd_OP_singleBoxes_chunks = (!empty($snd_OP_singleBoxes)) ? array_chunk($snd_OP_singleBoxes, 4) : 'None for this week';
              $snd_DPD_singleBoxes_chunks = (!empty($snd_DPD_singleBoxes)) ? array_chunk($snd_DPD_singleBoxes, 4) : 'None for this week';
              $snd_APC_singleBoxes_chunks = (!empty($snd_APC_singleBoxes)) ? array_chunk($snd_APC_singleBoxes, 4) : 'None for this week';
              // Unique boxes will use the same multi-company template, and also need chunking into groups of 4.
              $snd_OP_UniqueBoxes_chunks = (!empty($snd_OP_uniqueBoxes)) ? array_chunk($snd_OP_uniqueBoxes, 4) : 'None for this week';
              $snd_DPD_UniqueBoxes_chunks = (!empty($snd_DPD_uniqueBoxes)) ? array_chunk($snd_DPD_uniqueBoxes, 4) : 'None for this week';
              $snd_APC_UniqueBoxes_chunks = (!empty($snd_APC_uniqueBoxes)) ? array_chunk($snd_APC_uniqueBoxes, 4) : 'None for this week';

              // Now the data has been chunked into groups of 4, we can add them to a session variable.
              session()->put('snackbox_OP_multicompany', $snd_OP_singleBoxes_chunks);
              session()->put('snackbox_DPD_multicompany', $snd_DPD_singleBoxes_chunks);
              session()->put('snackbox_APC_multicompany', $snd_APC_singleBoxes_chunks);
              // And do the same with unique boxes.
              session()->put('snackbox_OP_unique', $snd_OP_UniqueBoxes_chunks);
              session()->put('snackbox_DPD_unique', $snd_DPD_UniqueBoxes_chunks);
              session()->put('snackbox_APC_unique', $snd_APC_UniqueBoxes_chunks);

              // These 3 are for multiple box orders, which get a template of their own and the order split between the amount of boxes they require.
              if (!empty($snd_OP_multipleBoxes))     { session()->put('snackbox_OP_singlecompany', $snd_OP_multipleBoxes);      } else { session()->put('snackbox_OP_singlecompany', 'None for this week'); };
              if (!empty($snd_DPD_multipleBoxes))    { session()->put('snackbox_DPD_singlecompany', $snd_DPD_multipleBoxes);    } else { session()->put('snackbox_DPD_singlecompany', 'None for this week'); };
              if (!empty($snd_APC_multipleBoxes))    { session()->put('snackbox_APC_singlecompany', $snd_APC_multipleBoxes);    } else { session()->put('snackbox_APC_singlecompany', 'None for this week'); };

              // This is for the latest product list, we'll be matching these values up to the orders for each of the above variables.
              session()->put('snackbox_product_list', $product_list);

              // dd(session()->all());
              Log::channel('slack')->info('Snackbox data stored in sessions!');
              // This redirect was more for testing purposes as I want to redirect the user back to the upload snackbox and products page, with buttons to run/output each order option.
              // return back();

              // Not sure why but just had to replace the above 'return back();' because it caused a redirect loop/time out?!  No idea why and I don't like the mystery.
              return redirect()->back();

              // This was also for testing purposes, so I could see the data being produced before exporting the results as an excel file.
              // return view('snackboxes-multi-company')->with('product_list', $product_list)->with('chunks', $snd_OP_singleBoxes_chunks);

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_OP()
    {
        //
        $snd_OP_multipleBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '>', 1)->get();

        $snd_OP_singleBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '=', 1)->get();

        $chunks = $snd_OP_singleBoxes->chunk(4);
        $chunks = $chunks->all();


        // dd($chunks);
        $snd_OP_uniqueBoxes = SnackBox::where('delivered_by', 'OP')->where('no_of_boxes_split_between', '=', 0)->get();

        return view ('snackboxes-multi-company', ['snd_OP_singleBoxes' => $snd_OP_singleBoxes, 'snd_OP_multipleBoxes' => $snd_OP_multipleBoxes, 'snd_OP_uniqueBoxes' => $snd_OP_uniqueBoxes, 'chunks' => $chunks ]);
    }

    public function index_DPD()
    {
        //
        $snd_DPD = SnackBox::where('delivered_by', 'DPD')->get();
    }

    public function index_APC()
    {
        //
        $snd_APC = SnackBox::where('delivered_by', 'APC')->get();
    }

    //----- If I've been smart, and/or possibly organised, then the old system code is above and the new system code is below.  I make no guarantees about this though? -----//

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function test()
    {
        $snackbox = SnackBox::find(363);

        foreach ($snackbox->box_items->where('delivery_date', $snackbox->delivery_week) as $order_item) {
            dump($order_item->product->brand . ' ' . $order_item->product->flavour);
        }


        //dd('were here and not redirected');
        // foreach ($snackboxes as $snackbox) {
        //     dump($snackbox->products);
        // }

    }

    // This function saves new snackboxes, with or without products contained.
    // The most important element to create for the update function ( which create a new standard box ) is a snackbox id.
    // Though obviously connecting that snackbox to a company id and delivery info etc is integral to it being useful! :)

    public function store(Request $request)
    {
        // dd($request);
        // dd($request->order);
        $snackbox_id = request('company_details_id') . "-" . uniqid();
        $courier = request('details.delivered_by');
        $box_number = request('details.no_of_boxes');
        $snack_cap = request('details.snack_cap');
        $type = request('details.type');
        $delivery_day = request('details.delivery_day');
        $frequency = request('details.frequency');
        $week_in_month = request('details.week_in_month');
        $delivery_week = request('details.delivery_week');

        if (!empty($request->order)) {

            // if we have at least one entry in here then we need to
            // loop through and save (create) an entry for that snackbox product
            // and attached it to the snackbox id.

            foreach ( $request->order as $item ) {

                $new_snackbox = new SnackBox();
                $new_snackbox->snackbox_id = $snackbox_id;
                $new_snackbox->delivered_by = $courier;
                $new_snackbox->no_of_boxes = $box_number;
                $new_snackbox->snack_cap = $snack_cap;
                $new_snackbox->type = $type;
                $new_snackbox->company_details_id = $request->company_details_id;
                $new_snackbox->delivery_day = $delivery_day;
                $new_snackbox->frequency = $frequency;
                $new_snackbox->week_in_month = $week_in_month;
                $new_snackbox->delivery_week = $delivery_week;
                $new_snackbox->product_id = $item['id'];
                $new_snackbox->code = $item['code'];
                $new_snackbox->name = $item['name'];
                // I'm still trying to decide the best time to reduce the stock level by the item quantity?
                // If I do it here and then the product gets changed before delivery, we'll need to reapply the quantity to the stock level or chaos will ensue.
                $new_snackbox->quantity = $item['quantity'];
                $new_snackbox->unit_price = $item['unit_price'];
                $new_snackbox->case_price = $item['case_price'];
                $new_snackbox->save();

                //---------- Adjust stock levels ----------//

                    // Now we need to sort out the stock levels for these order items, keeping them in check and hopefully 100% accurate!
                    //  If these order items get cancelled for any reason, we must remember to add them back in too!!

                    // First let's grab the product
                    $product = Product::findOrFail($item['id']);
                    // Then it's current stock level, and deduct the order quantity as the new stock level.
                    $new_stock_level = $product->stock_level - $item['quantity'];
                    // Finally saving the new stocklevel to the database.
                    Product::where('id', $item['id'])->update([
                        'stock_level' => $new_stock_level,
                    ]);

                //---------- End of Adjust stock levels ----------//

            }
        } else {

            // it's also very likely we know a company wants a standard snackbox for next week
            // but there's no point attaching products to the snackbox id as they'll only be replaced before receiving their first order anyway.
            // It's basically a faff saver.

            $new_snackbox = new SnackBox();
            $new_snackbox->snackbox_id = $snackbox_id;
            $new_snackbox->delivered_by = $courier;
            $new_snackbox->no_of_boxes = $box_number;
            $new_snackbox->snack_cap = $snack_cap;
            $new_snackbox->type = $type;
            $new_snackbox->company_details_id = $request->company_details_id;
            $new_snackbox->delivery_day = $delivery_day;
            $new_snackbox->frequency = $frequency;
            $new_snackbox->week_in_month = $week_in_month;
            $new_snackbox->delivery_week = $delivery_week_start;
            $new_snackbox->save();
        }

        // We only want to create a route if Office Pantry are delivering it directly.
        if ($courier === 'OP') {

            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_details_id', $request['company_details_id'])->where('delivery_day', $delivery_day)->get())) {

                // $companyDetails = Company::findOrFail($request['company_details_id']);
                $companyDetails = CompanyDetails::findOrFail($request['company_details_id']);

                $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();

                switch ($delivery_day) {
                    case 'Monday':
                        $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                        break;
                    case 'Tuesday':
                        $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                        break;
                    case 'Wednesday':
                        $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                        break;
                    case 'Thursday':
                        $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                        break;
                    case 'Friday':
                        $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                        break;
                }

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_details_id = $request['company_details_id'];
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->route_postcode;

                //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                // $newRoute->address = $companyDetails->route_summary_address;

                // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
                $newRoute->address = implode(", ", array_filter([
                        $companyDetails->route_address_line_1,
                        $companyDetails->route_address_line_2,
                        $companyDetails->route_address_line_3,
                        $companyDetails->route_city,
                        $companyDetails->route_region
                    ]));

                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->assigned_route_id = $assigned_route_id;
                $newRoute->delivery_day = $delivery_day;
                $newRoute->save();


                $message = "Route $newRoute->route_name on " . $delivery_day . " saved.";
                Log::channel('slack')->info($message);
            }

        } else {

            $message = "Route for company details id - " . request('company_details_id') . " on " . $delivery_day . " not necessary, delivered by " . $courier;
            Log::channel('slack')->info($message);
        }

        // dd(uniqid());

        // This is where we build the snackbox, grouping this instance of the products into one order.

        // Now we should adjust the stocklevels by removing the quantity from this order.


    }

    // This update function for the moment is just used to change the quantity of an existing snackbox item.
    // Just as the destroy function is currently only used to remove a product from an existing snackbox.
    // I'm still deciding how best to add a new item from an existing snackbox.

    public function update(Request $request)
    {
        //---------- Calculate and update the new product stock level ----------//

            // This needs to check what the previous value was before adjusting stock levels with the difference.
            $snackbox_item_current = Snackbox::find(request('snackbox_item_id'));

            // This determines whether we need to add or remove quantities from stock
            if ($snackbox_item_current->quantity > request('snackbox_item_quantity')) {
                // Work out the difference
                $stock_difference = ($snackbox_item_current->quantity - request('snackbox_item_quantity'));
                // Then we need to return the difference to stock
                Product::where('id', $snackbox_item_current->product_id)->increment('stock_level', $stock_difference);

            } elseif ($snackbox_item_current->quantity < request('snackbox_item_quantity')) {
                // Work out the difference
                $stock_difference = (request('snackbox_item_quantity') - $snackbox_item_current->quantity);
                // Then we need to remove the difference from stock
                Product::where('id', $snackbox_item_current->product_id)->decrement('stock_level', $stock_difference);
            }

        //---------- End of Calculate and update the new product stock level ----------//

        //---------- Update the box entry with quantity ----------//

            // Now the stock levels are sorted we can go ahead and save the updated quantity for that item in the box.
            SnackBox::where('id', request('snackbox_item_id'))->update([
                'quantity' => request('snackbox_item_quantity'),
            ]);

        //---------- Update the box entry with quantity ----------//
    }

    public function increaseSnackboxOrderItemQuantity(Request $request)
    {
        $this->increaseOrderItemQuantity($request);
    }

    // This function is just used to update the snackbox company/delivery info - everything but the contents basically.
    // If the delivery day is changed then a check is made to see if we have a route for them already (on that day), creating it for them if not.
    public function updateDetails(Request $request)
    {

        $snackbox = SnackBox::where('snackbox_id', request('snackbox_details.snackbox_id'))->get();
        // Not sure I actually need this as I have the relationship availble while in the controller.
        $associated_allergens = Allergy::where('snackbox_id', request('snackbox_details.snackbox_id'))->first();

        foreach ($snackbox as $snackbox_entry) {
            $snackbox_entry->update([
                'is_active' => request('snackbox_details.is_active'),
                'delivered_by' => request('snackbox_details.delivered_by'),
                'no_of_boxes' => request('snackbox_details.no_of_boxes'),
                'snack_cap' => request('snackbox_details.snack_cap'),
                'type' => request('snackbox_details.type'),
                'delivery_day' => request('snackbox_details.delivery_day'),
                'frequency' => request('snackbox_details.frequency'),
                'week_in_month' => request('snackbox_details.week_in_month'),
                'delivery_week' => request('snackbox_details.delivery_week'),
            ]);
        }

        if ($snackbox[0]->allergies_and_dietary_requirements->allergy !== request('snackbox_details.snackbox_selected_allergens')) {
            dump($snackbox[0]->allergies_and_dietary_requirements->allergy);
            $snackbox[0]->allergies_and_dietary_requirements->allergy->update([
                'allergy' => request('snackbox_details.snackbox_selected_allergens'),
            ]);
        } else {
            dump($snackbox[0]->allergies_and_dietary_requirements->allergy);
            dump(request('snackbox_details.snackbox_selected_allergens'));
        }

        // We only want to create a route if Office Pantry are delivering it directly.
        if (request('snackbox_details.delivered_by') === 'OP') {

            // Now we've handled the order itself, we need to make sure there's a route to dispatch it on.  Well, if it's delivered by Office Pantry anyway.
            // If there is we're all done, if not, let's build a route.
            if (!count(CompanyRoute::where('company_details_id', request('snackbox_details.company_details_id'))->where('delivery_day', request('snackbox_details.delivery_day'))->get())) {

                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                // $companyDetails = Company::findOrFail(request('snackbox_details.company_id'));
                $companyDetails = CompanyDetails::findOrFail(request('snackbox_details.company_details_id'));

                $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
                $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
                $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
                $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
                $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();

                switch (request('delivery_day')) {
                    case 'Monday':
                        $assigned_route_id = $assigned_route_tbc_monday[0]->id;
                        break;
                    case 'Tuesday':
                        $assigned_route_id = $assigned_route_tbc_tuesday[0]->id;
                        break;
                    case 'Wednesday':
                        $assigned_route_id = $assigned_route_tbc_wednesday[0]->id;
                        break;
                    case 'Thursday':
                        $assigned_route_id = $assigned_route_tbc_thursday[0]->id;
                        break;
                    case 'Friday':
                        $assigned_route_id = $assigned_route_tbc_friday[0]->id;
                        break;
                }

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_details_id = request('snackbox_details.company_details_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->route_postcode;

                //  Route Summary Address isn't a field in the new model, instead I need to grab all route fields and combine them into the summary address.
                // $newRoute->address = $companyDetails->route_summary_address;

                // An if empty check is being made on the optional fields so that we don't unnecessarily add ', ' to the end of an empty field.
                $newRoute->address = $companyDetails->route_address_line_1 . ', '
                                    . $companyDetails->route_address_line_2 . ', '
                                    . $companyDetails->route_address_line_3 . ', '
                                    . $companyDetails->route_city . ', '
                                    . $companyDetails->route_region . ', '
                                    . $companyDetails->route_postcode;

                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->assigned_route_id = $assigned_route_id;
                $newRoute->delivery_day = request('snackbox_details.delivery_day');
                $newRoute->save();


                $message = "Route $newRoute->route_name on " . request('snackbox_details.delivery_day') . " saved.";
                Log::channel('slack')->info($message);
            }
        } else {

            $message = "Route on " . request('snackbox_details.delivery_day') . " not necessary, delivered by " . request('snackbox_details.delivered_by');
            Log::channel('slack')->info($message);
        }
    }

    public function updateBoxDetails($request)
    {
        $snackbox = SnackBox::find();
    }

    // This is just (?) used to add/remove contents from an existing box.  This may well be used to tailor a box after it's creation and before delivery, so automatiucally creating a archive
    // could cause more trouble than it's worth.

    public function addProductToSnackbox (Request $request)
    {
        // I need to add some sort of stock level amendments here too.
        // If stock levels were adjusted in the box creation we need to
        // return the stock from removed entries and subtract the stock from added ones.


        $addProduct = new SnackBox();
        $addProduct->snackbox_id = request('snackbox_details.snackbox_id');
        $addProduct->is_active = request('snackbox_details.is_active');
        $addProduct->delivered_by = request('snackbox_details.delivered_by');
        $addProduct->no_of_boxes = request('snackbox_details.no_of_boxes');
        $addProduct->snack_cap = request('snackbox_details.snack_cap');
        $addProduct->type = request('snackbox_details.type');
        $addProduct->company_details_id = request('snackbox_details.company_details_id');
        $addProduct->delivery_day = request('snackbox_details.delivery_day');
        $addProduct->frequency = request('snackbox_details.frequency');
        $addProduct->week_in_month = request('snackbox_details.week_in_month');
        $addProduct->previous_delivery_week = request('snackbox_details.previous_delivery_week');
        $addProduct->delivery_week = request('snackbox_details.delivery_week');
        $addProduct->product_id = request('product.id');
        $addProduct->code = request('product.code');
        $addProduct->brand = request('product.brand');
        $addProduct->flavour = request('product.flavour');
        $addProduct->quantity = request('product.quantity');
        $addProduct->selling_unit_price = request('product.selling_unit_price');
        $addProduct->selling_case_price = request('product.selling_case_price');
        $addProduct->save();

        // Looks I found a neat one liner to sort out reducing stock levels - I'm also guessing 'increment' will sort out returning stock too.
        // Just in case there's a problem saving the new product, we'll only worry about reducing the stock levels if we get this far without hitting an error.
        Product::find(request('product.id'))->decrement('stock_level', request('product.quantity'));
    }

    // While the function addProductToBox has been moved to a trait function, I (think) I still need to call it via a route/controller function.
    public function addProductToSnackBoxV2(Request $request)
    {
        // Using the function from App\Trait\Orders;
        $this->addProductToBox($request);

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

    // ----- This is the beginning of new system snackbox functions ----- // <-- EDIT: YOU SURE ABOUT THAT BUDDY?

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function archiveAndEmptySnackBoxes() {
        // what about if I make this a manual step?
        // 1. regardless of type, if the box is active and contains products (not the entry with product_id = 0), we save it to the archives.
        $snackboxes = SnackBox::where('is_active', 'Active')->get()->groupBy('snackbox_id');

        foreach ($snackboxes as $snackbox) {

            if (count($snackbox) === 1) {
            // we're probably looking at an empty box, so the product_id should be 0

                if ($snackbox[0]->product_id === 0) {
                // Then all is as expected.
                } else {
                // Something unexpected has happened, let's log it for review.
                $message = 'Well, shhhiiiitttttt! Snackbox ' . $snackbox[0]->snackbox_id
                . ' only has one item in it and it\'s ' . $snackbox[0]->product_id
                . ' rather than 0. You can find it at row ' . $snackbox[0]->id;

                Log::channel('slack')->info($message);
                }

            } elseif (count($snackbox) > 1) {
            // we have a box which needs to be archived

            //---------- Time to save the existing box as an archive ----------//

                // 1.(a) if the box has an invoiced_at date, we can save it as 'inactive'.
                if ($snackbox[0]->invoiced_at !== null) {
                    // We have a box that's already been invoiced, so we can save it to archives with an 'inactive' status.
                    foreach ($snackbox as $snackbox_item) {

                        // I'm currently switching all create new instances with updateOrInsert instances to prevent the possibility of duplicates.
                        // Snack_id and delivery_week isn't sufficient a check but combined with product_id we should be good.
                        // The only issue would be if the box has two entries for the same product rather than removing the original entry and combining the quantity.
                        // I should maybe prevent this from happening at all but for now just telling admins not to add duplicate product entries to the same box would suffice/save time.

                        SnackBoxArchive::updateOrInsert(
                        [
                            // Initial checks for matching entry
                            'snackbox_id' => $snackbox_item->snackbox_id,
                            'delivery_week' => $snackbox_item->delivery_week,
                            'product_id' => $snackbox_item->product_id,
                        ],
                        [
                            // Snackbox Info
                            'is_active' => 'Inactive',
                            'delivered_by' => $snackbox_item->delivered_by,
                            'no_of_boxes' => $snackbox_item->no_of_boxes,
                            'snack_cap' => $snackbox_item->snack_cap,
                            'type' => $snackbox_item->type,
                            // Company Info
                            'company_details_id' => $snackbox_item->company_details_id,
                            'delivery_day' => $snackbox_item->delivery_day,
                            'frequency' => $snackbox_item->frequency,
                            'week_in_month' => $snackbox_item->week_in_month,
                            'previous_delivery_week' => $snackbox_item->previous_delivery_week,
                            // Product Info
                            'code' => $snackbox_item->code,
                            'brand' => $snackbox_item->brand,
                            'flavour' => $snackbox_item->flavour,
                            'quantity' => $snackbox_item->quantity,
                            'selling_unit_price' => $snackbox_item->selling_unit_price,
                            'selling_case_price' => $snackbox_item->selling_case_price,
                            'invoiced_at' => $snackbox_item->invoiced_at,
                        ]);

                    }

                } else {
                    // 1.(b) if it doesn't, we need to save it to archives as 'active' so it can be pulled into the next invoicing run.
                    foreach ($snackbox as $snackbox_item) {

                        SnackBoxArchive::updateOrInsert(
                        [
                            // Initial checks for matching entry
                            'snackbox_id' => $snackbox_item->snackbox_id,
                            'delivery_week' => $snackbox_item->delivery_week,
                            'product_id' => $snackbox_item->product_id,
                        ],
                        [
                            // Snackbox Info
                            'is_active' => 'Active',
                            'delivered_by' => $snackbox_item->delivered_by,
                            'no_of_boxes' => $snackbox_item->no_of_boxes,
                            'snack_cap' => $snackbox_item->snack_cap,
                            'type' => $snackbox_item->type,
                            // Company Info
                            'company_details_id' => $snackbox_item->company_details_id,
                            'delivery_day' => $snackbox_item->delivery_day,
                            'frequency' => $snackbox_item->frequency,
                            'week_in_month' => $snackbox_item->week_in_month,
                            'previous_delivery_week' => $snackbox_item->previous_delivery_week,
                            // Product Info
                            'code' => $snackbox_item->code,
                            'brand' => $snackbox_item->brand,
                            'flavour' => $snackbox_item->flavour,
                            'quantity' => $snackbox_item->quantity,
                            'selling_unit_price' => $snackbox_item->selling_unit_price,
                            'selling_case_price' => $snackbox_item->selling_case_price,
                            'invoiced_at' => $snackbox_item->invoiced_at,
                        ]);
                    }

                } // End of if/else ($snackbox[0]->invoiced_at !== null)

            //---------- End of - Time to save the existing box as an archive ----------//

            //---------- Now we can strip out the orders ready for adding new products ----------//

            // But first we need to grab any details we'll be reusing.
            $snackbox_id_recovered = $snackbox[0]->snackbox_id;
            $delivered_by_recovered = $snackbox[0]->delivered_by;
            $delivery_day_recovered = $snackbox[0]->delivery_day;
            $no_of_boxes_recovered = $snackbox[0]->no_of_boxes;
            $snack_cap_recovered = $snackbox[0]->snack_cap;
            $type_recovered = $snackbox[0]->type;
            $company_details_id_recovered = $snackbox[0]->company_details_id;
            $frequency_recovered = $snackbox[0]->frequency;
            $week_in_month_recovered = $snackbox[0]->week_in_month;
            $previous_delivery_week_recovered = $snackbox[0]->previous_delivery_week;
            $delivery_week_recovered = $snackbox[0]->delivery_week;

            // Now we can loop through each entry and delete them
            foreach ($snackbox as $snack_item) {
                // Don't worry, we've rescued all we need ;) ...probably.
                SnackBox::destroy($snack_item->id);
            }

            //---------- End of - Now we can strip out the orders ready for adding new products ----------//

            //---------- But we still need to recreate the empty box entry to repopulate with products later on. ----------//

            // 2. regardless of type, if the snackbox exists we strip out its orders, leaving only 1 entry with box details and a product id of 0, ready for the next mass/solo box update.

            $empty_snackbox = new SnackBox();
            // Snackbox Info
            // $new_snackbox->is_active <-- Is already set to 'Active' by default.
            $empty_snackbox->snackbox_id = $snackbox_id_recovered;
            $empty_snackbox->delivered_by = $delivered_by_recovered;
            $empty_snackbox->no_of_boxes = $no_of_boxes_recovered;
            $empty_snackbox->snack_cap = $snack_cap_recovered;
            $empty_snackbox->type = $type_recovered;
            // Company Info
            $empty_snackbox->company_details_id = $company_details_id_recovered;
            $empty_snackbox->delivery_day = $delivery_day_recovered;
            $empty_snackbox->frequency = $frequency_recovered;
            $empty_snackbox->week_in_month = $week_in_month_recovered;
            $empty_snackbox->previous_delivery_week = $previous_delivery_week_recovered;
            $empty_snackbox->delivery_week = $delivery_week_recovered;
            // Product Information
            $empty_snackbox->product_id = 0;
            $empty_snackbox->code = null;
            $empty_snackbox->brand = null;
            $empty_snackbox->flavour = null;
            $empty_snackbox->quantity = null;
            $empty_snackbox->selling_unit_price = null;
            $empty_snackbox->selling_case_price = null;
            $empty_snackbox->invoiced_at = null;
            $empty_snackbox->save();

            //---------- End of - But we still need to recreate the empty box entry to repopulate with products later on. ----------//

            } // if (count($snackbox) === 1) & elseif (count($snackbox)) > 1)
        } // foreach ($snackboxes as $snackbox)

        // Now it's time for the redirect
        return redirect()->route('office');

         // If I take this approach, it would work fine for once a week processing but if we switch this to daily, then I'd need to either restrict mass updates in the same way
         // or write some logic to cater for this.
     }

     //----- Functions used in the massUpdateTypeV2 function -----//

     public function removeOutOfStockLikes($like)
     {
         // $like is a combination of the products' brand and flavour, specifying the variables we want create in list, assigns them to from exploding the $like
         list($brand, $flavour) = explode(' - ', $like);
         // This will only return a countable $option if the item is in stock.
         $option = Product::where('brand', $brand)->where('flavour', $flavour)->where('stock_level', '>', 0)->get();
         // If $option count returns nothing, it's not in stock and can be removed from selectable products this time around.
         if (!count($option)) {
             // Search for the product in array of $likes and grab its position (key) in array.
             $like_key = array_search($like, $likes);
             // Now use this key to unset (remove) the product from usable list of likes.
             unset($likes[$like_key]);
         }
     }

     public function checkProductForCompanyAllergies($new_standard_snack, $company_details_id_recovered, $snackbox_id_recovered, $products_already_in_box = [], $dislikes_by_id = [])
     {
         // First I need to get hold of the snackbox specific allergens.

         // Grab the first instance of the snackbox based on the snackbox_id.  The allergies should be connected to each entry so it doesn't really matter which one we get.
         $snackbox = SnackBox::where('snackbox_id', $snackbox_id_recovered)->first();
         // Now we can check its relationship with allergies and see if any are connected to this snackbox_id.
         $allergies_and_dietary_requirements = $snackbox->allergies_and_dietary_requirements;

         if ($allergies_and_dietary_requirements['allergy']) {
             // Then we have some allergies associated with this snackbox_id, so let's compare the allergies to the allergens included in this snack.
             // Any matches are returned as an array, which we save to $conflicting_allergies.
             $conflicting_allergies = array_intersect($new_standard_snack['allergen_info'], $allergies_and_dietary_requirements['allergy']);

         } else {
             // If they don't exist, I need to look for company allergens.
             $company = CompanyDetails::find($company_details_id_recovered);
             $company_allergies = $company->allergies;

             if ($company_allergies['allergy']) {

                 // If they exist we can check the company allergies for conflicts instead
                 $conflicting_allergies = array_intersect($new_standard_snack['allergen_info'], $company_allergies['allergy']);
             } else {
                 $conflicting_allergies = [];
             }
         }

         // If they don't exist either, we can either just return the snack or check it with dietary requirements.
        if ($conflicting_allergies) {

            $conflicting_snack_value = ( $new_standard_snack['selling_unit_price'] * $new_standard_snack['quantity'] );

            // If we have a conflicting allergy, then we need to reselect a product but we should reduce the possible pool of products,
            // excluding ones that also contain those allergens and those listed in their dislikes.

            // 1) Don't include products with the listed allergens
            // 2) Don't include any items listed in their dislikes either
            // 3) Or any already in the box.  This could factor in how many are in the box but for now, if it's already in, we don't want any more.
            // 4) Ensure it's a snack, and not a drink etc.
            // 5) And the value isn't more than the product/quantity we're replacing.
            // 6) And finally that the item is even in stock!

            $snack_replacement_options = Product::whereNotIn('allergen_info', $conflicting_allergies)
                                                ->whereNotIn('id', $dislikes_by_id)
                                                ->whereNotIn('id', $products_already_in_box)
                                                ->where('sales_nominal', '4010')
                                                ->where('selling_unit_price', '<=', $conflicting_snack_value)
                                                ->where('stock_level', '>', 0)->get();

            // 7) Save it to a collection so we can select a product at random.
            $snack_replacement = $snack_replacement_options->random();
            // Now work out how many of the item is a fair replacement but rounding down (we're not that nice!)
            $new_quantity = (floor($conflicting_snack_value / $snack_replacement->selling_unit_price));
            // Override the old quantity with the newly calculated one.
            $new_standard_snack['quantity'] = $new_quantity;

            // return the replacement new standard snack.
            return $new_standard_snack;

        } else {
            // return the original new standard snack.
            return $new_standard_snack;
        }
    }

     public function replaceDislikedItemWithOneFromLikes($likes, $new_standard_snack)
     {
         // Array_rand grabs an item from $likes at random, the 1 signifies that we only want 1 random item - returning the item key from the array.
         $key = array_rand($likes, 1);
         // Now we can select it from the $likes array.
         $selection = $likes[$key];
         list($liked_brand, $liked_flavour) = explode(' - ', $selection);

         //dd($liked_brand);

         $old_product = Product::find($new_standard_snack['id']);
         $product_details = Product::where('brand', $liked_brand)->where('flavour', $liked_flavour)->get();

         // This will need further work but so far, we find out the value of product quantity to be replaced
         // i.e value of product to be replaced (£1.50) multiplied by quantity in standard snackbox for this week (3), totals £4.50 of stock needing to be substituted.

         $old_standard_snack_value = ( $new_standard_snack['quantity'] * $old_product->selling_unit_price );

         // Now we have a total to be divided by the new product unit price
         // I'm using ceil to ensure we get a whole number that keeps the product/quantity value at a minimum of what it was before.
         // I still need to elaborate on this further to limit the quantity at 3 and prevent multiple low value items as a replacement,
         // however this will come later, let's get it working like this first!

         $new_quantity = ( $old_standard_snack_value / $product_details->selling_unit_price );

         // If the new quantity has risen to 4 or more, then the randomly selected product is likely a low value item and shouldn't really dominate the box contents.
         // In this scenario we'd like to select a second item to add some variety.

         // I'm not sure what the best approach for this is yet?
         // 1. What would we do if the next randomly selected item is far more expensive than the randomly selected product 1 it's replacing?
         // - should we reduce the quantity of randomly selected product 1?
         // - what if randomly selected product 2 is more expensive than the original item being replaced?
         // - we don't want the default behaviour to make office pantry less profit.
         // 2.

         $new_standard_snack['quantity'] = ceil($new_quantity);
         //$new_standard_snack['product_id'] = $product_details[0]->product_id; //  This looks wrong?  I'm pretty sure it should be $product_details[0]->id?
         $new_standard_snack['product_id'] = $product_details->id;
         $new_standard_snack['code'] = $product_details->code;
         $new_standard_snack['brand'] = $product_details->brand;
         $new_standard_snack['flavour'] = $product_details->flavour;
         $new_standard_snack['selling_unit_price'] = $product_details->selling_unit_price;
         $new_standard_snack['selling_case_price'] = $product_details->selling_case_price;

         return $new_standard_snack;
     }

     public function replaceDislikedItemWithOneFromNeutralSnackPool($dislikes, $new_standard_snack, $products_already_in_box)
     {
         // Then the company either didn't have any specified likes or we don't have the item in stock
         // Instead all we can do is reselect from the list of Products in stock.

         $old_product = Product::find($new_standard_snack['id']);

         dd($old_product);
         $old_standard_snack_value = ( $new_standard_snack['quantity'] * $old_product->selling_unit_price );

         foreach ($dislikes as $dislike) {
             list($disliked_brand, $disliked_flavour) = explode(' - ', $dislike);
             $disliked_product = Product::where('brand', $disliked_brand)->where('flavour', $disliked_flavour)->get();
             $disliked_products[] = $disliked_product[0]->id;
         }

         // Now let's grab all product options, so long as they're not in the company dislikes section, or already in the box.
         // Let's also limit it to mixed snack products i.e not drinks etc, where the unit value (of 1 item) isn't worth more than the replacement (total) that we're trying to make.
         // And that we have at least one of the item in stock.

         $products_in_stock = Product::whereNotIn('id', $disliked_products)
                                 ->whereNotIn('id', $products_already_in_box)
                                 ->where('sales_nominal', '4010')
                                 ->where('selling_unit_price', '<=', $old_standard_snack_value)
                                 ->where('stock_level', '>', 0)
                                 ->pluck('id')->toArray(); // <-- We now have an array of possible products to choose from as a replacement.

         $key = array_rand($products_in_stock, 1);
         // Now we can select it from the $likes array.
         $selection = $products_in_stock[$key];

         $product_details = Product::find('id', $selection);

         $new_quantity = ( $old_standard_snack_value / $product_details->selling_unit_price );

         // Now we've selected a replacement product, we just need to overwrite details of the old item, with the new.

         $new_standard_snack['quantity'] = ceil($new_quantity);
         //$new_standard_snack['product_id'] = $product_details[0]->product_id; //  This looks wrong?  I'm pretty sure it should be $product_details[0]->id?
         $new_standard_snack['product_id'] = $product_details->id;
         $new_standard_snack['code'] = $product_details->code;
         $new_standard_snack['brand'] = $product_details->brand;
         $new_standard_snack['flavour'] = $product_details->flavour;
         $new_standard_snack['unit_price'] = $product_details->selling_unit_price;
         $new_standard_snack['case_price'] = $product_details->selling_case_price;


         return $new_standard_snack;
     }

     // I might only keep this temporarily, however I want to keep my chain of thought fresh, and while the comments are useful for reference they also kinda get in the way. :)
     public function massUpdateTypeV2(Request $request)
     {

         // dd(request('order'));

         // As we're going to be updating all the boxes of a particular type, let's grab them and group them by snackbox_id (to process them box by box)
         // *** should this be further limited to snackboxes for this next delivery week?  Otherwise a whole bunch of boxes not due for delivery will be assigned stock! I'm doing it! ***
         $snackboxes = Snackbox::where('type', request('type'))->where('is_active', 'Active')->where('delivery_week', $this->week_start)->get()->groupBy('snackbox_id');

         // dd($snackboxes);

         // Now we can loop through each box
         foreach ($snackboxes as $snackbox) {

             // Grabbing the data unique to each box, not each item.

             // Snackbox Info
             $snackbox_id_recovered = $snackbox[0]->snackbox_id;
             $delivered_by_recovered = $snackbox[0]->delivered_by;
             $delivery_date_recovered = $snackbox[0]->delivery_day;
             $no_of_boxes_recovered = $snackbox[0]->no_of_boxes;
             $snack_cap_recovered = $snackbox[0]->snack_cap;
             // Company Info
             $company_details_id_recovered = $snackbox[0]->company_details_id;
             $frequency_recovered = $snackbox[0]->frequency;
             $week_in_month_recovered = $snackbox[0]->week_in_month;
             $previous_delivery_week_recovered = $snackbox[0]->previous_delivery_week;
             $delivery_week_recovered = $snackbox[0]->delivery_week;

             // Now we can process each snack within the snackbox
             foreach ($snackbox as $snack) {

                 // Before we mass update all the boxes we want to save a record of the previous contents.
                     // Option A - firstOrCreate - look for an existing match, if we find one * DO NOTHING *, or create the entry if not.
                     // Option B - updateOrCreate - look for an existing match, if we find one * REPLACE IT *, or create the entry if not.
                 // In the original version of this function I've gone with Option A - I assume for reasons, we'll see.

                 SnackBoxArchive::firstOrCreate(
                 [
                     // If this is written the same way as updateOrInsert, this is the initial check
                     'snackbox_id' => $snack->snackbox_id,
                     'delivery_week' => $snack->delivery_week,
                     'product_id' => $snack->product_id,
                 ],
                 [
                     // Snackbox Info
                     'is_active' => $snack->is_active, // Mind you we're only getting the 'Active' ones, so this could be hardcoded.
                     'delivered_by' => $snack->delivered_by,
                     'no_of_boxes' => $snack->no_of_boxes,
                     'snack_cap' => $snack->snack_cap,
                     'type' => $snack->type,
                     // Company Info
                     'company_details_id' => $snack->company_details_id,
                     'delivery_day' => $snack->delivery_day,
                     'frequency' => $snack->frequency,
                     'previous_delivery_week' => $snack->previous_delivery_week,
                     // Product info
                     'code' => $snack->code,
                     'brand' => $snack->brand,
                     'flavour' => $snack->flavour,
                     'quantity' => $snack->quantity,
                     'selling_unit_price' => $snack->selling_unit_price,
                     'selling_case_price' => $snack->selling_case_price,
                     'invoiced_at' => $snack->invoiced_at,
                 ]);

                 // Destroy each item from the box, ready to repopulate
                 // Do not destroy the default snackbox container which will have a product id of 0.
                 if ($snack->product_id !== 0) {
                     SnackBox::destroy($snack->id);
                 }
             } // end of foreach $snackbox as $snack (i.e back to $snackbox scale not item by item.)

             $likes = Preference::where('company_details_id', $snack->company_details_id)->where('snackbox_likes', '!=', null)->pluck('snackbox_likes')->toArray();
             $dislikes = Preference::where('company_details_id', $snack->company_details_id)->where('snackbox_dislikes', '!=', null)->pluck('snackbox_dislikes')->toArray();

             // Convert the human readable dislike names into a more useful array of id's.
             foreach ($dislikes as $dislike) {
                 list($brand, $flavour) = explode(' - ', $dislike);
                 // This will only return a countable $option if the item is in stock.
                 $dislikes_by_id[] = Product::where('brand', $brand)->where('flavour', $flavour)->pluck('id');
             }


             // There's no point including any 'liked' items in this array if we don't have any of them in stock,
             // so let's check the name in Products and see what the stock level looks like.
             foreach ($likes as $like) {
                 // I've moved this logic out of the function so I can try to keep it as simple as possible to read.
                 // I may change this again, or refactor more functions this way - we will see.
                 $this->removeOutOfStockLikes($like);
             }

             // create an array of id's we can check through later so we don't replace items with ones already included in the box.
             foreach (request('order') as $new_standard_snack) {
                 $products_already_in_box[] = $new_standard_snack['id'];
             }

             // now we can process the snacks in the box, if it's a disliked item, we're gonna need to replace it, otherwise we're good to save it
             foreach (request('order') as $new_standard_snack) {

                // if new snack item is in company list of dislikes and at least one of their listed likes is in stock.
                // i've changed the search to match the way it should look in the dislikes array.
                if (in_array(($new_standard_snack['brand'] . ' - ' . $new_standard_snack['flavour']), $dislikes) && !empty($likes)) {

                    // REPLACE SNACKBOX ITEM AS IT'S LISTED IN THE COMPANY DISLIKES AND THEY HAVE LIKED ITEM(S) IN STOCK
                    $this->replaceDislikedItemWithOneFromLikes($likes, $new_standard_snack);

                 // if the new standard snack is in the company list of dislikes but we have nothing in stock they really 'like'
                } elseif (in_array(($new_standard_snack['brand'] . ' - ' . $new_standard_snack['flavour']), $dislikes) && empty($likes)) {

                    // THEY DISLIKE THIS SNACK ITEM BUT NOTHING THEY LIKE IS IN STOCK - REPICK ITEM FROM THE SNACKS IN STOCK.
                    $this->replaceDislikedItemWithOneFromNeutralSnackPool($dislikes, $new_standard_snack, $products_already_in_box);

                }

                // $dislikes_by_id will only be created if the company has at least one dislike to push into the array - I could set it before hand, or give it a default value here.
                // I'm putting the allergy check here so that disliked products have already had the chance to be removed, replacements have been made and we can do one final check again before the saving the snack to the box.
                // Should I include Dietary Requirements or is that more of an ops assist for filtering products?
                $this->checkProductForCompanyAllergies($new_standard_snack, $company_details_id_recovered, $snackbox_id_recovered, $products_already_in_box = [], $dislikes_by_id = []);

                 // if we're here, the (original) product, or its replacement should be fine to add to the company snackbox.

                 $new_snackbox = new SnackBox();
                 // Snackbox Info
                 $new_snackbox->snackbox_id = $snackbox_id_recovered;
                 $new_snackbox->delivered_by = $delivered_by_recovered;
                 $new_snackbox->no_of_boxes = $no_of_boxes_recovered;
                 $new_snackbox->snack_cap = $snack_cap_recovered;
                 $new_snackbox->type = $request['type'];
                 // Company Info
                 $new_snackbox->company_details_id = $company_details_id_recovered;
                 $new_snackbox->delivery_day = $delivery_date_recovered;
                 $new_snackbox->frequency = $frequency_recovered;
                 $new_snackbox->week_in_month = $week_in_month_recovered;
                 $new_snackbox->previous_delivery_week = $previous_delivery_week_recovered;
                 $new_snackbox->delivery_week = $delivery_week_recovered;
                 // Product Information
                 $new_snackbox->product_id = (isset($new_standard_snack['product_id'])) ? $new_standard_snack['product_id'] : $new_standard_snack['id'];
                 $new_snackbox->code = $new_standard_snack['code'];
                 $new_snackbox->brand = $new_standard_snack['brand'];
                 $new_snackbox->flavour = $new_standard_snack['flavour'];
                 $new_snackbox->quantity = $new_standard_snack['quantity'];
                 $new_snackbox->selling_unit_price = $new_standard_snack['selling_unit_price'];
                 $new_snackbox->selling_case_price = $new_standard_snack['selling_case_price'];
                 $new_snackbox->save();

             } // end of foreach ($request['order'] as $new_standard_snack)
         }
     }

     public function massUpdateType(Request $request)
     {

        // If this is an upload of the new weekly standard box, we won't have a company to attach
        // - instead any snackbox with 'standard' as type and unique delivery day/company id needs to pulled through,
        // stripped of all listed entries and replaced with the new order.

        // dd($request);

        // Grab all the snackboxes we have of the requested type.
        // GroupBy must follow '->get()' in query to utilise 'Collections' rather than 'Query Builder' which treats 'groupBy' differently.
        // I've added the where('is_active', 'Active') to prevent boxes no longer in use - getting updated with products, stripped of products, archived and (without sufficient checks) potentially pulled into invoicing.
        $snackboxes = Snackbox::where('type', request('type'))->where('is_active', 'Active')->get()->groupBy('snackbox_id');

        // BUT WHAT DO WE DO ABOUT A BOX WITH A DELIVERY DATE SET IN THE FUTURE, FROM BEING UPDATED EACH WEEK UNTIL ITS ACTUAL DELIVERY DATE?
        // CURRENT LOGIC WOULD HAVE THESE CREATED IN ARCHIVE UNTIL DELIVERY DATE UNLESS THEY WERE SET TO INACTIVE WHICH KINDA DEFEATS THE PURPOSE OF SETTING IT UP IN ADVANCE!

        // Maybe 'IS_ACTIVE' could be changed to include more options, such as 'PAUSED' - Which waits until the 'delivery_week' matches current delivery week (a new thing I could create)
        // before changing its status to ACTIVE?
        // Hmmn, though as we process orders a week in advance, we'd need to set this to act a week in advance, which is annoying?
        // We also cant use the current 'WEEK START' variable as this is manually changed and wouldn't be a reliable way to activate orders?

        // dd($snackboxes);
        //
        //
        // // Grab all distinct snackbox_id's, this should (will) grab all unique snackbox Id's for step 2.
        // $snackboxes = SnackBox::select('snackbox_id')->distinct()->get();

        //---------- Grab any relevant snackbox data and then strip out the rest. ----------//

        foreach ($snackboxes as $snackbox) {

            // dd($snackbox);

            //----- By improving the steps above I've bypassed the need for this step as we only get the snackbox type we want. -----//
                // // In step 2 we want run through all id's, checking that the type is for a standard snackbox.
                // $snackbox = SnackBox::where('snackbox_id', $snackbox_id['snackbox_id'])->where('type', request('type'))->get();
            //----- End of - By improving the steps above I've bypassed the need for this step as we only get the snackbox type we want. -----//

            // If we're about to update the box we should probably create an archive of the existing box contents for posterity.

            // Grab these before deleting old entry, kinda important
            // However I only really need to do this once per snackbox,
            // as I'm basically just writing over these variables each time
            // until I get to the last entry of the box!

            // To be clearer - this is primarily to repopulate the new box with the same details,
            // however we'll also be creating an archive entry using the old data.

            // Snackbox Info
            $snackbox_id_recovered = $snackbox[0]->snackbox_id;
            $delivered_by_recovered = $snackbox[0]->delivered_by;
            $delivery_date_recovered = $snackbox[0]->delivery_day;
            $no_of_boxes_recovered = $snackbox[0]->no_of_boxes;
            $snack_cap_recovered = $snackbox[0]->snack_cap;
            // Company Info
            $company_details_id_recovered = $snackbox[0]->company_details_id;
            $frequency_recovered = $snackbox[0]->frequency;
            $week_in_month_recovered = $snackbox[0]->week_in_month;
            $previous_delivery_week_recovered = $snackbox[0]->previous_delivery_week;
            $delivery_week_recovered = $snackbox[0]->delivery_week;

            // We can't updateOrInsert (per snack line) based on the uniqueness of snackbox_id/delivery_week in the archives due to having multiple rows (one for each item) in the snackbox.
            // However taking it up a level to here, we can check if the snackbox_id/delivery_week currently exists in the snackbox_archives.

            // While I quite like this approach and I'm sure it does a job, I've now implemented updateOrInsert everywhere else, using product_id to make the check more specific to each entry.
            // To try and create predictable behaviour I'm going to change this as well, so the same checks are being made throughout the site.

            // $has_archive = SnackBoxArchive::where('snackbox_id', $snackbox_id_recovered)->where('delivery_week', $delivery_week_recovered)->get();


            if (count($snackbox)) { // Now I'm not checking all of the snackbox entries I don't need to worry about empty boxes. Though I suppose it's not doing any harm either.

                foreach ($snackbox as $snack) {

                    // By default we want to save the old box to archives so we can update the box on the front end with new products
                    // but keep a record of what they've had in the past, either for our records or because we invoice them monthly etc...

                    // updateOrCreate wont work here because there will be numerous entries with matching information
                    // Snackbox_id & delivery_week

                    // So we need to think of something else to determine whether to update or create.
                    // Also when trying to 'groupBy' - 'snackbox_id' from the 'snackbox_archives', it'll group all archived entries into one box
                    // so we need to add a second stipulation to 'groupBy - 'snackbox_id' & 'delivery_week'

                    //----- HAVE I TACKLED THIS YET? IS IT SOMETHING I NEED TO CONSIDER AND FIX - MUST CHECK!! -----//

                    // Now that we should already have archives in place before this step, we can go ahead and check for the archive,
                    // only adding the entry if it doesn't exist, as we expect an entry to be present and don't want to update unnecessarily.

                    // First checks to see if the entry exists, if it does, do nothing, if it doesn't, create a new entry but the odds of this needing to happen are infinitesimally small.  I think?
                    SnackBoxArchive::firstOrCreate(
                    [
                        // If this is written the same way as updateOrInsert, this is the initial check
                        'snackbox_id' => $snack->snackbox_id,
                        'delivery_week' => $snack->delivery_week,
                        'product_id' => $snack->product_id,
                    ],
                    [
                        // Snackbox Info
                        'is_active' => $snack->is_active, // Mind you we're only getting the 'Active' ones, so this could be hardcoded.
                        'delivered_by' => $snack->delivered_by,
                        'no_of_boxes' => $snack->no_of_boxes,
                        'snack_cap' => $snack->snack_cap,
                        'type' => $snack->type,
                        // Company Info
                        'company_details_id' => $snack->company_details_id,
                        'delivery_day' => $snack->delivery_day,
                        'frequency' => $snack->frequency,
                        'previous_delivery_week' => $snack->previous_delivery_week,
                        // Product info
                        'code' => $snack->code,
                        'brand' => $snack->brand,
                        'flavour' => $snack->flavour,
                        'quantity' => $snack->quantity,
                        'selling_unit_price' => $snack->selling_unit_price,
                        'selling_case_price' => $snack->selling_case_price,
                        'invoiced_at' => $snack->invoiced_at,
                    ]);



                    // // If it doesn't we can go ahead and save each row of the old box contents
                    // if (!count($has_archive)) {
                    //     $archived_snackbox = new SnackBoxArchive();
                    //     // Snackbox Info
                    //     $archived_snackbox->snackbox_id = $snack->snackbox_id;
                    //     $archived_snackbox->is_active = $snack->is_active;
                    //     $archived_snackbox->delivered_by = $snack->delivered_by;
                    //     $archived_snackbox->no_of_boxes = $snack->no_of_boxes;
                    //     $archived_snackbox->snack_cap = $snack->snack_cap;
                    //     $archived_snackbox->type = $snack->type;
                    //     // Company Info
                    //     $archived_snackbox->company_details_id = $snack->company_details_id;
                    //     $archived_snackbox->delivery_day = $snack->delivery_day;
                    //     $archived_snackbox->frequency = $snack->frequency;
                    //     $archived_snackbox->previous_delivery_week = $snack->previous_delivery_week;
                    //     $archived_snackbox->delivery_week = $snack->delivery_week;
                    //     // Product Information
                    //     $archived_snackbox->product_id = $snack->product_id;
                    //     $archived_snackbox->code = $snack->code;
                    //     $archived_snackbox->name = $snack->name;
                    //     $archived_snackbox->quantity = $snack->quantity;
                    //     $archived_snackbox->unit_price = $snack->unit_price;
                    //     $archived_snackbox->invoiced_at = $snack->invoiced_at;
                    //     $archived_snackbox->save();
                    // } else {
                    //     // But what should we do if it does?
                    //     // What are the possible reasons for this?
                    //
                    //     // 1. Mass update has already been run for this 'type', backing up orders for that 'delivery_week' (i.e the previous week).
                    //     // 2. An update to a company snackbox (i.e not a mass update) <-- This isn't in place yet and I'm not sure it should be unless specifically requested via a button to back up contents before editing?
                    //     // 3. OR SHOULD I JUST CREATE A SPECIFIC TIME WHEN ALL SNACKBOX ORDERS ARE EMPTIED AND ARCHIVES CREATED?
                    //     // - IF BOXES ARE EMPTIED PRIOR TO MASS UPDATING, WE WOULDN'T NEED TO DO THAT HERE EITHER?
                    // }


                    // Now we return to the existing code and delete the entry...

                    // If the snackbox entry exists we can go ahead and delete it - as the snackbox contents may vary in quantity,
                    // I just want to strip them all out and replace with the new standard order.

                //----- LOOK AT THIS!  DID I MEAN TO LEAVE IT COMMENTED OUT OR HAVE I JUST SPOTTED AN OVERSIGHT! -----//

                    // Snackbox::where('id', $snack->id)->delete(); <-- Temporarily commenting out to test without having to rebuild the orders again!  Must uncomment again when I'm done!!

                    // Kinda pointless but I'd like to change this ( Snackbox::where('id', $snack->id)->delete(); ) to
                    // --> SnackBox::destroy($snack->id);

                    // On reflection, this could be reinstated or deleted (boxes should be empty before this function is called, however should the unexpected happen, and as
                    // we're going to the trouble of checking for unarchived entries, let's delete any entries other than default product_id == 0.
                    // Then hopefully we have the best of both worlds.

                    // EDIT: SOMETHING IS CAUSING EMPTY BOX DUPLICATION - IT HAPPENS TO SNACKS, DRINKS AND I'D ASSUME OTHER BUT I'LL NEED TO GENERATE AN OTHERBOX TO CONFIRM.
                    // IF IT'S HAPPENING TO THE REST, THEN HOW CAN THIS BUT OF CODE BE RESPONSIBLE?  I'M NOT SURE BUT NEED TO HIGHLIGHT THIS JUST IN CASE!
                    if ($snack->product_id !== 0) {
                        SnackBox::destroy($snack->id);
                    }

                //----- LOOK AT THIS!  DID I MEAN TO LEAVE IT COMMENTED OUT OR HAVE I JUST SPOTTED AN OVERSIGHT! -----//

            } // End of foreach ($snackbox as $snack) <-- IT'S GETTING LOST IN ALL THE COMMENTS, JEEZ STOP TALKING TO YOURSELF!

                //---------- End of Grab any relevant snackbox data and then strip out the rest. ----------//

                //---------- Now let's grab their list of likes and dislikes ----------//

                // Moved this further up to keep it out of a second (unnecessary for this query) foreach.
                $likes = Preference::where('company_details_id', $snack->company_details_id)->where('snackbox_likes', '!=', null)->pluck('snackbox_likes')->toArray();
                $dislikes = Preference::where('company_details_id', $snack->company_details_id)->where('snackbox_dislikes', '!=', null)->pluck('snackbox_dislikes')->toArray();

                // There's no point including any 'liked' items in this array if we don't have any of them in stock,
                // so let's check the name in Products and see what the stock level looks like.
                foreach ($likes as $like) {
                    // This will only return a countable $option if the item is in stock.
                    // If we're reducing stock as we go, then there'll be a slightly unfair hierarchy
                    // to get their 'liked' snacks depending on whether they get picked first out of the hat or not,
                    // which isn't a random process (unfortunately?).
                    $option = Product::where('name', $like)->where('stock_level', '>', 0)->get();
                    // If $option count returns nothing, it's not in stock and can be removed from selectable products this time around.
                    if (!count($option)) {
                        // Search for the product in array of $likes and grab its position (key) in array.
                        $like_key = array_search($like, $likes);
                        // Now use this key to unset (remove) the product from usable list of likes.
                        unset($likes[$like_key]);
                    }
                }

                //-------------------- LIKES NOW ONLY CONTAIN PRODUCTS IN STOCK! ---------------------//
                //---------- End of Now let's grab their list of likes and dislikes ----------//

                //----- Notes To Self -----//

                    // Should I be deleting and creating in the same function?
                    // I'm not sure but for test purposes and problem solving clarity, I'm gonna start off this way.
                    // In fact due to the reuse of data, I kinda need to keep it together, so let's hope this will work without complications.

                //----- End of Notes To Self -----//

                //---------- Now we need to run through the new selection of snacks, adding them (if not specified as a dislike) for the company being processed ----------//

                foreach ($request['order'] as $new_standard_snack) {
                    $products_already_in_box[] = $new_standard_snack['id'];
                }

                foreach ($request['order'] as $new_standard_snack) {
                    // dump($request['order']);
                    // dump($new_standard_snack);

                    // if new snack item is in company list of dislikes and at least one of their listed likes is in stock.
                    if (in_array($new_standard_snack['name'], $dislikes)
                                                    && !empty($likes)
                        ) { // I've added '&& !empty($likes)' to only replace a disliked item if they've specified a liked item and we have one in stock.

                        // Make a random selection from their $likes, which now only contains products we currently have in stock.
                        // However this doesn't take into account any price differences between items, something for later. <-------- Needs to be addressed at some point!

                        // Array_rand grabs an item from $likes at random, the 1 signifies that we only want 1 random item - returning the item key from the array.
                        $key = array_rand($likes, 1);
                        // Now we can select it from the $likes array.
                        $selection = $likes[$key];

                        $old_product = Product::where('name', $new_standard_snack['name'])->get();
                        $product_details = Product::where('name', $selection)->get();

                        // This will need further work but so far, we find out the value of product quantity to be replaced
                        // i.e value of product to be replaced (£1.50) multiplied by quantity in standard snackbox for this week (3), totals £4.50 of stock needing to be substituted.

                        $old_standard_snack_value = ( $new_standard_snack['quantity'] * $old_product[0]->unit_price );

                        // Now we have a total to be divided by the new product unit price
                        // I'm using ceil to ensure we get a whole number that keeps the product/quantity value at a minimum of what it was before.
                        // I still need to elaborate on this further to limit the quantity at 3 and prevent multiple low value items as a replacement,
                        // however this will come later, let's get it working like this first!

                        $new_quantity = ( $old_standard_snack_value / $product_details[0]->unit_price );

                        // If the new quantity has risen to 4 or more, then the randomly selected product is likely a low value item and shouldn't really dominate the box contents.
                        // In this scenario we'd like to select a second item to add some variety.

                        // I'm not sure what the best approach for this is yet?
                        // 1. What would we do if the next randomly selected item is far more expensive than the randomly selected product 1 it's replacing?
                        // - should we reduce the quantity of randomly selected product 1?
                        // - what if randomly selected product 2 is more expensive than the original item being replaced?
                        // - we don't want the default behaviour to make office pantry less profit.
                        // 2.

                        $new_standard_snack['quantity'] = ceil($new_quantity);
                        //$new_standard_snack['product_id'] = $product_details[0]->product_id; //  This looks wrong?  I'm pretty sure it should be $product_details[0]->id?
                        $new_standard_snack['product_id'] = $product_details[0]->id;
                        $new_standard_snack['code'] = $product_details[0]->code;
                        $new_standard_snack['name'] = $product_details[0]->name;
                        $new_standard_snack['unit_price'] = $product_details[0]->unit_price;
                        $new_standard_snack['case_price'] = $product_details[0]->case_price;

                    // if the new standard snack is in the company list of dislikes but we have nothing in stock they really 'like'
                    } elseif (in_array($new_standard_snack['name'], $dislikes) && empty($likes)) {

                        // Then the company either didn't have any specified likes or we don't have the item in stock
                        // Instead all we can do is reselect from the list of Products in stock.

                        $old_product = Product::where('name', $new_standard_snack['name'])->get();
                        $old_standard_snack_value = ( $new_standard_snack['quantity'] * $old_product[0]->unit_price );

                        // Now let's grab all product options, so long as they're not in the company dislikes section, or already in the box.
                        // Let's also limit it to mixed snack products i.e not drinks etc, where the unit value (of 1 item) isn't worth more than the replacement (total) that we're trying to make.
                        // And that we have at least one of the item in stock.

                        $products_in_stock = Product::whereNotIn('name', $dislikes)
                                                ->whereNotIn('id', $products_already_in_box)
                                                ->where('sales_nominal', '4010')
                                                ->where('unit_price', '<=', $old_standard_snack_value)
                                                ->where('stock_level', '>', 0)
                                                ->pluck('id')->toArray(); // <-- We now have an array of possible products to choose from as a replacement.

                        $key = array_rand($products_in_stock, 1);
                        // Now we can select it from the $likes array.
                        $selection = $products_in_stock[$key];

                        $product_details = Product::where('id', $selection)->get();

                        $new_quantity = ( $old_standard_snack_value / $product_details[0]->unit_price );

                        // Now we've selected a replacement product, we just need to overwrite details of the old item, with the new.

                        $new_standard_snack['quantity'] = ceil($new_quantity);
                        //$new_standard_snack['product_id'] = $product_details[0]->product_id; //  This looks wrong?  I'm pretty sure it should be $product_details[0]->id?
                        $new_standard_snack['product_id'] = $product_details[0]->id;
                        $new_standard_snack['code'] = $product_details[0]->code;
                        $new_standard_snack['name'] = $product_details[0]->name;
                        $new_standard_snack['unit_price'] = $product_details[0]->unit_price;
                        $new_standard_snack['case_price'] = $product_details[0]->case_price;

                    }

                    // if we're here, the (original) product, or its replacement should be fine to add to the company snackbox.

                    $new_snackbox = new SnackBox();
                    // Snackbox Info
                    $new_snackbox->snackbox_id = $snackbox_id_recovered;
                    $new_snackbox->delivered_by = $delivered_by_recovered;
                    $new_snackbox->no_of_boxes = $no_of_boxes_recovered;
                    $new_snackbox->snack_cap = $snack_cap_recovered;
                    $new_snackbox->type = $request['type'];
                    // Company Info
                    $new_snackbox->company_details_id = $company_details_id_recovered;
                    $new_snackbox->delivery_day = $delivery_date_recovered;
                    $new_snackbox->frequency = $frequency_recovered;
                    $new_snackbox->week_in_month = $week_in_month_recovered;
                    $new_snackbox->previous_delivery_week = $previous_delivery_week_recovered;
                    $new_snackbox->delivery_week = $delivery_week_recovered;
                    // Product Information
                    // dump($new_standard_snack->product_id);
                    // dump(isset($new_standard_snack['product_id'])); // <-- Need
                    // $new_snackbox->product_id = $new_standard_snack['product_id'];
                    $new_snackbox->product_id = (isset($new_standard_snack['product_id'])) ? $new_standard_snack['product_id'] : $new_standard_snack['id'];
                    $new_snackbox->code = $new_standard_snack['code'];
                    $new_snackbox->name = $new_standard_snack['name'];
                    $new_snackbox->quantity = $new_standard_snack['quantity'];
                    $new_snackbox->unit_price = $new_standard_snack['unit_price'];
                    $new_snackbox->case_price = $new_standard_snack['case_price'];
                    $new_snackbox->save();

                } // end of foreach ($request['order'] as $new_standard_snack)

                //---------- End of Now we need to run through the new selection of snacks, adding them (if not specified as a dislike) for the company being processed ----------//

            } // end of if (count($snackbox))
        } // end of foreach ($snackboxes as $snackbox_id)
    }

    public function showTypes() {
        $types = SnackBox::select('type')->distinct()->get();
        //$types->toArray();
        //dd($types);
        return $types;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyItem($id, Request $request)
    {
        // NEW APPROACH
            //dd($id);  // I have a sneaky suspicion this may have worked fine.  The error was for displaying the change on the frontend.
            $this->removeProductFromBox($id);


        // PREVIOUS APPROACH

        // // We need some logic here to decide if the item to be deleted is the last item in the snackbox.
        // // Grab all the entries with the same snackbox_id.
        // $snackbox_total_items = SnackBox::where('snackbox_id', request('snackbox_id'))->get();
        //
        //
        // // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // // Use the id of the snackbox entry...
        // $snackbox_item = SnackBox::find(request('id'));
        //
        // // New addition, if the snackbox is wholesale we need to multiply the quantity by case size in order to get an accurate number of units to return to stock.
        // if (request('type') === 'wholesale') {
        //         // currently untested...
        //         $product_case_size = Product::where($snackbox_item->product_id)->pluck('case_size')->first();
        //         $case_to_unit_adjustment = ($product_case_size * $snackbox_item->quantity);
        //         Product::find($snackbox_item->product_id)->increment('stock_level', $case_to_unit_adjustment);
        // }
        // // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        // Product::find($snackbox_item->product_id)->increment('stock_level', $snackbox_item->quantity);
        //
        // // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        // if (count($snackbox_total_items) === 1) {
        //     // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
        //     // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
        //
        //
        //     SnackBox::where('id', $id)->update([
        //         'product_id' => 0,
        //         'code' => null,
        //         'name' => null,
        //         'quantity' => null,
        //         'unit_price' => null,
        //         'case_price' => null,
        //     ]);
        //
        // } else {
        //
        //     // We still have another entry with the necessary box info, so we can destroy this one.
        //     SnackBox::destroy($id);
        // }

    }

    public function destroyBox(Request $request)
    {
        $snackbox = SnackBox::where('snackbox_id', request('snackbox_id'))->get();
        // dd($snackbox);
        foreach ($snackbox as $snackbox_item) {
            SnackBox::destroy($snackbox_item->id);
        }
    }
}
