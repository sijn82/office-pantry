
@extends('welcome')
@section('content-excel')
<div class="import-file-fod-csv">
    <import-fod-file></import-fod-file>
    <import-snacks-n-drinks-file></import-snacks-n-drinks-file>
    <import-rejigged-routes-file></import-rejigged-routes-file>
</div>
@endsection

@section('importing-csv-files')
<style>

.flex-center {
  display: block;
}

</style>
@endsection
