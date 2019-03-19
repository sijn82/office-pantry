@extends('welcome')
@section('assigned-routes')
<div>
  <h1 style="padding-top:20px;"> Assigned Routes </h1>
  <add-new-assigned-route></add-new-assigned-route>
  <assigned-routes-list></assigned-routes-list>
</div>
@endsection

@section('assigned-routes-styling')
<style>
    .flex-center {
        display: block;
    }
</style>
@endsection