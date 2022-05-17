@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('content')
    <div class="row">
    <div class="col-lg-12">
        <div class="panel iq-card-block iq-card-stretch iq-card-height">
          <div class="panel-heading d-flex justify-content-between">
            
            <h4 class="card-title text-primary ">PMS Dashboard </h4>
      
          </div>
          <div class="panel-body min-height-400" style="min-height: 400px;">
            
          </div>
        </div>
        
    </div>
</div>
   
@endsection
@section('page-script')
@endsection
