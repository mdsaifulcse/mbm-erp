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
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="to_date" id="to_date" class="form-control search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="status">{{ __('Status') }}:</label></p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <select name="status" id="status" class="form-control rounded">
                                    <option value="{{ null }}">{{ __('Select One') }}</option>
                                    @foreach($status as $key=> $values)
                                    <option value="{{ $values }}">{{ ucfirst($values) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <p class="mb-1 font-weight-bold"><label for="searchDeliveredRequisitonBtn"></label></p>
                            <div class="input-group input-group-lg">
                                <a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.requisition.delivered.requistion.search')}}" id="searchDeliveredRequisitonBtn"> <i class="las la-search"></i>Search</a>
                            </div>
                        </div>

                    </div>                    
                </div>
                <div class="panel-body" id="viewResult">
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
                        <ul>
                            {{$delivered_requisition->links()}}
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

    function openRequisitionDetailsModal(requisitionId) {
        $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/requisition")}}/'+requisitionId);
        $('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
        $('#requisitionDetailModal').modal('show')
    }
    (function ($) {
        "use script";
         //$('#dataTable').DataTable();

         $('#searchDeliveredRequisitonBtn').on('click', function () {
            let from_date=$('#from_date').val();
            let to_date=$('#to_date').val();
            let status=$('#status').val();

            const searchDeliveredRequisitonBtn = () => {
                let container = document.querySelector('.searchPagination');
                let pageLink = container.querySelectorAll('.page-link');
                Array.from(pageLink).map((item, key) => {
                    item.addEventListener('click', (e)=>{
                        e.preventDefault();
                        let getHref = item.getAttribute('href');
                        showPreloader('block');
                        $.ajax({
                            type: 'post',
                            url: getHref,
                            dataType: "json",
                            data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,status:status},
                            success:function (data) {
                                if(data.result == 'success'){
                                    showPreloader('none');
                                    $('#viewResult').html(data.body);
                                    searchDeliveredRequisitonBtn();
                                }else{
                                    showPreloader('none');
                                    notify(data.message,'error');

                                }
                            }
                        });
                    })

                });
                deliveredAcknowledge();
            };

            if (from_date !='' || to_date !='' || status) {
                showPreloader('block');
                $.ajax({
                    type: 'post',
                    url: $(this).attr('data-src'),
                    dataType: "json",
                    data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,status:status},
                    success:function (data) {
                        if(data.result == 'success'){

                            showPreloader('none');
                            $('#viewResult').html(data.body);
                            searchDeliveredRequisitonBtn();
                        }else{
                            showPreloader('none');
                            $('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');
                        }   
                    }
                });
                return false;
            }else{
                notify('Please enter data & status first !!','error');

            }
        });

        const deliveredAcknowledge = () => {
            $('.deliveredAcknowledge').on('click', function () {

                let id = $(this).attr('data-id');
                $.ajax({
                    url: "{{ url('pms/requisition/delivered-requistion-ack') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "{{ csrf_token() }}", id:id},
                })
                .done(function(response) {
                    if(response.result=='success'){
                        $('#statusName'+id).html('Acknowledged');
                        $('#hideBtn'+id).hide();
                        notify(response.message,response.result);
                    }
                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
                return false;
            });
        };
        deliveredAcknowledge();

    })(jQuery);
</script>
@endsection
