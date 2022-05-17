@extends('merch.layout')
@section('title', 'Order Create')
@section('main-content')
@push('css')
<style>
   .panel-heading {
	    border-top: 1px solid rgb(195 225 228);
	}
	.select2 {
	    width: 100% !important;
	}
	.size-qty{
		position: relative;
	}
	.preview-po{
		width: 14%;
    	margin: 0 auto;
	}
	.preview-small {
	    width: 20% !important;
	}
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Merchandising</a>
              </li>
              <li>
                  <a href="#">Order</a>
              </li>
              <li class="active">Create</li>
              <li class="top-nav-btn">
                <a class="btn btn-sm btn-primary text-white" href="{{ url('merch/order/order_list') }}"><i class="las la-list"></i> Order List</a>
              </li>
          </ul><!-- /.breadcrumb -->

      </div>

    <div class="page-content">
        <div class="panel panel-success">
            <div class="panel-body pb-2">
            	<div class="row">
		            <div class="offset-sm-2 col-sm-8">
		              <form role="form" method="get" action="{{ url("merch/orders/create")}}" class="style" id="styleForm">
		                <div class="panel" style="margin-bottom: 0;">
		                    
		                    <div class="panel-body" style="padding-bottom: 5px;">
		                        <div class="row">
		                            <div class="col-8">
		                                <div class="form-group has-float-label has-required select-search-group">
		                                	@if(isset($style) && $style != null)
		                                	{{ Form::select('stl_id', [$style->stl_id=>$style->stl_no], $style->stl_id, ['placeholder'=>'Select Style No', 'id'=>'stl_id', 'class'=> 'bulk-style-no no-select ','style', 'required'=>'required']) }}
		                                	@else
		                                    {{ Form::select('stl_id', [Request::get('stl_id') => Request::get('stl_id')], Request::get('stl_id'), ['placeholder'=>'Select Style No', 'id'=>'stl_id', 'class'=> 'bulk-style-no no-select ','style', 'required'=>'required']) }}
		                                    @endif
		                                    <label  for="stl_id"> Style No </label>
		                                </div>
		                            </div>
		                            
		                            <div class="col-4">
		                                <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-search"></i> Search</button>
		                                
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </form>
		          </div>
		      	</div>
		      	@if(isset($style) && $style != null)
		      	<div class="row">
		      		<div class="offset-1 col-10">
						<form class="form-horizontal" id="orderForm" role="form" method="post" >
						    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
						    <input type="hidden" id="page-type" value="reservation-store" />
						    <input type="hidden" name="stl_id" value="{{ $style->stl_id}}">
						    <input type="hidden" name="unit_id" value="{{ $style->unit_id}}">
						    <input type="hidden" name="mr_buyer_b_id" value="{{ $style->mr_buyer_b_id}}">
						    <input type="hidden" name="prd_type_id" value="{{ $style->prd_type_id}}">
						    <div class="row">
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="unit" placeholder="Unit Name" value="{{ $unitList[$style->unit_id]['hr_unit_name']??'' }}" readonly autocomplete="off" />
								      <label for="unit" > Unit Name </label>
								    </div>
						    	</div>
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="buyer" placeholder="Buyer Name" value="{!! $buyerList[$style->mr_buyer_b_id]->b_name??'' !!}" readonly autocomplete="off" />
								      <label for="buyer" > Buyer Name </label>
								    </div>
						    	</div>
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="product-type" placeholder="Product Type Name" value="{!! $productType[$style->prd_type_id]->prd_type_name??'' !!}" readonly autocomplete="off" />
								      <label for="product-type" > Product Type Name </label>
								      
								    </div>
						    	</div>
						    </div>
						    <div class="row">
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="season" placeholder="Season Name" value="{{ $season[$style->mr_season_se_id]->se_name??'' }}-{{$style->stl_year}}" readonly autocomplete="off" />
								      <label for="season" > Season </label>
								    </div>
						    	</div>
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="brand" placeholder="Brand Name" value="{!! $brand[$style->mr_brand_br_id]->br_name??'' !!}" readonly autocomplete="off" />
								      <label for="brand" > Brand </label>
								    </div>
						    	</div>
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" class="form-control" id="style-reference-2" placeholder="Style Reference 2" value="{!! $style->stl_product_name??'' !!}" readonly autocomplete="off" />
								      <label for="style-reference-2" > Style Reference 2 </label>
								    </div>
						    	</div>
						    	
						    </div>
						    <div class="row">
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label select-search-group">
								      <input type="text" name="order_ref_no" class="form-control" id="reference-no" placeholder="Order Reference No" value="" required autocomplete="off" />
								      <label for="reference-no" > Order Reference No </label>
								    </div>
						    	</div>
						    	<div class="col-sm-4">
						    		<div class="form-group has-required has-float-label">
								        <input type="number" id="order-quantity" name="order_qty" placeholder="Enter Order Quantity" class="form-control" autocomplete="off" value="0" onClick="this.select()" required min="0" />
								        <label for="order-quantity" > Order Quantity </label>
								    </div>
						    	</div>
						    	<div class="col-sm-2">
						    		<div class="form-group has-required has-float-label">
								        <input type="month" class="form-control" id="month" name="order_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m') }}"autocomplete="off" />
								        <label for="month" >Order Year-Month </label>
								    </div>
						    	</div>
						    	<div class="col-sm-2">
						    		<div class="form-group">
								        <button class="btn btn-primary pull-right" type="button" id="checkReservation">
								            <i class="fa fa-search"></i> Reservation
								        </button>
								    </div>
						    	</div>
						    </div>
						    <div id="reservation-info"></div>
						                                   
						</form>
					</div>
		      	</div>
		      	@endif
            </div> 
        </div>

    </div><!-- /.page-content -->

  </div>
