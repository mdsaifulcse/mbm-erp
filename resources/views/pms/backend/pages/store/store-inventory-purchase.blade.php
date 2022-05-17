@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
<?php
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Request;
    ?>
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
                   <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form  method="post" id="updateInventoryForm" action="{{ route('pms.rfp.store-requistion.purchase') }}">
                        @csrf
                        <div class="panel-body">

                             <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('request_date', 'RFE Date', array('class' => 'mb-1 font-weight-bold')) !!} 
                                            {!! Form::text('request_date',Request::old('request_date')?Request::old('request_date'):date('d-m-Y'),['id'=>'request_date','class' => 'form-control rounded air-datepicker','placeholder'=>'','readonly'=>'readonly']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('reference_no', 'Ref No', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::text('reference_no',Request::old('reference_no'),['id'=>'reference_no','required'=>true,'class' => 'form-control rounded','placeholder'=>'Enter Reference No']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('supplier_id', 'Supplier', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::Select('supplier_id', $supplierList ,Request::old('supplier_id'),['id'=>'supplier_id','required'=>true,'class'=>'form-control rounded select2 select2-tags',]) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="QuotationFile">{{ __('Quotation File (Pdf)') }} *:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        <input type="file" name="quotation_file" id="QuotationFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="application/pdf">
                                    </div>
                                </div>


                            </div><!--end row -->
                            <div class="table-responsive">

                                <h5>Summary Table</h5>

                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit Price</th>

                                            <th>Requistion Qty</th>
                                            <th>Sub Total</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($requisition))
                                        @php 
                                        $total_stock_qty = 0;
                                        $total_requision_qty = 0;
                                        $itemTotal=0;
                                        $subTotalPrice=0;
                                        @endphp
                                        @foreach($requisition as $key=>$item)
                                        <tr id="SelectedRow{{$item->product->id}}">
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->product->category->name}}</td>
                                            <td>
                                                {{$item->product->name}}
                                                <input type="hidden" name="product_id[]" class="form-control" value="{{$item->product->id}}">
                                            </td>
                                            
                                            <td>

                                                <input type="number" name="unit_price[{{$item->product->id}}]" required class="form-control"  min="0.0" value="{{$item->product->unit_price}}"  id="unit_price_{{$item->product->id}}" readonly placeholder="0.00" onkeyup="calculateSubtotal({{$item->product->id}})">

                                            </td>

                                            <td>
                                                <input type="number" name="qty[{{$item->product->id}}]" required class="form-control"  min="0" id="qty_{{$item->product->id}}" value="{{round($item->qty)}}" onkeyup="calculateSubtotal({{$item->product->id}})">
                                            </td>
                                            <td>

                                                <?php
                                                $itemTotal=$item->product->unit_price*round($item->qty);

                                                $subTotalPrice+=$itemTotal;

                                                ?>
                                                <input type="number" name="sub_total_price[{{$item->product->id}}]" value="{{$itemTotal}}" required readonly class="form-control calculateSumOfSubtotal" id="sub_total_price_{{$item->product->id}}">

                                            </td>
                                            
                                        </tr>

                                        @endforeach
                                        @endif

                                        <tr>
                                            <td colspan="5" class="text-right">Total Price</td>
                                            <td>
                                                <input type="number" name="sum_of_subtoal" value="{{$subTotalPrice}}" readonly class="form-control" id="sumOfSubtoal" placeholder="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Discount (%)</td>
                                            <td>
                                                <input type="number" onkeyup="discountCalculate()" name="discount" class="form-control" id="discount" placeholder="0.00">
                                                <input type="hidden" value="{{$subTotalPrice}}" id="sub_total_with_discount" name="sub_total_with_discount"  min="0" placeholder="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Vat (%)</td>
                                            <td><input type="number" onkeyup="vatCalculate()" name="vat" class="form-control" id="vat"  min="0" placeholder="0.00"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Gross Amount</td>
                                            <td><input type="number" name="gross_price" value="{{$subTotalPrice}}" readonly class="form-control" id="grossPrice" placeholder="0.00"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                             <div class="form-row">
                                    <input type="hidden" name="requisition_id" value="{{$req_id}}">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-success rounded">{{ __('Send To Purchase') }}</button>
                                    </div>
                                </div>
                        </div>

                    </form>
                </div>
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
        let qty = $('#qty_'+id).val();

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
        let parcentage = $('#vat').val();
        console.log(parcentage)
        let vat = (parcentage * price)/100;
        let total = parseInt(price)+parseInt(vat);
        
        $('#grossPrice').val(parseFloat(total).toFixed(2));
    }
</script>
@endsection