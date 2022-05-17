@php
	$buyerList= buyer_by_id();
	$buyerList = collect($buyerList)->pluck('b_name','b_id');
    $prdtypList= product_type_by_id();
  	$prdtypList = collect($prdtypList)->pluck('prd_type_name','prd_type_id');
    $yearMonth = date('Y-m', strtotime('+1 months'));
		
@endphp
<div class="row">
    <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    <input type="hidden" id="page-type" value="reservation-store" />
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('hr_unit_id', $unitList, null, [ 'id' => 'unit', 'placeholder' => 'Select Unit Name', 'class' => 'form-control filter unitChange', 'required'])}}
				      <label for="unit" > Unit Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="res-year-month" name="res_year_month" placeholder=" Month-Year"required="required" value="{{ $yearMonth }}"autocomplete="off" />
				        <label for="res-year-month" >Reservation Year-Month </label>
				    </div>
		    	</div>
		    </div>
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('b_id', $buyerList, null, [ 'id' => 'buyer', 'placeholder' => 'Select Buyer Name', 'class' => 'form-control filter buyerChange', 'required'])}}
				      <label for="buyer" > Buyer Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="row">
		    			<div class="col-sm-6">
				    		<div class="form-group has-required has-float-label">
						        <input type="number" id="res-quantity" name="res_quantity" placeholder="Enter Quantity" class="form-control sah_cal" autocomplete="off" value="0" onClick="this.select()" required min="0" />
						        <label for="res-quantity" > Quantity </label>
						    </div>
				    	</div>
				    	<div class="col-sm-6">
				    		<div class="form-group has-required has-float-label">
						        <input type="number" id="res-smv" name="res_sewing_smv" placeholder="Enter Sewing SMV " class="form-control sah_cal" autocomplete="off" value="0" step="any" onClick="this.select()" required min="0" />
						        <label for="res-smv" > Sewing SMV </label>
						    </div>
				    	</div>
		    		</div>
		    	</div>
		    	
		    </div>
		    
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('prd_type_id', $prdtypList, null, [ 'id' => 'product-type', 'placeholder' => 'Select Product Type Name', 'class' => 'form-control filter', 'required'])}}
				      <label for="product-type" > Product Type Name </label>
				      
				    </div>
		    	</div>
		    	
		    	<div class="col-sm-6">
				    <div class="form-group has-required has-float-label">
				        <input type="number" id="sah" name="res_sah" placeholder="Enter SAH" class="form-control" autocomplete="off" step="any" required readonly value="0" min="0" />
				        <label for="sah" > SAH </label>
				    </div>
		    	</div>
		    </div>
		    {{-- <div class="form-group">
		    	<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                  <input type="checkbox" name="order_check" class="custom-control-input bg-primary" value="0" id="order-check" >
                  <label class="custom-control-label cursor-pointer" for="order-check"> Order Entry</label>
                </div>
		    </div> --}}
		    <div id="order-entry-section" style="display: none">
		    	<input type="hidden" name="res_id" value="0">
		    	<div class="row">
		    		<div class="col-sm-6">
		    			<div class="form-group has-required has-float-label select-search-group">
					      {{Form::select('season_n_year', [], null, [ 'id' => 'season', 'placeholder' => 'Select Season', 'class' => 'seasonChange form-control filter', 'readonly'])}}
					      <label for="season" > Season </label>
					    </div>
					    <div class="form-group has-float-label select-search-group">
					      {{Form::select('mr_style_stl_id', [], null, [ 'id' => 'style-no', 'placeholder' => 'Select Style Number', 'class' => 'style-no form-control filter', 'readonly'])}}
					      <label for="style-no"> Style Number </label>
					    </div>
					    <div class="form-group has-required has-float-label">
					        <input type="text" id="brand" placeholder="Brand Name" class="form-control" autocomplete="off" readonly />
					        <label for="brand" > Brand </label>
					    </div>
					    <div class="form-group has-float-label">
					        <input type="text" id="style-ref-2" placeholder="Style Reference 2" class="form-control" autocomplete="off" readonly />
					        <label for="style-ref-2" > Style Ref. 2 </label>
					    </div>
					    <div class="form-group has-required has-float-label">
					        <input type="month" class="form-control" id="month" name="order_year_month" placeholder=" Month-Year"required="required" value="{{ $yearMonth }}"autocomplete="off" max="{{ $yearMonth }}" />
					        <label for="month" >Order Year-Month </label>
					    </div>
		    		</div>
		    		<input type="hidden" name="mr_season_se_id" id="mr_season_se_id" value="">
		    		<div class="col-sm-6">
		    			<div class="form-group has-required has-float-label">
					        <input type="text" id="reference-no" name="order_ref_no" placeholder="Enter Reference No" class="form-control" autocomplete="off" />
					        <label for="reference-no" > Reference No </label>
					    </div>
					    <div class="form-group has-required has-float-label">
					        <input type="number" id="order-qty" name="order_qty" placeholder="Enter Order Quantity" class="form-control" autocomplete="off" value="0" required onClick="this.select()" min="0" />
					        <label for="order-qty"> Order Quantity </label>
					    </div>
					    <div class="form-group has-required has-float-label">
					        <input type="date" class="form-control" id="pcd-date" name="pcd" placeholder="Enter Planned Cut Date" required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
					        <label for="pcd-date" > PCD </label>
					    </div>
					    <div class="form-group has-required has-float-label">
					        <input type="date" class="form-control" id="delivery-date" name="order_delivery_date" placeholder="Enter Delivery Date"required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
					        <label for="delivery-date" > Delivery Date </label>
					    </div>
		    		</div>
		    	</div>
		    	
		    </div>
		    <div class="form-group">
		        <button class="btn btn-outline-success pull-right" type="button" id="itemBtn">
		            <i class="fa fa-save"></i> Save
		        </button>
		    </div>                                 
		</form>
	</div>
</div>