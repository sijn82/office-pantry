

@extends('welcome')
@section('routes')

<div>
  <h3 class="route-header">Click on the title below, then follow the links (in order) to generate fresh picklists and routes for the coming week.</h3>
  <update-picklist-n-routes></update-picklist-n-routes>
  <!-- <routes></routes>
  <picklists></picklists> -->
</div>

@endsection

@section('routing-assets')
<style>

  h3.route-header {
    margin-top: 30px;
    margin-bottom: 80px;
    padding-left: 50px;
    padding-right: 50px;
  }

</style>
@endsection
