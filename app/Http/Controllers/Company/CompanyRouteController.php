<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Exports;

use App\CompanyRoute;
use App\AssignedRoute;
use App\WeekStart;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RejiggedCompanyRoutes;
use Illuminate\Support\Facades\Storage;

class CompanyRouteController extends Controller
{
    // I don't think these really need to be declared here as protected.
    // protected $week_start;
    // protected $delivery_days;

    public function __construct()
    {
        $week_start = WeekStart::first();

        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
            // dd($this->delivery_days);
        }
        if (($this->week_start && $this->delivery_days) === null) {
            abort(400, 'Week Start and Delivery Days not set!');
        }
        elseif ($this->week_start === null) {
            abort(400, 'Week Start not set!');
        }
        elseif ($this->delivery_days === null) {
            abort(400, 'Delivery days not set!');
        }
    }

    // new system route download export function call.
    public function download_new_routes()
    {
        return \Excel::download(new Exports\RoutesExportNew($this->week_start), 'routelists' . $this->week_start . '.xlsx');
    }

    // new system route download export function call.
    public function download_new_routes_override()
    {
        return \Excel::download(new Exports\RoutesExportNewOverride($this->week_start), 'routelists-override' . $this->week_start . '.xlsx');
    }

    public function import(Request $request)
    {
        // I don't understand what Excel::toCollection is doing other than a great job! The first parameter is an empty class (new RejiggedCompanyRoutes(), $request->file an excel (xlsx) file of numerous tabs).
        // I appear to have the data in exactly the form I need, so wtf is the class for? I mean I could use it, (and it fails if I remove it) but I'm clearly missing something. Hmmn?

        $rejiggedRoutesByTab = Excel::toCollection(new RejiggedCompanyRoutes(), $request->file('rejigged-routes-file'));

        // First foreach loop will run through each tab of the excel file i.e each rejigged route (AssignedRoute).
        foreach ($rejiggedRoutesByTab as $rejiggedRoutesTab) {
            // Now we can loop through each entry in the route
            foreach ($rejiggedRoutesTab as $rejiggedRoute) {
                // And so long as the entry has a company route id, we should have an entry worth processing.
                // This eliminated the total row at the bottom, through probably not the header.
                // Which thinking about it, looks to have been skipped as well. Clever girl.
                // ... Yep, the empty class is ensuring we use headers as keys, so it IS doing something!
                if ($rejiggedRoute['company_route_id'] !== null) {
                    // The route export changes the assigned route id to it's name, which means we need to either reverse it here or provide it as an additional field to the routes.
                    // Which will be better for Vlad, I guess. To give him an additional field (to ignore), or hope he doesn't manually change the route names while re-routing like a man posessed?
                    
                    // Grab delivery route based on the name from 'assigned_to' column in the rejigged routes, so we can use the id from here on.
                    $assigned_route = AssignedRoute::where('name', $rejiggedRoute['assigned_to'])->get();
                    // Grab company route info from 'company_route_id' column in rejigged routes
                    $companyRoute = CompanyRoute::find($rejiggedRoute['company_route_id']);
                    // Using the route info we can grab the company details id
                    $companyDetailsId = $companyRoute->company_details_id;
                    
                    $companyDetails = CompanyDetails::find($companyDetailsId);

                    $allRoutesForCompany = CompanyRoute::where('company_details_id', $companyDetailsId)->get();

                    $companyRoute->postcode = $rejiggedRoute['postcode'];
                    $companyRoute->address = $rejiggedRoute['address'];
                    $companyRoute->delivery_information = $rejiggedRoute['delivery_information'];
                    $companyRoute->assigned_route_id = $assigned_route[0]->id;
                    $companyRoute->position_on_route = $rejiggedRoute['position_on_route'];

                    if ($companyRoute->isDirty('assigned_route_id') || $companyRoute->isDirty('position_on_route')) {

                    //    dump('Filthy route and position needs a good clean');

                        $companyRoute->update([
                            'assigned_route_id' => $assigned_route[0]->id,
                            'position_on_route' => $rejiggedRoute['position_on_route'],
                        ]);
                    }


                    // If postcode or route has been changed during the rejig, then let's update all their other routes with this information.
                    if ($companyRoute->isDirty('postcode') || $companyRoute->isDirty('address')) {

                    //    dump('Filthy address data needs a clean.');
                        
                        
                        // While we're at it let's update the CompanyDetails info as well. - Address will be a pain, so let's not worry about that yet.
                        $companyDetails->update([
                            'postcode' => $companyRoute->postcode,
                        ]);
                        
                        // and all the routes we have stored
                        foreach ($allRoutesForCompany as $route) {

                            $route->update([
                                'postcode' => $companyRoute->postcode,
                                'address' => $companyRoute->address,
                            ]);
                        }
                    }
                    // And do the same with delivery information.
                    if ($companyRoute->isDirty('delivery_information')) {

                    //    dump('Filthy delivery information needs a clean.');
                        
                        // Update CompanyDetails delivery info
                        $companyDetails->update([
                            'delivery_information' => $companyRoute->delivery_information,
                        ]);
                        // and all the routes we have stored.
                        foreach ($allRoutesForCompany as $route) {

                            $route->update([
                                'delivery_information' => $companyRoute->delivery_information,
                            ]);
                        }
                    }
                //    dump($companyRoute);
                } // if ($rejiggedRoute['company_route_id'] !== null)
            } // foreach ($rejiggedRoutesTab as $rejiggedRoute)
        } // foreach ($rejiggedRoutesByTab as $rejiggedRoutesTab)
        
        return redirect('exporting')->with('status', 'Routes Rejigged!');
    } // public function import(Request $request)

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyRoute  $companyRoutes
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyRoute $companyRoute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyRoute  $companyRoutes
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyRoute $companyRoute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyRoute  $companyRoutes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // dd($request);
        CompanyRoute::where('id', request('id'))->update([
            //'company_details_id' => request('company_details_id'),  <--- This will not be changing, or I can't think of a justifable reason why?
            'is_active' => request('is_active'),
            'fruit_crates' => request('fruit_crates'),
            'fruit_boxes' => request('fruit_boxes'),
            'route_name' => request('route_name'),
            'snacks' => request('snacks'),
            'drinks' => request('drinks'),
            'other' => request('other'),
            'delivery_day' => request('delivery_day'),
            'assigned_route_id' => request('assigned_route_id'),
            'position_on_route' => request('position_on_route'),
            'postcode' => request('postcode'),
            'address' => request('address'),
            'delivery_information' => request('delivery_information'),
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyRoutes  $companyRoutes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        CompanyRoute::destroy($id);
    }
}
