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
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <form  method="post" action="{{ route('pms.rfp.quotations.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="quotation_date">{{ __(' Date') }}:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        <input type="text" name="quotation_date" id="quotation_date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm"  required readonly value="{{ old('quotation_date')?old('quotation_date'):date('d-m-Y') }}">
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
                                        <select name="supplier_id" id="supplier_id" class="form-control rounded" required>
                                            <option value="{{ null }}">{{ __('Select One') }}</option>
                                            @if($requestProposal->defineToSupplier)
                                            @foreach($requestProposal->defineToSupplier()->whereNotIn('supplier_id',$quotationSupplier_array)->get() as $key=>$data)
                                            <option value="{{ $data->supplier->id }}">{{ $data->supplier->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="payment_term">{{ __('Payment Term') }}:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        {{ Form::select('payment_term', $supplierPaymentTerms, null, ['class'=>'form-control', 'placeholder'=>'Please select One','id'=>'payment_term']) }}
                                    </div>
                                </div> --}}

                                <!-- <div class="col-md-2 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="payment_percent">{{ __('Payment Percent %') }}:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        {{ Form::number('payment_percent', old('payment_percent'), ['class'=>'form-control', 'placeholder'=>'0%','min'=>'0','max'=>'100','step'=>'0.1','id'=>'payment_percent']) }}
                                    </div>
                                </div> -->

                                <div class="col-md-4 col-sm-12">
                                    <p class="mb-1 font-weight-bold"><label for="QuotationFile">{{ __('Quotation File (Pdf)') }} *:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        <input type="file" name="quotation_file" id="QuotationFile" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="" accept="application/pdf">
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
                                            <th>Qty</th>
                                            <th>Item Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($requestProposal->requestProposalDetails))
                                        
                                        @foreach($requestProposal->requestProposalDetails as $key=>$item)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->product->category?$item->product->category->name:''}}</td>
                                            <td>
                                                {{$item->product->name}}
                                                <input type="hidden" name="product_id[]" class="form-control" value="{{$item->product->id}}">
                                            </td>
                                            <td>
                                                <input type="number" name="unit_price[{{$item->product->id}}]" required class="form-control" onKeyPress="if(this.value.length==8) return false;"  min="0.0" step="1"  id="unit_price_{{$item->product->id}}" placeholder="0.00" onkeyup="calculateSubtotal({{$item->product->id}})">
                                            </td>

                                            <td>
                                                <input type="number" name="qty[{{$item->product->id}}]" required class="form-control"  min="0" id="qty_{{$item->product->id}}" value="{{round($item->request_qty)}}" onKeyPress="if(this.value.length==4) return false;" onkeyup="calculateSubtotal({{$item->product->id}})">
                                            </td>
                                            <td>
                                                <input type="number" name="sub_total_price[{{$item->product->id}}]" required class="form-control calculateSumOfSubtotal" readonly id="sub_total_price_{{$item->product->id}}" placeholder="0.00">
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <tr>
                                            <td colspan="5" class="text-right">Sub Total</td>
                                            <td>
                                                <input type="number" name="sum_of_subtoal" readonly class="form-control" id="sumOfSubtoal" placeholder="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Discount (%)</td>
                                            <td>
                                                <input type="number" onkeyup="discountCalculate()" name="discount" class="form-control" id="discount" placeholder="0.00">
                                                <input type="hidden" id="sub_total_with_discount" name="sub_total_with_discount"  min="0" placeholder="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Vat (%)</td>
                                            <td><input type="number" onkeyup="vatCalculate()" name="vat" class="form-control" id="vat"  min="0" placeholder="0.00"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right">Gross Amount</td>
                                            <td><input type="number" name="gross_price" readonly class="form-control" id="grossPrice" placeholder="0.00"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-row">

                                <input type="hidden" name="request_proposal_id" value="{{$requestProposal->id}}">
                                <input type="hidden" name="type" value="manual">

                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-success rounded">{{ __('Generate Quotation') }}</button>
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
        let parcentage = parseFloat($('#vat').val()).toFixed(2);
        let vat = (parcentage * price)/100;
        let total = parseInt(price)+parseInt(vat);
        
        $('#grossPrice').val(parseFloat(total).toFixed(2));
    }
</script>
@endsection