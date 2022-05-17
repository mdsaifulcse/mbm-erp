@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

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
              
        </ul>
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered miw-500" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                                <th width="2%">SL No</th>
                                <th>Requistion Date</th>
                                <th>Notification Date</th>
                                <th>Requistion RefNo</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th class="text-center">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($notification))
                            @foreach($notification as $key => $values)
                            <tr>
                                <td width="5%">{{($notification->currentpage()-1) * $notification->perpage() + $key + 1 }}</td>
                                <td>{{ date('d-m-Y',strtotime($values->relRequisitionItem->requisition->requisition_date)) }}</td>

                                <td>{{ date('d-m-Y',strtotime($values->created_at)) }}</td>

                                <td><a href="javascript:void(0)" onclick="openModal({{$values->relRequisitionItem->requisition_id}})"  class="btn btn-link requisition m-1 rounded">{{ $values->relRequisitionItem->requisition->reference_no }}</a></td>
                                <td>{{ $values->relRequisitionItem->product->category->name }}</td>
                                <td>{{ $values->relRequisitionItem->product->name }}</td>
                                <td>{{ $values->relRequisitionItem->qty}}</td>
                                <td>{{ $values->messages}}</td>
                                <td id="type{{$values->id}}">
                                    @if($values->type=='unread')
                                    <span class="btn btn-sm btn-warning">Unread</span>
                                    @else
                                    <span class="btn btn-sm btn-success">Read</span>
                                    @endif
                                </td>
                                <td class="text-center action" id="action{{$values->id}}">
                                    @if($values->type=='unread')
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName{{$values->id}}">
                                                {{ __('Option')}}
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                           <li><a href="javascript:void(0)" class="markAsRead" data-id="{{$values->id}}" title="Mark As Read"><i class="la la-check"></i> {{ __('Mark As Read')}}</a>
                                           </li>
                                       </ul>
                                   </div>
                                   @else
                                   Already Read
                                   @endif
                               </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                    </table>
                    <div class="la-1x text-center">
                        @if(count($notification)>0)
                        <ul>
                            {{$notification->links()}}
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
    
    
    function openModal(requisitionId) {
        $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/requisition")}}/'+requisitionId);
        $('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
        $('#requisitionDetailModal').modal('show')
    }

    $('.markAsRead').on('click', function () {

        let id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('pms/requisition/mark-as-read') }}",
            type: 'POST',
            dataType: 'json',
            data: {_token: "{{ csrf_token() }}", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#type'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                $('#action'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });
    
</script>
@endsection
