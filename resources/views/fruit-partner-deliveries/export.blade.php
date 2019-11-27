@extends('welcome')
@section('jobs')
<div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <span class="pull-left">Queue Reports</span>
                    </div>
                    <div class="panel-body">
                        <jobs :fruitpartners="{{ $fruitpartners }}"></jobs>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section ('jobs-styling')

<style media="screen" scoped>
    #homepage.flex-center {
        display: block;
    }
</style>

@endsection
