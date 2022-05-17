<table class="table table-striped table-bordered miw-500" cellspacing="0" width="100%" id="dataTable">
    <thead>
        <tr>
            <th width="2%">SL No</th>
            <th>Requisition Date</th>
            <th>Delivered Date</th>
            <th>Requisition RefNo</th>
            <th>Delivered RefNo</th>
            <th>Category</th>
            <th>SubCategory</th>
            <th>Product</th>
            <th>Qty</th>
            
            <th class="text-center">Option</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($delivered_requisition))
        @foreach($delivered_requisition as $key => $values)
        <tr>
            <td width="5%">{{($delivered_requisition->currentpage()-1) * $delivered_requisition->perpage() + $key + 1 }}</td>
            <td>{{ date('d-m-Y',strtotime($values->relRequisitionDelivery->relRequisition->requisition_date)) }}</td>

            <td>{{ date('d-m-Y',strtotime($values->relRequisitionDelivery->delivery_date)) }}</td>

            <td><a href="javascript:void(0)" onclick="openRequisitionDetailsModal({{$values->relRequisitionDelivery->relRequisition->id}})"  class="btn btn-link requisition m-1 rounded">{{ $values->relRequisitionDelivery->relRequisition->reference_no }}</a></td>
            <td>
                {{$values->relRequisitionDelivery->reference_no}}
            </td>

            <td>
                {{ $values->product->category->category->name }}
            </td>

            <td>
                {{ $values->product->category->name }}
            </td>
            <td>{{ $values->product->name }}</td>
            <td>{{ number_format($values->delivery_qty,0)}}</td>

            <td class="text-center" id="action{{$values->id}}">
                @if($values->status=='pending')
                <div class="btn-group">
                    <button class="btn dropdown-toggle" data-toggle="dropdown">
                        <span id="statusName{{$values->id}}">
                            {{ ucfirst($values->status)}}
                        </span>
                    </button>
                    <ul class="dropdown-menu">
                     <li id="hideBtn{{$values->id}}"><a href="javascript:void(0)" class="deliveredAcknowledge" data-id="{{$values->id}}" title="Acknowledged"><i class="la la-check"></i> {{ __('Acknowledged')}}</a>
                     </li>
                 </ul>
             </div>
             @else
             Acknowledged
             @endif
         </td>
     </tr>
     @endforeach
     @endif
 </tbody>

</table>
<div class="la-1x text-center">
    @if(count($delivered_requisition)>0)
    <ul class="searchPagination">
        {{$delivered_requisition->links()}}
    </ul>
    @endif
</div>