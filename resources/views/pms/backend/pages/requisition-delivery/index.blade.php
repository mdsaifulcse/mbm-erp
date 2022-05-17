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
							{{--<div class="row">--}}
							{{--<div class="col-md-3 col-sm-6">--}}
							{{--<p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>--}}
							{{--<div class="input-group input-group-lg mb-3 d-">--}}
							{{--<input type="text" name="from_date" id="from_date" class="air-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('from_date')?old('from_date'):date('d-m-Y') }}">--}}
							{{--</div>--}}
							{{--</div>--}}
							{{--<div class="col-md-3 col-sm-6">--}}
							{{--<p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>--}}
							{{--<div class="input-group input-group-lg mb-3 d-">--}}
							{{--<input type="text" name="to_date" id="to_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}">--}}
							{{--</div>--}}
							{{--</div>--}}
							{{--<div class="col-md-2 col-sm-6">--}}
							{{--<p class="mb-1 font-weight-bold"><label for="requisition_by">{{ __('Requisition Users') }}:</label></p>--}}
							{{--<div class="input-group input-group-lg mb-3 d-">--}}
							{{--<select name="requisition_by" id="requisition_by" class="form-control rounded">--}}
							{{--<option value="{{ null }}">{{ __('Select One') }}</option>--}}
							{{--@foreach($requisition_user_list as $value)--}}
							{{--<option value="{{ $value->id }}">{{ $value->name }}</option>--}}
							{{--@endforeach--}}
							{{--</select>--}}
							{{--</div>--}}
							{{--</div>--}}

							{{--<div class="col-md-2 col-sm-6">--}}
							{{--<p class="mb-1 font-weight-bold"><label for="searchStoreRequisitonBtn"></label></p>--}}
							{{--<div class="input-group input-group-lg">--}}
							{{--<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.store-manage.store-requistion-search')}}" id="searchStoreRequisitonBtn"> <i class="las la-search"></i>Search</a>--}}
							{{--</div>--}}
							{{--</div>--}}

							{{--</div>--}}


							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
								<tr>
									<th width="5%">{{__('SL No.')}}</th>
									<th>{{__('Req.Date')}}</th>
									<th>{{__('Ref')}}</th>
									<th>{{__('Requisition By')}}</th>
									<th>{{__('Qty')}}</th>
									<th>{{__('Delivery Qty')}}</th>
									<th>{{__('Left Qty')}}</th>
									<th>{{__('Delivery')}}</th>
									<th class="text-center">{{__('Option')}}</th>
								</tr>
								</thead>
								<tbody id="viewResult">
								@if(count($requisitions)>0)
									@foreach($requisitions as $key=> $value)
										<tr id="row{{$value->id}}">
											<td>{{$key+1}}</td>
											<td>{{date('d-m-Y', strtotime($value->requisition_date))}}</td>
											<td><a href="javascript:void(0)" onclick="openModal({{$value->id}})"  class="btn btn-link">{{$value->reference_no}}</a></td>
											<td> {{$value->relUsersList->name}}</td>
											<td> {{$value->requisition_qty}}</td>
											<td> {{$value->total_delivery_qty}}</td>
											<td> {{$value->requisition_qty-$value->total_delivery_qty}}</td>
											<td>
												<a href="{{route('pms.store-manage.requisition-delivered-list',$value->id)}}" data-toggle="tooltip" title="Click here to view details" target="_blank"> Total ({{count($value->relRequisitionDelivery)}})</a>
											</td>
											<td class="text-center action">

												@if($value->delivery_status=='delivered')
													<span>Full Delivered</span>
												@else
													<div class="btn-group">
														<button class="btn dropdown-toggle" data-toggle="dropdown">
												<span id="statusName{{$value->id}}">
													Action
												</span>
														</button>
														<ul class="dropdown-menu">
															@can('confirm-delivery')
																<li><a href="{{route('pms.store-manage.store-requistion.delivery',$value->id)}}" title="Click Here To Confirm Delivery" >{{ __('Confirm Delivery')}}</a>
																</li>
															@endcan
														</ul>
													</div>
												@endif

											</td>
										</tr>
									@endforeach
								@endif
								</tbody>
								<tfoot>
								@if(count($requisitions)>0)
									<ul>
										{{$requisitions->links()}}
									</ul>

								@endif
								</tfoot>
							</table>
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
//Search
            $('#searchStoreRequisitonBtn').on('click', function () {

                let from_date=$('#from_date').val();
                let to_date=$('#to_date').val();
                let requisition_by=$('#requisition_by').val();
                let requisition_status='1';

                if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
                    $.ajax({
                        type: 'post',
                        url: $(this).attr('data-src'),
                        dataType: "json",
                        data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
                        success:function (data) {
                            if(data.result == 'success'){
                                $('#viewResult').html(data.body);
                                sendToPurchaseDepartment();
                            }else{
                                //notify(data.message,'error');
                                $('#viewResult').html('<tr><td colspan="6" style="text-align: center;">No Data Found</td></tr>');

                            }
                        }
                    });
                    return false;
                }else{
                    notify('Please enter data first !!','error');

                }
            });

            const sendToPurchaseDepartment = () => {
                $('.sendToPurchaseDepartment').on('click', function () {

                    let requisition_id=$(this).attr('data-id');

                    if (requisition_id !='') {
                        swal({
                            title: "{{__('Are you sure?')}}",
                            text: "{{__('Once you send it for RFP, You can not rollback from there.')}}",
                            icon: "warning",
                            dangerMode: true,
                            buttons: {
                                cancel: true,
                                confirm: {
                                    text: 'Send To RFP',
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

            sendToPurchaseDepartment();

        })(jQuery);

        function openModal(requisitionId) {
            $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/store-inventory-compare")}}/'+requisitionId);
            $('#requisitionDetailModal').modal('show');
        }
	</script>
@endsection