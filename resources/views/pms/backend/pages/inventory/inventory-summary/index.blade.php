@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')

<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
				</li>
				<li>
					<a href="#">PMS</a>
				</li>
				<li class="active">{{__($title)}}</li>
				<li class="top-nav-btn">

				</li>
			</ul><!-- /.breadcrumb -->

		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Category') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<select name="category_id" id="category_id" onchange="getProduct()" class="form-control rounded">
										<option value="0">{{ __('Select One') }}</option>
										@foreach($categories as $values)
										<option value="{{ $values->id }}" {{$category_id==$values->id ? 'selected' : ''}}>{{ $values->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<p class="mb-1 font-weight-bold"><label for="product_id">{{ __('Product') }}:</label></p>
								<div class="input-group input-group-lg mb-3">
									<select class="form-control rounded" name="product_id" id="product_id">

									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
								<div class="input-group input-group-lg">

									<a href="javascript:void(0)" class="btn btn-success rounded mt-8"  onclick="searchOptions()"> <i class="las la-search"></i>Search</a>
								</div>
							</div>
						</div>

						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Product')}}</th>
										<th>{{__('Unit Price')}}</th>
										<th>{{__('Qty')}}</th>
										<th>{{__('Total Price')}}</th>
										<th class="text-center">{{__('Option')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($inventory_data)>0)
									@foreach($inventory_data as $key=> $values)
									<tr>
										<td>{{$key+1}}</td>
										<td>{{$values->relCategory->name}}</td>
										<td>
											<a href="javascript:void(0)" onclick="openModal({{$values->relProduct->id}})"  class="btn btn-link">
												{{$values->relProduct->name}}
											</a>
										</td>
										
										<td>{{number_format($values->unit_price,2)}}</td>
										<td>{{$values->qty}}</td>
										<td>{{number_format($values->total_price,2)}}</td>
										
										<td class="text-center action">
											<div class="btn-group">
												<button class="btn dropdown-toggle" data-toggle="dropdown">
													<span id="statusName{{$values->id}}">
														@if($values->status=='active')
														{{ __('Active')}}
														@elseif($values->status=='inactive')
														{{ __('inactive')}}
														@else
														{{ __('Cancel')}}
														@endif
													</span>
													
													
												</button>
												<ul class="dropdown-menu">
													<li><a href="javascript:void(0)" title="Click Here To Active" id="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="active">{{ __('Active')}}</a>
													</li>
													<li><a href="javascript:void(0)" title="Click Here To Inactive" id="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="inactive">{{ __('Inactive')}}</a>
													</li>
													<li><a href="javascript:void(0)" title="Click Here To Cancel" id="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="Cancel">{{ __('Cancel')}}</a>
													</li>
												</ul>
											</div>

										</td>
									</tr>
									@endforeach
									@endif
								</tbody>
								<tfoot>
									{{-- @if(count($inventory_data)>0)
									{{$inventory_data->links()}}
									@endif --}}
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="warehouseDetailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Inventory Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body" id="tableData">



			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>

		</div>
	</div>
</div>
@endsection
@section('page-script')
<script>
	getProduct();
	function getProduct(){
		var category_id = $('#category_id').val();
		var product_id = "{{$product_id}}";

		$.ajax({
			url: "{{ url('pms/inventory/inventory-summary') }}/"+category_id+"/get-products",
			type: 'GET',
			dataType: 'json',
			data: {},
		})
		.done(function(response) {
			var product = '<option value="0">Select One</option>';
			$.each(response, function(index, val) {
				var selected = '';
				if(product_id == val.id){
					selected = 'selected';
				}
				product += '<option value="'+(val.id)+'" '+(selected)+'>'+(val.name)+'</option>';
			});

			$('#product_id').html(product);
		});
	}

	function searchOptions() {
		window.open("{{ url('pms/inventory/inventory-summary') }}/"+$('#category_id').val()+"&"+$('#product_id').val(),"_parent");
	}

	function openModal(product_id) {
		$('#tableData').load('{{URL::to('pms/inventory/warehouse-wise-product-inventory-details')}}/'+product_id);
		$('#warehouseDetailModal').modal('show');
	}
</script>
@endsection