</div>
@push('js')
<script type="text/javascript">
	$(document).on('click', '#checkReservation', function(event) {
	  
	  var curStep = jQuery(this).closest("#orderForm"),
	    curInputs = curStep.find("input[type='text'], input[type='number'],input[type='hidden'],input[type='date'], input[type='month'],input[type='checkbox'],input[type='radio'],textarea,select"),
	    isValid = true;
	    
	  $(".form-group").removeClass("has-error");
	  for (var i = 0; i < curInputs.length; i++) {
	    if (!curInputs[i].validity.valid) {
	      isValid = false;
	      $(curInputs[i]).closest(".form-group").addClass("has-error");
	    }
	  }
	  // check order qty
	  if($("#order-quantity").val() < 1){
	  	$("#order-quantity").notify('Order quantity is at least more than 0', 'error');
	  	return false;
	  }
	  $("#app-loader").show();
	  if (isValid){
	     $.ajax({
	        type: 'GET',
	        url: '{{ url("merch/check-reservation") }}',
	        data: curInputs.serialize(), // serializes the form's elements.
	        success: function(response)
	        {
	          $("#app-loader").hide();
	          console.log(response)
	          if(response !== 'error'){
	          	$("#reservation-info").html(response)
	          }else{
	          	$.notify('Something Wrong!, please try again', 'error');
	          }
	          // if(response.type === 'success'){
	          //   setTimeout(function(){
	          //     window.location.href=response.url;
	          //   }, 1000);
	          // } 
	        },
	        error: function (reject) {
	          $("#app-loader").hide();
	          if( reject.status === 400) {
	              var data = $.parseJSON(reject.responseText);
	              $.notify(data.message, data.type);
	          }else if(reject.status === 422){
	            var data = $.parseJSON(reject.responseText);
	            var errors = data.errors;
	            // console.log(errors);
	            for (var key in errors) {
	              var value = errors[key];
	              $.notify(value[0], 'error');
	            }
	             
	          }
	        }
	     });
	  }else{
	      $("#app-loader").hide();
	      $.notify("Some field are required", 'error');
	  }
	});
</script>
@endpush
@endsection