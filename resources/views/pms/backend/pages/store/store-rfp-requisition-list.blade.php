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
								<p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}">
								</div>
							</div>
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}">
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="department_id">{{ __('Department') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<select name="department_id" id="department_id" class="form-control rounded">
										<option value="{{ null }}">{{ __('Select One') }}</option>
										@foreach($department as $values)
										<option value="{{ $values->hr_department_id }}">
											{{ $values->hr_department_name }}
										</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by">{{ __('Requisition Users') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="{{ null }}">{{ __('Select One') }}</option>
										
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="rfpSearchStoreRequisitonBtn"></label></p>
								<div class="input-group input-group-lg">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.store-manage.rfp.requisition.search')}}" id="rfpSearchStoreRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>
						</div>

						<div id="viewResult">
							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Date')}}</th>
										<th>{{__('Department')}}</th>
										<th>{{__('Reference No')}}</th>
										<th>{{__('Requisition By')}}</th>
										<th>{{__('Stock Qty')}}</th>
										<th>{{__('Req Qty')}}</th>
										<th class="text-center">{{__('Option')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($requisition)>0)
									@foreach($requisition as $key=> $values)
									<?php 
									$stockQty=0;
									foreach($values->items as $item){
										$stockQty +=isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
									}
								?>
								@if($stockQty >= $values->items->sum('qty'))
								<tr id="row{{$values->id}}">
									<td>{{($requisition->currentpage()-1) * $requisition->perpage() + $key + 1 }}</td>
									<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
									<td>{{isset($values->relUsersList->employee)?$values->relUsersList->employee->department->hr_department_name:''}}</td>
									<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>

									<td> {{$values->relUsersList->name}}</td>
									<td> {{$stockQty}}</td>
									<td> {{$values->items->sum('qty')}}</td>
									<td class="text-center action">
										<a href="{{route('pms.store-manage.requisition.items.list',$values->id)}}">{{ __('Show Items')}}</a>
									</td>
								</tr>
								@endif
								@endforeach
								@endif
							</tbody>

						</table>
						<div class="py-2 col-md-12">
							@if(count($requisition)>0)
							<ul >
								{{$requisition->links()}}
							</ul>

							@endif
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
</div>


<div class="modal" id="requisitionDetailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Requisition Details</h4>
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
	function openModal(requisitionId) {
		$('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/store-inventory-compare")}}/'+requisitionId);
		$('#requisitionDetailModal').modal('show');
	}


	(function ($) {

		$('#department_id').on('change', function () {
			let department_id=$(this).val();
			showPreloader('block');
			$.ajax({
				type: 'POST',
				url: '{{route('pms.store-manage.rfp.department.wise.employee')}}',
				dataType: "json",
				data:{_token:'{!! csrf_token() !!}',department_id:department_id},
				success:function (response) {
					if(response.result == 'success'){
						showPreloader('none');
						$('#requisition_by').html(response.data);
					}
				}
			});

		});

		$('#rfpSearchStoreRequisitonBtn').on('click', function () {

			let from_date=$('#from_date').val();
			let to_date=$('#to_date').val();
			let requisition_by=$('#requisition_by').val();
			let requisition_status='1';


			const searchRequisitonPagination = () => {
				let container = document.querySelector('.searchPagination');
				let pageLink = container.querySelectorAll('.page-link');
				Array.from(pageLink).map((item, key) => {
					item.addEventListener('click', (e)=>{
						e.preventDefault();
						let getHref = item.getAttribute('href');
						showPreloader('block');
						$.ajax({
							type: 'post',
							url: getHref,
							dataType: "json",
							data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
							success:function (data) {
								if(data.result == 'success'){
									showPreloader('none');
									$('#viewResult').html(data.body);
									searchRequisitonPagination();
								}else{
									showPreloader('none');
									notify(data.message,'error');

								}
							}
						});
					})

				});
			};
			
			if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
				showPreloader('block');
				$.ajax({
					type: 'post',
					url: $(this).attr('data-src'),
					dataType: "json",
					data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
					success:function (data) {
						if(data.result == 'success'){
							showPreloader('none');
							$('#viewResult').html(data.body);
							searchRequisitonPagination();
						}else{
							showPreloader('none');
							$('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

						}
					}
				});
				return false;
			}else{
				notify('Please enter data first !!','error');

			}
		});
	})(jQuery);
</script>
@endsection

