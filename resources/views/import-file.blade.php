
@extends('welcome')
@section('content-excel')
<div class="import-file-fod-csv">
    <update-week-start></update-week-start>
    <import-fod-file success="{{ Session::get('picklist_success') }}"></import-fod-file>
    <import-snacks-n-drinks-file></import-snacks-n-drinks-file>
    <import-rejigged-routes-file></import-rejigged-routes-file>
    <berry-picklist></berry-picklist>
</div>
@endsection

@section('importing-csv-files')
<style>

.flex-center {
  display: block;
}

</style>
@endsection
