@extends('hr.layout')
@section('title', 'Holiday Roster')
@section('main-content')

@push('css')
    
    <style>
        table.header-fixed1 tbody {max-height: 500px; overflow-y: scroll;}
        input[type=checkbox] {
            transform: scale(1.5);
        }
        .form-group { margin-bottom: 25px;}
        .fc-widget-content{
            height: auto !important;
        }
        .fc-bgevent {
            background: #0db5c8 !important;
            color: #fff !important;
        }
        .fc-bgevent, .fc-highlight {
            opacity: .7 !important;
        }
        .fc-day-grid-container{
            height: auto !important;
        }
        .fc-center h2{
            font-size: 17px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar2.min.css') }}">
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
                    <a href="#"> Operation </a>
                </li>
                <li class="active"> Holiday Roster Assign </li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/operation/shift_assign')}}" target="_blank" class="btn btn-warning btn-sm pull-right"> <i class="fa fa-eye"></i> Shift Roster</a>
                    &nbsp;
                    <a href="{{ url('/hr/operation/holiday-roster')}}" class="btn btn-primary btn-sm pull-right"> <i class="fa fa-list"></i> Holiday Roster</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="iq-accordion career-style mat-style  ">
            <div class="iq-card iq-accordion-block ">
               <div class="active-mat clearfix">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Filter </span> </a></div>
                     </div>
                  </div>
               </div>
               <div class="accordion-details">
                  <div class="row1">
                    <div class="col-12">
                        <div class="panel panel-info">
                            <div class="panel-body pb-0">
                                <div class="row">
                                    <div class="col-sm-3"> 
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unit_shift', 'class'=> 'form-control', 'required'=> 'required']) }} 
                                            <label  for="unit">Unit </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('location', $locationList, null, ['placeholder'=>'Select location','id'=>'location_shift', 'class'=> 'form-control', 'required'=> 'required']) }} 
                                            <label  for="location">Location </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('area', $areaList, null, ['placeholder'=>'Select Area','id'=>'area_shift', 'class'=> 'form-control ', 'disabled']) }} 
                                            <label  for="area">Area </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="department" id="department_shift" class= "form-control" disabled ><option value="">Select Department</option></select>
                                            <label  for="department">Department </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('section', [], null, ['placeholder'=>'Select Section','id'=>'section_shift', 'class'=> 'form-control','disabled']) }} 
                                            <label  for="section">Section </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('subsection', [], null, ['placeholder'=>'Select Sub Section','id'=>'subsection_shift', 'class'=> 'form-control', 'disabled']) }}
                                            <label  for="subsection">Sub Section </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-3"> 
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control']) }} 
                                            <label  for="emp_type">Employee Type </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="otnonot" id="otnonot" class="form-control">
                                                <option value="">Select OT/Non-OT</option>
                                                <option value="0">Non-OT</option>
                                                <option value="1">OT</option>
                                            </select>
                                            <label  for="otnonot">OT/Non-OT </label>
                                        </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select class="form-control" id="emp_status" name="emp_status">
                                                <option value="">Select Status</option>
                                                <option value="0" >Shift</option>
                                                <option value="1">Roster</option>
                                            </select> 
                                            <label  for="emp_status" style="color: maroon;">Employee Status </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 pl-0">
                                        <div class="form-group has-float-label select-search-group">
                                            <select class="form-control" id="dayType" name="searchtype">
                                                <option value="">Select Type</option>
                                                <option value="Holiday" >Holiday</option>
                                                <option value="General" >General</option>
                                                <option value="OT" >OT</option>
                                            </select> 
                                            <label  for="dayType" style="color: maroon;">Day Type </label>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <input type="date" class="report_date datepicker form-control" id="dates" name="dates" placeholder="Y-m-d" multiple autocomplete="off" />
                                            <label for="dates">Select Date</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group has-float-label">
                                                    <input type="date" class="report_date datepicker form-control" id="doj_to" name="doj_to" placeholder="Y-m-d" value="" autocomplete="off" />
                                                    <label for="doj_to">DOJ To</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 pl-0">
                                                <div class="form-group has-float-label">
                                                    <input type="date" class="report_date datepicker form-control" id="doj_from" name="doj_from" placeholder="Y-m-d" value="" autocomplete="off" />
                                                    <label for="doj_from">DOJ From</label>
                                                </div>
                                            </div>
                                            
                                          </div>
                                        <div class="form-group">
                                            <button class="btn btn-outline-success nextBtn btn-lg pull-right filter" type="button" ><i class="fa fa-search"></i> Search</button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
               </div>
            </div>
            
        </div>
        
        <form role="form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-sm-5">
                <div class="iq-accordion career-style mat-style  ">
                    <div class="iq-card iq-accordion-block ">
                       <div class="active-mat clearfix">
                          <div class="container-fluid">
                             <div class="row">
                                <div class="col-sm-12"><a class="accordion-title multiple-selected"><span class="header-title"> Multiple Selected </span> </a></div>
                             </div>
                          </div>
                       </div>
                       <div class="accordion-details">
                          <div class="row1">
                            <div class="col-12">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 text-center" style="background-color: rgb(218 240 243); color: rgb(0 0 0); border-right: 1px solid #ccc;">
                                                <p style="padding-top: 2px;">Selected Employee: <span id="selectEmp" style="font-weight: bold;">0</span></p>
                                            </div>
                                            <div class="col-sm-6 text-center" style="background-color: rgb(218 240 243); color: rgb(0 0 0);">
                                                <p style="padding-top: 2px;">Total Employee: <span id="totalEmp" style="font-weight: bold;">0</span></p>
                                            </div>
                                        </div>
                                        <div class="row" style="height: 346px; overflow: auto;">
                                            <div class="col p-0">
                                                <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style='position: sticky; top: -1px; background:#fff;z-index: 1;'><input type="checkbox" id="checkAll"/></th>
                                                            <th class="sticky-th">Image</th>
                                                            <th class="sticky-th">ID</th>
                                                            {{-- <th class="sticky-th">Oracle ID</th> --}}
                                                            <th class="sticky-th">Name</th>
                                                            <th class="sticky-th">Current Shift - ref.</th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="5" id="user_filter" style='position: sticky; top: -1px;'></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="user_info">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                       </div>
                    </div>
                    <div class="iq-card iq-accordion-block accordion-active">
                       <div class="active-mat clearfix">
                          <div class="container-fluid">
                             <div class="row">
                                <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Employee Wise </span> </a></div>
                             </div>
                          </div>
                       </div>
                       <div class="accordion-details">
                          <div class="row1">
                              <div class="col-12">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        {{ Form::select('associate[]', [],'', ['id'=>'associate', 'class'=> 'allassociates form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                        <label for="associate">Employees</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                              </div>
                          </div>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-7 pl-0">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4 pr-0">
                                <div class="form-group has-required has-float-label select-search-group">
                                    <select class="form-control" id="targetType" name="type" required onchange="dayType(this.value)">
                                        <option value="">Select Type</option>
                                        <option value="Holiday" >Holiday</option>
                                        <option value="Substitute-Holiday" >Substitute Holiday</option>
                                        <option value="General" >General</option>
                                        <option value="OT" >OT</option>
                                    </select>
                                    <label  for="targetType" style="color: maroon;">Day Type </label>
                                </div>

                                <div class="form-group has-float-label">
                                    {{-- <input type="text" class="form-control" id="comment" name="comment" placeholder="Comment" value="" autocomplete="off" /> --}}
                                    <textarea name="comment" class="form-control" id="comment" rows="2" placeholder="Comment"></textarea>
                                    <label for="comment">Comment</label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="date" name="reference_date" id="ref_date" placeholder="Reference Date" value="" class="form-control"> 
                                    <label for="ref_date">Reference Date </label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="text" name="reference_comment" id="reference_comment" placeholder="Comment" value="" class="form-control"> 
                                    <label for="reference_comment">Reference Comment </label>
                                </div>
                                
                                <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                    <input type="checkbox" name="holiday_type" class="custom-control-input bg-primary" id="customCheck" value="1">
                                    <label class="custom-control-label" for="customCheck"> Festival Holiday</label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="widget-body">
                                    <div class="widget-main padding-16">
                                        <div id="event-calendar"></div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <input type="hidden" id="assignDates" name="assignDates" value="">
                        <br>
                        <div class="form-group overflow-hidden mb-0"> 
                            <button type="button" id="formSubmit" class="btn btn-primary pull-right" >
                                <i class="fa fa-save"></i> Save
                            </button> 
                        </div>
                        
                    </div>   
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar2.min.js') }}"></script>

<script type="text/javascript">
var multiselect = [];
var singleselect = [];
$(document).ready(function(){
    var totalempcount = 0;
    var totalemp = 0;

    //Filter User
    $("body").on("keyup", "#AssociateSearch", function(e) {
        if(e.keyCode == 13){
            var value = $(this).val().toLowerCase();
            // $('#AssociateTable tr input:checkbox').prop('checked', false);
            $('#AssociateTable tr').removeAttr('class');
            $("#AssociateTable #user_info tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                if($(this).text().toLowerCase().indexOf(value) > -1) {
                    $(this).attr('class','add');
                    var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                    var numberOfCheckBox = $('#AssociateTable tr.add input:checkbox').length;
                    if(numberOfChecked == numberOfCheckBox) {
                        $('#checkAll').prop('checked', true);
                    } else {
                        $('#checkAll').prop('checked', false);
                    }
                }
            });
        }
    });

    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var location     = $("select[name=location]");
    var otnonot     = $("select[name=otnonot]");
    var shift    = $("select[name=shift_id]");
    var section  = $("select[name=section]");
    var subsection = $("select[name=subsection]");
    var area = $("select[name=area]");
    var department = $("select[name=department]");
    var type = $("select[name=searchtype]");
    var emp_status = $("select[name=emp_status]");
    var dojTo = $("#doj_to");
    var dojFrom = $("#doj_from");
    // var condition = $("select[name=condition]");
    $(".filter").on('click', function(){
        loadEmployeeSearchWise();
    });
    function loadEmployeeSearchWise(){
        
        if(unit.val() !== '' || location.val() !== ''){
            $(".multiple-selected").click();
            userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th>');
            $.ajax({
                url: '{{ url("hr/operation/holiday_roster_assign_employee") }}',
                data: {
                    emp_type: emp_type.val(),
                    otnonot: otnonot.val(),
                    unit    : unit.val(),
                    location : location.val(),
                    shift   : shift.val(),
                    section : section.val(),
                    subsection : subsection.val(),
                    area : area.val(),
                    department : department.val(),
                    dates:$('#dates').val(),
                    type:type.val(),
                    shift_roster_status:emp_status.val(),
                    status:$("select[name=status]").val(),
                    doj_to:dojTo.val(),
                    doj_from:dojFrom.val()

                },
                success: function(data)
                {
                    // console.log(data);
                    userFilter.html(data.filter);
                    totalempcount = 0;
                    totalemp = 0;
                    if(data.result == ""){
                        $('#totalEmp').text('0');
                        $('#selectEmp').text('0');
                        userInfo.html('<th colspan="5" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th>');
                    }
                    else{
                        //console.log(data.total);
                        userInfo.html(data.result);
                        totalemp = data.total;
                        $('#selectEmp').text(totalempcount);
                        $('#totalEmp').text(data.total);
                    }
                    
                },
                error:function(xhr)
                {
                    console.log(xhr);
                }
            });
        }else{
            $.notify("Select Unit Or Location", 'error');
        }
    }

    

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('#AssociateTable tr.add input:checkbox:checked').length;
            selectemp = totalempcount - selectemp;
            totalempcount = 0;
        } else {
            selectemp = $('#AssociateTable tr.add input:checkbox:not(:checked)').length;
        }
        $('#AssociateTable tr.add input:checkbox').prop('checked', checked);
        totalempcount = totalempcount+selectemp;
        $('#selectEmp').text(totalempcount);
    });

    $('body').on('click', 'input:checkbox', function() {
        if(!this.checked) {
            $('#checkAll').prop('checked', false);
        }
        else {
            var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
            var numTotal = $('input:checkbox:not(#checkAll)').length;
            if(numTotal == numChecked) {
                $('#checkAll').prop('checked', true);
            }
        }
        if($(this).prop('checked')) {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount += 1;
            }
        } else {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount -= 1;
            }
        }
        $('#selectEmp').text(totalempcount);
    });
    
    $('#formSubmit').on("click", function(e){
        var checkedBoxes = [];
        var msg = '';
        var flag = 0;
        var calendar = $('#event-calendar');
        var assignDates = $("input[name=assignDates]").val();
        var type = $("select[name=type]").val();
        var comment = $("input[name=comment]").val();
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            {
                checkedBoxes.push($(this).val());
            }
        });
        var employeeId = $('.allassociates').select2("val");

        if(checkedBoxes.length === 0 && employeeId.length === 0){
            msg = "Please Select Employee At Least One";
            flag = 1;
        }
        if(type == null || type == ''){
            msg = "Please Select Target Day Type";
            flag = 1;
        }
        if(assignDates.length === 0){
            msg = "Please Select Action Date On The Calendar";
            flag = 1;
        }
        if(employeeId.length > 0){
            checkedBoxes = [];
            checkedBoxes = employeeId;
        }
        var holidayType = 1;
        if($("#customCheck").is(":checked")){
            holidayType = 2;
        }
        // console.log(holidayType);
        if(flag === 0){
            $(".app-loader").show();
            $.ajax({
                type: "POST",
                url: '{{ url("/hr/operation/holiday-roster") }}',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    assigned: checkedBoxes,
                    type: type,
                    assignDates: assignDates,
                    comment: comment,
                    reference_date: $("#ref_date").val(),
                    reference_comment: $("#reference_comment").val(),
                    holiday_type: holidayType
                },
                success: function(response)
                {
                    console.log(response);
                    for (i = 0; i < response.message.length; i++) {
                        $.notify(response.message[i], response.type);
                    }
                    
                    if(response.type === 'success'){
                        $('input[type="checkbox"]:checked').each(function() {
                            $(this).prop("checked", false);
                        });
                        $("#selectEmp").text('0');
                        $("#assignDates").val('');
                        totalempcount = 0;
                        // Clear all events
                        
                        calendar.fullCalendar('removeEvents', function(eventObject) {
                            if(eventObject.id !== undefined){
                                removeEventCalendar(eventObject);
                                
                            }
                            return true;
                        });
                        loadEmployeeSearchWise();
                        setTimeout(function(){
                            window.location.href='{{ url("/hr/operation/holiday-roster/create") }}';
                        }, 1000);
                    }
                    
                    setTimeout(function(){
                        $(".app-loader").hide();
                    }, 2000);
                    
                    
                },
                error: function (reject) {
                    $(".app-loader").hide();
                    // console.log(reject)
                }
            });
        }else{
            $.notify(msg, 'error');
        }

    });
    function removeEventCalendar(event) {
        console.log(event);
        $('#event-calendar').fullCalendar('removeEvents', [event._id]);
        $("#assignDates").val('');
    }
    /// Target & Current Shift

    var unit= $("#unit_shift");

    var row=$("#shifttablerow");
    ///
    
    unit.on("change", function(){
        //userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Area</th>');
        $( "#area_shift" ).prop( "disabled", false );
        // $( "#dates" ).prop( "disabled", false );

    });
    ///unit on change section enable

    $("#dayType").on("change", function(){
        // console.log($(this).val());
        if($(this).val() !== ''){
            $( "#dates" ).prop( "disabled", false );
        }else{
           $( "#dates" ).prop( "disabled", true );
        }

    });

    /// area on change department select

    $('#area_shift').on('change', function(){
        //userInfo.html('<th colspan="6" style=\"text-align: center; font-size: 14px; color: green;\">Please Choose Department</th>');
        $( "#department_shift" ).prop( "disabled", false );

        // console.log($(this).val());
        $.ajax({
            url : "{{ url('hr/timeattendance/areaDepartment') }}",
            type: 'get',
            data: {area_id : $(this).val()},
            success: function(data)
            {
                $("#department_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

    $('#department_shift').on('change', function(){

        $( "#section_shift" ).prop( "disabled", false );

        // console.log($(this).val());
        $.ajax({
            url : "{{ url('hr/timeattendance/departmentSection') }}",
            type: 'get',
            data: {department_id : $(this).val()},
            success: function(data)
            {
                $("#section_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });
    //section on change subsection enable

    $("#section_shift").on("change", function(){

        $( "#subsection_shift" ).prop( "disabled", false );

        $.ajax({
            url : "{{ url('hr/timeattendance/sectionSubsection') }}",
            type: 'get',
            data: {section_id : $(this).val()},
            success: function(data)
            {
                $("#subsection_shift").html(data);


            },
            error: function(data)
            {
                console.log(data);
            }
        });

    });

});
function formatState (state) {
 //console.log(state.element);
    if (!state.id) {
        return state.text;
    }
    var $state = $(
        '<span><img /> <span></span></span>'
    );

    var targetName = state.text;
    $state.find("span").text(targetName);
    return $state;
};
// $('select.associates').select2({
//     templateSelection:formatState,
//     placeholder: 'Select Associate\'s ID',
//     ajax: {
//         url: '{{ url("hr/associate-search") }}',
//         dataType: 'json',
//         delay: 250,
//         data: function (params) {
//             return {
//                 keyword: params.term
//             };
//         },
//         processResults: function (data) {
//             return {
//                 results:  $.map(data, function (item) {
//                     var oCode = '';
//                     if(item.as_oracle_code !== null){
//                         oCode = item.as_oracle_code + ' - ';
//                     }
//                     return {
//                         text: oCode + item.associate_name,
//                         id: item.associate_id,
//                         name: item.associate_name
//                     }
//                 })
//             };
//       },
//       cache: true
//     }
// });

$(document).ready(function() {
    var i = 0;
    var subArrayDate = new Array();
    $('#event-calendar').fullCalendar({
        // defaultDate: '2014-11-10',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        events: [{
            start: '2014-11-12T13:00:00',
            end: '2014-11-12T16:00:00',
        }, ],
        selectable: true,
        select: function (start, end, jsEvent, view) {
            var selectDate = jsEvent.target.dataset.date;
            if(selectDate !== undefined){
                subArrayDate.push(selectDate);
                $("#assignDates").val(subArrayDate);
                // console.log(selectDate);
                // console.log(subArrayDate);
                $("#event-calendar").fullCalendar('addEventSource', [{
                    _id: i,
                    selectDate: selectDate,
                    start: start,
                    end: end,
                    rendering: 'background',
                    block: true,
                }, ]);
                i++;
            }

            $("#event-calendar").fullCalendar("unselect");
        },
        selectOverlap: function(event) {
            var selectDate = event.selectDate;
            // console.log(event);
            subArrayDate.splice(subArrayDate.indexOf(selectDate), 1);
            // console.log(subArrayDate);
            $('#event-calendar').fullCalendar('removeEvents', event._id);
            $("#assignDates").val(subArrayDate);

        }
    });
    
});

$(document).ready(function() {
    $(".fc-prev-button").click(function(event) {
        return false;
    });
});
function dayType(val){
    if(val === 'Substitute-Holiday'){
        $("#comment").html('Substitute');
        $("#comment").attr('readonly', true);
    }else{
        $("#comment").html('');
        $("#comment").attr('readonly', false);
    }
}
</script>
@endpush
@endsection
