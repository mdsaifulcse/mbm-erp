@if ($grn->rel_goods_received_items->count() >0)
        
@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .invoiceBody{
        margin-top:10px;
        background:#eee;
        padding: 10px;

    }
    .ratings {
        margin-right: 10px
    }

    .ratings i {
        color: #cecece;
        font-size: 14px
    }

    .rating-color {
        color: #fbc634 !important
    }

    .review-count {
        font-weight: 400;
        margin-bottom: 2px;
        font-size: 14px !important
    }

    .small-ratings i {
        color: #cecece
    }

    .review-stat {
        font-weight: 300;
        font-size: 18px;
        margin-bottom: 2px
    }
</style>
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
            <div >

                <div class="row">

                    <?php 
                    $TS = number_format($grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
                    $TC = $grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

                    $totalScore = isset($TS)?$TS:0;
                    $totalCount = isset($TC)?$TC:0;
                ?>

                <div class="col-md-12">
                    <div class="panel panel-info">

                        <div class="col-lg-12 invoiceBody">
                            <div class="invoice-details mt25 row">

                                <div class="well col-6">
                                    <ul class="list-unstyled mb0">
                                        <li>
                                            <div class="ratings">
                                                <a href="{{route('pms.supplier.profile',$grn->relPurchaseOrder->relQuotation->relSuppliers->id)}}" target="_blank"><span>Rating:</span></a> {!!ratingGenerate($totalScore,$totalCount)!!}
                                            </div>
                                            <h5 class="review-count"></h5>
                                        </li>
                                        <li><strong>{{__('Supplier') }} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->name}}</li>
                                        <li><strong>{{__('Email')}} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->email}}</li>
                                        <li><strong>{{__('Phone')}} :</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->phone}}</li>
                                        <li><strong>{{__('Address')}}:</strong> {{$grn->relPurchaseOrder->relQuotation->relSuppliers->address}}</li>

                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-unstyled mb0 pull-right">
                                        <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($grn->received_date))}}</li>
                                        <li><strong>{{__('GRN Reference No.')}}:</strong> {{$grn->reference_no}}</li>
                                        <li><strong>{{__('Challan No.')}}:</strong> {{$grn->challan}}</li>
                                        <li><strong>{{__('Receive Qty.')}}:</strong> {{$grn->rel_goods_received_items->sum('qty')}}</li>
                                        <li><strong>{{__('Receive Status.')}}:</strong> <span class="capitalize"> {{$grn->received_status}}</span></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">

                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            
                                            <th>WareHouse</th>
                                            <th class="text-center">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sumOfLeftQty=0;
                                        
                                    ?>
                                    @foreach($grn->rel_goods_received_items as $key=>$item)
                                    <?php 
                                    $sumOfLeftQty +=($item->qty-$item->received_qty);

                                ?>
                                
                                <tr id="removeApprovedRow{{$item->id}}">
                                    <td>{{$key+1}}</td>
                                    <td>{{$item->relProduct->category->name}}</td>
                                    <td>{{$item->relProduct->name}}</td>
                                    <td>{{$item->unit_amount}}</td>
                                    <td>{{number_format($item->qty,0)}}</td>
                                    <td>{{$item->sub_total}}</td>
                                    
                                    <td class="text-center">
                                        <div class="input-group input-group-lg mb-3 d-">
                                            <select style="width:200px" name="warehouse_id" id="warehouse_id{{$item->id}}" class="form-control rounded">
                                                <option value="">Select One</option>
                                                @foreach($wareHouses as $key=> $values)
                                                <option value="{{ $values->id }}">{{ $values->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                <span id="statusName{{$item->id}}" title="Click Here To Quality Ensure">
                                                    {{ucwords($item->quality_ensure)}}
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    @if($item->quality_ensure=='pending')
                                                    @can('quality-ensure-approved')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Approved" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-status="approved">{{ __('Approved')}}
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    @endif

                                                    @can('quality-ensure-return')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Return" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-status="return">{{ __('Return')}}
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @can('quality-ensure-return-change')
                                                    <li>
                                                        <a href="javascript:void(0)" title="Click Here To Return Change" class="qualityEnsureStatusChange" data-id="{{$item->id}}" data-status="return-change">{{ __('Return Change')}}
                                                        </a>
                                                    </li>
                                                    @endcan

                                                </li>
                                            </ul>

                                        </div>
                                    </td>
                                </tr>
                                
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td colspan="">{{isset($grn->rel_goods_received_items)?number_format($grn->rel_goods_received_items->sum('qty'),0):0}}</td>
                                    <td colspan="">{{isset($grn->rel_goods_received_items)?number_format($grn->rel_goods_received_items->sum('sub_total'),2):0}}</td>
                                    
                                    <td>{{isset($sumOfLeftQty)?number_format($sumOfLeftQty,0):0}}</td>

                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label for="remarks"><strong>GRN Note</strong>:</label>
                        <span>{!! $grn->note?$grn->note:'' !!}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="modal" id="qualityensureModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quality Ensure</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <form action="{{route('pms.quality.ensure.status.save')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="ReturnQty">

                    </div>
                    <div class="form-group">
                        <label for="return_note">Note :</label>
                        <textarea class="form-control" name="return_note" rows="3" id="return_note" placeholder="Write down here reason for return"></textarea>

                        <input type="hidden" readonly required name="id" id="goodsReceiveItemsId">
                        <input type="hidden" readonly required name="quality_ensure" id="QualityEnsureStatus">
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>
    (function ($) {
        "use script";

        $('.qualityEnsureStatusChange').on('click', function () {

            let id = $(this).attr('data-id');
            let quality_ensure = $(this).attr('data-status');
            let warehouse_id = $('#warehouse_id'+id).val();

            if(quality_ensure==='return-change' || quality_ensure==='return'){
                if (warehouse_id =='') {
                    notify('Please Select Warehouse/Store','error');
                }else{

                    $('#goodsReceiveItemsId').val(id);
                    $('#QualityEnsureStatus').val(quality_ensure);

                    $('#ReturnQty').empty().append('<label for="return_qty">Return Qty :</label><input type="number" class="form-control" placeholder="0" required name="return_qty" id="return_qty"><input type="hidden" class="form-control" placeholder="0" name="warehouse_id" id="warehouse_id" value="'+warehouse_id+'">');

                    return $('#qualityensureModal').modal('show').on('hidden.bs.modal', function (e) {
                        let form = document.querySelector('#qualityensureModal').querySelector('form').reset();

                    });
                }

            }else if(quality_ensure==='approved'){
                if (warehouse_id =='') {
                    notify('Please Select Warehouse/Store','error');
                }else{
                    swal({
                        title: "{{__('Are you sure?')}}",
                        text: 'Would you like to ensure the quality of this product & approved It?',
                        icon: "warning",
                        dangerMode: true,
                        buttons: {
                            cancel: true,
                            confirm: {
                                text: 'Approved',
                                value: true,
                                visible: true,
                                closeModal: true
                            },
                        },
                    }).then((value) => {
                        if(value){
                            $.ajax({
                                url: "{{ url('pms/quality-ensure/ensure-status-save') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {_token: "{{ csrf_token() }}", id:id,quality_ensure:quality_ensure,warehouse_id:warehouse_id},
                            })
                            .done(function(response) {
                                if(response.success){

                                    $('#statusName'+id).html(response.new_text);
                                    $('#removeApprovedRow'+id).hide();

                                    notify(response.message,'success');
                                }else{
                                    notify(response.message,'error');
                                }
                            })
                            .fail(function(response){
                                notify('Something went wrong!','error');
                            });

                            return false;
                        }
                    });
                } //end elseif
            }

        });

    })(jQuery);
</script>
@endsection
@else
<script type="text/javascript">
    window.location = "{{ url('pms/grn/grn-process') }}";
</script>
@endif