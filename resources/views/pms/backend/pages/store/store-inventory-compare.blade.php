
<div class="table-responsive">

    <h5>  Store Requisition Compare </h5>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Sl No.</th>
            <th>Category</th>
            <th>Product</th>
            <th>Stock Qty</th>
            <th>Requisition Qty</th>
        </tr>
        </thead>
        <tbody>

        @if(isset($requisition))
        @php 
            $total_stock_qty = 0;
            $total_requision_qty = 0;
        @endphp
        @foreach($requisition as $key=>$item)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$item->product->category->name}}</td>
                <td>{{$item->product->name}}</td>
                <td>{{isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0}}</td>
                <td>{{$item->qty}}</td>
            </tr>

            @php 
            
             $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
             $total_requision_qty += $item->qty;
            @endphp

        @endforeach
        @endif

        <tr>
            <td colspan="3" class="text-right">Total</td>
            <td colspan="">{{$total_stock_qty}}</td>
            <td colspan="">{{$total_requision_qty}}</td>
        </tr>

        </tbody>
    </table>
</div>