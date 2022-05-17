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
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
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
                            <tbody >
                                @if(count($quotations)>0)
                                @foreach($quotations as $key=> $values)
                                <?php 
                                    $total_qty=0;
                                    $total_unit_price=0;
                                    $total_amount=0;

                                    foreach($values->relSelfQuotationSupplierByProposalId()->where('is_approved','pending')->get() as $supplier){
                                        $total_qty +=$supplier->relQuotationItems->sum('qty');
                                        $total_unit_price +=$supplier->relQuotationItems->sum('unit_price');
                                        $total_amount +=$supplier->relQuotationItems->sum('total_price');
                                    }
                                ?>


                                <tr>
                                    <td>{{ ($quotations->currentpage()-1) * $quotations->perpage() + $key + 1 }}</td>
                                    
                                    <td>{{$values->relRequestProposal->reference_no}}</td>
                                    <td>{{number_format($total_qty,2)}}</td>
                                    <td>{{number_format($total_unit_price,2)}}</td>
                                    <td>{{number_format($total_amount,2)}}</td>
                                    
                                    <td>
                                        @if($values->relSelfQuotationSupplierByProposalId)
                                        @foreach($values->relSelfQuotationSupplierByProposalId()->where('is_approved','pending')->get() as $supplier)
                                        <button class="btn btn-sm btn-primary">{{$supplier->relSuppliers->name}}</button>
                                        @endforeach
                                        @endif
                                    </td>
                                    
                                    <td class="text-center action">
                                       <a href="javascript:void(0)" onclick="openModal({{$values->request_proposal_id}})" title="Compare Process Analysis"  class="btn btn-success"><i class="las la-file-alt"></i></a>

                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            
                        </table>
                        <div class="p-3">
                            @if(count($quotations)>0)
                                <ul>
                                    {{$quotations->links()}}
                                </ul>

                                @endif
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
            {!! Form::open(['route' => 'pms.quotation.quotations.cs.compare.store',  'files'=> false, 'id'=>'', 'class' => '']) !!}
            <div id="tableData">
                
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

            {!! Form::close() !!}

            </div>
            

        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>

   function openModal(rp_id) {
    $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/cs-compare")}}/'+rp_id);
    $('#requisitionDetailModal').find('form').reset;
    $('#requisitionDetailModal').modal('show');
}
</script>
@endsection