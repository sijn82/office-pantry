@extends('layouts.auth')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                    Hi {{ Auth::user()->name }}!
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>
                                <a href=" {{ route('import-file') }}"> Import and Process Files </a>
                            </li>
                            <li>
                                <a href=" {{ route('import-products') }}"> Import and Process Snackboxes </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection