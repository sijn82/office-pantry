@extends('welcome')
@section('process-snackboxes')

<div class="snackbox-processing">
    <process-snacks-into-templates></process-snacks-into-templates>
</div>

@endsection

@section('process-snackboxes-styling')
<style>

.flex-center {
  display: block;
}

</style>
@endsection