@extends('hr.layout')
@section('title', 'Job Card')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
   .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .iq-card {
        border: 1px solid #ccc;
    }
    td .label {
      padding: 0px 5px !important;
      background: #daf0f3 !important;
      color: #000 !important;
      border-radius: 4px !important;
      font-weight: 400;
    }
    .bg-default{
      background-color: #fff !important;
      border: 1px solid #000;
    }
    .table-bordered th, .table-bordered td {
      border: 1px solid #aeaeae99;
    }
    .table tbody + tbody {
        border-top: 0px solid #dee2e6;
    }
    td span p{display: inline;}
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Human Resource</a>
        </li>
        <li>
          <a href="#">Operation</a>
        </li>
        <li class="active"> Job Card</li>
      </ul><!-- /.breadcrumb -->
    </div>
    @php
      $view = Request::get('view')??'show';
    @endphp
    <div class="page-content">
      <div class="row">
        <div class="col">
          <job-card :attributes="{{ json_encode($input) }}" :view="{{ json_encode($view) }}"></job-card>  
        </div>
      </div>
    </div>
  </div>
</div>

@push('js')
  <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
@endpush
@endsection