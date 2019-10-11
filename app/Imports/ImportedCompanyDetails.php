<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithEvents; // Not sure we need any events for imports.

use App\CompanyDetails;

class ImportedCompanyDetails implements
toModel,
// WithEvents,
WithHeadingRow
{

    public function model(array $row)
    {
        //dd($row); // I think to access the column names, I'll need to use - 'invoice_name' => $row['Invoice Name'] (for example).

        return new CompanyDetails([

            'is_active' => 'Active',
            // Company Name(s)
            'invoice_name'=> $row['invoice_name'],
            'route_name' => $row['route_name'],
            // Contact Details
            'primary_contact_first_name' => $row['primary_contact_first_name'],
            'primary_contact_surname' => $row['primary_contact_surname'],
            'primary_contact_job_title' => $row['primary_contact_job_title'],
            'primary_email' => $row['primary_email'],
            //'primary_tel' => $row['primary_tel'], missing from sheet
            // 'secondary_contact_first_name' => $row['secondary_contact_first_name'],
            // 'secondary_contact_surname' => $row['secondary_contact_surname'],
            // 'secondary_contact_job_title' => $row['secondary_contact_job_title'],
            // 'secondary_email' => $row['secondary_email'],
            // 'secondary_tel' => $row['secondary_tel'],
            'delivery_information' => $row['delivery_information'],
            // Route Address
            'route_address_line_1' => $row['route_address_line_1'],
            'route_address_line_2' => $row['route_address_line_2'],
            'route_address_line_3' => $row['route_address_line_3'],
            'route_city' => $row['route_city'],
            'route_region' => $row['route_region'],
            'route_postcode' => $row['route_postcode'],
            // Invoice Address
            'invoice_address_line_1' => $row['invoice_address_line_1'],
            'invoice_address_line_2' => $row['invoice_address_line_2'],
            'invoice_address_line_3' => $row['invoice_address_line_3'],
            'invoice_city' => $row['invoice_city'],
            'invoice_region' => $row['invoice_region'],
            'invoice_postcode' => $row['invoice_postcode'],
            'invoice_email' => $row['invoice_email'],
            // Billing and Delivery
            'branding_theme' => $row['branding_theme'],
            'surcharge' => $row['surcharge'],
            'supplier_id' => $row['supplier_id'],
            'model' => $row['model'],
            'monthly_surprise' => $row['monthly_surprise'],
            'no_of_surprises' => $row['no_of_staff'],
        ]);
    }

}
