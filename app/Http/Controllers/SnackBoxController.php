<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Session;

use Illuminate\Http\Request;
use App\SnackBox;
use App\WeekStart;
use App\Product;
use App\Preference;
// use App\Company;
use Appz\CompanyDetails;
use App\CompanyRoute;



set_time_limit(0);
class SnackBoxController extends Controller
{
        protected $week_start;

        public function __construct()
        {
            // $this->week_start = 170918;
            $week_start = WeekStart::all()->toArray();
            $this->week_start = $week_start[0]['current'];
            $this->delivery_days = $week_start[0]['delivery_days'];

        }
        // There are a couple of options here, use the same function with a switch statement value based on the button pressed, or as I'm going to do for now, create several functions
        // one to handle each scenario.

        // Single Company, Multiple Boxes
        public function download_snackbox_op_singlecompany()
        {
            session()->put('snackbox_courier', 'OP');

            return \Excel::download(new Exports\SnackboxSingleCompanyExportNew, 'snackboxesOPSingleCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_singlecompany()
        {
            session()->put('snackbox_courier', 'DPD');

            return \Excel::download(new Exports\SnackboxSingleCompanyExport, 'snackboxesDPDSingleCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_singlecompany()
        {
            session()->put('snackbox_courier', 'APC');

            return \Excel::download(new Exports\SnackboxSingleCompanyExport, 'snackboxesAPCSingleCompany' . $this->week_start . '.xlsx');
        }

        // Single Box, Multiple Companies
        public function download_snackbox_op_multicompany()
        {
            session()->put('snackbox_courier', 'OP');

            return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesOPMultiCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_multicompany()
        {
            session()->put('snackbox_courier', 'DPD');

            return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesDPDMultiCompany' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_multicompany()
        {
            session()->put('snackbox_courier', 'APC');

            return \Excel::download(new Exports\SnackboxMultiCompanyExportNew, 'snackboxesAPCMultiCompany' . $this->week_start . '.xlsx');
        }

        // Unique Box, Multiple Companies
        public function download_snackbox_op_unique()
        {
            session()->put('snackbox_courier', 'OP');

            return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesOPUnique' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_dpd_unique()
        {
            session()->put('snackbox_courier', 'DPD');

            return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesDPDUnique' . $this->week_start . '.xlsx');
        }
        public function download_snackbox_apc_unique()
        {
            session()->put('snackbox_courier', 'APC');

            return \Excel::download(new Exports\SnackboxUniqueExportNew, 'snackboxesAPCUnique' . $this->week_start . '.xlsx');
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
        $snackbox_id = $request->company_id . "-" . uniqid();
        $courier = $request->details[0];
        $box_number = $request->details[1];
        $type = $request->details[2];
        $delivery_day = $request->details[3];
        $frequency = $request->details[4];
        $week_in_month = $request->details[5];
        $next_delivery_week_start = $request->details[6];

        if (!empty($request->order)) {

            // if we have at least one entry in here then we need to
            // loop through and save (create) an entry for that snackbox product
            // and attached it to the snackbox id.

            foreach ( $request->order as $item ) {

                $new_snackbox = new SnackBox();
                $new_snackbox->snackbox_id = $snackbox_id;
                $new_snackbox->delivered_by = $courier;
                $new_snackbox->no_of_boxes = $box_number;
                $new_snackbox->type = $type;
                $new_snackbox->company_id = $request->company_id;
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
                $new_snackbox->save();

            }
        } else {

            // it's also very likely we know a company wants a standard snackbox for next week
            // but there's no point attaching products to the snackbox id as they'll only be replaced before receiving their first order anyway.
            // It's basically a faff saver.

            $new_snackbox = new SnackBox();
            $new_snackbox->snackbox_id = $snackbox_id;
            $new_snackbox->delivered_by = $courier;
            $new_snackbox->no_of_boxes = $box_number;
            $new_snackbox->type = $type;
            $new_snackbox->company_id = $request->company_id;
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
            if (!count(CompanyRoute::where('company_id', $request['company_id'])->where('delivery_day', $delivery_day)->get())) {

                $companyDetails = Company::findOrFail($request['company_id']);

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_id = $request['company_id'];
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->postcode;
                $newRoute->address = $companyDetails->route_summary_address;
                $newRoute->delivery_information = $companyDetails->delivery_information;
                $newRoute->delivery_day = $delivery_day;
                $newRoute->save();


                $message = "Route $newRoute->route_name on " . $delivery_day . " saved.";
                Log::channel('slack')->info($message);
            }

        } else {

            $message = "Route $newRoute->route_name on " . $delivery_day . " not necessary, delivered by " . $courier;
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
        //dd(request('snackbox_item_id'));
        SnackBox::where('id', request('snackbox_item_id'))->update([
            'quantity' => request('snackbox_item_quantity'),
        ]);
    }

    public function updateDetails(Request $request)
    {
        // dd(request('snackbox_details'));
        $snackbox = SnackBox::where('snackbox_id', request('snackbox_details.snackbox_id'))->get();

        foreach ($snackbox as $snackbox_entry ) {
            $snackbox_entry->update([
                'is_active' => request('snackbox_details.is_active'),
                'delivered_by' => request('snackbox_details.delivered_by'),
                'no_of_boxes' => request('snackbox_details.no_of_boxes'),
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
            if (!count(CompanyRoute::where('company_id', request('snackbox_details.company_id'))->where('delivery_day', request('snackbox_details.delivery_day'))->get())) {

                // This is currently pulling info from the Company table, although I want to create a CompanyData table to replace this.
                $companyDetails = Company::findOrFail(request('snackbox_details.company_id'));

                // We need to create a new entry.
                $newRoute = new CompanyRoute();
                // $newRoute->is_active = 'Active'; // Currently hard coded but this is also the default.
                $newRoute->company_id = request('snackbox_details.company_id');
                $newRoute->route_name = $companyDetails->route_name;
                $newRoute->postcode = $companyDetails->postcode;
                $newRoute->address = $companyDetails->route_summary_address;
                $newRoute->delivery_information = $companyDetails->delivery_information;
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

    public function addProductToSnackbox (Request $request)
    {
        //dd(request('snackbox_details'));
        $addProduct = new SnackBox();
        $addProduct->snackbox_id = request('snackbox_details.snackbox_id');
        $addProduct->is_active = request('snackbox_details.is_active');
        $addProduct->delivered_by = request('snackbox_details.delivered_by');
        $addProduct->no_of_boxes = request('snackbox_details.no_of_boxes');
        $addProduct->type = request('snackbox_details.type');
        $addProduct->company_id = request('snackbox_details.company_id');
        $addProduct->delivery_day = request('snackbox_details.delivery_day');
        $addProduct->frequency = request('snackbox_details.frequency');
        $addProduct->week_in_month = request('snackbox_details.week_in_month');
        $addProduct->previous_delivery_week = request('snackbox_details.previous_delivery_week');
        $addProduct->next_delivery_week = request('snackbox_details.next_delivery_week');
        $addProduct->code = request('product.code');
        $addProduct->name = request('product.name');
        $addProduct->quantity = request('product.quantity');
        $addProduct->unit_price = request('product.unit_price');
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

    // ----- This is the beginning of new system snackbox functions ----- //

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massUpdateType(Request $request)
    {
         // dd($request['type'][0]);
        // If this is an upload of the new weekly standard box, we won't have a company to attach
        // - instead any snackbox with 'standard' as type and unique delivery day/company id needs to pulled through,
        // stripped of all listed entries and replaced with the new order.

        // Grab all distinct snackbox_id's, this should (will) grab all unique snackbox Id's for step 2.
        $snackboxes = SnackBox::select('snackbox_id')->distinct()->get();
        // dd($snackboxes);
        foreach ($snackboxes as $snackbox_id) {
            // dd($snackbox_id['snackbox_id']);
            // In step 2 we want run through all id's, checking that the type is for a standard snackbox.
            $snackbox = SnackBox::where('snackbox_id', $snackbox_id['snackbox_id'])->where('type', $request['type'][0])->get();
             // dd($snackbox);
            if (count($snackbox)) {

                foreach ($snackbox as $snack) {
                    // dd($snack);
                    // Grab these before deleting old entry, kinda important
                    $delivered_by_recovered = $snack->delivered_by;
                    $delivery_date_recovered = $snack->delivery_day;
                    $no_of_boxes_recovered = $snack->no_of_boxes;
                    $company_id_recovered = $snack->company_id;
                    $frequency_recovered = $snack->frequency;
                    $week_in_month_recovered = $snack->week_in_month;
                    $previous_delivery_week_recovered = $snack->previous_delivery_week;
                    $next_delivery_week_recovered = $snack->next_delivery_week;

                    // If the snackbox entry exists we can go ahead and delete it - as the snackbox contents may vary in quantity,
                    // I just want to strip them all out and replace with the new standard order.

                    Snackbox::where('id', $snack->id)->delete();
                }

                // Moved this further up to keep it out of a second (unnecessary for this query) foreach.
                $likes = Preference::where('company_id', $snack->company_id)->where('snackbox_likes', '!=', null)->pluck('snackbox_likes')->toArray();
                $dislikes = Preference::where('company_id', $snack->company_id)->where('snackbox_dislikes', '!=', null)->pluck('snackbox_dislikes')->toArray();

                // There's no point including any 'liked' items in this array if we don't have any of them in stock,
                // so let's check the name in Products and see what the stock level looks like.
                foreach ($likes as $like) {
                    // This will only return a countable $option if the item is in stock.
                    $option = Product::where('name', $like)->where('stock_level', '>', 0)->get();
                    // If $option count returns nothing, it's not in stock and can be removed from selectable products this time around.
                    if (!count($option)) {
                        // Search for the product in array of $likes and grab its positin (key) in array.
                        $like_key = array_search($like, $likes);
                        // Now use this key to unset (remove) the product from usable list of likes.
                        unset($likes[$like_key]);
                    }
                }
                // Likes now only contain products in stock.
                // dd($likes);


                // Should I be deleting and creating in the same function?
                // I'm not sure but for test purposes and problem solving clarity, I'm gonna start off this way.

                // In fact due to the reuse of data, I kinda need to keep it together, so let's hope this will work without complications.
                foreach ($request['order'] as $new_standard_snack) {
                    // dd($new_standard_snack);
                    // I think this is a good place to put the likes and dislikes company check.
                    // $likes = Preference::where('id', $snack->company_id)->select('snackbox_likes')->get();
                    // $dislikes = Preference::where('id', $snack->company_id)->select('snackbox_dislikes')->get();


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
                        // dd($old_product);
                        // This will need further work but so far, we find out the value of product quantity to be replaced
                        // i.e value of product to be replaced (£1.50) multiplied by quantity in standard snackbox for this week (3), totals £4.50 of stock needing to be substituted.
                        $old_standard_snack_value = ($new_standard_snack['quantity'] * $old_product[0]->unit_price);
                        // Now we have a total to be divided by the new product unit price
                        // I'm using ceil to ensure we get a whole number that keeps the product/quantity value at a minimum of what it was before.
                        // I still need to elaborate on this further to limit the quantity at 3 and prevent multiple low value items as a replacement,
                        // however this will come later, let's get it working like this first!

                        $new_quantity = ( $old_standard_snack_value / $product_details[0]->unit_price );
                        // dd($new_quantity);
                        $new_standard_snack['quantity'] = ceil($new_quantity);

                        $new_standard_snack['code'] = $product_details[0]->code;
                        $new_standard_snack['name'] = $product_details[0]->name;
                        $new_standard_snack['unit_price'] = $product_details[0]->unit_price;

                    }

                    $new_snackbox = new SnackBox();
                    // Snackbox Info
                    $new_snackbox->snackbox_id = $snackbox_id['snackbox_id'];
                    $new_snackbox->delivered_by = $delivered_by_recovered;
                    $new_snackbox->no_of_boxes = $no_of_boxes_recovered;
                    $new_snackbox->type = $request['type'][0];
                    // Company Info
                    $new_snackbox->company_id = $company_id_recovered;
                    $new_snackbox->delivery_day = $delivery_date_recovered;
                    $new_snackbox->frequency = $frequency_recovered;
                    $new_snackbox->week_in_month = $week_in_month_recovered;
                    $new_snackbox->previous_delivery_week = $previous_delivery_week_recovered;
                    $new_snackbox->next_delivery_week = $next_delivery_week_recovered;
                    // Product Information
                    $new_snackbox->code = $new_standard_snack['code'];
                    $new_snackbox->name = $new_standard_snack['name'];
                    $new_snackbox->quantity = $new_standard_snack['quantity'];
                    $new_snackbox->unit_price = $new_standard_snack['unit_price'];
                    $new_snackbox->save();

                } // end of foreach ($request['order'] as $new_standard_snack)
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
    public function destroy($id)
    {
        //
        SnackBox::destroy($id);
    }
}
