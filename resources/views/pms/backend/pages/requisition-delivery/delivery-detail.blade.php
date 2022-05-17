
<div class="table-responsive">

    <h5>Delivery Product with Qty </h5>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Sl No.</th>
            <th>Category</th>
            <th>Product</th>
            <th>Delivery Qty</th>
        </tr>
        </thead>
        <tbody>

        @if(isset($requisitionDelivery))
            @php
                $totalDeliveryQty = 0;
            @endphp
            @foreach($requisitionDelivery->relDeliveryItems as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->product->category->name}}</td>
                    <td>{{$item->product->name}}</td>
                    <td>{{round($item->delivery_qty)}}</td>
                </tr>

                @php
                    $totalDeliveryQty += $item->delivery_qty;
                @endphp

            @endforeach
        @endif

        <tr>
            <td colspan="3" class="text-right">Total</td>
            <td colspan="">{{$totalDeliveryQty}}</td>
        </tr>

        </tbody>
    </table>
</div>