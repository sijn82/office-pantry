<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

use App\AssignedRoute;
use Illuminate\Http\Request;

class AssignedRouteController extends Controller
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
        $newAssignedRoute = new AssignedRoute();
        $newAssignedRoute->name = request('assigned_route.name');
        $newAssignedRoute->delivery_day = request('assigned_route.delivery_day');
        $newAssignedRoute->tab_order = request('assigned_route.tab_order');
        $newAssignedRoute->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AssignedRoute  $assignedRoute
     * @return \Illuminate\Http\Response
     */
    public function listAssignedRoutes()
    {
        $assigned_routes = AssignedRoute::all();
        
        return $assigned_routes;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AssignedRoute  $assignedRoute
     * @return \Illuminate\Http\Response
     */
    public function edit(AssignedRoute $assignedRoute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AssignedRoute  $assignedRoute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        //dd($request);
        AssignedRoute::where('id', request('assigned_route.id'))->update([
            'name' => request('assigned_route.name'),
            'delivery_day' => request('assigned_route.delivery_day'),
            'tab_order' => request('assigned_route.tab_order')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AssignedRoute  $assignedRoute
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //dd($id);
        
        AssignedRoute::destroy($id);
    }
}
