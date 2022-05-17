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
                        <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            {!! Form::open(['route' => 'pms.rfp.request-proposal.store',  'files'=> false, 'id'=>'', 'class' => 'form-horizontal']) !!}

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('request_date', 'Date', array('class' => 'mb-1 font-weight-bold')) !!} 
                                            {!! Form::text('request_date',Request::old('request_date')?Request::old('request_date'):date('d-m-Y'),['id'=>'request_date','class' => 'form-control rounded air-datepicker','placeholder'=>'','readonly'=>'readonly']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('reference_no', 'Ref No', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::text('reference_no',(Request::old('reference_no'))?Request::old('reference_no'):$refNo,['id'=>'reference_no','required'=>true,'class' => 'form-control rounded','readonly'=>'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::label('supplier_id', 'Select Supplier', array('class' => 'mb-1 font-weight-bold')) !!}
                                            {!! Form::Select('supplier_id[]', $supplierList ,Request::old('supplier_id'),['id'=>'supplier_id','multiple' => 'multiple', 'required'=>true,'class'=>'form-control rounded select2 select2-tags']) !!}

                                        </div>
                                    </div>
                                </div>


                            </div><!--end row -->

                            <div class="table-responsive style-scroll">
                                <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th width="5%">{{__('SL No.')}}</th>
                                        <th>{{__('Category')}}</th>
                                        <th>{{__('Items Name')}}</th>
                                        <th>{{__('Requisition Qty')}}</th>
                                        <th width="10%">{{__('RFP')}}</th>
                                        <th class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chkbx_all_first" id="checkAllProduct" onclick="return CheckAll()">
                                                <label class="form-check-label mt-8" for="checkAllProduct">
                                                    <strong>All</strong>
                                                </label>

                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($products) > 0)

                                        @foreach($products as $key=> $values)
                                            @php 
                                                $sumOfqty = collect($values->requisitionItem)->where('requisition.status', 1)->where('is_send','no')->sum('qty');
                                                
                                            @endphp

                                            @if(isset($sumOfqty) && $sumOfqty!=0)
                                            <tr>
                                                <td>
                                                    {{ ($products->currentpage()-1) * $products->perpage() + $key + 1 }}
                                                </td>
                                                <td>
                                                    {{$values->category?$values->category->name:''}}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->name}}</a>
                                                </td>
                                                <td>
                                                    {{collect($values->requisitionItem)->where('requisition.status', 1)->where('is_send','no')->sum('qty')}}
                                                    <input type="hidden" name="qty[{{$values->id}}]" value="{{collect($values->requisitionItem)->where('requisition.status', 1)->where('is_send','no')->sum('qty')}}" class="form-control rounded">
                                                </td>
                                                <td>
                                                    <input type="number" name="request_qty[{{$values->id}}]" min="1" max="99999999" value="{{collect($values->requisitionItem)->where('requisition.status', 1)->where('is_send','no')->sum('qty')}}" class="form-control rounded">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="product_id[]"
                                                           class="element_first" value='{{$values->id}}'>
                                                </td>
                                            </tr>
                                            @endif

                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3">

                                 <label class="btn btn-xs rounded font-5 mt-10 pull-right ">
                                     <input type="checkbox" name="type" class="" value='online'> Allow Online Quotation
                                </label>

                                </div>
                                <div class="col-md-3">

                                    {!! Form::submit('Send RFP to supplier', ['class' => 'pull-right btn btn-success rounded font-10 mt-10','data-placement'=>'top','data-content'=>'click save changes button for send rfp']) !!}&nbsp;

                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="requisitionDetailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Requisitions Details</h4>
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
@endsection
@section('page-script')
    <script>

        function CheckAll() {

            if ($('#checkAllProduct').is(':checked')) {
                $('input.element_first').prop('checked', true);
            } else {
                $('input.element_first').prop('checked', false);
            }
        }

        function openModal(product_id) {
            $('#tableData').empty().load('{{URL::to(Request()->route()->getPrefix()."request-proposal/details")}}/'+product_id);
            $('#requisitionDetailModal').modal('show')
        }
    </script>
@endsection