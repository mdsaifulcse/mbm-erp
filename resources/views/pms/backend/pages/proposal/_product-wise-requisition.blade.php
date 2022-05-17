<div class="table-responsive style-scroll">
    <h5> <span> Product Name: {{$product->name}} </span> || <span>Total Requistion:  {{$product->requisitionItem->count()}}</span></h5>

    <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
        <thead>
            <tr>
                <th width="5%">{{__('SL No.')}}</th>
                <th>{{__('Reference No')}}</th>
                <th>{{__('Requisition By')}}</th>
                <th>{{__('Date')}}</th>
                <th class="text-center">{{__('Items')}}</th>
                <th class="text-center">{{__('Requested Qty')}}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($items[0]))

            @foreach($items as $key=> $item)
            
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>{{$item->requisition->reference_no}}</td>
                <td>{{$item->requisition->relUsersList->name}}</td>
                <td>
                    {{date("Y-m-d", strtotime($item->requisition->requisition_date))}}
                </td>
                <td>{{$product->name}}</td>
                <td>{{$item->qty}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>Total</strong></td>
                <td>{{$items->sum('qty')}}</td>
            </tr>

            @endif
        </tbody>
    </table>
    
</div>