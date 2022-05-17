
<div class="table-responsive">

    <strong class="mb-1"> <span> Reference No: {{$requisition->reference_no}} </span> <span class="pull-right">Date: {{date('d-m-Y  h:i a',strtotime($requisition->requisition_date))}}</span> </strong>
    
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Sl No.</th>
            
            <th>Category</th>
            <th>Product</th>
            @if(Route::current()->getName() ==='pms.requisition.list.view.show')
            <th>Stock Qty</th>
            @endif
            <th>Requisition Qty</th>
        </tr>
        </thead>

        <tbody>
        @php 
        $total_stock_qty = 0;
        $total_requision_qty = 0;
        @endphp
        @forelse($requisition->items as $key=>$item)

            <tr>
                <td>{{$key+1}}</td>
                
                <td>{{$item->product->category->name}}</td>
                <td>{{$item->product->name}}</td>
                @if(Route::current()->getName() ==='pms.requisition.list.view.show')
                <td>{{isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0}}</td>
                @endif
                <td>{{$item->qty}}</td>
            </tr>

             @php 
             if(Route::current()->getName() ==='pms.requisition.list.view.show'){
             $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
             }
             
             $total_requision_qty += $item->qty;
            @endphp
        @empty

        @endforelse
        <tr>
            <td colspan="3" class="text-right">Total</td>
             @if(Route::current()->getName() ==='pms.requisition.list.view.show')
            <td colspan="">{{$total_stock_qty}}</td>
            @endif
            <td colspan="">{{$total_requision_qty}}</td>
        </tr>
        </tbody>
    </table>
    <div>
        <strong> Note: </strong>
        {{$requisition->remarks}}
    </div>
    @if($requisition->status==2 && !empty($requisition->admin_remark))
        <div>
            <strong> Holding Reason: </strong>
            {!!$requisition->admin_remark!!}
        </div>

    @endif

</div>