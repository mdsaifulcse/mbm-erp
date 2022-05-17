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
        #primary-additional-break .remove-additional-break{
            display: none;
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
                <li class="active"> Shift </li>
                <li class="top-nav-btn">
                    
                    <a class="btn btn-primary  btn-sm" href="{{ url('hr/operation/shift/list') }}"><i class="fa fa-list"></i></a>
                    <a class="btn btn-primary btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-users"></i> Shift Assign</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">

                    

                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-9">
                                
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group has-required has-float-label select-search-group" style="height:80px;">
                                            {{ Form::select('hr_shift_unit_id[]', $unitList, [], ['id'=>'hr_shift_unit_id', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                                            <label  for="hr_shift_unit_id"> Unit Name  </label>
                                        </div>
                                        <p class="label-head mb-3 mt-3"><strong>
                                            <i class="fa fa-clock-o text-primary"></i>
                                            &nbsp; Default Break
                                        </strong></p>
                                        <div class="portion">                   
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="text" id="hr_shift_break_time" name="hr_shift_break_time" required="required" placeholder="Break time in Minutes" value="{{ old('hr_shift_break_time') ?? 0 }}" class="form-control" onClick="this.select();" />
                                                        <label  for="hr_shift_break_time">Break Minute</label>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group has-required has-float-label">
                                                        <input type="text" name="hr_default_break_start" id="hr_default_break_start" class="time form-control" value="{{ old('hr_default_break_start') ?? '00:00:00' }}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                                        <label  for="hr_default_break_start">Start Time</label>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="extra-break-rule-div"></div>
                                                    <button  type="button" class="btn btn-custom-danger btn-danger btn-sm btn-rule" data-break="extra-break-rule-div"  style="font-size:11px;" >+ Rule</button>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group has-required has-float-label">
                                            <input type="text" name="hr_shift_name" id="hr_shift_name" placeholder="Shift Name" class="form-control" required="required" value="{{ old('hr_shift_name') }}" autocomplete="off" />
                                            <label  for="hr_shift_name" > Name  </label>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <input type="text" name="hr_shift_name_bn" id="hr_shift_name_bn" placeholder="শিফট এর নাম" class="form-control" autocomplete="off" value="{{ old('hr_shift_name_bn') }}" />
                                            <label  for="hr_shift_name_bn" > নাম (বাংলা) </label>
                                        </div> 
                                        <p class="label-head mb-3 mt-3"><strong>
                                            <i class="fa fa-money text-primary"></i>
                                            &nbsp; Bill
                                        </strong></p>
                                        <div class=""> 
                                            @foreach($bill as $k => $b)
                                                <div class="form-group custom-control custom-checkbox custom-checkbox-color-check mb-1">
                                                   <input type="checkbox" name="bill_type[]" class="custom-control-input bg-primary" id="bill-check-{{$k }}"  value="{{$k }}">
                                                   <label class="custom-control-label" for="bill-check-{{$k }}"> {{$b}}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group has-required has-float-label">
                                            <input type="text" name="hr_shift_start_time" id="hr_shift_start_time" class="time form-control" value="{{ old('hr_shift_start_time') ?? '00:00:00' }}" required="required" placeholder="--:--:--" onClick="this.select();" />
                                            <label  for="hr_shift_start_time">Start Time</label>
                                        </div>
                                        <div class="form-group has-required has-float-label">
                                            <input type="text" name="hr_shift_end_time" id="hr_shift_end_time" class="time form-control" value="{{ old('hr_shift_end_time') ?? '00:00:00' }}"  placeholder="--:--:--" onClick="this.select();" required/> 
                                            <label  for="hr_shift_end_time">End Time</label>
                                        </div>
                                        <p class="label-head mb-3 mt-3"><strong>
                                            <i class="fa fa-info-circle text-primary"></i>
                                            &nbsp; Additional  Information
                                        </strong></p>
                                        <div class="form-group has-float-label select-search-group">
                                            {{ Form::select('ot_shift', $ot_shift, null , ['id'=>'ot_shift', 'class'=> 'form-control','placeholder'=>'Select OT Shift']) }} 
                                            <label  for="ot_shift"> Include OT Shift  </label>
                                        </div>
                                        <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                           <input type="checkbox" name="hr_shift_default" class="custom-control-input bg-primary" id="customCheck-2"  value="1">
                                           <label class="custom-control-label" for="customCheck-2"> Mark as default shift</label>
                                        </div>
                                        <div class="form-group custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                           <input type="checkbox" name="ot_status" class="custom-control-input bg-primary" id="customCheck-1"  value="1">
                                           <label class="custom-control-label" for="customCheck-1"> Mark as full OT</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-12">
                                        <p class="label-head mb-3 mt-3"><strong>
                                            <i class="fa fa-clock-o text-primary"></i>
                                            &nbsp; Additional Break
                                        </strong></p>
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
                                                        <button type="button" class="btn btn-danger btn-sm remove-additional-break">
                                                            <i class="fa fa-trash "></i>
                                                        </button>
                                                        <button  type="button" class="btn btn-custom-danger btn-primary btn-sm btn-additional-break" >+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group text-right">
                                            
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-3 " style="border-left:1px solid #d1d1d1">
                                <p class="mb-3"><strong>
                                    <i class="fa fa-history text-primary" aria-hidden="true"></i>
                                    &nbsp; History
                                </strong></p>
                                <p>No history found!</p>
                                
                            </div>

                        </div>
                        
                    </form>
                    
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

function generateContent(arr, id = null)
{
    let pt  = (id?'additional_break_rule['+id+']':'break_rule');
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
$(document).on('click', '.remove-additional-break', function(){
    if(confirm('Are you sure?')){
        $(this).parent().parent().remove();
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

