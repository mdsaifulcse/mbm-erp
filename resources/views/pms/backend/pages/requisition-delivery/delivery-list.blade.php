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

							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
								<tr>
									<th width="5%">{{__('SL No.')}}</th>
									<th>{{__('Req.Date')}}</th>
                                    <th>{{__('Requisition By')}}</th>
                                    <th>{{__('Delivery Date')}}</th>
                                    <th>{{__('Delivery Ref.')}}</th>
									<th>{{__('Delivery Qty')}}</th>
									<th>{{__('Delivery By')}}</th>
								</tr>
								</thead>
								<tbody id="viewResult">

									@forelse($requisitionDeliveries as $key=> $value)
										<tr id="row{{$value->id}}">
											<td>{{$key+1}}</td>
											<td>{{date('d-m-Y', strtotime($value->relRequisition->requisition_date))}}</td>

											<td>
                                                <a href="javascript:void(0)" onclick="openModal({{$value->relRequisition->id}})"  class="btn btn-link">
                                                    {{$value->relRequisition->relUsersList->name}}  ({{$value->relRequisition->requisitionItems->sum('qty')}} Qty)
                                                </a>
                                            </td>
											<td>{{date('d-m-Y', strtotime($value->delivery_date))}} </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="openDeliveryModal({{$value->id}})" ta-toggle="tooltip" title="Click here to view details"  class="btn btn-link">
                                                    {{$value->reference_no}}
                                                </a>
                                            </td>
											<td> {{$value->relDeliveryItems->sum('delivery_qty')}}</td>
											<td> {{$value->relDeliveryBy->name}}</td>
										</tr>
									@empty
								@endforelse
								</tbody>
								<tfoot>
									<ul>{{$requisitionDeliveries->links()}}</ul>
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


    <div class="modal" id="deliveryDetailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Delivery Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="detailTable">

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

        function openDeliveryModal(requisitionDeliveryId) {
            $('#detailTable').load('{{URL::to(Request()->route()->getPrefix()."/requisition-delivered-detail")}}/'+requisitionDeliveryId);
            $('#deliveryDetailModal').modal('show');
        }
	</script>
@endsection