@php 
	$salaryMax = get_salary_max();
@endphp
@extends('hr.layout')
@section('title', 'Attendance Summary Report')
@push('css')
	<style type="text/css">
		.select2-container .select2-selection--multiple{
			height: 85px;
		}
		.popover-block-container .popover-icon {
		  background: none;
		  color: none;
		  border: none;
		  padding: 0;
		  outline: none;
		  cursor: pointer;
		}
		.popover-block-container .popover-icon i {
		  color: #04a0b2;
		  text-align: center;
		  margin-top: 4px;
		}

		.popover-header {
		  display: none;
		}

		.popover {
		  max-width: 306.6px;
		  border-radius: 6px;
		  border: none;
		  box-shadow: 2px 2px 10px 2px #d1d1d1;
		  color: #000;
		  width: 400px;

		}
		.popover-body hr{
			border-color: #000 !important;
		}
		.popover-body br{
			content: "";
			  margin: 2em;
			  display: block;
  			font-size: 35%;
		}


		.popover-body {
		    padding: 20px 49.4px 24px 24px;
		    z-index: 2;
		    line-height: 1.53;
		    letter-spacing: 0.1px;
		    font-size: 10px !important;
		    width: 400px;
		    
		}
		.popover-body .popover-close {
		  position: absolute;
		  top: 5px;
		  right: 10px;
		  opacity: 1;
		}
		.popover-body .popover-close .fa {
		  font-size: 16px;
		  font-weight: bold;
		  color: #04a0b2;
		}
		#right_modal_lg_drawer_emplist .table-title{
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
	                    <a href="#">Human Resource</a>
	                </li>
	                <li>
	                    <a href="#">Reports</a>
	                </li>
	                <li class="active">Attendance Summary Report</li>
	            </ul>
	        </div>
	    </div>
	    <div class="panel" style="position:relative;">
	    	<div class="panel-heading">
	    		<form id="formReport" role="form" method="get" action="{{ url("hr/reports/get_att_summary_report") }}" >
                    @csrf
		    		<div class="row pl-0">
		    			<div class="col-sm-6">
		    				<button type="button" class="btn btn-sm btn-primary hidden-print"
                                onclick="printDiv('attendance-content')" data-toggle="tooltip"
                                data-placement="top" title=""
                                data-original-title="Print Report" style="font-size: 12px;"><i
                                class="las la-print"></i></button>
		    			</div>
		                <div class="col-sm-6 text-right">
		                	
		                  	<a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
		                    	<i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
		                  	</a>
		                  	<a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
		                    	<i class="fa fa-filter"></i>
		                  	</a>
		                </div>
		            </div>
	            </form>
                        
	    	</div>
	    	<div class="panel-body">
	    		<div class="result-data" id="result-data">
                          
                </div>
	    	</div>
	    </div>
	</div>
	<!------- include right nav filtering  -->
	@section('right-nav-top')
		<label class="m-0 fwb">Date</label>
		<hr class="mt-1">
		<div class="form-group has-float-label has-required mb-3">
			<input id="report_date" type="date" name="report_date" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
			
		</div>
		<hr class="mt-2">
		<div id="shift-checkbox-area" class="form-group mb-2">
		  <label for="" class="m-0 fwb">Shift <input type='checkbox' id="shift" class="shift-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></label>
		  <hr class="mt-2">
		  <div id="shift-checkbox-area" class="row">
		    @php $incr = 0; @endphp
		    @foreach($shifts as $shift)
	      		<div class="col-sm-6 pr-0 ">
	        		@php $incr++ @endphp
			        <div class="custom-control custom-checkbox custom-checkbox-color-check" title="{{ $shift }}">
			          <input type="checkbox" name="shift[]" class="custom-control-input bg-primary shift" value="{{$shift}}" id="shift-checkbox-{{$incr}}" checked>
			          <label class="custom-control-label" for="shift-checkbox-{{$incr}}"> {{ $shift }}</label>
			        </div>
		      	</div>
		    @endforeach
		  </div>
		</div>
		
	@endsection
	@section('right-nav')
	  	<hr class="mt-2">
	  	<label for="" class="m-0 fwb">Exclude</label>
	  	<hr class="mt-2">
	  	@php
	  		$departments = collect(department_by_id())->pluck('hr_department_name','hr_department_id');
	  		$sections = collect(section_by_id())->pluck('hr_section_name','hr_section_id');
	  		$subSections = collect(subSection_by_id())->pluck('hr_subsec_name','hr_subsec_id');
	  	@endphp

	  	<div class="form-group  has-float-label select-search-group" style="height:85px !important;">
            {{ Form::select('excludes[as_department_id][]', $departments, [], ['id'=>'exclude_department', 'class'=> 'form-control', 'multiple']) }} 
            <label  for="exclude_department"> Departments  </label>
        </div>

        <div class="form-group  has-float-label select-search-group" style="height:85px !important;">
            {{ Form::select('excludes[as_section_id][]', $sections, [], ['id'=>'exclude_section', 'class'=> 'form-control', 'multiple']) }} 
            <label  for="exclude_section"> Sections  </label>
        </div>

        <div class="form-group  has-float-label select-search-group" style="height:85px !important;">
            {{ Form::select('excludes[as_subsection_id][]', $subSections, [], ['id'=>'exclude_subsection', 'class'=> 'form-control', 'multiple']) }} 
            <label  for="exclude_subsection"> Sub-Sections  </label>
        </div>

	@endsection
	<!--- include right modal -->
	@include('common.right-navbar', ['filter_status' => 0 ])


	<div class="modal right fade" id="right_modal_lg_drawer_emplist" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg_drawer_emplist">
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
	                <button class="btn btn-sm btn-primary modal-print" onclick="printDiv('content-result-drawer_emp')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
	                <div class="modal-content-result-drawer_emp" id="content-result-drawer_emp">

	                </div>
	            </div>

	        </div>
	    </div>
	</div>
	
	@include('common.right-modal')
