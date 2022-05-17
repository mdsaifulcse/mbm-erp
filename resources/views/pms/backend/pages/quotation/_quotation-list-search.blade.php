<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
    <thead>
        <tr>
            <th width="5%">{{__('SL No.')}}</th>
            <th>{{__('Date')}}</th>
            <th>{{__('Reference No')}}</th>
            <th>{{__('Supplier')}}</th>
            <th>{{__('Request Proposal')}}</th>
            <th>{{__('Price')}}</th>
            <th>{{__('Discount %')}}</th>
            <th>{{__('Vat %')}}</th>
            <th>{{__('Total Price')}}</th>
            <th>{{__('Note')}}</th>
            <th>{{__('Quotation Type')}}</th>
            <th class="text-center">{{__('Option')}}</th>
        </tr>
    </thead>
    <tbody>
        @if(count($quotationList)>0)
        @foreach($quotationList as $key=> $values)
        <tr>
            <td>{{ ($quotationList->currentpage()-1) * $quotationList->perpage() + $key + 1 }}</td>
            <td>{{date('d-m-Y',strtotime($values->quotation_date))}}</td>
            <td> <a href="javascript:void(0)" class="btn btn-link" onclick="openModal({{$values->id}})">{{$values->reference_no}}</a></td>
            <td>{{$values->relSuppliers->name}}</td>
            <td>{{$values->relRequestProposal->reference_no}}</td>
            <td>{{$values->total_price}}</td>
            <td>{{$values->discount}} %</td>
            <td>{{$values->vat}} %</td>
            <td>{{$values->gross_price}}</td>
            <td>{{ucfirst($values->note)}}</td>
            <td>{{ucfirst($values->type)}}</td>


            <td class="text-center action">
                <div class="btn-group">
                    <button class="btn dropdown-toggle" data-toggle="dropdown">
                        <span id="statusName{{$values->id}}">
                            {{ucfirst($values->is_approved)}}
                        </span>
                    </button>
                    <ul class="dropdown-menu">
                     <li>
                        <a href="javascript:void(0)" onclick="openModal({{$values->id}})">View</a>

                    </li>
                    
                    @if($values->is_approved === 'approved')
                    @can('quotation-halt')
                    <li>
                        <a class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="halt">{{ __('Halt')}}
                        </a>
                    </li>
                    @endcan
                    @endif
                    @if($values->is_approved === 'approved')
                    @can('generate-po') 
                    <li>
                        <a class="generatePoBtn" data-id="{{$values->id}}" data-status="generatePo">{{ __('Generate PO')}}
                        </a>
                    </li>
                    @endcan
                    @endif
                </ul>
            </div>

        </td>
    </tr>
    @endforeach
    @endif


</tbody>

</table>
<div class="p-3">
    @if(count($quotationList)>0)
    <ul class="searchPagination">
        {{$quotationList->links()}}
    </ul>

    @endif
</div>