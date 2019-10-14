<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use App\CompanyDetails;
use Illuminate\Http\Request;

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
        $company_search = CompanyDetails::where('route_name', 'LIKE', '%' . $request->keywords . '%')->get();

        return response()->json($company_search);
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
        // dd($request);
        $new_company = new CompanyDetails();
        // $new_company->is_active = request('company_details.);
        // Company Name(s)
        $new_company->invoice_name = request('company_details.invoice_name');
        $new_company->route_name = request('company_details.route_name');
        // Contact Details
        $new_company->primary_contact_first_name = request('company_details.primary_contact_name');
        $new_company->primary_contact_surname = request('company_details.primary_surname');
        $new_company->primary_contact_job_title = request('company_details.primary_contact_job_title');
        $new_company->primary_email = request('company_details.primary_contact_email');
        $new_company->primary_tel = request('company_details.primary_contact_telephone');
        $new_company->secondary_contact_first_name = request('company_details.secondary_contact_name');
        $new_company->secondary_contact_surname = request('company_details.secondary_surname');
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
        CompanyDetails::where('id', $company_details_id)->update([
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