@endsection

@push('js')
	<script type="text/javascript">
		$(document).ready(function(){
			advFilter();
		})
		$("[data-toggle=popover]").popover({
	        html : true,
	        trigger: 'focus',
	        content: function() {
	            var content = $(this).attr("data-popover-content");
	            return $(content).children(".popover-body").html();
	        }
	    });
	    $(document).on('change', '#report_date', function(){
	    	let dateForShift = $(this).val(); 
	    	$.ajax({
		      url : "{{url('hr/fetch/shift-list-checkbox')}}"+'?date='+dateForShift,
		      type: 'get',
		      success: function(data)
		      {
		      	$('#shift-checkbox-area').html(data);
		      },
		      error: function(reject)
		      { 
		      }
		    });
	    })

	    function fetchEmployeeByStatus(status, ot = null)
	    {
	    	let urldata = $('#query_params').val();
	    	let  fetchUrl = '{{ url('hr/reports/get_att_summary_report') }}?'+urldata+'&report_format=employee';
	    	fetchUrl += '&status='+status;
	    	if(ot !=null) fetchUrl +='&otnonot='+ot;

	    	fetch(fetchUrl)

	    }

	    function fetchEmployeeList(field, field_id, ot, status = null)
	    {
	    	let urldata = $('#query_params').val();
	        let fetchUrl = '{{ url('hr/reports/get_att_summary_report') }}?'+urldata+'&report_format=employee&'+field+'='+field_id+'&otnonot='+ot

	        if(status !=null) fetchUrl +='&status='+status;
	        
	        fetch(fetchUrl)
	    }

	    function fetch(url)
	    {
	        $("#modal-title-right-drawer").html('');
	        $('#right_modal_lg_drawer_emplist').modal('show');
	        $("#content-result-drawer_emp").html(loaderContent);
	        $.ajax({
	            url: url,
	            type: "GET",
	            success: function(response){
	                // console.log(response);
	                if(response !== 'error'){
	                    setTimeout(function(){
	                        $("#content-result-drawer_emp").html(response);
	                    }, 1000);
	                }else{
	                }
	            }
	        })
	    }



	</script>
@endpush