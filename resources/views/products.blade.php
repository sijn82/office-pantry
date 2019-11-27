@extends('welcome')
@section('products')
<div>
  <h1 style="padding-top:20px;"> Products </h1>
  <add-new-product></add-new-product>
  <products-list></products-list>
</div>
@endsection

@section('product-styles')
<style>
    #homepage.flex-center {
      display: block;
    }
</style>
@endsection
