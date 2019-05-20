@extends('layouts.auth')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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
                                <a href=" {{ route('import-file') }}"> Import and Process Files </a>
                            </li>
                            <li style="list-style:none;">
                                <a href=" {{ route('import-products') }}"> Import and Process Snackboxes </a>
                            </li>
                        </ul>
                        <h4> New System </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href="{{ route('new-company') }}"> Add New Company </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('snackboxes') }}"> Add New Snackbox, Mass Update Standard (Types) </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('preferences') }}"> Add/Edit/View Company Preferences (Allergies, Likes, Dislikes & Additional Info) </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('exporting') }}"> Exporting Processes </a>
                            </li>
                            <li style="list-style:none;">
                                <a href="{{ route('invoicing') }}"> Process/Export Invoicing </a>
                            </li>
                        </ul>
                        <h4> View / Add New System Ingredients </h4>
                        <ul>
                            <li style="list-style:none;">
                                <a href=" {{ route('products') }}"> View / Add New Products </a>
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
                            <search-companies></search-companies>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
