<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use App\CompanyDetails;
use Illuminate\Http\Request;

use Carbon\CarbonImmutable;

class CompanyDetailsController extends Controller
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

    public function search(Request $request)
    {
        $company_search = CompanyDetails::where('route_name', 'ILIKE', '%' . $request->keywords . '%')->get();

        return response()->json($company_search);
    }

    // This is going to be used to allow the admin to check the invoice name in CompanyDetails
    // and to grab the first entry that matches to autopopulate the associated fields on the add new company form.

    public function invoice_details_lookup(Request $request)
    {

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
        // Ok let's start getting valdiation in here to prevent submission errors and provide better feedback to the user.

        // Hang on I need to combine this with vue in order to translate the validation results to the user...
        // Need to research that first, will return to this.




        // dd($request);
        $new_company = new CompanyDetails();
        // $new_company->is_active = request('company_details.);
        // Company Name(s)
        $new_company->invoice_name = request('company_details.invoice_name');
        $new_company->route_name = request('company_details.route_name');
        // Contact Details
        $new_company->primary_contact_first_name = request('company_details.primary_contact_first_name');
        $new_company->primary_contact_surname = request('company_details.primary_contact_surname');
        $new_company->primary_contact_job_title = request('company_details.primary_contact_job_title');
        $new_company->primary_email = request('company_details.primary_contact_email');
        $new_company->primary_tel = request('company_details.primary_contact_telephone');
        $new_company->secondary_contact_first_name = request('company_details.secondary_contact_first_name');
        $new_company->secondary_contact_surname = request('company_details.secondary_contact_surname');
        $new_company->secondary_contact_job_title = request('company_details.secondary_contact_job_title');
        $new_company->secondary_email = request('company_details.secondary_contact_email');
        $new_company->secondary_tel = request('company_details.secondary_contact_telephone');
        $new_company->delivery_information = request('company_details.delivery_information');
        // Route Address
        $new_company->route_address_line_1 = request('company_details.route_address_line_1');
        $new_company->route_address_line_2 = request('company_details.route_address_line_2');
        $new_company->route_address_line_3 = request('company_details.route_address_line_3');
        $new_company->route_city = request('company_details.route_address_city');
        $new_company->route_region = request('company_details.route_address_region');
        $new_company->route_postcode = request('company_details.route_address_postcode');
        // Invoice Address
        $new_company->invoice_address_line_1 = request('company_details.invoice_address_line_1');
        $new_company->invoice_address_line_2 = request('company_details.invoice_address_line_2');
        $new_company->invoice_address_line_3 = request('company_details.invoice_address_line_3');
        $new_company->invoice_city = request('company_details.invoice_address_city');
        $new_company->invoice_region = request('company_details.invoice_address_region');
        $new_company->invoice_postcode = request('company_details.invoice_address_postcode');
        $new_company->invoice_email = request('company_details.invoice_email');
        // Billing and Delivery
        $new_company->branding_theme = request('company_details.branding_theme');
        $new_company->surcharge = request('company_details.surcharge');
        $new_company->supplier_id = request('company_details.supplier_id');
        $new_company->model = request('company_details.model');
        $new_company->monthly_surprise = request('company_details.monthly_surprise');
        $new_company->no_of_surprises = request('company_details.no_of_surprises');
        $new_company->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyDetails  $companyDetails
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyDetails $companyDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyDetails  $companyDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyDetails $companyDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyDetails  $companyDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $company_details_id)
    {
        $companyDetailsForUpdating = CompanyDetails::find($company_details_id);

        $companyDetailsForUpdating->update([
            'is_active' => request('company_details.is_active'),
            // Company Name(s)
            'invoice_name' => request('company_details.invoice_name'),
            'route_name' => request('company_details.route_name'),
            // Contact Details
            'primary_contact_first_name' => request('company_details.primary_contact_first_name'),
            'primary_contact_surname' => request('company_details.primary_contact_surname'),
            'primary_contact_job_title' => request('company_details.primary_contact_job_title'),
            'primary_email' => request('company_details.primary_email'),
            'primary_tel' => request('company_details.primary_tel'),
            'secondary_contact_first_name' => request('company_details.secondary_contact_first_name'),
            'secondary_contact_surname' => request('company_details.secondary_contact_surname'),
            'secondary_contact_job_title' => request('company_details.secondary_contact_job_title'),
            'secondary_email' => request('company_details.secondary_email'),
            'secondary_tel' => request('company_details.secondary_tel'),
            'delivery_information' => request('company_details.delivery_information'),
            // Route Address
            'route_address_line_1' => request('company_details.route_address_line_1'),
            'route_address_line_2' => request('company_details.route_address_line_2'),
            'route_address_line_3' => request('company_details.route_address_line_3'),
            'route_city' => request('company_details.route_city'),
            'route_region' => request('company_details.route_region'),
            'route_postcode' => request('company_details.route_postcode'),
            // Invoice Address
            'invoice_address_line_1' => request('company_details.invoice_address_line_1'),
            'invoice_address_line_2' => request('company_details.invoice_address_line_2'),
            'invoice_address_line_3' => request('company_details.invoice_address_line_3'),
            'invoice_city' => request('company_details.invoice_city'),
            'invoice_region' => request('company_details.invoice_region'),
            'invoice_postcode' => request('company_details.invoice_postcode'),
            // Billing and Delivery
            'branding_theme' => request('company_details.branding_theme'),
            'surcharge' => request('company_details.surcharge'),
            'supplier_id' => request('company_details.supplier_id'),
            'model' => request('company_details.model'),
            'monthly_surprise' => request('company_details.monthly_surprise'),
            'no_of_surprises' => request('company_details.no_of_surprises'),
        ]);

        //----- Now we need to configure the order change check -----//

        // We only want to check some fields for changes, as getChanges can't be filtered, we'll need to remove them afterwards.
        $fields_we_can_ignore = [
            'id',
            'is_active',
            'invoice_name',
            'primary_contact_first_name',
            'primary_contact_surname',
            'primary_contact_job_title',
            'primary_email',
            'primary_tel',
            'secondary_contact_first_name',
            'secondary_contact_surname',
            'secondary_contact_job_title',
            'secondary_email',
            'secondary_tel',
            'invoice_address_line_1',
            'invoice_address_line_2',
            'invoice_address_line_3',
            'invoice_city',
            'invoice_region',
            'invoice_postcode',
            'invoice_email',
            'monthly_surprise',
            'no_of_surprises',
            'branding_theme',
            'surcharge',
            'supplier_id',
            'model',
            'created_at',
            'updated_at',
            'order_changes',
            'date_changed'
        ];

        // Effectively this just leaves the remaining fields as

            // 'route_name',
            // 'delivery_information',
            // 'route_address_line_1',
            // 'route_address_line_2',
            // 'route_address_line_3',
            // 'route_city',
            // 'route_region',
            // 'route_postcode',

        // because these are the only fields which would affect the fruit partner making the delivery.

            // So first let's get all the changes.
            $order_changes = $companyDetailsForUpdating->getChanges();
            dump($order_changes);
            // Then loop through them all, removing the changes we don't need to track.
            if ($order_changes) {
                foreach ($order_changes as $key => $order_change) {
                    if (in_array($key, $fields_we_can_ignore)) {
                        unset($order_changes[$key]);
                    }
                }

            }
            dump($order_changes);

            // With them removed, are there any changes left which we do want to track?
            if ($order_changes) {
                // If so let's grab the current time.
                $carbon_now = CarbonImmutable::now('Europe/London');
                // And save this info to the box.
                $companyDetailsForUpdating->update([
                    'order_changes' => $order_changes,
                    'date_changed' => $carbon_now,
                ]);
            }

        //----- End of configure the order change check -----//
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyDetails  $companyDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyDetails $companyDetails)
    {
        //
    }
}
