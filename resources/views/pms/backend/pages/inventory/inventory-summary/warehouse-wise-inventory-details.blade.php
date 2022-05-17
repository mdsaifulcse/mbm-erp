<div class="table-responsive style-scroll">
    <h5> <span> Category: {{$product->category->name}} </span> || <span>Product Name:  {{$product->name}}</span>|| <span>Total Qty:  {{$product->relInventorySummary->qty}}</span></h5>

    <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
        <thead>
            <tr>
                <th width="5%">{{__('SL No.')}}</th>
                <th>{{__('Warehouses')}}</th>
                <th>{{__('Unit Price')}}</th>
                <th>{{__('Qty')}}</th>
                <th>{{__('Price')}}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($product->relInventoryDetails))
            @foreach($product->relInventoryDetails as $key=> $values)
            
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>{{$values->relWarehouse->name}}</td>
                <td>{{number_format($values->unit_price,2)}}</td>
                <td>{{$values->qty}}</td>
                <td>{{number_format($values->total_price,2)}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right">Total</td>
                
                <td>{{$product->relInventorySummary->qty}}</td>
                <td>{{number_format($product->relInventorySummary->total_price,2)}}</td>
            </tr>

            @endif
        </tbody>
    </table>
</div>