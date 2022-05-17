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
                        {{--<a href="{{ route('pms.grn.grn-process.index') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>--}}
                    </li>
                </ul><!-- /.breadcrumb -->
            </div>

            <div class="page-content">
                <div class="">

                        <div class="panel-body">
                            <div class="table-responsive ">
                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th>{{__('SL No.')}}</th>
                                        <th>{{__('P.O Reference')}}</th>
                                        <th>{{__('P.O Date')}}</th>
                                        <th>{{__('GRN Reference')}}</th>
                                        <th>{{__('GRN Date')}}</th>
                                        <th>{{__('Po Qty')}}</th>
                                        <th>{{__('Received Qty')}}</th>
                                        {{--<th>{{__('Left Qty')}}</th>--}}
                                        <th>{{__('Receive Status')}}</th>
                                        <th>{{__('Supplier Rating')}}</th>
                                        <th>{{__('Quality Ensure')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if(count($grns)>0)
                                        @foreach($grns as $key=>$grn)
                                    <tr>
                                        <td>{{$key+1}}</td>

                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$grn->relPurchaseOrder->reference_no}}
                                            </a>
                                        </td>

                                        <td>
                                            {{date('d-M-Y',strtotime($grn->relPurchaseOrder->po_date))}}
                                        </td>

                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->reference_no}}
                                            </a>
                                        </td>

                                        <td>
                                            {{date('d-M-Y',strtotime($grn->received_date))}}
                                        </td>


                                        <td>{{$grn->relPurchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                                        <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>

                                        {{--<td>{{$grn->relPurchaseOrder->relPurchaseOrderItems->sum('qty')-$grn->relGoodsReceivedItems->sum('qty')}}</td>--}}
                                        <td class="capitalize">{{$grn->received_status}}</td>
                                        <td class="text-center">
                                            <div class="btn-group">

                                                @if($grn->is_supplier_rating=='no')
                                                    <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                    <span id="statusName{{$grn->id}}" title="Click Here To Supplier Review">
                                                        {{ __('No')}}
													</span>
                                                    </button>
                                                @elseif($grn->is_supplier_rating=='yes')

                                                        {{ __('Yes')}}
                                                @endif


                                                @if($grn->is_supplier_rating=='no')
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a  href="{{url('pms/supplier/rating/'.$grn->relPurchaseOrder->relQuotation->supplier_id.'/'.$grn->id)}}" title="Click Here To Supplier Review" target="_blank" data-id="{{$grn->id}}" data-status="active">{{ __('Give Supplier Rating')}}</a>
                                                    </li>
                                                </ul>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                           @can('quality-ensure')
                                                @if($grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->count() > 0)
                                                <a href="{{route('pms.quality.ensure.check',$grn->id)}}" title="Quality Ensure" class="btn btn-success btn-sm"><i class="las la-check-circle"> {{ __('Quality Ensure')}}</i></a>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach

                                        @else
                                    <tr>
                                        <td></td>
                                    </tr>
                                    @endif


                                    </tbody>
                                </table>
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="POdetailsModel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Purchase Order</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="body">

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
            "use script";

            const showPODetails = () => {
                $('.showPODetails').on('click', function () {

                    var modalTitle= $(this).attr('data-title');
                    $.ajax({
                        url: $(this).attr('data-src'),
                        type: 'get',
                        dataType: 'json',
                        data: '',
                    })
                        .done(function(response) {

                            if (response.result=='success') {
                                $('#POdetailsModel').find('#body').html(response.body);
                                $('#POdetailsModel').find('.modal-title').html(modalTitle);
                                $('#POdetailsModel').modal('show');
                            }

                        })
                        .fail(function(response){
                            notify('Something went wrong!','error');
                        });
                });
            }
            showPODetails();

        })(jQuery);
    </script>
@endsection