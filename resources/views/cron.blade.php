@extends('welcome')
@section('cron')
<div class="cron">
    <cron-list></cron-list>
</div>


@endsection
@section('cron-styling')
<style media="screen">
    .flex-center {
        display: block;
    }
    .cron {
        padding-top: 40px;
    }
</style>

@endsection