@if(isset($purchaseOrder))
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
<div class="row">
    
    <?php 
         $TS = number_format($purchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
         $TC = $purchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

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
                            <a href="{{route('pms.supplier.profile',$purchaseOrder->relQuotation->relSuppliers->id)}}" target="_blank"><span>Rating:</span></a> {!!ratingGenerate($totalScore,$totalCount)!!}
                        </div>
                        <h5 class="review-count"></h5>
                    </li>
                    <li><strong>{{__('Supplier') }} :</strong> {{$purchaseOrder->relQuotation->relSuppliers->name}}</li>
                    <li><strong>{{__('Email')}} :</strong> {{$purchaseOrder->relQuotation->relSuppliers->email}}</li>
                    <li><strong>{{__('Phone')}} :</strong> {{$purchaseOrder->relQuotation->relSuppliers->phone}}</li>

                </ul>
            </div>
            <div class="col-6">
                <ul class="list-unstyled mb0 pull-right">
                    <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($purchaseOrder->po_date))}}</li>
                    <li><strong>{{__('Reference No')}}:</strong> {{$purchaseOrder->reference_no}}</li>
                    <li><strong>{{__('Quotation No')}}:</strong> {{$purchaseOrder->relQuotation->reference_no}}</li>
            </ul>
            </div>
            
        </div>
    </div>
    <div class="table-responsive">

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>


                @foreach($purchaseOrder->relPurchaseOrderItems as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->relProduct->category->name}}</td>
                    <td>{{$item->relProduct->name}}</td>
                    <td>{{$item->unit_price}}</td>
                    <td>{{number_format($item->qty,0)}}</td>
                    <td>{{$item->sub_total_price}}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="3" class="text-right">Total</td>
                    <td colspan="">{{$purchaseOrder->relPurchaseOrderItems->sum('unit_price')}}</td>
                    <td colspan="">{{$purchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                    <td colspan="">{{$purchaseOrder->relPurchaseOrderItems->sum('sub_total_price')}}</td>
                </tr>

                <tr>
                    <td colspan="5" class="text-right">Discount (%)</td>
                    <td>-<?= $discount = ($purchaseOrder->discount * $purchaseOrder->relPurchaseOrderItems->sum('sub_total_price'))/100; ?> ({{$purchaseOrder->discount}})</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Vat (%)</td>
                    <td>+<?= $vat = ($purchaseOrder->vat * $purchaseOrder->relPurchaseOrderItems->sum('sub_total_price'))/100; ?> ({{$purchaseOrder->vat}})</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Amount</strong></td>
                    <td><strong>{{$purchaseOrder->gross_price}}</strong></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    <div class="form-group">
        <label for="remarks"><strong>Remarks</strong>:</label>

        <span>{!! $purchaseOrder->remarks?$purchaseOrder->remarks:'' !!}</span>

    </div>
    
</div>
</div>

</div>
@endif