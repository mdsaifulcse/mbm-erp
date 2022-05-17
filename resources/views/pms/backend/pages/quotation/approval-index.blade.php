@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="from_date" id="from_date" class="form-control rounded  search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))  }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="to_date" id="to_date" class="form-control  search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="is_approved">{{ __('Status') }}:</label></p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <select name="is_approved" id="is_approved" class="form-control rounded">

                                        <option value="">Select Option</option>
                                        <option value="processing">Processing</option>
                                        <option value="approved">Approved</option>
                                        <option value="halt">Halt</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchQuotationBtn"></label></p>
                                <div class="input-group input-group-lg">
                                    <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.quotation.approved.view.search')}}" id="searchQuotationBtn"> <i class="las la-search"></i>Search</a>
                                </div>
                            </div>

                        </div>
                        
                    </div>
                    <div id="dataTableView">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('SL No.')}}</th>
                                    <th>{{__('Request Proposal')}}</th>
                                    <th>{{__('Qty')}}</th>
                                    <th>{{__('Unit Price')}}</th>
                                    <th>{{__('Total Amount')}}</th>
                                    <th>{{__('Supplier')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($quotationList)>0)
                                @foreach($quotationList as $key=> $values)

                                <?php 
                                    $total_qty=0;
                                    $total_unit_price=0;
                                    $total_amount=0;

                                    foreach($values->relSelfQuotationSupplierByProposalId()->whereNotIn('is_approved',['pending','approved'])->get() as $supplier){

                                        $total_qty +=$supplier->relQuotationItems->sum('qty');
                                        $total_unit_price +=$supplier->relQuotationItems->sum('unit_price');
                                        $total_amount +=$supplier->relQuotationItems->sum('total_price');
                                    }
                                ?>
                                <tr>
                                    <td>{{ ($quotationList->currentpage()-1) * $quotationList->perpage() + $key + 1 }}</td>
                                    
                                    <td><a href="javascript:void(0)" class="btn btn-link" onclick="requestProposalDetails({{$values->relRequestProposal->id}})">{{$values->relRequestProposal->reference_no}}</a></td>
                                    <td>{{number_format($total_qty,2)}}</td>
                                    <td>{{number_format($total_unit_price,2)}}</td>
                                    <td>{{number_format($total_amount,2)}}</td>
                                    <td>
                                        @if($values->relSelfQuotationSupplierByProposalId)
                                        @foreach($values->relSelfQuotationSupplierByProposalId()->whereNotIn('is_approved',['pending','approved'])->get() as $supplier)

                                        <button class="btn btn-sm btn {{$supplier->is_approved=='halt'?' btn-warning':'btn-info'}}">{{$supplier->relSuppliers->name}}</button>

                                        @endforeach
                                        @endif
                                    </td>


                                    <td class="text-center action">
                                     <a href="javascript:void(0)" onclick="openModalForCompare({{$values->request_proposal_id}})" title="Compare Process Analysis"  class="btn btn-success"><i class="las la-file-alt"></i></a>

                                 </td>
                             </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="p-3"> 
                           @if(count($quotationList)>0)
                           <ul>
                            {{$quotationList->links()}}
                        </ul>
                        @endif
                    </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal" id="requisitionDetailModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Quotation Comparison</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
            {!! Form::open(['route' => 'pms.quotation.quotations.cs.compare.approved',  'files'=> false, 'id'=>'', 'class' => '']) !!}
            <div id="tableData">
                
            </div>
            
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" id="qty-submit-btn" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

            {!! Form::close() !!}

            </div>
            

        </div>
    </div>
</div>


<div class="modal" id="quotationHaldModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Hold the Quotations</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form action="{{route('pms.quotation.halt.status')}}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <label for="remarks">Remarks :</label>

                        <input type="hidden" readonly required name="id" id="quotationId">

                        <textarea class="form-control" name="remarks" rows="3" id="remarks" placeholder="Write down here reason for hold"></textarea>

                    </div>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>
    
    function openModal(quotation_id) {
        $('.modal-body').load('{{URL::to(Request()->route()->getPrefix()."/quotation-items")}}/'+quotation_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Quotation Details`);
        $('#requisitionDetailModal').modal('show');
    }

    function openModalForCompare(rp_id) {
        $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/cs-compare-view")}}/'+rp_id);
        $('#requisitionDetailModal').find('.modal-body #qty-submit-btn').show();
        $('#requisitionDetailModal').modal('show').on('hidden.bs.modal', function (e) {
                    let form = document.querySelector('#requisitionDetailModal').querySelector('form').reset();

                });
        }

    function requestProposalDetails(request_proposal_id) {
        $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/request-proposal-details")}}/'+request_proposal_id);
        $('#requisitionDetailModal').find('.modal-title').html(`Request Proposal Details`);
        $('#requisitionDetailModal').find('.modal-body #qty-submit-btn').hide();
        $('#requisitionDetailModal').modal('show').on('hidden.bs.modal', function (e) {
                document.querySelector('#requisitionDetailModal').querySelector('form').reset();
        });
    }


    (function ($) {
        "use script";

        $('#searchQuotationBtn').on('click', function () {

            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let is_approved=$('#is_approved').val();
            let is_po_generate='no';


            const searchPagination = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,is_approved:is_approved,is_po_generate:is_po_generate},
                            success:function (data) {
                                if(data.result == 'success'){
                                    $('#dataTableView').html(data.body);
                                    searchPagination();

                                }else{
                                    
                                    $('#dataTableView').html('<div><center>No Data Found !!</center></div>');

                                }
                            }
                        });
                    })

                });

                approveQa();

            };

            if (from_date !='' || to_date !='' || is_approved) {
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,is_approved:is_approved,is_po_generate:is_po_generate},
                    success:function (data) {
                        if(data.result == 'success'){
                            $('#dataTableView').html(data.body);
                            searchPagination();
                        }else{
                            $('#dataTableView').html('<div><center>No Data Found !!</center></div>');

                        }
                    }
                });
                return false;
            }else{
                notify('Please enter data first !!','error');

            }
        });

        //Approved Reject 
        const approveQa = () => {
            $('.requisitionApprovedBtn').on('click', function () {

                let id = $(this).attr('data-id');
                let status = $(this).attr('data-status');


                if (status==='halt'){

                    $('#quotationId').val(id)
                    return $('#quotationHaldModal').modal('show').on('hidden.bs.modal', function (e) {
                        let form = document.querySelector('#quotationHaldModal').querySelector('form').reset();

                    })
                }

                $.ajax({
                    url: "{{ url('pms/quotation/approved-status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "{{ csrf_token() }}", id:id, status:status},
                })
                .done(function(response) {
                    if(response.success){

                        $('#statusName'+id).html(response.new_text);
                        notify(response.message,'success');
                    }else{
                        notify(response.message,'error');
                    }
                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }

})(jQuery)
</script>
@endsection