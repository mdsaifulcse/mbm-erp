@extends('hr.layout')
@section('title', 'Summary Report')

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
#right_modal_lg_drawer .table-responsive{
    display: initial !important;
}
#right_modal_lg_drawer .table-title{
    display: none;
}
/*.generate-drawer{
    color:#089bab !important;
    font-weight: bold;
}*/

/*#monthdata th, .table-bordered td {
    border-color: black !important;
}
#monthdata th {
    border-bottom: 2px solid black !important;
}*/
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
                <li class="active">Management Attendance</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action=""> 
                        <div class="panel">
                            <div class="panel-body pb-0">
                                @php
                                $mbmFlag = 0;
                                $mbmAll = [1,4,5];
                                $permission = auth()->user()->unit_permissions();
                                $checkUnit = array_intersect($mbmAll,$permission);
                                if(count($checkUnit) > 2){
                                    $mbmFlag = 1;
                                        // dd($signatory_name);
                                }
                                @endphp
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit"  >
                                                <option selected="" value="">All Unit...</option>
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="unit">Unit</label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="section">Section</label>
                                        </div>
                                       {{--  <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="location">Location</label>
                                        </div> --}}
                                {{--         <div class="form-group has-float-label select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($areaList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="area">Area</label>
                                        </div> --}}

                                    </div>
                                    <div class="col-3">

                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" class="form-control capitalize select-search" id="department"  >
                                             {{--  department  <option selected="" value="">Choose...</option> --}}
                                                  @foreach($department as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="department">Department</label>
                                        </div>
                                 {{--        <div class="form-group has-float-label select-search-group">
                                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="section">Section</label>
                                        </div> --}}
                                       {{--  <div class="form-group has-float-label select-search-group">
                                            <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                <option selected="" value="">Choose...</option> 
                                            </select>
                                            <label for="subSection">Sub Section</label>
                                        </div> --}}

                                    </div> 
                                    <div class="col-2">

                                               <div class="form-group has-float-label select-search-group">
                                            <select name="location" class="form-control capitalize select-search" id="location">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($locationList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="location">Location</label>
                                        </div>

                                        {{-- <div class="form-group has-float-label select-search-group">
                                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="floor_id">Floor</label>
                                        </div> --}}
                              {{--           <div class="form-group has-float-label select-search-group">
                                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="line_id">Line</label>
                                        </div> --}}
                               {{--          <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                <option selected="" value="">Choose...</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label for="otnonot">OT/Non-OT</label>
                                        </div> --}}
                                        <input type="hidden" id="reportformat" name="report_format" value="1">
                                        <input type="hidden" id="reportGroup" name="report_group" value="as_subsection_id">
                                    </div>
                                    <div class="col-4">

                                  {{--       <div class="form-group has-float-label has-required select-search-group">

                                            {{ Form::select('report_type', $reportType, null, ['placeholder'=>'Select Report Type ', 'class'=>'form-control capitalize select-search', 'id'=>'reportType']) }}
                                            <label for="reportType">Report Type</label>
                                        </div> --}}
                                        <div id="double-date" >
                                            <div class="row">
                                                <div class="col pr-0">
                                                    <div class="form-group has-float-label has-required">
                                                        <input type="date" class="report_date datepicker form-control" id="from_date" name="from_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                        <label for="from_date">Date From</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group has-float-label has-required">
                                                        <input type="date" class="report_date datepicker form-control" id="to_date" name="to_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                                        <label for="to_date">Date To</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="double-date" >
                                            <div class="row">
                                                <div class="col pr-0">
                                                   {{--  <div class="form-group has-float-label select-search-group mb-0" id="latecountdiv">
                                                        <select name="latecount" class="form-control capitalize select-search" id="latecount">
                                                            @for ($i=1; $i<=30; $i++)
                                                            <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                        <label for="reportGroupHead">Late Gater</label>
                                                    </div> --}}
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary nextBtn btn-lg pull-right"  id="Generate"type="submit" ><i class="fa fa-save"></i> Generate</button>
                                                    </div>

                                                   


                                                </div>
                                            </div>
                                             <br>
                                        </div>
                                    </div>   
                                </div>

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

            {{-- @php
                               
                                dd($signatory_name);
                            
                                @endphp --}}
            <div class="row">
                <div class="col">
                    <div class="iq-card" id="result-section">
                        <div class="iq-card-header d-flex mb-0">
                            <div class="iq-header-title w-100">
                                <div class="row">
                                    <div class="col-3">
                                        <h4 class="card-title capitalize inline">
                                            <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                     {{--        <button type="button" target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                                <i class="fa fa-file-excel-o"></i>
                                            </button> --}}
                                        </h4>

                                    </div>
                                    <div class="col-6 text-center">

                                    </div>
                                    <div class="col-3" >
                                        <form id="top-form">
                                            <div class="row">
                                                <div class="col-4 pr-0">
                                                    <div class="format">

                                                    </div>
                                                </div>

                                                {{--  @php
                                                 dd('sss');
                                                  @endphp --}}
                                                <div class="col-8 pl-0">
                                                              <input class="form-control pull-right" id="myInput" type="text" placeholder="Search.." style="width:200px;float: right;">    
                                                        {{-- <div class="form-group has-float-label select-search-group" id="managerdiv">
                                                            <select name="manager" class="form-control capitalize select-search" id="manager">
                                                                <option selected="" value="null">Choose...</option>

                                                                @foreach($signatory_name as $key => $value)

                                                                <option value="{{$key}}">{{$value}}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="manager">Manager Name</label>
                                                        </div> --}}
                                   {{--                  <div class="text-right">
                                                        <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                                            <i class="las la-th-large"></i>
                                                        </a>
                                                        <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                                            <i class="las la-list-ul"></i>
                                                        </a>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="iq-card-body no-padding" id="print-area">
                            <style type="text/css">
                            .table{width: 100%;}a{text-decoration: none;}.table-bordered {border-collapse: collapse;}.table-bordered th,.table-bordered td {border: 1px solid #777 !important;padding:3px;}.no-border td, .no-border th{border:0 !important;vertical-align: top;}.f-16 th,.f-16 td, .f-16 td b{font-size: 12px !important;}
                        </style>
                        <div class="result-data" id="result-data" style="overflow-x:auto;">
                        
                        </div>
                       
                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.page-content -->
</div>
</div>
@include('common.right-modal')
@include('hr.reports.daily_activity.attendance.employee_activity_modal')

<div class="modal right fade" id="right_modal_lg_drawer" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg_drawer">
    <div class="modal-dialog modal-lg right-modal-width" role="document" > 
        <div class="modal-content">
            <div class="modal-header">
                <a class="view " data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
                    <i class="las la-chevron-left"></i>
                </a>
                <h5 class="modal-title right-modal-title text-center" id="modal-title-right-drawer"> &nbsp; </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button class="btn btn-sm btn-primary modal-print" onclick="printDiv('content-result-drawer')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                <div class="modal-content-result-drawer" id="content-result-drawer">

                </div>
            </div>

        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    var loaderModal = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:10px;" class="loader-p"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';


// $("#latecount").val(4).trigger('change');
// $("#latecountdiv").hide();
    $(document).ready(function(){   
        var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
        $('#activityReport').on('submit', function(e) {
          e.preventDefault();
          // activityProcess();
          console.log('ac');
        });
        // $(".next_btn").click(function(event) {
        //   var date = $('input[name="date"]').val();
        //   var type = $('select[name="report_type"]').val();
        //   var dateAfter = moment(date).add(1 , 'day').format("YYYY-MM-DD");
        //   $('input[name="date"]').val(dateAfter);
        //   var head = type+' - '+dateAfter;
        //   $("#result-head").html(head);
        //   // activityProcess();
        //   console.log('nb');
        // });

        // $(".prev_btn").click(function(event) {
        //   var date = $('input[name="date"]').val();
        //   var type = $('select[name="report_type"]').val();
        //   var dateBefore = moment(date).subtract(1 , 'day').format("YYYY-MM-DD");
        //   $('input[name="date"]').val(dateBefore);
        //   var head = type+' - '+dateBefore;
        //   $("#result-head").html(head);
        //   // activityProcess();
        //   console.log('pb');
        // });
        // $(".grid_view, .list_view").click(function() {
        //   var value = $(this).attr('id');
        //   // console.log(value);
        //   $("#reportformat").val(value);
        //   $('input[name="employee"]').val('');
        //   // activityProcess();
        //   console.log('lv');
        // });
          
        // $("#reportGroupHead").on("change", function(){
        //   var group = $(this).val();
        //   $("#reportGroup").val(group);

        //   // activityProcess();
        //   console.log('rh');
        // });

        // function activityProcess() {
        //   $("#result-section").show();
        //   $("#result-data").html(loader);
        //   $("#single-employee-search").hide();
        //   $("#xxxx").show();
        //   $("#1").show();
        //   $("#0").show();
        //   $("#employeeid").hide();
        //   $("#latecountdiv").hide();
        //   $("#managerdiv").hide();

          
        //   var unit = $('select[name="unit"]').val();
        //   var location = $('select[name="location"]').val();
        //   var area = $('select[name="area"]').val();
        //   var from_date = $('input[name="from_date"]').val();
        //   var to_date = $('input[name="to_date"]').val();
        //   var format = $('input[name="report_format"]').val();
        //   var type = $('select[name="report_type"]').val();

        //   console.log(type);
        //   if(type === 'before_absent_after_present'){
        //     $("#head-arrow").hide();
        //   }else{
        //     $("#head-arrow").show();
        //   }
        //   var form = $("#activityReport");
        //   var flag = 0;
        //   if(unit === '' || from_date === '' || type === '' || to_date === ''){
        //     flag = 1;
        //     $.notify('Select required field', 'error');
        //   }
          
        //   if(flag === 0){
        //     $(".next_btn").attr('disabled', true);
        //     $(".prev_btn").attr('disabled', true);
        //     $('html, body').animate({
        //         scrollTop: $("#result-data").offset().top
        //     }, 2000);
        //     if(type == 'attendance'){
        //       url = '{{ url("hr/reports/daily-present-absent-activity-report") }}';
        //     }else if(type == 'absentreason')
        //     {
        //     url = '{{ url("hr/reports/habitual-absent") }}';
        //     }else if(type == 'latewarning')
        //     {
        //         $("#xxxx").hide();
        //         $("#1").hide();
        //         $("#0").hide();
        //         $("#employeeid").show();
        //         $("#latecountdiv").show();
        //         $("#managerdiv").show();
           
        //          url = '{{ url("hr/reports/latewarning") }}';
        //     }else if(type == 'linechangedaily')
        //     {
        //         $("#xxxx").hide();
        //         $("#1").hide();
        //         $("#0").hide();
        //         // $("#employeeid").show();
        //         // $("#latecountdiv").show();

           
        //          url = '{{ url("hr/reports/linechangedaily") }}';
        //     }

        //     else{
        //       url = '{{ url('hr/reports/summary/report') }}';
        //     }
     
        //     var contentForm = form.serialize();
        //     var topForm = $("#top-form").serialize();
        //     var formData = contentForm +'&'+ topForm;
            
        //     $.ajax({
        //         type: "GET",
        //         url: url,
        //         data: formData, // serializes the form's elements.
        
        //         success: function(response)
        //         {
        //             console.log('recieved');
        //           $(".next_btn").attr('disabled', false);
        //           $(".prev_btn").attr('disabled', false);
        //           // console.log(response);
        //           if(response !== 'error'){
        //             $("#result-data").html(response);
        //             console.log('replaced');
        //           }else{
        //             // console.log(response);
        //             $("#result-data").html('');
        //           }

        //           if(format == 0 && response !== 'error'){
        //             $("#single-employee-search").show();
        //             $('.list_view').addClass('active').attr('disabled', true);
        //             $('.grid_view').removeClass('active').attr('disabled', false);
        //           }else{
        //             $("#single-employee-search").hide();
        //             $('.grid_view').addClass('active').attr('disabled', true);
        //             $('.list_view').removeClass('active').attr('disabled', false);
        //           }
        //         },
        //         error: function (reject) {
        //           console.log(reject);
        //         }
        //     });

        //   }else{
        //     console.log('required');
        //     $("#result-data").html('');
        //   }
        // }

        
        $('#excel').click(function(){
        

          var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#report_section').html())
          location.href=url;
          return false;
 
        });
        // change from data action
        $('#from_date').on('change', function() {
          $('#to_date').attr('min',$('#from_date').val());
          $('#to_date').val($('#from_date').val());
        });
        // change unit
        $('#unit').on("change", function(){
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
               url : "{{ url('hr/reports/getSectionListByDepartmentID') }}",
               type: 'get',
               data: { department_id: $(this).val()},
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
        

        $('#reportFormat').on("change", function(){
          $('input[name="employee"]').val('');
        });



       
    });


    




 $("#result-section").hide();

    $(document).on('click','#Generate', function(){
     // console.log('dvfdgfd');
     $("#result-section").show();
      getinoutdata();
    });

    function getinoutdata(id = null) {
        $("#result-data").html(loader);
        var contentForm = $("#activityReport").serialize();
        var topForm = $("#top-form").serialize();
        var formData = contentForm +'&'+ topForm;
     $.ajax({
                type: "GET",
                url : "{{ url('hr/reports/management-in-out-getdata') }}",
                data: formData,
                success: function(response)
                {
                    console.log('recieved');
                 
                    $("#result-data").html(response);
                }
                   
            });
    }


</script>
@endpush
@endsection