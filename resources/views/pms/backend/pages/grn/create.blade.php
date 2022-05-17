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
                    <a href="{{ route('pms.grn.po.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <form  method="post" action="{{ route('pms.grn.grn-process.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="received_date">{{ __(' Date') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="received_date" id="received_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm"  required readonly value="{{ old('received_date')?old('received_date'):date('d-m-Y') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="reference_no">{{ __('Reference No') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="reference_no" id="reference_no" class="form-control rounded" readonly aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ ($refNo)?($refNo):0 }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="supplier_id">{{ __('Supplier') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" readonly class="form-control rounded" value="{{$purchaseOrder->relQuotation->relSuppliers->name}}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="challan">{{ __('Challan No.') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    {{ Form::text('challan', '', ['class'=>'form-control', 'placeholder'=>'Enter Challan Number here','id'=>'challan','required'=>'required']) }}

                                </div>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <p class="mb-1 font-weight-bold"><label for="challanFile">{{ __('Challan Photo') }} *:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="file" name="challan_file" id="challanFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="image/*">
                                    <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-10">
                            <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Po Qty</th>
                                        <th>Prv.Rcv Qty</th>
                                        <th>Receiving Qty</th>
                                        <th>Left Qty</th>
                                        <th>Price</th>
                                        {{--<th>Store Location</th>--}}
                                    </tr>
                                </thead>
                                <tbody>

                                <?php
                                $totalReceiveQty=0;
                                $totalPrice=0;
                                ?>

                                    @if(isset($purchaseOrder->relPurchaseOrderItems))

                                    @foreach($purchaseOrder->relPurchaseOrderItems as $key=>$item)


                                     <?php
                                        $leftQty=number_format($item->grn_qty??$item->qty);
                                        $receiveQty=0;

                                        if (count($item->relReceiveProduct)>0){

                                            $receiveQty=$item->grn_qty??0;
                                            $totalReceiveQty+=$receiveQty;
                                        }
                                        $leftQty=number_format($item->qty,0)-$receiveQty

                                        ?>
                                    @if($leftQty !=0)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->relProduct->category?$item->relProduct->category->name:''}}</td>
                                        <td>
                                            {{$item->relProduct->name}}
                                            <input type="hidden" name="product_id[]" class="form-control" value="{{$item->relProduct->id}}">
                                        </td>

                                        <td>
                                            <input type="text" name="unit_price[{{$item->relProduct->id}}]" class="form-control"  min="0.0"  id="unit_price_{{$item->relProduct->id}}" value="{{number_format($item->unit_price,2)}}" readonly placeholder="0">
                                        </td>

                                       

                                        <td>
                                            {{number_format($item->qty,0)}}
                                            <input type="hidden" value="{{$leftQty}}" id="main_qty_{{$item->relProduct->id}}">
                                        </td>

                                        {{--@if(count($item->relReceiveProduct)>0)--}}
                                        <td>
                                            {{$receiveQty}}
                                        </td>
                                            {{--@endif--}}


                                        <td>
                                            <input type="number" name="qty[{{$item->relProduct->id}}]"  class="form-control"  min="0"  max="{{$leftQty}}" id="receive_qty_{{$item->relProduct->id}}" value="{{$leftQty}}" placeholder="0" onkeyup="calculateSubtotal({{$item->relProduct->id}})">
                                        </td>

                                        <td id="left_qty_{{$item->relProduct->id}}">
                                            {{--{{$leftQty}}--}}
                                        </td>

                                        <td>
                                            <?php

                                            $unit_amount=$leftQty*$item->unit_price;

                                            $totalPrice+=$unit_amount;

                                            ?>

                                            <input type="text" name="unit_amount[{{$item->relProduct->id}}]" value="{{number_format($unit_amount,2)}}" required readonly class="form-control calculateSumOfSubtotal" id="sub_total_price_{{$item->relProduct->id}}" placeholder="0">
                                        </td>
                                        {{--<td>--}}
                                          {{--<?php $productId=$item->relProduct->id?>--}}
                                                {{--{{ Form::select("warehouse_id[$productId]", $wareHouses, [], array('id'=>"warehouse_id_$productId",'class'=>'form-control', 'placeholder'=>'Please select ...')) }}--}}
                                        {{--</td>--}}
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif

                                </tbody>
                            </table>

                        </div>

                        <div class="row">
                            <br>
                            <br>
                            {{--<div class="col-md-4">--}}

                                {{--<textarea name="delivery_by" class="form-control" rows="2" placeholder="Delivery By"></textarea>--}}
                            {{--</div>--}}
                            <div class="col-md-8">

                                <textarea name="note" class="form-control" rows="2" placeholder="GRN Notes"></textarea>
                            </div>

                            <div class="col-md-4">
                                <table align="right" class="table table-striped table-bordered table-head">
                                    <tbody>
                                    <tr>
                                        <td colspan="6" class="text-right">Total Price</td>
                                        <td>
                                            <input type="text" value="{{number_format($totalPrice,2)}}" name="total_price" required readonly class="form-control" id="sumOfSubtoal" placeholder="0.00">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="6" class="text-right">Gross Amount</td>
                                        <td><input type="text" value="{{number_format($totalPrice,2)}}" name="gross_price" readonly required class="form-control" id="grossPrice" placeholder="0.00"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="form-row">

                        <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
                        
                        <div class="col-12 text-right">
                                <button type="submit" class="btn btn-primary rounded">{{ __('Receive Product') }}</button>
                        </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>
    "use strcit"

    function calculateSubtotal(id) {

        let unit_price = $('#unit_price_'+id).val();
        let qty = $('#receive_qty_'+id).val();
        let mainQty = $('#main_qty_'+id).val();

        let leftQty = $('#left_qty_'+id).text(mainQty-qty);

        if (qty>=1){
            $('#warehouse_id_'+id).attr('required',true);
        }else {
            $('#warehouse_id_'+id).attr('required',false);
        }

        if(unit_price !='' && qty !=''){

            let sub_total = parseFloat(unit_price*qty).toFixed(2);
            $('#sub_total_price_'+id).val(sub_total);

            var total=0
            $(".calculateSumOfSubtotal").each(function(){
                total += parseFloat($(this).val()||0);
            });
            $("#sumOfSubtoal").val(parseFloat(total).toFixed(2));

            discountCalculate();

        }else{
            notify('Please enter unit price and qty!!','error');
        }

        return false;
    }

    function discountCalculate() {
        let sumOfSubtoal = parseFloat($('#sumOfSubtoal').val()||0).toFixed(2);
        let discount = parseFloat($('#discount').val()||0).toFixed(2);

        if(sumOfSubtoal !=null && discount !=null){
            let value = (discount * sumOfSubtoal)/100;
            let grossPrice = parseInt(sumOfSubtoal)-parseInt(value);

            $('#grossPrice').val(parseFloat(grossPrice).toFixed(2));
            $('#sub_total_with_discount').val(parseFloat(grossPrice).toFixed(2));
        }
        return false;
    }

    function vatCalculate() {

        let price = parseFloat($('#sub_total_with_discount').val()).toFixed(2);
        let parcentage = parseFloat($('#vat').val()).toFixed(2);
        let vat = (parcentage * price)/100;
        let total = parseInt(price)+parseInt(vat);
        
        $('#grossPrice').val(parseFloat(total).toFixed(2));
    }
</script>
@endsection