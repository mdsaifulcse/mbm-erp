@extends('hr.layout')
@section('title', 'Daily Attendance')

@section('main-content')
@push('css')
<style type="text/css">
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 8px;
    }
    #right_modal_lg .table-responsive{
        display: initial !important;
    }
    #right_modal_lg .table-title{
        display: none;
    }
    .generate-drawer{
        color:#089bab !important;
        font-weight: bold;
    }
    .cursor-pointer{
        cursor: pointer;
    }
    .lib-roster-holiday {
        background-color: #09b0ff !important;
    }
    .report-header-items {
        width: 32%;
        padding: 0 0.5%;

    }
    .report-header{display: flex; flex-wrap: wrap;}
    .report-header-items span {
        display: inline-block;
        width: 60%;
    }
    .report-header-items b {width: 38%;display: inline-block;position: relative;}
    .report-header {
        display: flex;
        flex-wrap: wrap;
        border: 1px solid #a5a5a5;
        padding: 5px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .report-header-items b::after {
        content: ':';
        position: absolute;
        top: 0;
        right: 0;
    }
    .table thead th {
        vertical-align: inherit;
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
                <li class="active"> Daily Attendance Report</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action="#"> 
                        <div class="shift-filter-copy" style="display: none;"></div>
                        <div class="panel">
                            <div class="panel-body pb-0">
                              @php
                                $mbmFlag = 0;
                                $mbmAll = [1,4,5];
                                $permission = auth()->user()->unit_permissions();
                                $checkUnit = array_intersect($mbmAll,$permission);
                                if(count($checkUnit) > 2){
                                  $mbmFlag = 1;
                                }
                              @endphp
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit"  >
                                                <option value=""> Select Unit</option>
                                                @if($mbmFlag == 1)
                                                <option value="145">MBM + MBF + MBM 2</option>
                                                @endif
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>

                                        <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="location">Location</label>
                                        </div>

                                        <div class="form-group has-float-label select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($areaList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="area">Area</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-3">
                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="department">Department</label>
                                        </div>

                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="section">Section</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                <option selected="" value="">Choose...</option> 
                                            </select>
                                            <label for="subSection">Sub Section</label>
                                        </div>
                                        
                                    </div> 
                                    <div class="col-3">
                                      <div class="form-group has-float-label select-search-group">
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="line_id">Line</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                <option selected="" value="">Choose...</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label for="otnonot">OT/Non-OT</label>
                                        </div>
                                        <input type="hidden" id="reportformat" name="report_format" value="1">
                                        <input type="hidden" id="reportGroup" name="report_group" value="as_department_id">
                                    </div>
                                    <div class="col-3">
                                      
                                        <div class="form-group has-float-label select-search-group">

                                            {{ Form::select('report_type', $reportType, Request::get('report_type')??null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'reportType']) }}
                                            <label for="reportType">Report Type</label>
                                        </div>
                                        
                                        <div id="single-date">
                                          <div class="form-group has-float-label has-required">
                                            <input type="date" class="report_date datepicker form-control" id="report-date" name="date" placeholder="Y-m-d" required="required" value="{{ Request::get('date')??date('Y-m-d') }}" autocomplete="off" />
                                            <label for="report-date">Date</label>
                                          </div>
                                        </div>
                                        <div id="double-date" style="display: none">
                                          <div class="row">
                                            <div class="col pr-0">
                                                <div class="form-group has-float-label has-required">
                                                    <input type="date" class="report_date datepicker form-control" id="present_date" name="present_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                    <label for="present_date">Present Date</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group has-float-label has-required">
                                                    <input type="date" class="report_date datepicker form-control" id="absent_date" name="absent_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d', strtotime('-1 day')) }}" autocomplete="off" />
                                                    <label for="absent_date">Absent Date</label>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div id="salary-range" style="display: none">
                                            <div class="row">
                                              <div class="col-5 pr-0">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="50000" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                  <label for="min_sal">Range From</label>
                                                </div>
                                              </div>
                                              <div class="col-1 p-0">
                                                <div class="c1DHiF text-center">-</div>
                                              </div>
                                              <div class="col-6">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="number" class="max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                  <label for="max_sal">Range To</label>
                                                </div>
                                              </div>
                                            </div>       
                                        </div>
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" ><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                    </div>   
                                </div>
                                {{-- <div class="row">
                                    <div class="offset-8 col-4">
                                        
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="single-employee-search" id="single-employee-search" style="display: none;">
                          <div class="form-group">
                            <input type="text" name="employee" class="form-control" placeholder="Search Employee Associate ID..." id="searchEmployee" autocomplete="off">
                          </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                  <div class="iq-card" id="result-section" style="display: none">
                    <div class="iq-card-header d-flex mb-0">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-3">
                              <h4 class="card-title capitalize inline">
                                  <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                  <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </button>
                                </h4>
                            </div>
                            <div class="col-6 text-center">
                              <div id="head-arrow">
                                <h4 class="card-title capitalize inline">
                                  <a class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Date Report" >
                                    <i class="las la-chevron-left"></i>
                                  </a>
                                  <b class="f-16 uppercase" id="result-head"> </b>
                                  <a class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Date Report" >


                                    <i class="las la-chevron-right"></i>
                                  </a>
                                </h4>
                              </div>
                            </div>
                            <div class="col-3">
                              <div class="row">
                                <div class="col-7 pr-0">
                                  <div class="format">
                                    <div class="form-group has-float-label select-search-group mb-0">
                                        <?php
                                            $type = ['as_unit_id'=>'Unit','as_designation_id'=>'Designation','as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                        ?>
                                        {{ Form::select('report_group_select', $type, 'as_department_id', ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                        <label for="reportGroupHead">Report Format</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-5 pl-0">
                                  <div class="text-right">
                                    <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                      <i class="las la-th-large"></i>
                                    </a>
                                    <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                      <i class="las la-list-ul"></i>
                                    </a>
                                    
                                  </div>
                                </div>
                              </div>
                              
                              
                            </div>
                          </div>
                       </div>
                    </div>
                    <div class="iq-card-body no-padding" id="print-area">
                      <style type="text/css" media="print">
                          .table{
                            width: 100%;
                          }
                          a{text-decoration: none;}
                          .table-bordered {
                              border-collapse: collapse;
                          }
                          .table-bordered th,
                          .table-bordered td {
                            border: 1px solid #777 !important;
                            padding:5px;
                          }
                          .no-border td, .no-border th{
                            border:0 !important;
                            vertical-align: top;
                          }
                          .f-16 th, .f-16 td, .f-16 td b{
                            font-size: 16px !important;
                          }
                      </style>
                      <div class="result-data" id="result-data">

                        xxxxxxx
                        
                      </div>
                    </div>
                 </div>
                  
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<div class="modal right fade" id="right_modal_lg" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
    <div class="modal-dialog modal-lg right-modal-width" role="document" > 
        <div class="modal-content">
            <div class="modal-header">
                <a class="view " data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
                    <i class="las la-chevron-left"></i>
                </a>
                <h5 class="modal-title right-modal-title text-center" id="modal-title-right-extra"> &nbsp; </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button class="btn btn-sm btn-primary modal-print" onclick="printDiv('content-result-extra')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                <div class="modal-content-result-extra" id="content-result-extra">
          
                </div>
            </div>
      
        </div>
    </div>
</div>
@include('common.right-modal')
@include('hr.reports.daily_activity.attendance.employee_activity_modal')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){  
    @if(Request::get('report_type') != null && Request::get('date') != null)
        @if(Request::get('report_type') == 'ot')
            $('#reportGroupHead').append('<option value="ot_hour">OT Hour</option>');
            $('#reportGroupHead').val('ot_hour');
            $('#reportGroup').val('ot_hour');
        @endif 
        activityProcess();
    @endif
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    $('#activityReport').on('submit', function(e) {
      e.preventDefault();
      activityProcess();
    });

    $(document).on('click','#shiftDropdown', function(){
        $('.shift-filter').toggle();
    });
    $(document).on('click','#filter-by-shift', function(){
        var ins = '';
        $('.shift-checkbox').each(function(){
            if($(this).prop('checked') == true){
                ins +='<input type="hidden" name="filter_shift[]" value="'+$(this).val()+'">';
            }
        })
        $('.shift-filter-copy').html(ins);
        activityProcess();
    });
    $(".next_btn").click(function(event) {
      var date = $('input[name="date"]').val();
      var type = $('select[name="report_type"] option:selected').text();
      var dateAfter = moment(date).add(1 , 'day').format("YYYY-MM-DD");
      $('input[name="date"]').val(dateAfter);
      var head = type+' - '+dateAfter;
      $("#result-head").html(head);
      activityProcess();
    });

    $(".prev_btn").click(function(event) {
      var date = $('input[name="date"]').val();
      var type = $('select[name="report_type"] option:selected').text();
      var dateBefore = moment(date).subtract(1 , 'day').format("YYYY-MM-DD");
      $('input[name="date"]').val(dateBefore);
      var head = type+' - '+dateBefore;
      $("#result-head").html(head);
      activityProcess();
    });
    $(".grid_view, .list_view").click(function() {
      var value = $(this).attr('id');
      // console.log(value);
      $("#reportformat").val(value);
      $('input[name="employee"]').val('');
      activityProcess();
    });
      
    $("#reportGroupHead").on("change", function(){
      var group = $(this).val();
      $("#reportGroup").val(group);
      activityProcess();
    });

    function activityProcess() 
    {
      $("#result-section").show();
      $("#result-data").html(loaderContent);
      $("#single-employee-search").hide();
      
      var unit = $('select[name="unit"]').val();
      var location = $('select[name="location"]').val();
      var area = $('select[name="area"]').val();
      var date = $('input[name="date"]').val();
      var format = $('input[name="report_format"]').val();
      var type = $('select[name="report_type"]').val();
      var typeText = $('select[name="report_type"] option:selected').text();
      // console.log(type);
      if(type === 'before_absent_after_present'){
        $("#head-arrow").hide();
      }else{
        $("#head-arrow").show();
      }
      var form = $("#activityReport");
      var flag = 0;
      if(type !== 'executive_attendance' && type !== 'employee' && type !=='management_attendance'){
        if(unit === '' ){
            flag = 1;
            $.notify('Please Select Unit', 'error');
        }
      }
      if(date === '' || type === ''){
        flag = 1;
        $.notify('Date / Type Field Require', 'error');
      }
      
      if(flag === 0){
        $(".next_btn").attr('disabled', true);
        $(".prev_btn").attr('disabled', true);
        $('html, body').animate({
            scrollTop: $("#result-data").offset().top
        }, 2000);
        if(type == 'attendance'){
          url = '{{ url("hr/reports/daily-present-absent-activity-report") }}';
        }
        else if (type == 'management_attendance') {
          url = '{{ url("hr/reports/daily-attendance-management") }}';
        }
        else{
          url = '{{ url("hr/reports/daily-attendance-activity-report") }}';
        }
        var head = typeText+' - '+date;
        $("#result-head").html(head);
        
        $.ajax({
            type: "GET",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(response)
            {
              $(".next_btn").attr('disabled', false);
              $(".prev_btn").attr('disabled', false);
              // console.log(response);
              if(response !== 'error'){
                $("#result-data").html(response);
              }else{
                // console.log(response);
                $("#result-data").html('');
              }

              if(format == 0 && response !== 'error'){
                $("#single-employee-search").show();
                $('.list_view').addClass('active').attr('disabled', true);
                $('.grid_view').removeClass('active').attr('disabled', false);
              }else{
                $("#single-employee-search").hide();
                $('.grid_view').addClass('active').attr('disabled', true);
                $('.list_view').removeClass('active').attr('disabled', false);
              }
            },
            error: function (reject) {
              console.log(reject);
            }
        });

      }else{
        console.log('required');
        $("#result-data").html('');
      }
    }
    $('#excel').click(function(){
      var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#report_section').html())
      location.href=url;
      return false;
    });
    // change from data action
    $('#present_date').on('change', function() {
      var before = 1 ;
      var dateBefore = moment($(this).val()).subtract(before , 'day');
      var dateBefore = dateBefore.format("YYYY-MM-DD");
      var absentDate = $('#absent_date').val();
      if(dateBefore !== '') {
        $('#absent_date').val(dateBefore);
      }
    });
    // change unit
    $('#unit').on("change", function(){
        $('.shift-filter-copy').html('');
        $.ajax({
            url : "{{ url('hr/attendance/floor_by_unit') }}",
            type: 'get',
            data: {unit : $(this).val()},
            success: function(data)
            {
                $('#floor_id').removeAttr('disabled');
                
                $("#floor_id").html(data);
            },
            error: function(reject)
            {
               console.log(reject);
            }
        });

        //Load Line List By Unit ID
        $.ajax({
           url : "{{ url('hr/reports/line_by_unit') }}",
           type: 'get',
           data: {unit : $(this).val()},
           success: function(data)
           {
                $('#line_id').removeAttr('disabled');
                $("#line_id").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });
    //Load Department List By Area ID
    $('#area').on("change", function(){
        $.ajax({
           url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
           type: 'get',
           data: {area_id : $(this).val()},
           success: function(data)
           {
                $('#department').removeAttr('disabled');
                
                $("#department").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });

    //Load Section List By department ID
    $('#department').on("change", function(){
        $.ajax({
           url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
           type: 'get',
           data: {area_id: $("#area").val(), department_id: $(this).val()},
           success: function(data)
           {
                $('#section').removeAttr('disabled');
                
                $("#section").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });
    //Load Sub Section List by Section
    $('#section').on("change", function(){
       $.ajax({
         url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
         type: 'get',
         data: {
           area_id: $("#area").val(),
           department_id: $("#department").val(),
           section_id: $(this).val()
         },
         success: function(data)
         {
            $('#subSection').removeAttr('disabled');
            
            $("#subSection").html(data);
         },
         error: function(reject)
         {
           console.log(reject);
         }
       });
    });
    //Report type
    $('#reportType').on("change", function(){
      var type = $(this).val();
      // console.log(type);
      $('input[name="employee"]').val('');
      if(type == 'ot'){
        $('#reportGroupHead').append('<option value="ot_hour">OT Hour</option>');
        $('#reportGroupHead').val('ot_hour');
        $('#reportGroup').val('ot_hour').change();
      }else{
        $("#reportGroupHead option[value='ot_hour']").remove();
        $('#reportGroupHead').val('as_department_id');
        $('#reportGroup').val('as_department_id');

      }
      if(type == 'executive_attendance'){
        $("#unit").val('').trigger('change');
        $('#reportGroupHead').append('<option value="as_area_id">Area</option>');
        $('#reportGroupHead').val('as_area_id');
        $('#reportGroup').val('as_area_id');
      }else{
        $("#reportGroupHead option[value='as_area_id']").remove();
        $('#reportGroupHead').val('as_department_id');
        $('#reportGroup').val('as_department_id');

      }
      var date = "{{ date('Y-m-d') }}";
      if(type == 'ot' || type == 'working_hour'|| type == 'in_out_missing'){
        date = "{{ date('Y-m-d', strtotime('-1 day')) }}";
      }
      $("#report-date").val(date);
      if(type === 'before_absent_after_present'){
        $("#single-date").hide();
        $("#double-date").show();
      }else{
        $("#single-date").show();
        $("#double-date").hide();
      }
      if(type === 'executive_attendance'){
        $("#salary-range").show();
      }else{
        $("#salary-range").hide();
      }
    });

    $('#reportFormat').on("change", function(){
      $('input[name="employee"]').val('');
    });


    $(document).on('click','.generate-drawer', function(){
        var urldata = $(this).data('url'),
            body = $(this).data('body');
        $("#modal-title-right-extra").html(body);
        $('#right_modal_lg').modal('show');
        $("#content-result-extra").html(loaderContent);
        // console.log(urldata);
        $.ajax({
            url: '{{ url('hr/reports/daily-attendance-activity-report') }}?'+urldata+'&report_format=0',
            type: "GET",
            success: function(response){
                // console.log(response);
                if(response !== 'error'){
                    setTimeout(function(){
                        $("#content-result-extra").html(response);
                    }, 1000);
                }else{
                    console.log(response);
                }
            }
        });

    });
       
});
function selectedGroup(e, body, inputUrl){
    var part = e;
    var urldata = inputUrl+part;
    $("#modal-title-right-extra").html(' '+body+' Report Details');
    $('#right_modal_lg').modal('show');
    $("#content-result-extra").html(loaderContent);
    $.ajax({
        url: '{{ url('hr/reports/daily-attendance-activity-report') }}?'+urldata+'&report_format=0',
        type: "GET",
        success: function(response){
            // console.log(response);
            if(response !== 'error'){
                setTimeout(function(){
                    $("#content-result-extra").html(response);
                }, 1000);
            }else{
                console.log(response);
            }
        }
    });

}


    
</script>
@endpush
@endsection