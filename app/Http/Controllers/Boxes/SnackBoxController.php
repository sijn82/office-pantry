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
use App\WeekStart;
use App\Product;
use App\Preference;
use App\CompanyDetails;
use App\CompanyRoute;
use App\AssignedRoute;
use App\Allergy;
use App\OrderItem;

use App\Traits\Orders;
use App\Traits\Routes;

// Can be deleted when fully removed from the controller
use App\SnackBoxArchive;



set_time_limit(0);
class SnackBoxController extends Controller
{

    use Orders;
    use Routes;

    protected $week_start;

    public function __construct()
    {
        $week_start = WeekStart::first();

        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }

    }

    // Edit 16-03-2020: Now that several things have changed and all (or certainly most) existing functions need tweaking again,
    // I'm going to repopulate the top of this controller with the new and improved functions before removing the outdated functions as they're replaced.

    public function store()
    {
        foreach (request('delivery_day') as $delivery_day) {

            // Boxes are simpler now - we just need some basic info to associate with order items later on.

            $new_snackbox = new SnackBox();
            // Active is the default of is_active so we can just leave it to do its thing.  
            // However I don't see the default value listed in pgadmin?  Time to test.
            $new_snackbox->company_details_id = request('company_details_id');
            $new_snackbox->name = request('name');
            $new_snackbox->type = request('type');
            $new_snackbox->delivered_by = request('delivered_by');
            $new_snackbox->no_of_boxes = request('no_of_boxes');
            $new_snackbox->snack_cap = request('snack_cap');
            $new_snackbox->delivery_day = $delivery_day;
            $new_snackbox->frequency = request('frequency');
            $new_snackbox->week_in_month = request('week_in_month');
            $new_snackbox->delivery_week = request('delivery_week');
            $new_snackbox->save();

            // Though we still need to make sure there's a route to deliver the box on.

            if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', $delivery_day)->get()) == 0) {

                // If we're here, we need to create a new route
                // (Interesting fact :) ) - All we're using from request() is the company_details_id
                $this->createNewRoute(request(), $delivery_day);

            } else {
                // We already have a route this delivery can go on - so we dont need to do anything else.
            }
        }
    }

    public function updateBoxDetails(Request $request)
    {
        //dd(request());
        $snackbox = SnackBox::find(request('id'));
        $snackbox->is_active = request('is_active');
        $snackbox->name = request('name');
        $snackbox->type = request('type');
        $snackbox->delivered_by = request('delivered_by');
        $snackbox->no_of_boxes = request('no_of_boxes');
        $snackbox->snack_cap = request('snack_cap');
        $snackbox->delivery_day = request('delivery_day');
        $snackbox->frequency = request('frequency');
        $snackbox->week_in_month = request('week_in_month');
        $snackbox->delivery_week = request('delivery_week');
        $snackbox->invoiced_at = request('invoiced_at'); // Do we want to allow this to be manually updated or restricted to the invoicing function only?
        $snackbox->save();

        // Ah, we also need to check if there's still a route for the updated details (in case we just chnged the delivery day).
        if (count(CompanyRoute::where('company_details_id', request('company_details_id'))->where('delivery_day', request('delivery_day'))->get()) == 0) {

            // If we're here, we need to create a new route
            $this->createNewRoute(request());

        } else {
            // We already have a route this delivery can go on - so we dont need to do anything else.
        }

    }

    // While the function addProductToBox has been moved to a trait function, I (think) I still need to call it via a route/controller function.
    public function addProductToSnackBoxV2(Request $request)
    {
        // Using the function from App\Trait\Orders;
        $this->addProductToBox($request);
    }

    public function increaseSnackboxOrderItemQuantity(Request $request)
    {
        $this->increaseOrderItemQuantity($request);
    }
 
    public function removeProduct($id, Request $request)
    {
        $this->removeProductFromBox($id);
    }   

    public function destroyBox(Request $request)
    {
        SnackBox::destroy(request('id'));
    }

    public function showTypes()
    {
        $types = SnackBox::select('type')->distinct()->get();
        return $types;
    }

     public function massUpdateTypeV3()
     {
        // Grab all boxes which are active, of the given type we're mass updating and due to be delivered on the current processing week.
         $snackboxes = SnackBox::where('is_active', 'Active')->where('type', request('type'))->where('delivery_week', $this->week_start)->get();

         foreach ($snackboxes as $snackbox) {

            // Foreach snackbox we need to create OrderItems for each item in the request('order');
            // 'product_id',
            // 'quantity',
            // 'box_id',
            // 'box_type',
            // 'delivery_week'

             dd(request('order'));
             dd(request('type'));
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

        foreach ($snackbox->box_items->where('delivery_week', $snackbox->delivery_week) as $order_item) {
            dump($order_item->product->brand . ' ' . $order_item->product->flavour);
        }
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


}
