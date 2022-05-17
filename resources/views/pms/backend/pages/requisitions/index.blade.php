@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
<style type="text/css">
    .bs4-order-tracking {
        margin-bottom: 30px;
        overflow: hidden;
        color: #878788;
        padding-left: 0px;
        margin-top: 30px
    }

    .bs4-order-tracking li {
        list-style-type: none;
        font-size: 13px;
        width: 20%;
        float: left;
        position: relative;
        font-weight: 400;
        color: #878788;
        text-align: center
    }

    .bs4-order-tracking li i {
       
        font-size: 16px;
        
        text-align: center
    }

    .bs4-order-tracking li:first-child:before {
        margin-left: 15px !important;
        padding-left: 11px !important;
        text-align: left !important
    }

    .bs4-order-tracking li:last-child:before {
        margin-right: 5px !important;
        padding-right: 11px !important;
        text-align: right !important
    }

    .bs4-order-tracking li>div {
        color: #fff;
        width: 29px;
        text-align: center;
        line-height: 29px;
        display: block;
        font-size: 12px;
        background: #878788;
        border-radius: 50%;
        margin: auto
    }

    .bs4-order-tracking li:after {
        content: '';
        width: 150%;
        height: 2px;
        background: #878788;
        position: absolute;
        left: 0%;
        right: 0%;
        top: 15px;
        z-index: -1
    }

    .bs4-order-tracking li:first-child:after {
        left: 50%
    }

    .bs4-order-tracking li:last-child:after {
        left: 0% !important;
        width: 0% !important
    }

    .bs4-order-tracking li.active {
        font-weight: bold;
        color: #0db5c8
    }

    .bs4-order-tracking li.active>div {
        background: #0db5c8
    }

    .bs4-order-tracking li.active:after {
        background: #0db5c8
    }

    .card-timeline {
        background-color: #fff;
        z-index: 0
    }
</style>
@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
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
              <li class="active">{{__($title)}} List</li>
              <li class="top-nav-btn">
                <a href="{{route('pms.requisition.requisition.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Requisition" id="addProductBtn"> <i class="las la-plus"></i>Add</a>
            </li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="2%">{{__('SL No.')}}</th>
                                <th>{{__('Date')}}</th>
                                <th>{{__('RefNo')}}</th>
                                <th>{{__('Requisition By')}}</th>
                                <th>{{__('Qty')}}</th>
                                <th>{{__('Status')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisitions as $key => $requisition)
                            <tr>
                                <td width="5%">{{($requisitions->currentpage()-1) * $requisitions->perpage() + $key + 1 }}</td>
                                <td>{{ date('d-m-Y',strtotime($requisition->requisition_date)) }}</td>

                                <td><a href="javascript:void(0)" onclick="openModal({{$requisition->id}})"  class="btn btn-link requisition m-1 rounded">{{ $requisition->reference_no }}</a></td>
                                <td>{{ $requisition->relUsersList->name }}</td>
                                <td>{{$requisition->items->sum('qty')}}</td>
                                <td id="status{{$requisition->id}}">
                                    @if($requisition->status==0)
                                    <span class="btn btn-sm btn-warning">Pending</span>
                                    @elseif($requisition->status==1)
                                    <span class="btn btn-sm btn-success">Approved</span>
                                    @elseif($requisition->status==2)
                                    <span class="btn btn-sm btn-danger">HALT</span>

                                    @elseif($requisition->status==3)
                                    <span class="btn btn-sm btn-warning">Draft</span>
                                    @endif
                                    
                                </td>

                                <td class="text-center action">
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName{{$requisition->id}}">
                                                {{ __('Option')}}
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($requisition->status==0 || $requisition->status==2 || $requisition->status==3)
                                            <li><a href="{{ route('pms.requisition.requisition.edit',$requisition->id) }}" title="Click Here To Edit"><i class="la la-edit"></i>{{ __('Edit')}}</a>
                                            </li>
                                            @if($requisition->status==3)
                                            <li><a href="javascript:void(0)" class="sendRequisition" data-id="{{$requisition->id}}" data-status="0" title="Click Here To Send"><i class="la la-paper-plane"></i> {{ __('Send')}}</a>
                                            </li>
                                            @endif
                                            @endif
                                            <li><a href="javascript:void(0)" title="Tracking Requisition" class="trackingRequistionStatus" data-id="{{$requisition->id}}"><i class="la la-map"></i> {{ __('Track Your Requisition')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    <div class="la-1x text-center">
                        @if(count($requisitions)>0)
                        <ul>
                            {{$requisitions->links()}}
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<!-- Requisition Details Modal Start -->
<div class="modal" id="requisitionDetailModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Requisition Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="tableData">

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<!-- Requisition Details Modal End -->

@endsection

@section('page-script')
<script>
    
    $('#dataTable').DataTable();
    function openModal(requisitionId) {
        $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."requisition")}}/'+requisitionId);
        $('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
        $('#requisitionDetailModal').modal('show')
    }

    $('.trackingRequistionStatus').on('click', function () {

        let id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('pms/requisition/tracking-show') }}",
            type: 'POST',
            dataType: 'json',
            data: {_token: "{{ csrf_token() }}", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#requisitionDetailModal').find('.modal-title').html(`Requisition Tracking`);
                $('#requisitionDetailModal').find('#tableData').html(response.body);
                $('#requisitionDetailModal').modal('show');
            }else{
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });

    $('.sendRequisition').on('click', function () {
        let sendButton=$(this).parent('li');
        let id = $(this).attr('data-id');
        let status = $(this).attr('data-status');

        let texStatus='Send';
        let textContent='Would you like to send this requisition to your department head?';

        swal({
            title: "{{__('Are you sure?')}}",
            text: textContent,
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: true,
                confirm: {
                    text: texStatus,
                    value: true,
                    visible: true,
                    closeModal: true
                },
            },
        }).then((value) => {
            if(value){
                $.ajax({
                    url: "{{ url('pms/requisition/approved-status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "{{ csrf_token() }}", id:id, status:status},
                })
                .done(function(response) {
                    if(response.success){


                        $('#statusName'+id).html(response.new_text);
                        $('#status'+id).html('<span class="btn btn-sm btn-warning">'+response.new_text+'</span>');
                        notify(response.message,'success')
                        sendButton.remove();
                    }else{
                        notify(response.message,'error');
                    }
                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
                return false;
            }
        });
    });

    
</script>
@endsection
