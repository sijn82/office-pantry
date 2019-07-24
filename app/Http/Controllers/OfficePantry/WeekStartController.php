<?php

// Updated namespace, after moving controllers into their own (grouped) folders.
namespace App\Http\Controllers\OfficePantry;
use App\Http\Controllers\Controller;

use App\WeekStart;
use Illuminate\Http\Request;

class WeekStartController extends Controller
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
    public function storeWeekStart(WeekStart $week_start, Request $request)
    {
        // dd($request->week_start);
        //
        $week_start =  WeekStart::first();
        
        if ($week_start !== null) {
            $week_start->current = $request->week_start;
            $week_start->save();
        } else {
            $week_start = new WeekStart();
            $week_start->current = $request->week_start;
            $week_start->save();
        }
    }
    public function storeDeliveryDays(WeekStart $week_start, Request $request)
    {
        // dd($request->week_start);
        //
        $week_start =  WeekStart::first();
        
        if ($week_start !== null) {
            $week_start->delivery_days = $request->delivery_days;
            $week_start->save();
        } else {
            $week_start = new WeekStart();
            $week_start->delivery_days = $request->delivery_days;
            $week_start->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WeekStart  $weekStart
     * @return \Illuminate\Http\Response
     */
    public function show(WeekStart $weekStart)
    {
        //
        $week_start = WeekStart::get();
        return view('import-file', ['week_start' => $week_start]);
    }
    
    public function showAndSet()
    {
        $week_start = WeekStart::findOrFail(1);
        return $week_start;
        // return ['week_start' => $week_start->current, 'delivery_days' => $week_start->delivery_days];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WeekStart  $weekStart
     * @return \Illuminate\Http\Response
     */
    public function edit(WeekStart $weekStart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WeekStart  $weekStart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WeekStart $weekStart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WeekStart  $weekStart
     * @return \Illuminate\Http\Response
     */
    public function destroy(WeekStart $weekStart)
    {
        //
    }
}
