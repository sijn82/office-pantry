@extends('layouts.auth')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        Hi boss!
                    </div>
                    <div class="card-body">
                        <a href=" {{route('import-file') }}"> Import and Process Files </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    @endsection