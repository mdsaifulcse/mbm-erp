@php 
	$salaryMax = get_salary_max();
@endphp
@extends('hr.layout')
@section('title', 'Audit - End of Job')
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
	                    <a href="#">Audit</a>
	                </li>
	                <li class="active">End of Job</li>
	            </ul>
	        </div>
	    </div>
	    <div class="panel">
	    	<div class="panel-heading">
	    		<form id="formReport" role="form" method="get" action="{{ url("hr/audit/fetch/end-of-job") }}" >
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
		<label class="m-0 fwb">Date Range</label>
		<hr class="mt-1">
		<div class="row">
			<div class="col-sm-6 pr-0">
				<div class="form-group has-float-label has-required mb-3">
					<input id="from_date" type="date" name="from_date" class="form-control" value="{{date('Y-m-01')}}" max="{{date('Y-m-01')}}">
					<label for="from_date">From</label>
					
				</div>
			</div>
			<div class="col-sm-6">
				
				<div class="form-group has-float-label has-required mb-3">
					<input id="to_date" type="date" name="to_date" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
					
					<label for="to_date">To</label>
				</div>
			</div>
		</div>
	@endsection
	@section('right-nav')
	  	
	  	

	@endsection
	<!--- include right modal -->
	@include('common.right-navbar', ['filter_status' => 0 ])
	<div class="modal right fade" id="right_modal_maternity" tabindex="-1" role="dialog" aria-labelledby="right_modal_jobcard">
	  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
	    <div class="modal-content">
	      <div class="modal-header">
	        <a class="view prev_btn-job" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
	      <i class="las la-chevron-left"></i>
	    </a>
	        <h5 class="modal-title right-modal-title text-center capitalize" id="modal-title-right"> &nbsp; </h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body" style="padding-top: 0;background: #f1f1f1;">
	      	<div class="d-flex justify-content-center">
		        <div style="width: 850px;" class="eob-content-result  bg-white p-3" id="eob-content-result"></div>
	      	</div>
	      </div>
	      
	    </div>
	  </div>
	</div>
	<!-- audit action -->
	<div class="modal fade apps-modal" id="eobAudit" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false" >
    

    	<div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content" style="height:400px;width: 380px;left: 40%;top: 50px;background: #fff;box-shadow: rgb(71 70 70) 0px 0px 5px 2px;border-radius: 10px;min-height: 400px;margin-bottom: 50px;">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            <div class="content-area p-4">
            	<h4 class="font-weight-bold text-center">Audit</h4>
            	<hr>
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                    	<div class="info"></div>
                	    <p class="font-weight-bold mb-2"> Accept </p>
                    	<div class="custom-control custom-switch custom-switch-icon custom-switch-color custom-control-inline mb-2">

                    	  <input type="hidden" id="audit-eob-id" value="">
                          <div class="custom-switch-inner">
                             <input type="checkbox" class="custom-control-input bg-primary" id="audit-checked" checked="">
                             <label class="custom-control-label" for="audit-checked">
                             <span class="switch-icon-left" style="top:0"><i class="fa fa-check"></i></span>
                             <span class="switch-icon-right" style="top:0"><i class="fa fa-times"></i></span>
                             </label>
                          </div>
                       </div>
                    	<div class="form-group">
                    		<label class="font-weight-bold" for="audit-comment">Comment</label>
                            <textarea id="audit-comment" class="form-control" placeholder="Write comments..." ></textarea> 
                    	</div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<button id="audit-submit" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
         </div>
    </div>
@endsection

@push('js')
	<script type="text/javascript">
		$(document).ready(function(){
			advFilter();
		})

		$(document).on('click','#benefits', function(){
			$('#right_modal_maternity').modal('show')
			$("#eob-content-result").html(loaderContent)
			var id = $(this).data('id');
			$.ajax({
		        url: '{{ url('hr/preview/end-of-job-benefits') }}/'+id,
		        type: "GET",
		        success: function(response){
		            if(response !== 'error'){
		                $("#eob-content-result").html(response);
		            }else{
		                $("#eob-content-result").html('Nothing found!');
		            }
		        }
    		});
		});

		$(document).on('click','#partial-salary', function(){
			$('#right_modal_maternity').modal('show')
			$("#eob-content-result").html(loaderContent)
			var id = $(this).data('emp'), salary_date = $(this).data('salary-date');
			$.ajax({
		        url: '{{ url('hr/preview/partial-salary') }}?associate_id='+id+'&salary_date='+salary_date,
		        type: "GET",
		        success: function(response){
		            if(response !== 'error'){
		                $("#eob-content-result").html(response);
		            }else{
		                $("#eob-content-result").html('Nothing found!');
		            }
		        }
    		});
		});

		$(document).on('click','#auditAction', function(){
			$('#eobAudit').modal('show')
			$('#audit-eob-id').val($(this).data('id'));
		});

		$(document).on('click','#crossReason', function(){
			$('#eobAudit').modal('show')
			$('#audit-eob-id').val($(this).data('id'));
			$('#audit-comment').val($(this).data('comment'))
			$('#audit-checked').val($(this).data('comment'))
		});



		$(document).on('click', '#audit-submit', function($q){
			let eob_id = $('#audit-eob-id').val(),
			    status = ($('#audit-checked').is(":checked")?1:2),
			    comment = $('#audit-comment').val()

			$(this).attr('disabled',true)
			$.ajax({
		        url: '{{ url('hr/audit/action/end-of-job') }}',
		        type: "POST",
		        data:{
		        	'_token' : "{{ csrf_token() }}",
		        	'id': eob_id,
		        	'status': status,
		        	'comment': comment
		        },
		        success: function(response){

		            if(response.status == 1){
		            	$('.action-'+response.hr_end_of_job_id).html('<i class="las la-check-circle text-success" style="font-size: 16px;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Audited by ...... at '+response.audit_date+'"></i>')
		            }else{
		            	$('.action-'+response.hr_end_of_job_id).html('<i class="las la-times-circle text-danger" style="font-size: 16px" id="crossReason" data-id="'+response.hr_end_of_job_id+'" data-message="'+response.comment+'" data-date="'+response.date+'"></i>')
		            }
		            $('#audit-submit').attr('disabled',false)
		            $('#eobAudit').modal('hide')
		            $('#audit-eob-id').val('')
			    	$('#audit-checked').prop("checked",true)
			    	$('#audit-comment').val('')
		        }
    		});


		});

		$("[data-toggle=popover]").popover({
	        html : true,
	        trigger: 'focus',
	        content: function() {
	            var content = $(this).attr("data-popover-content");
	            return $(content).children(".popover-body").html();
	        }
	    });
	</script>
@endpush