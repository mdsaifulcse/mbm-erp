@extends('hr.layout')
@section('title', 'Holiday Planner Create')
@section('main-content')
@push('css')
    <style>
        .list-group-flush > .list-group-item {border-width: thin;}
        .min-h-62{min-height: 62px;}
        .list-group-flush > .list-group-item:last-child {border-bottom-width: thin;}
        .type{width: 100%; padding: 5px; border-radius: 5px;}
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
				<li class="#"> Holiday Planner</li>
                <li class="active"> Create</li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/operation/holiday-planner')}}" class="pull-right btn btn-sm btn-success"><i class="fa fa-list"></i> Holiday list</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>
		<div class="page-content"> 
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="">
                        <form class="form-horizontal" role="form" id="formData">
                            {{ csrf_field() }}
                            <div class="row justify-content-center">
                                <div class="col-sm-8">
                                    <div class="form-section">
                                        <div class="row">
                                            <div class="col-sm-4 pr-0">
                                                <div class="row">
                                                    <div class="col-sm-12 pr-0">
                                                        {{-- <label>Amount Type</label><br> --}}
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                           <input type="radio" id="per_month" name="type_option" class="holiday_type custom-control-input" value="monthly" checked>
                                                           <label class="custom-control-label" for="per_month"> Monthly </label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                           <input type="radio" id="per_year" name="type_option" class="holiday_type custom-control-input" value="yearly">
                                                           <label class="custom-control-label" for="per_year"> Yearly </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group  has-required has-float-label" id="target-per-month" style="margin-top: 3px;">
                                                            <input type="month" class="report_date form-control" id="year_month" name="year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m')}}"autocomplete="off" />
                                                            <label for="year_month"> Month </label>
                                                        </div>
                                                        <div class="form-group has-float-label has-required" id="target-per-year" style="display: none;margin-top: 3px;">
                                                            <input type="year" class="report_date form-control" id="year" name="year" placeholder=" Year"required="required" value="{{ date('Y')}}"autocomplete="off" />
                                                            <label for="year"> Year </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 pr-0">
                                                <div class="custom-control custom-radio custom-control-inline"></div>
                                                <div class="form-group has-float-label has-required select-search-group">
                                                    <select name="unit[]" class="form-control capitalize select-search" id="unit" required="" multiple="">
                                                        @foreach($unitList as $key => $value)
                                                        <option value="{{ $key }}" selected="">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                  <label for="unit">Unit (Multiple)</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2"></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class=" justify-content-center">
                                <!-- PAGE CONTENT BEGINS -->
                                
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="ml-5">
                                            <legend>Weekday</legend>
                                            <div class="form-group">
                                                <div class="">
                                                    <div class="control-group"> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Saturday" class="ace">
                                                                <span class="lbl"> Saturday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Sunday" class="ace">
                                                                <span class="lbl"> Sunday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Monday" class="ace">
                                                                <span class="lbl"> Monday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Tuesday" class="ace">
                                                                <span class="lbl"> Tuesday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Wednesday" class="ace">
                                                                <span class="lbl"> Wednesday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Thursday" class="ace">
                                                                <span class="lbl"> Thursday</span>
                                                            </label>
                                                        </div> 
                                                        <div class="checkbox custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                            <label>
                                                                <input name="weekdays[]" type="checkbox" value="Friday" class="ace">
                                                                <span class="lbl"> Friday</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-9 pl-0">
                                        <div class="row" id="weekendData">
                                        </div>
                                    </div>
                                </div>
                                
                            <!-- PAGE CONTENT ENDS -->
                            </div>
                            <div class="row justify-content-center">
                                <div class="col">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h5>Other Holiday</h5>
                                        </div>
                                        <div class="panel-body">
                                            <div class='table-wrapper-scroll-y table-custom-scrollbar'>
                                                <table class="table table-bordered table-hover table-fixed table-head table-responsive" id="itemList">
                                                    <thead>
                                                        <tr class="text-center active">
                                                            <th width="2%">
                                                                <button class="btn btn-sm btn-outline-success addmore" type="button" ><i class="fa fa-plus"></i></button>
                                                            </th>
                                                            <th width="2%">SL.</th>
                                                            <th width="15%">Holiday Name</th>
                                                            <th width="10%">Date</th>
                                                            <th width="8%">Type</th>
                                                            <th width="17%">Reference Comment</th>
                                                            <th width="10%">Reference date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="table-body">
                                                      
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="employee-select"></div>
                                            <div class="bottom-section pt-3" style="overflow: hidden;">
                                              
                                              <button type="button" class="btn btn-md btn-outline-primary pull-right" onclick="saveHoliday()"> <i class="fa fa-save"></i> Save </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript">  
