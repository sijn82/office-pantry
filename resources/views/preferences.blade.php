@extends('welcome')
@section('preferences')
<div>
    <h1 style="padding-top:20px;"> Preferences </h1>
    <add-new-preference></add-new-preference>
</div>
@endsection

@section('preferences-assets')
<style>
.flex-center {
    display: block;
}
</style>
@endsection