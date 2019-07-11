
@extends('welcome')
@section('export-excel')
<div class="exporting">
    <update-week-start></update-week-start>
    <form enctype="multipart/form-data" method="POST" action="{{ route('import-rejigged-routes') }}">
        @csrf
        <input type="file" name="rejigged-routes-file"> </input>
        <input type="submit" value="Import"></input>
    </form>
    <exporting></exporting>
    <berry-picklist></berry-picklist>
</div>
@endsection

@section('exporting-styles')
<style>

.flex-center {
  display: block;
}

</style>
@endsection
