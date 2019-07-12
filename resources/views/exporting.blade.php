
@extends('welcome')
@section('export-excel')
<div class="exporting">
    
    <!-- The weakstart variable used in setting a week for processing and if applicable which day(s) too. -->
    <update-week-start></update-week-start>
    
    <!-- What with javascript encoding and entangling my imports with jargon last time, 
         I'm just skipping that step out and processing directly through the blade template
         and into the caring arms of Laravel Excel. -->
    
    <h3> Import Rejigged Routes </h3>
     
         
    <form class="rejigged-import" enctype="multipart/form-data" method="POST" action="{{ route('import-rejigged-routes') }}">
        @csrf
        <input class="" type="file" name="rejigged-routes-file"> </input>
        <button class="btn btn-primary" type="submit">Import Xlsx</button>
    </form>
    
    <!-- There's probably a better way but if we don't hit an error running through the rejigged routes, 
         let's assume it was a success and offer some friendly feedback.  
         With Vue I use alerts, but here I'm using sessions. -->
         
    @if (session('status'))
        <div class="alert alert-success position">
            {{ session('status') }}
        </div>
    @endif
    
    <!-- A gazillion buttons to download stuff regarding snacks, drinks and other! -->
    <exporting></exporting>
    
    <!-- Just berries -->
    <berry-picklist></berry-picklist>
    
</div>
@endsection

@section('exporting-styles')
<style>

.flex-center {
  display: block;
}
.position {
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}
.rejigged-import {
    padding: 30px;
    border: 1px solid #636b6f;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 15px;
}

</style>
@endsection
