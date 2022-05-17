@extends('hr.layout')
@section('title', ' Shift')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
    <style>
        .iq-accordion-block{
            padding: 10px 0;
        }
        .iq-accordion.career-style .iq-accordion-block {
            margin-bottom: 15px;
        }
        .select2-container--default .select2-selection--multiple {height: 85px;}
        .portion .form-control{background: #fff !important;margin-bottom: 5px;}
        .portion .form-group{margin-bottom: 10px;}
        .portion .has-float-label label { color: #000 !important;}
        .w-80px{width: 80px!important;}
        .extra-break-json {
            padding: 5px;
            background: #dffafc;
            margin-bottom: 3px;
            position: relative;
        }
        .close span {
            font-size: 24px;
        }
        .remove-item {
            color: #ff0000;
            cursor: pointer;
            position: absolute;
            top: 5px;
            right: 5px;
        }
        .label-head{
            background: #ffefef;
            padding: 3px 5px;
        }

        
        .btn-special{
            margin-top: 10px;
            border: 1px solid #089eaf;
            border-radius: 20px;
            padding: 2px 15px;
            color: #089eaf;
            text-transform: uppercase;
        }
        .btn-special:hover{
            color: #fff;
            background: #089eaf;
        }
        .panel-heading {padding: 5px 15px !important;}

        .time-format span {
            font-family: monospace;
            font-size: 17px;
            margin-right: 0;
            border-radius: 3px;
            color: #fff;
            background: #454545;
            text-align: center;
            padding: 0;
            line-height: 1.3;
            margin: 0;
            display: inline-block;
            width: 85px;
        }
        b.shift-label {
            width: 90px;
            display: inline-block;
        }
        .time-format i{
            font-size: 20px;
            display: inline-block;
            text-align: center;
        }
        .s-second-label{
            font-size: 13px;
            display: inline-block;
            width: 100px;
    }

    </style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class=""> Shift </li>
                <li class="active"> {{$shift->hr_shift_name}} </li>
                <li class="top-nav-btn">
                    
                    <a class="btn btn-primary pull-right btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-users"></i> Shift Edit</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-sm-4 pr-0">
                    <div class="panel">
                        <div class="panel-heading text-left">
                            <h6>{{$shift->hr_shift_name}}</h6>
                        </div>
                        <div class="panel-body">

                            @include('hr.setup.shift.shift_intro')

                            <b class="d-block mb-2 mt-3 text-primary"> <i class="las la-arrow-right" style="font-size: 20px;vertical-align: middle;"></i> History</b>

                            @include('hr.setup.shift.shift-history')

                        </div>
                    </div>

                </div>
                <div class="col-sm-8 ">
                    <div class="row">
                        <div class="col-sm-9">
                            
                            <div class="panel">
                                <div class="panel-heading">
                                    <h6>Change Shift Time </h6>
                                </div>
                                <div class="panel-body " style="min-height: 200px;padding: 10px 15px;">
                                    <form action="{{ url('hr/operation/shift/update-time/'.$shift->hr_shift_id) }}" method="POST" enctype="multipart/form-data"  class="needs-validation form" novalidate >
                                        @csrf 

                                        <div class="row">
                                            <div class="col-sm-7">
                                                <b class="d-block mb-3"> <i class="las la-arrow-right" style="font-size: 20px;vertical-align: middle;"></i> Shift</b>
                                                <div class="row">
                                                    
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-required has-float-label">
                                                            <input type="text" name="hr_shift_start_time" id="hr_shift_start_time" class="time form-control" value="{{$shift->current_shift_time->hr_shift_start_time ?? ''}}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                                            <label  for="hr_shift_start_time">Start Time</label>
                                                        </div>
                                                        <div class="form-group has-float-label has-required">
                                                            <input type="date" name="hr_shift_start_date" id="hr_shift_start_date" class=" form-control" required="required" />
                                                            <label  for="hr_shift_start_date">Start Date</label>
                                                        </div>
                            
                                                        
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-required has-float-label">
                                                            <input type="text" name="hr_shift_end_time" id="hr_shift_end_time" class="time form-control" value="{{$shift->current_shift_time->hr_shift_end_time ?? ''}}"  placeholder="--:--:--" onClick="this.select();" required/> 
                                                            <label  for="hr_shift_end_time">End Time</label>
                                                        </div>
                                                        <div class="form-group has-float-label">
                                                            <input type="date" name="hr_shift_end_date" id="hr_shift_end_date" class=" form-control"  />
                                                            <label  for="hr_shift_end_date">End Date</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <b class="d-block mb-3"> <i class="las la-arrow-right" style="font-size: 20px;vertical-align: middle;"></i> Break</b>
                                                <div class="portion">                   
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group has-required has-float-label">
                                                                <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" required="required" placeholder="Break time in Minutes" value="{{ $shift->hr_shift_break_time}}" class="form-control" onClick="this.select();" />
                                                                <label  for="hr_shift_break_time">Break Minute</label>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group has-required has-float-label">
                                                                <input type="text" name="hr_default_break_start" id="hr_default_break_start" class="time form-control" value="{{$shift->current_shift_time->hr_break_start_time ?? ''}}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                                                <label  for="hr_default_break_start">Start Time</label>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-sm-12">
                                                            @if(count($shift->current_break_rules ) > 0)
                                                                <b>Custom Rules</b>
                                                                @foreach($shift->current_break_rules as $k => $rules)
                                                                <div class="break-rule p-2 mb-2" style="background: rgb(254 237 233);">
                                                                    <b>Break:</b> {{$rules->break_time}} Minute(s)
                                                                    @if($rules->designations)
                                                                       <p><b> for</b> 
                                                                        @foreach(explode(",",$rules->designations) as $k => $des)
                                                                            <span class="badge badge-primary">{{$designation[$des]??''}}</span>
                                                                        @endforeach
                                                                        <p>
                                                                    @endif
                                                                    @if($rules->days)
                                                                       <p><b> on </b>
                                                                        @foreach(explode(",",$rules->days) as $k => $day)
                                                                            <span class="badge badge-primary">{{$day}}</span>
                                                                        @endforeach
                                                                        <p>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            @endif
                                                            <div class="extra-break-rule-div"></div>
                                                            <button  type="button" class="btn btn-custom-danger btn-danger btn-sm btn-rule" data-break="extra-break-rule-div"  style="font-size:11px;" >+ Add Break Rules</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="col-sm-5">
                                                <b class="d-block mb-3"> <i class="las la-arrow-right" style="font-size: 20px;vertical-align: middle;"></i> Additional  Info</b>
                                                <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                   <input type="checkbox" name="hr_shift_default" class="custom-control-input bg-primary" id="customCheck-2"  value="1">
                                                   <label class="custom-control-label" for="customCheck-2"> Mark as default shift</label>
                                                </div>
                                                <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                   <input type="checkbox" name="ot_status" class="custom-control-input bg-primary" id="customCheck-1"  value="1">
                                                   <label class="custom-control-label" for="customCheck-1"> Mark as full OT</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    {{ Form::select('ot_shift', $ot_shift, null , ['id'=>'ot_shift', 'class'=> 'form-control','placeholder'=>'Select OT Shift']) }} 
                                                    <label  for="ot_shift"> Include OT Shift  </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-special">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 pl-0">
                            <div class="panel">
                                <div class="panel-heading text-center">
                                    <h6>Bill</h6>
                                </div>
                                <div class="panel-body " style="min-height: 200px;padding: 10px 15px;">
                                    <form action="{{ url('hr/operation/shift/sync-bill/'.$shift->hr_shift_id) }}" method="POST" enctype="multipart/form-data"  class="needs-validation form" novalidate >
                                        @csrf 
                                        @foreach($bill as $k => $b)
                                            <div class="form-group custom-control custom-checkbox custom-checkbox-color-check mb-1">
                                               <input type="checkbox" name="bill_type[]" class="custom-control-input bg-primary" id="bill-check-{{$k }}"  value="{{$k }}" @if(in_array($k, $shift->current_bills)) checked @endif >
                                               <label class="custom-control-label" for="bill-check-{{$k }}"> {{$b}}</label>
                                            </div>
                                        @endforeach
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-special">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            
                        </div>
                        <div class="col-sm-12">
                            <div class="panel">
                                <div class="panel-heading text-left">
                                    <h6>Additional  Break</h6>
                                </div>
                                <div class="panel-body" >
                                    <div class="additional-break-parent">
                                        <div id="primary-additional-break">
                                            <div  class="row additional-break-child">
                                                <div class="col-sm-2 pr-0">
                                                    <div class="form-group  has-float-label">
                                                        <input type="text" id="additional_break_time" name="additional_break_time[]" placeholder="Break time in Minutes" value="" class="form-control" onClick="this.select();" />
                                                        <label  for="additional_break_time">Break Minute</label>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-2 pr-0">
                                                    <div class="form-group  has-float-label">
                                                        <input type="text" name="additional_break_start[]" id="additional_break_start[]" class="time form-control" value="" placeholder="--:--:--" onClick="this.select();" />
                                                        <label  for="additional_break_start">Start Time</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group has-float-label">
                                                        <input type="date" name="additional_break_start_date[]" id="additional_break_start_date" class=" form-control" value="" />
                                                        <label  for="additional_break_start_date">Start Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group has-float-label">
                                                        <input type="date" name="additional_break_end_date[]" id="additional_break_end_date" class=" form-control" value="" />
                                                        <label  for="additional_break_end_date">End Date</label>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-sm-2 pl-0">
                                                    <button class="btn btn-danger btn-sm remove-additional-break">
                                                        <i class="fa fa-trash "></i>
                                                    </button>
                                                    <button  type="button" class="btn btn-custom-danger btn-primary btn-sm btn-additional-break" >+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-special">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="panel">
                        <div class="panel-heading text-center">
                            <h6>History</h6>
                        </div>
                        <div class="panel-body">
                            <p>No history found!</p>
                        </div>
                    </div> --}}
                                
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

