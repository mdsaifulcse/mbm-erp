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
									<input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}" readonly>
								</div>
							</div>
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}" readonly>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by">{{ __('Requisition Users') }}:</label></p>
								<div class="input-group input-group-lg mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="{{ null }}">{{ __('Select One') }}</option>
										@foreach($requisition_user_list as $values)
										<option value="{{ $values->id }}">{{ $values->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="searchRfpRequisitonBtn"></label></p>
								<div class="input-group input-group-lg">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.rfp.rfp-requistion-search')}}" id="searchRfpRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>

						</div>
						<div id="viewResult">
							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Date')}}</th>
										<th>{{__('Reference No')}}</th>
										<th>{{__('Request By')}}</th>
										
										<th>{{__('Qty')}}</th>
										<th class="text-center">{{__('Option')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($requistion_data)>0)
									@foreach($requistion_data as $key=> $values)
									<tr id="row{{$values->id}}">
										<td>{{$key+1}}</td>
										<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
										<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
										<td>{{$values->relUsersList->name}}</td>
										
										<td> {{$values->items->sum('qty')}}</td>
										<td class="text-center action">
											<div class="btn-group">
												<button class="btn dropdown-toggle" data-toggle="dropdown">
													<span id="statusName{{$values->id}}">
														Status
													</span>
												</button>
												<ul class="dropdown-menu">

													<li><a class="convertToRfp" data-src="{{route('pms.rfp.convert.to.rfp')}}" data-id="{{$values->id}}" title="Prepare to RFP" >{{ __('Prepare To RFP')}}</a>
													</li>

													<li>
														<a target="__blank" href="{{route('pms.rfp.send.to.purchase',$values->id)}}" title="Direct Purchase">{{ __('Direct Purchase')}}
														</a>
													</li>


												</ul>
											</div>

										</td>
									</tr>
									@endforeach
									@endif
								</tbody>

							</table>
							<div class="py-2 col-md-12">
								@if(count($requistion_data)>0)
								<ul>
									{{$requistion_data->links()}}
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
	(function ($) {
		$('#searchRfpRequisitonBtn').on('click', function () {

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

				convertToRfp();
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
							
							$('#viewResult').html('<tr><td colspan="7" style="text-align: center;">No Data Found</td></tr>');
						}
					}
				});
				return false;
			}else{
				notify('Please enter data first !!','error');

			}
		});

		const convertToRfp = () => {
			$('.convertToRfp').on('click', function () {

				let requisition_id=$(this).attr('data-id');

				if (requisition_id !='') {
					swal({
						title: "{{__('Are you sure?')}}",
						text: "",
						icon: "warning",
						dangerMode: true,
						buttons: {
							cancel: true,
							confirm: {
								text: 'Prepare To RFP',
								value: true,
								visible: true,
								closeModal: true
							},
						},
					}).then((value) => {
						if(value){

							$.ajax({
								type: 'POST',
								url: $(this).attr('data-src'),
								dataType: "json",
								data:{_token:'{!! csrf_token() !!}',requisition_id:requisition_id},
								success:function (data) {
									if(data.result == 'success'){
										$('#row'+requisition_id).hide();
										notify(data.message,'success');
									}else{
										notify(data.message,data.result);
									}
								}
							});
							return false;
						}

					});

				}else{
					notify('Please Select Requisitoin!!','error');
				}
			});
		};

		convertToRfp();

	})(jQuery);

	function openModal(requisitionId) {
		$('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/store-inventory-compare")}}/'+requisitionId);
		$('#requisitionDetailModal').modal('show');
	}
</script>
@endsection