$(document).ready(function() {
    $('.checkbox').on('change',function(e){
        var monthYear= $('#year_month').val();
        var year = $("#year").val();
        var type = $("input[name='type_option']:checked").val();
        var chkArray = [];
        $('#weekendData').html(loaderContent)
        $(".ace:checked").each(function() {
            chkArray.push($(this).val());
        }); 

        $.ajax({
            url: '{{ url("/hr/operation/planner-day-wise-date") }}',
            method: "GET",
            data: {
                'year_month': monthYear,
                'year': year,
                'weekdays': chkArray,
                'type': type
            },
            success: function(data)
            {
                // console.log(data);
                if(data.type === 'success'){
                    $("#weekendData").html(data.value);
                }else{
                    $.notify(data.message, 'error');
                    $("#weekendData").html('');
                }
            }
        });
    });
});
function saveHoliday(){
    $(".app-loader").show();
    var data = $("#formData").serialize();
    let url = '{{ url('hr/operation/holiday-planner') }}'
    $.ajax({
      type: 'POST',
      url: url,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      data: data, // serializes the form's elements.
      success: function(response)
      {
        // console.log(response)
        if(response.type === 'success'){
            setTimeout(function(){
                window.location.href = response.url;
            }, 500);
        }
        $.notify(response.message, response.type);
        $(".app-loader").hide();
        
      },
      error: function (reject) {
        $.notify('Something wrong, please try again!', 'error');
        $(".app-loader").hide();
      }
    });
}
var i=$('#table-body tr').length;
$(".addmore").on('click',function(){
    // check exists empty item
    var lastId = i;
    var lastItem = $("#name_"+lastId).val();
    if(lastItem !== ''){
        ++i;
        html = '<tr id="itemRow_'+i+'">';
        html += '<td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem'+i+'" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td>';
        html += '<td class="index">'+i+'</td>';
        html += '<td><input type="text" name="name[]" class="form-control" id="name_'+i+'"></td>';
        html += '<td><input type="date" value="" class="form-control" name="another_date[]" id="anotherdate_'+i+'"></td>';
        html += '<td><select name="another_type[]" id="anothertype_'+i+'" class="type"><option value="1">Holiday</option><option value="2">Festival</option></select></td>';
        html += '<td><input type="text" value="" class="form-control" id="refcomment_'+i+'" name="ref_comment[]"></td>';
        html += '<td><input type="date" value="" class="form-control" id="refdate_'+i+'" name="ref_date[]"></td>';
        
        
        html += '</tr>';
        $('#table-body').append(html);
        $('#name_'+i).focus();
        autoIndexing();
        //i++;
    }else{
        $('#name_'+lastId).focus();
    }
    
});
function autoIndexing(){
    let ind = 1;
    $('.index').each(function() {
        $(this).html(ind);
        ind++
    });
}
function deleteItem(itemId) {
    $("#"+itemId).parent().parent().remove();
    autoIndexing();
}
$(document).on('change','.holiday_type', function(){
    if($(this).val() == 'monthly'){
        $('#target-per-month').show();
        $('#target-per-year').hide();
        var target = '{{ date('Y-m') }}';
        $("#year_month").val(target);
    }else{
        $('#target-per-month').hide();
        $('#target-per-year').show();
        var target = '{{ date('Y') }}';
        $("#year").val(target);
    }
    $("#weekendData").html('');
    $('.ace').each(function() {
        $(this).prop("checked", false);
    });
});
</script>
@endpush
@endsection
