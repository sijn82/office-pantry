<?php

namespace App\Http\Controllers;

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



set_time_limit(0);
class SnackBoxController extends Controller
{
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
        $next_delivery_week_start = request('details.next_delivery_week');

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
                $new_snackbox->next_delivery_week = $next_delivery_week_start;
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
            $new_snackbox->next_delivery_week = $next_delivery_week_start;
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
                $newRoute->address = $companyDetails->route_address_line_1 . ', '
                                    . $companyDetails->route_address_line_2 . ', '
                                    . $companyDetails->route_address_line_3 . ', '
                                    . $companyDetails->route_city . ', '
                                    . $companyDetails->route_region . ', '
                                    . $companyDetails->route_postcode;
                
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

    // This function is just used to update the snackbox company/delivery info - everything but the contents basically.
    // If the delivery day is changed then a check is made to see if we have a route for them already (on that day), creating it for them if not.
    public function updateDetails(Request $request)
    {
        // dd(request('snackbox_details'));
        $snackbox = SnackBox::where('snackbox_id', request('snackbox_details.snackbox_id'))->get();

        foreach ($snackbox as $snackbox_entry ) {
            $snackbox_entry->update([
                'is_active' => request('snackbox_details.is_active'),
                'delivered_by' => request('snackbox_details.delivered_by'),
                'no_of_boxes' => request('snackbox_details.no_of_boxes'),
                'snack_cap' => request('snackbox_details.snack_cap'),
                'type' => request('snackbox_details.type'),
                'delivery_day' => request('snackbox_details.delivery_day'),
                'frequency' => request('snackbox_details.frequency'),
                'week_in_month' => request('snackbox_details.week_in_month'),
                'next_delivery_week' => request('snackbox_details.next_delivery_week'),
            ]);
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

    // This is just (?) used to add/remove contents from an existing box.  This may well be used to tailor a box after it's creation and before delivery, so automatiucally creating a archive
    // could cause more trouble than it's worth.

    public function addProductToSnackbox (Request $request)
    {
        // I need to add some sort of stock level amendments here too.  
        // If stock levels were adjusted in the box creation we need to 
        // return the stock from removed entries and subtract the stock from added ones.
        
        //dd(request('snackbox_details'));
        // dump();
        Product::find(request('product.id'))->decrement('stock_level', request('product.quantity'));
        
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
        $addProduct->next_delivery_week = request('snackbox_details.next_delivery_week');
        $addProduct->product_id = request('product.id');
        $addProduct->code = request('product.code');
        $addProduct->name = request('product.name');
        $addProduct->quantity = request('product.quantity');
        $addProduct->unit_price = request('product.unit_price');
        $addProduct->case_price = request('product.case_price');
        $addProduct->save();
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
                        
                        $snackbox_archive_entry = new SnackBoxArchive();
                        // Snackbox Info
                        $snackbox_archive_entry->is_active = 'Inactive';
                        $snackbox_archive_entry->snackbox_id = $snackbox_item->snackbox_id;
                        $snackbox_archive_entry->delivered_by = $snackbox_item->delivered_by;
                        $snackbox_archive_entry->no_of_boxes = $snackbox_item->no_of_boxes;
                        $snackbox_archive_entry->snack_cap = $snackbox_item->snack_cap;
                        $snackbox_archive_entry->type = $snackbox_item->type;
                        // Company Info
                        $snackbox_archive_entry->company_details_id = $snackbox_item->company_details_id;
                        $snackbox_archive_entry->delivery_day = $snackbox_item->delivery_day;
                        $snackbox_archive_entry->frequency = $snackbox_item->frequency;
                        $snackbox_archive_entry->week_in_month = $snackbox_item->week_in_month;
                        $snackbox_archive_entry->previous_delivery_week = $snackbox_item->previous_delivery_week;
                        $snackbox_archive_entry->next_delivery_week = $snackbox_item->next_delivery_week;
                        // Product Information
                        $snackbox_archive_entry->product_id = $snackbox_item->product_id;
                        $snackbox_archive_entry->code = $snackbox_item->code;
                        $snackbox_archive_entry->name = $snackbox_item->name;
                        $snackbox_archive_entry->quantity = $snackbox_item->quantity;
                        $snackbox_archive_entry->unit_price = $snackbox_item->unit_price;
                        $snackbox_archive_entry->case_price = $snackbox_item->case_price;
                        $snackbox_archive_entry->invoiced_at = $snackbox_item->invoiced_at;
                        $snackbox_archive_entry->save();
                    }
                    
                } else {
                    // 1.(b) if it doesn't, we need to save it to archives as 'active' so it can be pulled into the next invoicing run.
                    foreach ($snackbox as $snackbox_item) {
                        
                        $snackbox_archive_entry = new SnackBoxArchive();
                        // Snackbox Info
                        $snackbox_archive_entry->is_active = 'Active';
                        $snackbox_archive_entry->snackbox_id = $snackbox_item->snackbox_id;
                        $snackbox_archive_entry->delivered_by = $snackbox_item->delivered_by;
                        $snackbox_archive_entry->no_of_boxes = $snackbox_item->no_of_boxes;
                        $snackbox_archive_entry->snack_cap = $snackbox_item->snack_cap;
                        $snackbox_archive_entry->type = $snackbox_item->type;
                        // Company Info
                        $snackbox_archive_entry->company_details_id = $snackbox_item->company_details_id;
                        $snackbox_archive_entry->delivery_day = $snackbox_item->delivery_day;
                        $snackbox_archive_entry->frequency = $snackbox_item->frequency;
                        $snackbox_archive_entry->week_in_month = $snackbox_item->week_in_month;
                        $snackbox_archive_entry->previous_delivery_week = $snackbox_item->previous_delivery_week;
                        $snackbox_archive_entry->next_delivery_week = $snackbox_item->next_delivery_week;
                        // Product Information
                        $snackbox_archive_entry->product_id = $snackbox_item->product_id;
                        $snackbox_archive_entry->code = $snackbox_item->code;
                        $snackbox_archive_entry->name = $snackbox_item->name;
                        $snackbox_archive_entry->quantity = $snackbox_item->quantity;
                        $snackbox_archive_entry->unit_price = $snackbox_item->unit_price;
                        $snackbox_archive_entry->case_price = $snackbox_item->case_price;
                        $snackbox_archive_entry->invoiced_at = $snackbox_item->invoiced_at;
                        $snackbox_archive_entry->save();
                    }
                    
                }
                 
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
            $next_delivery_week_recovered = $snackbox[0]->next_delivery_week;
            
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
            $empty_snackbox->next_delivery_week = $next_delivery_week_recovered;
            // Product Information
            $empty_snackbox->product_id = 0;
            $empty_snackbox->code = null;
            $empty_snackbox->name = null;
            $empty_snackbox->quantity = null;
            $empty_snackbox->unit_price = null;
            $empty_snackbox->case_price = null;
            $empty_snackbox->invoiced_at = null;
            $empty_snackbox->save();
            
            //---------- End of - But we still need to recreate the empty box entry to repopulate with products later on. ----------//
                 
            } // if (count($snackbox) === 1) & elseif (count($snackbox)) > 1)
        } // foreach ($snackboxes as $snackbox)
         
         // If I take this approach, it would work fine for once a week processing but if we switch this to daily, then I'd need to either restrict mass updates in the same way
         // or write some logic to cater for this.
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
        
        // Maybe 'IS_ACTIVE' could be changed to include more options, such as 'PAUSED' - Which waits until the 'next_delivery_week' matches current delivery week (a new thing I could create) before changing its status to ACTIVE?
        // Hmmn, though as we process orders a week in advance, we'd need to set this to act a week in advance, which is annoying?  We also cant use the current 'WEEK START' variable as this is manually changed and wouldn't be a
        // reliable way to activate orders?

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
            
            
            $snackbox_id_recovered = $snackbox[0]->snackbox_id;
            $delivered_by_recovered = $snackbox[0]->delivered_by;
            $delivery_date_recovered = $snackbox[0]->delivery_day;
            $no_of_boxes_recovered = $snackbox[0]->no_of_boxes;
            $snack_cap_recovered = $snackbox[0]->snack_cap;
            $company_details_id_recovered = $snackbox[0]->company_details_id;
            $frequency_recovered = $snackbox[0]->frequency;
            $week_in_month_recovered = $snackbox[0]->week_in_month;
            $previous_delivery_week_recovered = $snackbox[0]->previous_delivery_week;
            $next_delivery_week_recovered = $snackbox[0]->next_delivery_week;
            
            // We can't updateOrCreate (per snack line) based on the uniqueness of snackbox_id/next_delivery_week in the archives due to having multiple rows (one for each item) in the snackbox.
            // However taking it up a level to here, we can check if the snackbox_id/next_delivery_week currently exists in the snackbox_archives. 
    
            $has_archive = SnackBoxArchive::where('snackbox_id', $snackbox_id_recovered)->where('next_delivery_week', $next_delivery_week_recovered)->get();

        
            if (count($snackbox)) { // Now I'm not checking all of the snackbox entries I don't need to worry about empty boxes. Though I suppose it's not doing any harm either.

                foreach ($snackbox as $snack) {

                    // By default we want to save the old box to archives so we can update the box on the front end with new products 
                    // but keep a record of what they've had in the past, either for our records or because we invoice them monthly etc...
                    
                    // updateOrCreate wont work here because there will be numerous entries with matching information
                    // Snackbox_id & Next_delivery_week
                    
                    // So we need to think of something else to determine whether to update or create.
                    // Also when trying to 'groupBy' - 'snackbox_id' from the 'snackbox_archives', it'll group all archived entries into one box
                    // so we need to add a second stipulation to 'groupBy - 'snackbox_id' & 'next_delivery_week'

                    // If it doesn't we can go ahead and save each row of the old box contents
                    if (!count($has_archive)) {
                        $archived_snackbox = new SnackBoxArchive();
                        // Snackbox Info
                        $archived_snackbox->snackbox_id = $snack->snackbox_id;
                        $archived_snackbox->is_active = $snack->is_active;
                        $archived_snackbox->delivered_by = $snack->delivered_by;
                        $archived_snackbox->no_of_boxes = $snack->no_of_boxes;
                        $archived_snackbox->snack_cap = $snack->snack_cap;
                        $archived_snackbox->type = $snack->type;
                        // Company Info
                        $archived_snackbox->company_details_id = $snack->company_details_id;
                        $archived_snackbox->delivery_day = $snack->delivery_day;
                        $archived_snackbox->frequency = $snack->frequency;
                        $archived_snackbox->previous_delivery_week = $snack->previous_delivery_week;
                        $archived_snackbox->next_delivery_week = $snack->next_delivery_week;
                        // Product Information
                        $archived_snackbox->product_id = $snack->product_id;
                        $archived_snackbox->code = $snack->code;
                        $archived_snackbox->name = $snack->name;
                        $archived_snackbox->quantity = $snack->quantity;
                        $archived_snackbox->unit_price = $snack->unit_price;
                        $archived_snackbox->invoiced_at = $snack->invoiced_at;
                        $archived_snackbox->save();
                    } else {
                        // But what should we do if it does?
                        // What are the possible reasons for this?
                        
                        // 1. Mass update has already been run for this 'type', backing up orders for that 'next_delivery_week' (i.e the previous week).
                        // 2. An update to a company snackbox (i.e not a mass update) <-- This isn't in place yet and I'm not sure it should be unless specifically requested via a button to back up contents before editing?
                        // 3. OR SHOULD I JUST CREATE A SPECIFIC TIME WHEN ALL SNACKBOX ORDERS ARE EMPTIED AND ARCHIVES CREATED?
                        // - IF BOXES ARE EMPTIED PRIOR TO MASS UPDATING, WE WOULDN'T NEED TO DO THAT HERE EITHER?
                    }
                    
                    
                    // Now we return to the existing code and delete the entry...
                    
                    // If the snackbox entry exists we can go ahead and delete it - as the snackbox contents may vary in quantity,
                    // I just want to strip them all out and replace with the new standard order.
                    Snackbox::where('id', $snack->id)->delete();
                    
                    // Kinda pointless but I'd like to change this ( Snackbox::where('id', $snack->id)->delete(); ) to
                    // --> SnackBox::destroy($snack->id);
                }
                
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
                    dump($new_standard_snack);
                    // I think this is a good place to put the likes and dislikes company check.
                    // $likes = Preference::where('id', $snack->company_details_id)->select('snackbox_likes')->get();
                    // $dislikes = Preference::where('id', $snack->company_details_id)->select('snackbox_dislikes')->get();


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
                        $new_standard_snack['product_id'] = $product_details[0]->product_id;
                        $new_standard_snack['code'] = $product_details[0]->code;
                        $new_standard_snack['name'] = $product_details[0]->name;
                        $new_standard_snack['unit_price'] = $product_details[0]->unit_price;

                    }

                    $new_snackbox = new SnackBox();
                    // Snackbox Info
                    $new_snackbox->snackbox_id = $snackbox_id_recovered;
                    $new_snackbox->delivered_by = $delivered_by_recovered;
                    $new_snackbox->no_of_boxes = $no_of_boxes_recovered;
                    $new_snackbox->snack_cap = $snack_cap_recovered;
                    $new_snackbox->type = $request['type'][0];
                    // Company Info
                    $new_snackbox->company_details_id = $company_details_id_recovered;
                    $new_snackbox->delivery_day = $delivery_date_recovered;
                    $new_snackbox->frequency = $frequency_recovered;
                    $new_snackbox->week_in_month = $week_in_month_recovered;
                    $new_snackbox->previous_delivery_week = $previous_delivery_week_recovered;
                    $new_snackbox->next_delivery_week = $next_delivery_week_recovered;
                    // Product Information
                    $new_snackbox->product_id = $new_standard_snack['product_id'];
                    $new_snackbox->code = $new_standard_snack['code'];
                    $new_snackbox->name = $new_standard_snack['name'];
                    $new_snackbox->quantity = $new_standard_snack['quantity'];
                    $new_snackbox->unit_price = $new_standard_snack['unit_price'];
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
        // We need some logic here to decide if the item to be deleted is the last item in the snackbox.
        // Grab all the entries with the same snackbox_id.
        $snackbox_total_items = SnackBox::where('snackbox_id', request('snackbox_id'))->get();
        
        
        // However we also need to return the quantity, as it's no longer being delivered, to maintain accurate stock levels.
        // Use the id of the snackbox entry...
        $snackbox_item = SnackBox::find(request('id'));
        
        // New addition, if the snackbox is wholesale we need to multiply the quantity by case size in order to get an accurate number of units to return to stock.
        if (request('type') === 'wholesale') {
                // currently untested...
                $product_case_size = Product::where($snackbox_item->product_id)->pluck('case_size')->first();
                $case_to_unit_adjustment = ($product_case_size * $snackbox_item->quantity);
                Product::find($snackbox_item->product_id)->increment('stock_level', $case_to_unit_adjustment);
        }
        // ...to grab the associated product_id and increment the stock level by the quantity; before we strip out or destroy the entry.
        Product::find($snackbox_item->product_id)->increment('stock_level', $snackbox_item->quantity);
        
        // If we've only retrieved 1 entry then this is the last vestige of box data and should be preserved.
        if (count($snackbox_total_items) === 1) {
            // To prevent an accidental extinction event, we don't want to destroy the entire entry, just strip out the product details and change the product_id to 0.
            // Having some update logic in the destroy function is probably breaking best practice rules, but I'm sure i'll be able to refactor it one day!
            
            
            SnackBox::where('id', $id)->update([
                'product_id' => 0,
                'code' => null,
                'name' => null,
                'quantity' => null,
                'unit_price' => null,
                'case_price' => null,
            ]);
            
        } else {
            
            // We still have another entry with the necessary box info, so we can destroy this one.
            SnackBox::destroy($id);
        }
        
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
