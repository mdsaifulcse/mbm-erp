@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
    <?php
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Request;
    ?>
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
                        <a href="{{route('pms.rfp.request-proposal.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Requisition" id="addRequestProposalBtn"> <i class="las la-plus"></i>Add</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">

                            <div class="table-responsive style-scroll">
                                <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th width="5%">{{__('SL No.')}}</th>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('RefNo')}}</th>
                                        <th>{{__('Qty')}}</th>
                                        <th>{{__('RFP Collection Type')}}</th>
                                        <th>{{__('Created By')}}</th>
                                        <th>{{__('Option')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($requestProposals) > 0)
                                        @foreach($requestProposals as $key=> $requestProposal)
                                        @php
                                            $quotationSupplier=$requestProposal->relQuotations()->pluck('supplier_id')->all();

                                            $data=$requestProposal->defineToSupplier()->whereNotIn('supplier_id',$quotationSupplier)->get();
                                        @endphp
                                        @if($data->count()>0)
                                        <tr id="rowId{{$requestProposal->id}}">
                                                <td>
                                                   {{$key+1}}
                                                </td>
                                                <td>
                                                    {{date('d-M-Y',strtotime($requestProposal->request_date))}}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="openModal({{$requestProposal->id}})"  class="btn btn-link">{{$requestProposal->reference_no}}</a>
                                                </td>

                                                <td>{{$requestProposal->requestProposalDetails->sum('request_qty')}}</td>

                                                <td>{{ucfirst($requestProposal->type)}}</td>
                                                <td>{{$requestProposal->createdBy->name}}</td>
                                                
                                                <td class="text-center action">
                                                    <div class="btn-group">
                                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                            <span id="statusName">
                                                                {{__('Option')}}
                                                            </span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a target="__blank" href="{{route('pms.rfp.quotations.generate',$requestProposal->id)}}">{{ __('Quotation Generate')}}</a>
                                                            </li>
                                                            @if($quotationSupplier)
                                                            <li>
                                                                <a href="javascript:void(0)" class="completeQG" data-src="{{route('pms.rfp.generate.complete')}}" data-id={{$requestProposal->id}}>{{ __('Complete')}}</a>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>

                                                </td>

                                            </tr>
                                            @endif

                                        @endforeach

                                        @else

                                        <tr>
                                            <td colspan="7">No Data Found</td>
                                        </tr>

                                    @endif
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-lg-end">
                                    {!! $requestProposals->links() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="requestProposalDetailModal">
        <div class="modal-dialog modal-lg" style="width: 80%">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Request Proposal Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body" id="modalContent">



                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>

        function openModal(requestId) {
            $('#modalContent').empty().load('{{URL::to(Request()->route()->getPrefix()."request-proposal")}}/'+requestId);
            $('#requestProposalDetailModal').modal('show')
        }

        $('.completeQG').on('click', function () {
            let req_proposal_id = $(this).attr('data-id');
            swal({
                title: "{{__('Are you sure?')}}",
                text: "{{__('Once you Complete, You can not generate quotation from this proposal.')}}",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Complete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token: "{{ csrf_token() }}", req_proposal_id:req_proposal_id},
                    success:function (data) {
                        if(data.result == 'success'){
                            $('#rowId'+req_proposal_id).hide();
                             notify(data.message,data.result);
                        }else{
                            notify(data.message,data.result);
                        }
                    }
                });
                return false;
            }
        });
    });

    </script>
@endsection