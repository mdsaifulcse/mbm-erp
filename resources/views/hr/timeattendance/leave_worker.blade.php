@extends('hr.layout')
@section('title', 'Employees Leave')
@section('main-content')
@push('css')
<style type="text/css">
    .widget-box{border-radius: 5px;}
    #toast-container>div{opacity: 0.95!important;}
    .history-title{
        box-shadow: 0px 4px 10px 5px #ece7e7;
        padding: 5px;
    }
    .notifyjs-wrapper {
        z-index: 100!important;
        bottom: 0!important;
    }
    b.h-text-left {
        width: 100px;
        display: inline-block;
    }
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Employee's Leave </li>

                <li class="top-nav-btn">
                    <a href="{{ url('hr/timeattendance/all_leaves') }}" class="btn btn-sm btn-primary pull-right" rel='tooltip' data-tooltip-location='left' data-tooltip='Leave List'>
                            List <i class="fa fa-list"> </i>
                        </a>
                </li>
            </ul>
        </div>
        @include('inc/message')
        <div class="page-content">
            <div class="panel panel-success">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <i class="fa fa-info-circle text-primary "></i> 
                            Leave Information
                        </div>
                        <div class="col-sm-5" style="padding-top: 10px;border-right: 1px solid #d1d1d1;">
                            {{ Form::open(['url'=>'hr/timeattendance/leave_worker', 'class'=>'form-horizontal needs-validation', 'files' => true, 'novalidate']) }}

                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('leave_ass_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'leave_ass_id', 'class'=> 'associates form-control', 'required'=>'required']) }}
                                    <label for="leave_ass_id"> Associate's ID </label>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-group has-required has-float-label select-search-group mb-0">
                                            <select name="leave_type" id="leave_type" class="form-control"  required="required" >
                                                <option value="">Select Leave Type</option>
                                                <option value="Casual">Casual</option>
                                                <option value="Earned">Earned</option>
                                                <option value="Sick">Sick</option>
                                                <option value="Special">Special</option>
                                            </select>
                                            <label for="leave_type">Leave Type</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group has-required has-float-label mb-0">
                                            <input type="date" name="leave_applied_date" id="leave_applied_date" class="form-control" required placeholder="YYYY-MM-DD"   value="{{date('Y-m-d')}}"/>
                                            <label  for="leave_applied_date"> Applied Date  </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 mb-3">
                                        <i class="fa fa-list text-primary "></i> 
                                        Find leave eligible date
                                    </div>
                                    <div class="col-6">

                                        <div class="form-group has-required has-float-label mb-0">
                                            <input type="date" name="leave_from" id="leave_from" class="form-control" required />
                                            <label  for="leave_from">From Date </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div id="multipleDateAccept" class="form-group has-required has-float-label mb-0">

                                            <input type="date"  name="leave_to" id="leave_to" class="form-control" required />
                                            <label  for="leave_from">To Date </label>
                                        </div>

                                    </div>
                                    <div class="col-sm-12">
                                        <p id="select_day" class="text-success"></p>
                                        <p id="error_leave_text" class="text-danger"></p>
                                    </div>
                                </div>
                                <div id="leave-date" class="p3">
                                    
                                </div>
                                <div class="form-group  file-zone mb-0">
                                    <label  for="file"> Supporting File </label>
                                    <input type="file" name="leave_supporting_file" class="file-type-validation" data-file-allow='["docx","doc","pdf","jpeg","png","jpg"]' autocomplete="off" />
                                    <div class="invalid-feedback" role="alert">
                                        <strong>Select a file</strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <span id="file_upload_error" class="text-danger" style="; font-size: 12px;">Only <strong>docx, doc, pdf, jpeg, jpg or png</strong> file supported(<1 MB).</span>

                                </div>

                                <div class="form-group has-float-label">
                                    <label for="leave_comment"> Note </label>
                                    <textarea name="leave_comment" id="leave_comment" class="form-control" placeholder="Description"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn  btn-primary" type="submit" id="leave_entry" disabled="disabled">
                                        <i class="fa fa-check bigger-110"></i> Submit
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn " type="reset">
                                        <i class="fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            {{ Form::close() }}
                        </div>

                        <div class="col-sm-7 pt-3">
                            <div class="row" id="associates_leave">
                                <div class="col-sm-5">
                                    <div class="user-details-block benefit-employee">
                                        <div class="user-profile text-center mt-0">
                                            <img id="avatar" class="avatar-130 img-fluid" src='{{asset("assets/images/user/09.jpg")}}'>
                                        </div>
                                        <p class="mt-3 text-center">
                                            <b class="text-primary f-16">--------</b> 
                                        </p>
                                        
                                    </div>
                                </div>


                                <div class="col-sm-7">
                                    <div class="mt-3">
                                        <p><b class="h-text-left">Name </b>: .......................... </b></p>
                                        <p class="mb-0" >
                                            <b class="h-text-left">Designation </b>: .......................... 
                                        </p>
                                        <p class="mb-0" >
                                            <b class="h-text-left">Department </b>: ..........................
                                        </p>
                                        <p class="mb-0" >
                                            <b class="h-text-left">Unit </b>: ..........................
                                        </p>
                                        
                                        <p class="mb-0" ><b class="h-text-left">Date of Join </b>: .......................... </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
<script type="text/javascript">
$(document).ready(function()
{
    $.notify.defaults({globalPosition: 'bottom right'})
    var balance = '';
    const sel_type = $("#leave_type"),
          sel_associate = $("#leave_ass_id"),
          sel_from = $('#leave_from'),
          sel_to   = $('#leave_to'),
          sel_day  = $('#select_day');

    function resetForm()
    {
        sel_from.val(''),sel_to.val(''),
        sel_day.html(''),$('#leave-date').html('')
        $('#error_leave_text').html(''),
        $('#leave_entry').attr("disabled",true);
    }

    sel_associate.on("change", function(e){
        if((sel_associate.val())){
            $('.app-loader').show();
            $.ajax({
                url: '{{ url("hr/timeattendance/get-leave-balance") }}',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    associate_id: sel_associate.val()
                },
                success: function(res)
                {
                    balance = res.balance
                    $('#associates_leave').html(res.view)
                    sel_type.val('').trigger('change')
                    resetForm()
                    $('.app-loader').hide();
                },
                error: function(xhr)
                {}
            });
        }
    });


    sel_type.on("change", function(e){
        var type = sel_type.val().toLowerCase();
        if(type){   
            if(!sel_associate.val()){
                sel_associate.notify('Please Select Associate ID!','error');
                resetForm()
                sel_type.val('').trigger('change')
            }else if(type != 'special'){
                bl = balance[type].total - balance[type].enjoyed;
                if(bl < 1){
                    $(this).notify('This employee has no available '+type+' leave','error');
                    resetForm()
                    sel_type.val('').trigger('change')
                }
            }
        }
        
    });

    

    $(document).on('change','#leave_from',function(){
        var leave_from = sel_from.val();
        sel_to.val(leave_from).attr('min',leave_from);
    });

    function calculateDays(from, to){
        from = new Date(from),
        to   = new Date(to);
        let diffTime = Math.abs(to.getTime() - from.getTime()),
            diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return (diffDays + 1);
    }

    function checkLength(associate_id, leave_from, leave_to, type, days)
    {
        
        $.ajax({
            url: '{{ url("hr/timeattendance/split-leave-days") }}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                associate_id: associate_id,
                from_date: leave_from,
                to_date: leave_to,
                type: type,
                days: days
            },
            success: function(res)
            {
                $('#leave-date').html(res);
            },
            error: function(xhr)
            {
            }
        });
    }

    function checkBalance(type, days)
    {
        if(type != 'special'){
            bl = balance[type].total - balance[type].enjoyed;
            if(bl < days){
                return 'This employee has available '+bl+' days of '+type+' leave!';
            }
        }
        return 'success';
        
    }

    $(document).on('click', '#day-group', function(){
        var checked = $(this).is(":checked");
        $('.leave-date-item:not(:disabled)').prop('checked',checked)
        $('#selLeaveDays').html($('.leave-date-item:checked').length)

    })
        


    $(document).on('change','#leave_from,#leave_to', function(){
        var leave_from = sel_from.val(),
            leave_to = sel_to.val(),
            associate_id = sel_associate.val(),
            type = sel_type.val().toLowerCase();

        if(associate_id && type && leave_from){
            var days = calculateDays(leave_from,leave_to);
            if(isNaN(days)){
                sel_day.html('');
                $('#error_leave_text').html('');
            }else{
                var fm = 'You have selected  <span style="color: #ff0909;font-weight:600;">'+days+'</span> day(s).';
                sel_day.html(fm);
                var msg = checkBalance(type, days)
                //var msg = checkLength(associate_id, leave_from, leave_to, type, days);
                if(msg == 'success'){
                    $('#leave-date').html(loaderContent)
                    // go to the next step
                    checkLength(associate_id, leave_from, leave_to, type, days)
                    sel_day.html('')
                    $('#leave_entry').attr("disabled",false);
                }else{
                    sel_day.html(fm+'<br><span class="text-danger">'+msg+'</span>')
                    $('#leave_entry').attr("disabled",true);
                    $('#leave-date').html('')
                }
            }
        }else{
            sel_associate.notify('Please select Associate ID and Leave type')
            $('#leave_from').val(''),$('#leave_to').val(''),$('#leave-date').html('')
        }
    });

});

</script>
@endpush

@endsection
