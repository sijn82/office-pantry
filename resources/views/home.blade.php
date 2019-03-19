
<!-- // resources/views/home.blade.php -->

@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                     Hi there, {{ Auth::user()->name }}
                </div>
                @php
                    // dd(Auth::user());
                    // dd($fruitboxes);
                    // dd(json_encode($fruitboxes));
                    // dd($routes);
                    // dd($companies);
                @endphp
                <!-- <div id="app"> -->
                    <company-data :userid="{{ Auth::user()->id }}" :fruitboxes="{{ json_encode($fruitboxes) }}" :companies="{{ json_encode($companies) }}"></company-data>
                    <add-new-fruitbox :user_associated_companies="{{ json_encode($user_associated_companies) }}"></add-new-fruitbox>
                    <fruit-orders :fruitboxes="{{ json_encode($fruitboxes) }}"></fruit-orders>
                <!-- </div> -->

                <div id="company-data">
                    <div>
                        @if ($companies->count())
                            @php  // dd($companies); @endphp

                            @foreach ($companies as $company)
                            <ul>
                                <h3> Company Data for {{ $company->invoice_name }}</h3>
                                <li><b> ID: </b> {{ $company->id }} {{-- Probably not something the user cares about --}}</li>
                                <li><b> Status: </b> {{ $company->is_active }} {{-- This is also something we may not want to make visible as it might suggest a certain ease in cancelling, especially if toggleable/editable --}} </li>
                                <li><b> Invoice Name: </b> {{ $company->invoice_name }} </li>
                                <li><b> Route Name: </b> {{ $company->route_name }} {{-- This is also more for our purposes than theirs, so may only show it for 'office' users --}}</li>

                                    @foreach ($company->box_names as $box_name)

                                        <li><b> Box Name: </b> {{ $box_name }} </li> {{-- This is taking info from company table data and not the fruit box tables, possibility of conflicting, outdated information. --}}

                                    @endforeach

                                <li><b> Primary Contact: </b> {{ $company->primary_contact }} </li>
                                <li><b> Primary Email: </b> {{ $company->primary_email }} </li>
                                @if (!empty($company->secondary_email))
                                    <li><b> Secondary Email: </b> {{ $company->secondary_email }} </li>
                                @endif
                                <li><b> Delivery Information: </b> {{ $company->delivery_information }} </li>
                                <li><b> Route Summary Address: </b> {{ $company->route_summary_address }} {{-- Currently a manual process and seperate to breakdown address but should it be automatically created from those fields? --}}</li>
                                <li><b> Address Line 1: </b> {{ $company->address_line_1 }} </li>
                                <li><b> Address Line 2: </b> {{ $company->address_line_2 }} </li>
                                <li><b> City: </b> {{ $company->city }} </li>
                                <li><b> Region: </b> {{ $company->region }} </li>
                                <li><b> Postcode: </b> {{ $company->postcode }} </li>
                                <li><b> Branding Theme: </b> {{ $company->branding_theme }} </li>
                                    {{-- $company->supplier         This field cannot be shown on as part of the company data!! --}}
                                    {{-- $company->blah             This is just to represent the fact there are more things to show and allow to edit, such as delivery days.  --}}
                            </ul>
                            @endforeach
                        @else
                            <h3> No Company Data Available </h3>
                        @endif
                     </div>  <!-- end of company-data div   -->
                     <div class="fruit-boxes">

                         <h3 class="headers"> Fruit Boxes </h3>
                         @foreach ($fruitboxes as $companybox)
                         @php // dd($fruitbox) @endphp
                         @if ($companybox->count())
                            @foreach ($companybox as $fruitbox)
                                @php // dd($fruitbox) @endphp
                                @php // dd($companybox) @endphp
                                @php // dd($fruitboxes) @endphp
                                {{-- <h4> {{ $fruitbox->company_id }} </h4> --}}

                                @if (!is_object($fruitbox))
                                    <h2> {{ $fruitbox }} </h2>
                                    @if ($companybox->count() < 2)
                                     <h4 class="headers"> No Fruit Boxes Available </h4>
                                    @endif
                                @else
                                    <h4 class="headers"> {{ $fruitbox->name }} </h4>
                                     <ul class="list-group inline col-sm-12 orders-top">
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> ID: </b> {{ $fruitbox->id }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Is Active: </b> {{ $fruitbox->is_active }} </li>
                                        {{-- <li class="list-group-item inline-item col-sm-3 {{ $fruitbox->is_active }}"><b> Name: </b> {{ $fruitbox->name }} </li> --}}
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Delivery Day: </b> {{ $fruitbox->delivery_day }} </li>
                                     </ul>
                                     <ul class="list-group inline col-sm-12 orders-bottom">
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Deliciously Red Apples: </b> {{ $fruitbox->deliciously_red_apples }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Pink Lady Apples: </b> {{ $fruitbox->pink_lady_apples }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Red Apples: </b> {{ $fruitbox->red_apples }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Green Apples: </b> {{ $fruitbox->green_apples }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Satsumas: </b> {{ $fruitbox->satsumas }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Pears: </b> {{ $fruitbox->pears }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Bananas: </b> {{ $fruitbox->bananas }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Nectarines: </b> {{ $fruitbox->nectarines }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Limes: </b> {{ $fruitbox->limes }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Lemons: </b> {{ $fruitbox->lemons }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Grapes: </b> {{ $fruitbox->grapes }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $fruitbox->is_active }}"><b> Seasonal Berries: </b> {{ $fruitbox->seasonal_berries }} </li>
                                     </ul>
                                 @endif
                             @endforeach
                         @else
                            <h3 class="headers"> No Fruit Box Available </h3>
                         @endif
                         @endforeach
                     </div>
                     <div class="milk-boxes">

                         <h3 class="headers"> Milk Boxes </h3>
                         @foreach ($milkboxes as $companymilkbox)
                         @php // dd($fruitbox) @endphp
                         @if ($companymilkbox->count())
                            @foreach ($companymilkbox as $milkbox)
                                @php // dd($milkbox) @endphp
                                @php // dd($companymilkbox) @endphp
                                @php // dd($milkboxes) @endphp
                                {{-- <h4> {{ $milkbox->company_id }} </h4> --}}

                                @if (!is_object($milkbox))
                                    <h2> {{ $milkbox }} </h2>
                                    @if ($companymilkbox->count() < 2)
                                     <h4 class="headers"> No Milk Boxes Available </h4>
                                    @endif
                                @else
                                    {{-- <h4 class="headers"> {{ $milkbox->name }} </h4> --}}
                                     <ul class="list-group inline col-sm-12 orders-top">
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> ID: </b> {{ $milkbox->id }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> Is Active: </b> {{ $milkbox->is_active }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> Delivery Day: </b> {{ $milkbox->delivery_day }} </li>
                                     </ul>
                                     <ul class="list-group inline col-sm-12 orders-bottom">
                                         @php // dd($milkbox); @endphp
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Coconut: </b> {{ $milkbox->{'1l_milk_alt_coconut'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Unsweetened Almond: </b> {{ $milkbox->{'1l_milk_alt_unsweetened_almond'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Almond: </b> {{ $milkbox->{'1l_milk_alt_almond'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Unsweetened Soya: </b> {{ $milkbox->{'1l_milk_alt_unsweetened_soya'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Soya: </b> {{ $milkbox->{'1l_milk_alt_soya'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Milk Alternative Lactose Free Semi: </b> {{ $milkbox->{'1l_milk_alt_lactose_free_semi'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 2l Semi Skimmed: </b> {{ $milkbox->{'2l_semi_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 2l Skimmed: </b> {{ $milkbox->{'2l_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 2l Whole: </b> {{ $milkbox->{'2l_whole'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Semi Skimmed: </b> {{ $milkbox->{'1l_semi_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Skimmed: </b> {{ $milkbox->{'1l_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $milkbox->is_active }}"><b> 1l Whole: </b> {{ $milkbox->{'1l_whole'} }} </li>
                                     </ul>
                                 @endif
                             @endforeach
                         @else
                            <h3 class="headers"> No Milk Box Available </h3>
                         @endif
                         @endforeach
                     </div>

                     <div class="routes">

                         <h3 class="headers"> Routes </h3>
                         @foreach ($routes as $companyroute)
                         @php // dd($fruitbox) @endphp
                         @if ($companyroute->count())
                            @foreach ($companyroute as $route)
                                @php // dd($milkbox) @endphp
                                @php // dd($companymilkbox) @endphp
                                @php // dd($milkboxes) @endphp
                                {{-- <h4> {{ $milkbox->company_id }} </h4> --}}

                                @if (!is_object($route))
                                    <h2> {{ $route }} </h2>
                                    @if ($companyroute->count() < 2)
                                     <h4 class="headers"> No Routes Available </h4>
                                    @endif
                                @else
                                    {{-- <h4 class="headers"> {{ $route->company_name }} </h4> --}}
                                     <ul class="list-group inline col-sm-12 orders-top">
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> ID: </b> {{ $route->id }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Is Active: </b> {{ $route->is_active }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Delivery Day: </b> {{ $route->delivery_day }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Postcode: </b> {{ $route->postcode }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Address: </b> {{ $route->address }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Delivery Information: </b> {{ $route->delivery_information }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Drinks: </b> {{ $route->drinks }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Snacks: </b> {{ $route->snacks }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Other: </b> {{ $route->other }} </li>
                                     </ul>
                                     <ul class="list-group inline col-sm-12 orders-bottom">
                                         @php // dd($route); @endphp
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Fruit Crates: </b> {{ $route->fruit_crates }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> Fruit Boxes: </b> {{ $route->fruit_boxes }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 2l Semi Skimmed: </b> {{ $route->{'milk_2l_semi_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 2l Skimmed: </b> {{ $route->{'milk_2l_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 2l Whole: </b> {{ $route->{'milk_2l_whole'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Semi Skimmed: </b> {{ $route->{'milk_1l_semi_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Skimmed: </b> {{ $route->{'milk_1l_skimmed'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Whole: </b> {{ $route->{'milk_1l_whole'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Coconut: </b> {{ $route->{'milk_1l_alt_coconut'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Unsweetened Almond: </b> {{ $route->{'milk_1l_alt_unsweetened_almond'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Almond: </b> {{ $route->{'milk_1l_alt_almond'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Unsweetened Soya: </b> {{ $route->{'milk_1l_alt_unsweetened_soya'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Soya: </b> {{ $route->{'milk_1l_alt_soya'} }} </li>
                                         <li class="list-group-item inline-item col-sm-4 {{ $route->is_active }}"><b> 1l Milk Alternative Lactose Free Semi: </b> {{ $route->{'milk_1l_alt_lactose_free_semi'} }} </li>
                                     </ul>
                                 @endif
                             @endforeach
                         @else
                            <h3 class="headers"> No Milk Box Available </h3>
                         @endif
                         @endforeach
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('home-dashboard')
    <style>
        .fruit-boxes, .milk-boxes, .routes {
            text-align: center;
        }
        .headers {
            margin-bottom: 20px;
            margin-top: 15px;
            margin-left: auto;
            margin-right: auto;
        }
        .orders-top {
            margin-top: 15px;

        }
        .orders-bottom {
            margin-bottom: 10px;
        }
        .inline {
            display: inline-table;
            padding-right: 0;
        }
        .inline-item {
            display: inline-table;
             height: 93px; /* This is a temporary style, what I need is to make the row height equal to the largest item */
        }
        .Active {
            background-color: rgba(116, 244, 66, 0.5);
        }
        .Inactive {
            background-color: rgba(201, 16, 16, 0.5);
        }
    </style>
@endsection
