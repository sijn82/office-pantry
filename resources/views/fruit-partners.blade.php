@extends('welcome')
@section('fruit-partners')
<div>
    <h1 style="padding-top:20px;"> Fruit Partners </h1>
    <fruitpartners-admin></fruitpartners-admin>
</div>
@endsection

@section('fruit-partner-styling')
<style>
    #homepage.flex-center {
      display: block;
    }
</style>
@endsection