{{-- include  --}}
@include('hr.setup.shift.break_rule_modal')
    
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">

const designation = JSON.parse('{!!json_encode($designation)!!}');
const pab = $('#primary-additional-break').html();
var br = 0;

// sum two time ex: 12:00:00+11:30:00
function additionTime() {
    var arr = [];
    $.each(arguments, function() {
        $.each(this.split(':'), function(i) {
            arr[i] = arr[i] ? arr[i] + (+this) : +this;
        });
    })
    return arr.map(function(n) {
        return n < 10 ? '0'+n : n;
    }).join(':');
}

$(document).on('click', '.btn-rule',function(){
    $('#extra_rule').modal('show');
    $('#add-break').attr('data-div', $(this).data('break'));
})

$(document).on('click', '.btn-additional-break',function(){
    $('.additional-break-parent').append(pab)
})

function generateContent(arr, id)
{
    let pt  = (id == ''?'break_rule':'additional_break_rule['+id+']'),
        cnt = '<div class="extra-break-json">';
    cnt += '<p><span class="d-inline-block w-80px">Break </span> : ' +arr.rule_break_time+' Min</p>';
    if(typeof arr.rule_days !== 'undefined'){ 
        cnt += '<p><span class="d-inline-block w-80px" >Days</span> : '+arr.rule_days.join()+'</p>';
        cnt += '<input type="hidden" name="'+pt+'['+br+'][days]" value="'+arr.rule_days.join()+'">';
    }
    if(typeof arr.rule_designation !== 'undefined'){ 
        cnt += '<p><span class="d-inline-block w-80px" >Designations</span> : ';
        $.each(arr.rule_designation, function(i,v){
            cnt += '<span class="badge badge-success">'+designation[v]+'</span> ';
        })
        cnt +='</p>';
        cnt += '<input type="hidden" name="'+pt+'['+br+'][designations]" value="'+arr.rule_designation.join()+'">';
    }
    if(arr.rule_break_start){ 
        cnt += '<p><span class="d-inline-block w-80px" >Break Start</span> : ' +arr.rule_break_start+'</p>';
        cnt += '<input type="hidden" name="'+pt+'['+br+'][break_start]" value="'+arr.rule_break_start+'">';
    }
    if(arr.rule_start_date){ 
        cnt += '<p><span class="d-inline-block w-80px" >Start Date</span> : ' +arr.rule_start_date+'</p>';
        cnt += '<input type="hidden" name="'+pt+'['+br+'][start_date]" value="'+arr.rule_start_date+'">';
    }
    if(arr.rule_end_date){ 
        cnt += '<p><span class="d-inline-block w-80px" >End Date</span> : ' +arr.rule_end_date+'</p>';
        cnt += '<input type="hidden" name="'+pt+'['+br+'][end_date]" value="'+arr.rule_end_date+'">';
    }
    cnt += '<input type="hidden" name="'+pt+'['+br+'][break_time]" value="'+arr.rule_break_time+'">';
    cnt += '<i class="fa fa-trash remove-item"></></div>';

    br++;
    return cnt;
    
}

