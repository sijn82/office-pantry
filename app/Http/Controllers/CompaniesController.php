<?php

namespace App\Http\Controllers;
// use File;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Company;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $companies = Company::all();
          return view ('companies', ['companies' => $companies]);
    }

    public function export($week_start = 300718)
    {
      // This determines what the exported file is called and which exporting controller is used.
      // I'd like to name the file after the selected route being exported, ignoring multiples but will need to work on this later.
      return \Excel::download(new Exports\CompaniesExport, 'companies' . $week_start . '.xlsx');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $date = 10818)
    {
        if (($handle = fopen(public_path() . '/company/company-import-full-details-' . $date . '-utf8-nobom.csv', 'r')) !== FALSE) {

          while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            // Clean the csv data of unwanted characters
            $company_data_encoded = json_encode($data);
            $company_data_fixed = str_replace('\u00a0', ' ', $company_data_encoded);
            $company_data = json_decode($company_data_fixed);
            $data = $company_data;

            // Convert the values in box_names to an array
            $box_names_array = explode(",", $data[3]);
            // Clean array of unwanted whitespace
            $box_names_array_cleaned = array_map('trim', $box_names_array);

            $companyData = new Company();
            $companyData->is_active = $data[0];
            $companyData->invoice_name = trim($data[1]);
            $companyData->route_name = trim($data[2]);
            $companyData->box_names = $box_names_array_cleaned;
            $companyData->primary_contact = $data[4];
            $companyData->primary_email = $data[5];
            $companyData->secondary_email = $data[6];
            $companyData->delivery_information = $data[7];
            $companyData->route_summary_address = $data[8];
            $companyData->address_line_1 = $data[9];
            $companyData->address_line_2 = $data[10];
            $companyData->city = $data[11];
            $companyData->region = $data[12];
            $companyData->postcode = $data[13];
            $companyData->branding_theme = $data[14];
            $companyData->supplier = $data[15];
            // $companyData->delivery_monday = $data[16];
            // $companyData->delivery_tuesday = $data[17];
            // $companyData->delivery_wednesday = $data[18];
            // $companyData->delivery_thursday = $data[19];
            // $companyData->delivery_friday = $data[20];
            // $companyData->assigned_to_monday = $data[21];
            // $companyData->assigned_to_tuesday = $data[22];
            // $companyData->assigned_to_wednesday = $data[23];
            // $companyData->assigned_to_thursday = $data[24];
            // $companyData->assigned_to_friday = $data[25];

            $companyData->save();

        }
        fclose ($handle);
      }
      return redirect('/');
    }

    /**
     * Update the specified resource in storage with route summary address and delivery information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRouteSummaryAddressAndDeliveryInfo(Request $request, $date = 300718)
    {
        //
        if (($handle = fopen(public_path() . '/company/route-summary-delivery-info-' . $date . '-noheaders-utf8-nobom.csv', 'r')) !== FALSE) {

          while (($data = fgetcsv ($handle, 1000, ',')) !== FALSE) {

            // Clean the csv data of unwanted characters
            $company_data_encoded = json_encode($data);
            $company_data_fixed = str_replace('\u00a0', ' ', $company_data_encoded);
            $company_data = json_decode($company_data_fixed);
            $data = $company_data;

            $foundCompanyEntry = Company::where('route_name', $data[0])->get();

            if(count($foundCompanyEntry) > 0) {

              Company::where('route_name', $foundCompanyEntry[0]->route_name)
              ->update([

                'postcode' => $data[1],
                'route_summary_address' => $data[2],
                'delivery_information' => $data[3],

              ]);

            } else {
              echo 'Well this was unexpected? Unable to find Route Name: ' . $data[0] . ' in Company table. I\'m not angry, just disappointed. <br>';
            }

          }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) // $id will be added to this later when we have the space for it in the companies table.
    {
        //
        // dd($request->company_data);
        
        // Set the array - maybe not even necessary.
        $box_names_array = [];
        
        // The amount of fruit boxes associated with a company can vary and we don't really want to have an array of empty strings
        // so if the $request->company_data['box_names']['box_blah'] is empty, strip the whole entry out.
        // There is also a current max od 4 boxes but this can be easily increased if needed.
        
        $box_names_array[] = $request->company_data['box_names']['box_one'] ?? 'n/a';
        $box_names_array[] = $request->company_data['box_names']['box_two'] ?? 'n/a';
        $box_names_array[] = $request->company_data['box_names']['box_three'] ?? 'n/a';
        $box_names_array[] = $request->company_data['box_names']['box_four'] ?? 'n/a';

        $box_names_array_cleaned = array_filter($box_names_array, function ($value) {
            return is_string($value) && 'n/a' !== trim($value);
        });
        
         // dd($box_names_array_cleaned);
        
        $companyData = new Company();
        $companyData->is_active = 'Active';
        $companyData->invoice_name = trim($request->company_data['invoice_name']);
        $companyData->route_name = trim($request->company_data['route_name']);
        $companyData->box_names = $box_names_array_cleaned;
        $companyData->primary_contact = $request->company_data['primary_contact'];
        $companyData->primary_email = $request->company_data['primary_email'];
        $companyData->secondary_email = $request->company_data['secondary_contact'];
        $companyData->delivery_information = $request->company_data['delivery_information'];
        $companyData->route_summary_address = $request->company_data['route_summary_address'];
        $companyData->address_line_1 = $request->company_data['address_line_1'];
        $companyData->address_line_2 = $request->company_data['address_line_2'];
        $companyData->city = $request->company_data['city'];
        $companyData->region = $request->company_data['region'];
        $companyData->postcode = $request->company_data['postcode'];
        $companyData->branding_theme = $request->company_data['branding_theme'];
        $companyData->supplier = $request->company_data['supplier_options'];
        $companyData->save();
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
