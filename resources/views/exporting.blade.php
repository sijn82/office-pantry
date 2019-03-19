
@extends('welcome')
@section('export-excel')
<div class="exporting">
    <update-week-start></update-week-start>
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