$(document).on('click', '.remove-item', function(){
    if(confirm('Are you sure?')){
        $(this).parent().remove();
    }
})


$(document).on('click','#add-break', function(){
    if($('#rule_break_time').val() == ''){
        $.notify('Please insert break minute first!','error')
    }else{ 
        let fm   = $('.extra-break'),    
            data = fm.serializeArray().reduce(function (newData, item) {
                // Treat Arrays
                if (item.name.substring(item.name.length - 2) === '[]') {
                    let key = item.name.substring(0, item.name.length - 2);
                    if(typeof(newData[key]) === 'undefined') {
                        newData[key] = [];
                    }
                    newData[key].push(item.value);
                } else {
                    newData[item.name] = item.value;
                }
                return newData;
            }, {}),
            t  = $(this).data('div'),
            ct = generateContent(data, $(this).data('id'))
        if(ct != ''){
            $('#extra_rule').modal('hide'), $('.'+t).append(ct),
            $('form.extra-break').trigger("reset"); //Line1
            $('form.extra-break select').trigger("change");
            $(this).attr('data-div', '').attr('data-id', '');
        }
    }
});



// convert min to hour:min
function convertMinsToHrsMins(mins) {
    let h = Math.floor(mins / 60);
    let m = mins % 60;
    h = h < 10 ? '0' + h : h;
    m = m < 10 ? '0' + m : m;
    return `${h}:${m}`;
}

