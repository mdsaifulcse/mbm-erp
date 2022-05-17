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
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('P.O. Date')}}</th>
                                <th>{{__('Supplier')}}</th>
                                <th>{{__('Reference No')}}</th>
                                <th>{{__('Quotation Ref No')}}</th>
                                <th>{{__('P.O Qty')}}</th>
                                <th>{{__('GRN Qty')}}</th>

                                <th>{{__('Total Price')}}</th>
                                <th>{{__('Discount %')}}</th>
                                <th>{{__('Vat %')}}</th>
                                <th>{{__('Gross Price')}}</th>

                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody id="viewResult">
                            @if(count($data)>0)
                            @foreach($data as $key=> $values)
                            <tr>
                                <td>{{ ($data->currentpage()-1) * $data->perpage() + $key + 1 }}</td>
                                <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
                                <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
                                <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>
                                <td>{{$values->relQuotation?$values->relQuotation->reference_no:''}}</td>

                                <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
                                <td>{{$values->total_grn_qty}}</td>

                                <td>{{$values->total_price}}</td>
                                <td>{{$values->discount}} %</td>
                                <td>{{$values->vat}} %</td>
                                <td>{{$values->gross_price}}</td>


                                <td class="text-center">

                                    @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0)

                                        <button class="btn btn-default">{{__('Full Received')}}</button>
                                        @else
                                        <a href="{{ route('pms.grn.grn-list.createGRN',$values->id) }}" data-toggle="tooltip" title="Click here to generate GRN">
                                            <button type="button" class="btn btn-sm btn-primary">{{ __('GRN') }}</button>
                                        </a>
                                    @endif
                                    
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="col-12 py-2">
                        @if(count($data)>0)
                        <ul>
                            {{$data->links()}}
                        </ul>

                        @endif
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

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
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