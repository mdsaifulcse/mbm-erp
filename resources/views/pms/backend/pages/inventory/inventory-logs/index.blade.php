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
			</ul>
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Product')}}</th>
										<th>{{__('Warehouses')}}</th>
										<th>{{__('Unit Price')}}</th>
										<th>{{__('Qty')}}</th>
										<th>{{__('Total Price')}}</th>
										<th>{{__('Status')}}</th>
										
									</tr>
								</thead>
								<tbody>
									@if(count($logs)>0)
									@foreach($logs as $key=> $values)
									<tr>
										<td> {{ ($logs->currentpage()-1) * $logs->perpage() + $key + 1 }}</td>
										<td>{{$values->relCategory->name}}</td>
										<td>{{$values->relProduct->name}}</td>
										<td>{{$values->relWarehouse->name}}</td>
										
										<td>{{number_format($values->unit_price,2)}}</td>
										<td>{{$values->qty}}</td>
										<td>{{number_format($values->total_price,2)}}</td>
										<td>{{ucfirst($values->type)}}</td>
									</tr>
									@endforeach
									@endif
								</tbody>
									@if(count($logs)>0)
									{{$logs->links()}}
									@endif
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
	
</script>
@endsection