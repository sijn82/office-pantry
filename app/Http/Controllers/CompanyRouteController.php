<?php

namespace App\Http\Controllers;

use App\CompanyRoute;
use App\WeekStart;
use Illuminate\Http\Request;

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
