<?php

namespace App\Traits;

use App\CompanyDetails;
use App\AssignedRoute;
use App\CompanyRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Routes
{
    //
    public function createNewRoute(Request $request, $delivery_day = null)
    {
        $companyDetails = CompanyDetails::findOrFail(request('company_details_id'));

        // Edit 16-03-2020: Just a thought.
        // As the only thing that changes between routes is the delivery day, I could probably use it to determine the query and shorten this to 1 db call?
        // I.e $assigned_route = AssignedRoute::where('name', 'TBC (' . $delivery_day . ')')->get();

        $assigned_route_tbc_monday = AssignedRoute::where('name', 'TBC (Monday)')->get();
        $assigned_route_tbc_tuesday = AssignedRoute::where('name', 'TBC (Tuesday)')->get();
        $assigned_route_tbc_wednesday = AssignedRoute::where('name', 'TBC (Wednesday)')->get();
        $assigned_route_tbc_thursday = AssignedRoute::where('name', 'TBC (Thursday)')->get();
        $assigned_route_tbc_friday = AssignedRoute::where('name', 'TBC (Friday)')->get();

        switch (is_string(request('delivery_day')) ? request('delivery_day') : $delivery_day) {
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
        $newRoute->company_details_id = request('company_details_id');
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
        $newRoute->delivery_day = is_string(request('delivery_day')) ? request('delivery_day') : $delivery_day;
        $newRoute->save();


        $message = 'Route ' . $newRoute->route_name . ' on ' . $newRoute->delivery_day . ' saved.';
        //dump($message);
        Log::channel('slack')->info($message);
    }
}
