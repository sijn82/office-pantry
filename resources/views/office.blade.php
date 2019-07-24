@extends('layouts.auth')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        Hi, @if (!empty(Auth::user()->name))
                                {{ Auth::user()->name }}
                            @endif
                    </div>

                    <div class="card-body">
                        <h3> Processing Tasks </h3>
                        <h4> Old System </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href=" {{ route('import.file') }}"> Import and Process Files </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('import.products') }}"> Import and Process Snackboxes </a>
                            </li>
                        </ul>

                        <h4> New System </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href="{{ route('company.new') }}"> Add New Company </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('snackboxes.massupdate') }}"> Mass Update Snackbox (By Type) </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('exporting') }}"> Exporting Processes </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('invoicing') }}"> Process/Export Invoicing </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('cron') }}"> View/Edit Cron Tasks </a>
                            </li>
                        </ul>

                        <!-- Routes are blocked from access if (admin is) already logged in,
                            defeating the purpose of these links until I review authentication again
                            and spend some dedicated time on it - add it to the list!! -->

                        <!-- <h4> Create New Users </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href=" {{ route('register') }}"> Add New Customer Admin </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('register.office') }}"> Add New Office Admin </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('register.warehouse') }}"> Add New Warehouse Admin </a>
                            </li>
                        </ul> -->

                        <h4> View / Add New System Ingredients </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href=" {{ route('products') }}"> View / Add New Products </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('office-pantry-products') }}"> View / Edit Office Pantry Products </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('fruit-partners') }}"> View / Add New Fruit Partners </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('assigned-routes') }}"> View / Add New Assigned Routes </a>
                            </li>
                        </ul>
                    </div>
                    <!-- <import-test-file></import-test-file> -->
                    <div class="card-body">
                        <div>
                            <h3> Review / Update Company Data </h3>
                            <p> Use the company search below to update existing or create new; company data and orders. </p>
                            <p> The search is currently case sensitive, so if you don't find what you're looking for, add capitals. </p>
                            <p> This is because of the switch to postgresql (database) and I'll be fixing this annoying quirk in phase 2. </p>
                            <search-companies></search-companies>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('office-admin-styling')
    <style>


    </style>
    @endsection
