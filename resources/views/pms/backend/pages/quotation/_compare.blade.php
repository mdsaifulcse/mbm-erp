@if(isset($quotations))
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
    @foreach($quotations as $quotation)
    <?php 
         $TS = number_format($quotation->relSuppliers->SupplierRatings->sum('total_score'),2);
         $TC = $quotation->relSuppliers->SupplierRatings->count();

         $totalScore = isset($TS)?$TS:0;
         $totalCount = isset($TC)?$TC:0;
    ?>

    <div class="col-md-<?=$quotations->count()>1?6:12?>">
        <div class="panel panel-info">
         
    <div class="col-lg-12 invoiceBody">
        <div class="invoice-details mt25 row">
            
            <div class="well col-6">
                <ul class="list-unstyled mb0">
                    <li>

                        <div class="ratings">
                            <a href="{{route('pms.supplier.profile',$quotation->relSuppliers->id)}}" target="_blank"><span>Rating:</span></a> {!!ratingGenerate($totalScore,$totalCount)!!}

                        </div>
                        <h5 class="review-count"></h5>
                    </li>
                    <li><strong>{{__('Supplier')}} :</strong> {{$quotation->relSuppliers->name}}</li>
                     <li><strong>{{__('Date')}} :</strong> {{date('d-m-Y',strtotime($quotation->quotation_date))}}</li>
                </ul>
            </div>
            <div class="col-6">
                <ul class="list-unstyled mb0 pull-right">
                   
                    <li><strong>{{__('Reference No')}}:</strong> {{$quotation->reference_no}}</li>
                    <li><strong>{{__('RFP No')}}:</strong> {{$quotation->relRequestProposal->reference_no}}</li>
                   
                    <li>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="quotation_id[]" id="is_approved_{{$quotation->id}}" value="{{$quotation->id}}">
                          <input type="hidden" name="request_proposal_id" value="{{$quotation->request_proposal_id}}">
                          <label class="form-check-label" for="is_approved_{{$quotation->id}}">
                            <strong>Request For Approval</strong>
                        </label>
                    </div>
                </li>
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
                    <th>Item Total</th>
                </tr>
            </thead>
            <tbody>


                @foreach($quotation->relQuotationItems as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->relProduct->category->name}}</td>
                    <td>{{$item->relProduct->name}}</td>
                    <td>{{$item->unit_price}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{$item->total_price}}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="3" class="text-right">Total</td>
                    <td colspan="">{{$quotation->relQuotationItems->sum('unit_price')}}</td>
                    <td colspan="">{{$quotation->relQuotationItems->sum('qty')}}</td>
                    <td colspan="">{{$quotation->relQuotationItems->sum('total_price')}}</td>
                </tr>

                <tr>
                    <td colspan="5" class="text-right">Discount (%)</td>
                    <td>-<?= $discount = ($quotation->discount * $quotation->relQuotationItems->sum('total_price'))/100; ?> ({{$quotation->discount}})</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Vat (%)</td>
                    <td>+<?= $vat = ($quotation->vat * $quotation->relQuotationItems->sum('total_price'))/100; ?> ({{$quotation->vat}})</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Amount</strong></td>
                    <td><strong>{{$quotation->gross_price}}</strong></td>
                </tr>

            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="payment_term_id{{$quotation->id}}"><strong>Supplier Term&Condition</strong>:</label>
                <span>
                    <?php
                   echo Str::limit($quotation->relSuppliers->term_condition,200);
                    ?>
                </span>

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label for="note"><strong>Note </strong>:</label>

                <textarea class="form-control" name="note[]" rows="1" id="note" placeholder="What is the reason for choosing this supplier?"></textarea>

            </div>
        </div>
    </div>
    
    
    
    
</div>
</div>
@endforeach
</div>

@endif