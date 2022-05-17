@extends('hr.layout')
@section('title', 'Cross Analysis')
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
          <a href="#">Reports</a>
        </li>
        <li class="active"> Employee Cross Analysis</li>
      </ul><!-- /.breadcrumb -->
    </div>
    <div class="iq-accordion career-style mat-style  ">
        <div class="iq-card iq-accordion-block mb-3 accordion-active">
            <div class="active-mat clearfix">
              <div class="container-fluid">
                 <div class="row">
                    <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;"> Cross Analysis </span> </a></div>
                 </div>
              </div>
            </div>
            <div class="accordion-details">
                <div class="row">
                    <div class="col">
                      <form role="form" class="dataReport" id="dataReportEmp">
                        <div class="panel mb-0">
                            <div class="panel-body " >
                                <div class="row justify-content-sm-center">
                                  <div class="col-sm-4">
                                    <div class="pr-0">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="type" id="type" class="form-control">
                                              <option value=""> - Select - </option>
                                              <option value="as_unit_id"> Unit</option>
                                              <option value="as_location"> Location</option>
                                              <option value="as_department_id"> Department</option>
                                              <option value="as_designation_id"> Designation</option>
                                              <option value="as_section_id"> Section</option>
                                              <option value="as_subsection_id"> Sub Section</option>
                                            </select>
                                            <label  for="type"> Type </label>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row hide" id="load-content-option">
                                    <div class="col-4 pr-0">
                                        <div class="form-group has-float-label has-required select-search-group" id="load-type-wise-data">
                                            <select name="category_data[]" id="data" class="form-control" disabled="" multiple>
                                            </select>
                                            <label  for="data"> Name </label>
                                        </div>
                                    </div>
                                    <div class="col-2 pr-0">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="otnonot" id="ot-nonot" class="form-control">
                                                <option value=""> Both </option>
                                                <option value="1"> OT </option>
                                                <option value="0"> Non-OT </option>
                                            </select>
                                            <label  for="ot-nonot"> OT/Non-OT </label>
                                        </div>
                                    </div>
                                    <div class="col-2 pr-0">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="month" class="form-control" id="month-from" name="month_from" placeholder=" Month-Year"required="required" value="{{ (request()->month_from?request()->month_from:date('Y-m') )}}"autocomplete="off" min="2020-10" max="{{ date('Y-m') }}" />
                                            <label  for="month-from"> Month From </label>
                                        </div>
                                    </div>
                                    <div class="col-2 pr-0">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="month" class="form-control" id="month-to" name="month_to" placeholder=" Month-Year"required="required" value="{{ (request()->month_to?request()->month_to:date('Y-m') )}}"autocomplete="off" min="2020-10" max="{{ date('Y-m') }}" />
                                            <label  for="month-to"> Month To </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                  </div>
              </div>
           </div>
        </div>
    </div>
    <div class="page-content content-show" style="display: none;">
      <div class="panel w-100">
        <div class="panel-body">
          <div class="col-12 h-min-400">
            <div id="result-data"></div>
          </div>
        </div>
      </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  
  $(document).on('change', '#type', function(event) {
    var type = $(this).val();
    $('#data').after('<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>');
    $('#data').attr('disabled','true');
    if(type !== ''){
      $.ajax({
        url: '{{ url("hr/type-wise-data-view")}}',
        data: {
          type: type
        },
        type: "GET",
        success: function(response){
          // console.log(response);
          if(response !== 'error'){
              setTimeout(function(){
                  $('#data').empty();
                  $("#data").select2({data:response});
                  $('.loading-select').remove();
                  $("#data").removeAttr('disabled');
              }, 1000);
          }else{
              $.notify('Something wrong, please reload the page and try again', 'error');
          }
          $("#load-content-option").removeClass('hide');
        }
      });
    }else{
      $("#load-content-option").addClass('hide');
    }
  });
  $(document).on('change', '#month-from', function(event) {
    var month = $(this).val();
    $("#month-to").attr('min', month)
  });
  $(document).on('click', '.activityReportBtn', function(event) {
    event.preventDefault();
    var data = $("#data").val();
    var form = $("#dataReportEmp")
    $("#result-data").html(loaderContent);
    $(".content-show").show();
    if(data.length > 0){
      $.ajax({
        url: '{{ url("hr/reports/employee-cross-analysis-report")}}',
        data: form.serialize(),
        type: "GET",
        success: function(response){
          // console.log(response);
          if(response !== 'error'){
            
            $("#result-data").html(response)
          }
        }
      });
    }
  });
</script>
@endpush
@endsection