$('#hr_shift_end_time, #hr_shift_break_time').on('keyup', function() {
    var breakTime = $('#hr_shift_break_time').val()==''?0:$('#hr_shift_break_time').val();
    var endTime = $('#hr_shift_end_time').val()==''?0:$('#hr_shift_end_time').val();
    var sum = (moment.utc(endTime,'HH:mm').add(breakTime,'minutes').format('HH:mm:ss'));
    // var sum = additionTime(endTime,convertMinsToHrsMins(breakTime));
    $('#hr_shift_out_time').val(sum);
});

    $(document).on('click','.add-extra-break-rule', function(){
        let rules = $('#shift-break-rules').html(),
            strndom = btoa(Math.random()).substr(3, 13);

        $(this).prev().append(rules);
        // modify id with a random string
        $(this).prev().children().last().find('[id]').map(function(q, i) {
            $(this).attr('id', $(this).attr('id')+strndom)
        });
        $(this).prev().children().last().find('[for]').map(function(q, i) {
            $(this).attr('for', $(this).attr('for')+strndom)
        });
    });

    $(document).ready(function(){
        // Show Line List by Unit ID
        var unit  = $("#hr_shift_unit_id");
        var floor = $("#hr_shift_floor_id");
        unit.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getFloorListByUnitID') }}",
            type: 'json',
            method: 'get',
            data: {unit_id: $(this).val() },
            success: function(data)
            {
                floor.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    // Show Line List by Floor ID
    var unit = $("#hr_shift_unit_id");
    var floor = $("#hr_shift_floor_id");
    var line = $("#hr_shift_line_id");
    floor.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getLineListByFloorID') }}",
            type: 'json',
            method: 'get',
            data: {unit_id: unit.val(), floor_id: $(this).val() },
            success: function(data)
            {
                line.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    $('body').on('click','.shift_times_btn', function(){
        var shift_code = $(this).val();
        // console.log(shift_code);
        $('#modal_table_body').html('');
        $.ajax({
            url : "{{ url('hr/setup/get_presfhit_times') }}",
            type: 'json',
            method: 'get',
            data: {shift_code: shift_code },
            success: function(data)
            {
                // console.log(data);
                var number_pos = shift_code.length;
                var push_html="";
                for(var i=0; i<data.length; i++){

                    var the_code = data[i]['hr_shift_code'];
                    var check    = the_code[number_pos];
                    
                    // console.log(check);
                    // console.log(!isNaN(check));

                    if(typeof check == 'undefined'){
                        push_html +="<tr>"+
                        "<td>"+data[i]['hr_shift_name']+"</td>"+
                        "<td>"+data[i]['hr_shift_code']+"</td>"+
                        "<td>"+data[i]['hr_shift_start_time']+"</td>"+
                        "<td>"+data[i]['hr_shift_end_time']+"</td>"+
                        "<td>"+data[i]['hr_shift_break_time']+" min</td>"+
                        "<td>"+data[i]['created_at']+"</td>"+
                        "</tr>";
                    }
                    else{

                        if(!isNaN(check)){
                            push_html +="<tr>"+
                            "<td>"+data[i]['hr_shift_name']+"</td>"+
                            "<td>"+data[i]['hr_shift_code']+"</td>"+
                            "<td>"+data[i]['hr_shift_start_time']+"</td>"+
                            "<td>"+data[i]['hr_shift_end_time']+"</td>"+
                            "<td>"+data[i]['hr_shift_break_time']+" min</td>"+
                            "<td>"+data[i]['created_at']+"</td>"+
                            "</tr>";       
                        }
                    }
                }
                $('#modal_table_body').html(push_html);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
    $('.time').datetimepicker({
      format:'HH:mm:ss',
      allowInputToggle: false
    });
    
});
</script>
@endpush
@endsection

