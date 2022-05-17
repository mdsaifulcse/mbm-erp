
<div class="table-responsive">

    <h5> <span> Reference No: {{$requestProposal->reference_no}} </span> <span style="float: right">Date: {{date('d-m-Y',strtotime($requestProposal->request_date))}}</span> </h5>

    <h4 class="py-2">Assign to suppliers:
        @foreach($requestProposal->defineToSupplier as $key=>$data)
        <span class="badge badge-primary">{{$data->supplier->name}}</span>
            @endforeach
    </h4>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Sl No.</th>
            <th>Category</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Created Date</th>
        </tr>
        </thead>

        <tbody>
        @forelse($requestProposal->requestProposalDetails as $key=>$requestProposalDetail)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$requestProposalDetail->product->category->name}}</td>
                <td>{{$requestProposalDetail->product->name}}</td>
                <td>{{$requestProposalDetail->request_qty}}</td>
                <td>{{date('d-m-Y  h:i a',strtotime($requestProposalDetail->created_at))}}</td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>

</div>