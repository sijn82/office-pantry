@extends('welcome')
@section('imports')

    <div class="return-link">
        <a href="{{ route('office') }}"> Return to Office Dashboard </a>
    </div>

    <div>
        <h3> Import Company Details (Xlsx) </h3>
        <form class="import-form" enctype="multipart/form-data" method="POST" action="{{ route('import-company-details') }}">
            @csrf
            <input class="" type="file" name="imported-company-details-file"> </input>
            <button class="btn btn-primary" type="submit">Import Company Details</button>
        </form>
        
        <h3> Import Fruit Partners (Xlsx) </h3>
        <form class="import-form" enctype="multipart/form-data" method="POST" action="{{ route('import-fruit-partners') }}">
            @csrf
            <input class="" type="file" name="imported-fruit-partners-file"> </input>
            <button class="btn btn-primary" type="submit">Import Fruit Partners</button>
        </form>
    </div>

@endsection

@section('import-styling')

    <style>
        .return-link {
            margin: 30px;
        }
        .import-form {
            margin: 30px;
        }
    </style>
@endsection
