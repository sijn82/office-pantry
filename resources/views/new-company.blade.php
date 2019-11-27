
@extends('welcome')
@section('companies')

<div>
    <add-new-company></add-new-company>
</div>

@endsection

@section('company-assets')
<style>

    .delivery-info {
        text-align:left;
        padding-left: 20px;
    }
    #assigned_route-dropdown {
        margin-bottom: 60px;
    }
    #homepage.flex-center {
        display: block;
    }
    h3.route-header {
        margin-top: 30px;
        margin-bottom: 10px;
        padding-left: 50px;
        padding-right: 50px;
    }

</style>
@endsection
