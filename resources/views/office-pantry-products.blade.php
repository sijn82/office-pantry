@extends('welcome')
@section('products/office-pantry-products')
<div>
  <h1 style="padding-top:20px;"> Office Pantry Products (Pricing) </h1>
  <office-pantry-product-list></office-pantry-product-list>
</div>
@endsection

@section('office-pantry-products-styling')
<style media="screen">
    #homepage.flex-center {
        display: block;
    }
</style>
@endsection
