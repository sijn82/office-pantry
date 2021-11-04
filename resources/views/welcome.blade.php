<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Office Pantry</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            html, body {
                background-color: #fff;
                /* background-color: #000000; */
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>

        @yield('importing-csv-files')
        @yield('routing-assets')
        @yield('routing-display-assets')
        @yield('company-assets')
        @yield('process-snackboxes-styling')
        
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else

                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Office Pantry
                </div>

                <div class="links">
                    <a href="{{ route('login/office') }}">Office Login</a>
                    <!-- <a href="{{ route('login/warehouse') }}">Warehouse Login</a> -->
                    <a href="https://laravel-news.com">Stocklist</a>
                    <a href="https://forge.laravel.com">Invoices</a>
                    <a href="https://github.com/laravel/laravel">Payments</a>
                </div>



                @yield('display-routes')
                @yield('snackboxes-multi-company')
                


                <div id="app">
                    @yield('content-excel')
                    @yield('process-snackboxes')
                    @yield('routes')
                    @yield('products')
                    @yield('companies')
                     <!-- <products></products> -->
                     <!-- <example-component></example-component> -->
                 </div>
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
