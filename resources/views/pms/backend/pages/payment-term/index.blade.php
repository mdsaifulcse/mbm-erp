
@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
    <style type="text/css">
        .modal-backdrop{
            position: relative !important;
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
                        <a href="#">Home</a>
                    </li>
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active">{{__($title)}} List</li>
                    <li class="top-nav-btn">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Payment Term" id="addPaymentBtn"> <i class="las la-plus"></i>Add</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body table-responsive">
                            <table  id="dataTable" class="table table-striped table-bordered table-head" border="1">
                                <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Payment Term</th>
                                    <th>Notes:</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @forelse($paymentTerms as $key=>$paymentTerm)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$paymentTerm->term}}</td>
                                        <td>{{$paymentTerm->remarks}}</td>
                                        <td>{{$paymentTerm->status}}</td>
                                        <td>
                                            {!! Form::open(array('route' => ['pms.payment-terms.destroy',$paymentTerm->id],'method'=>'DELETE','id'=>"deleteForm$paymentTerm->id")) !!}
                                            <a href="{{route('pms.payment-terms.edit',$paymentTerm->id)}}" id="editModal_{{$paymentTerm->id}}" data-toggle="modal" onclick="editPaymentTerm({{$paymentTerm->id}})" class="btn btn-success btn-sm "><i class="la la-pencil-square"></i> </a>

                                            <button type="button" class="btn btn-danger btn-sm" onclick='return deleteConfirm("deleteForm{{$paymentTerm->id}}")'><i class="la la-trash"></i></button>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                    @empty

                                @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
    <!-- Modal ------------------------------------------------------------------------->
    <div class="modal fade bd-example-modal-md" id="paymentTermModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Add Payment Term</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(array('route' => 'pms.payment-terms.store','id'=>'paymentTermsForm','class'=>'form-horizontal','method'=>'POST','role'=>'form')) !!}

                <div class="modal-body">

                     <div class="form-group row">
                        <label for="term" class="control-label col-md-12">Payment Term:</label>
                        <div class="col-md-12">
                            {!! Form::text('term' ,old('term'),['id'=>'term', 'required'=>true,'class'=>'form-control rounded']) !!}
                        </div>
                    </div>

                     {{--<div class="form-group row">--}}
                        {{--<label for="remarks" class="control-label col-md-12">Notes:</label>--}}
                        {{--<div class="col-md-12">--}}
                            {{--{!! Form::textarea('remarks' ,old('remarks'),['id'=>'remarks','rows' => 2, 'required'=>false,'class'=>'form-control rounded']) !!}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                     <div class="form-group row">
                        <label for="status" class="control-label col-md-12">Status:</label>
                        <div class="col-md-12">
                            {!! Form::select('status',$status,old('status'),['id'=>'status', 'required'=>true,'class'=>'form-control rounded','style'=>'width:100%']) !!}
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary text-white rounded" id="requisitionTypeFormSubmit">{{ __('Save') }}</button>
                </div>
                {!! Form::close(); !!}
            </div>
        </div>
    </div>
    <!-- END Modal ------------------------------------------------------------------------->

    <!--edit payment term-->
    <div class="modal fade bd-example-modal-md" id="editPaymentTermModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Add Payment Term</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--edit payment term-->

@endsection

@section('page-script')

    <script>
       function editPaymentTerm(paymentTermId) {

           showPreloader('block');
           $('#editPaymentTermModal').load('{{URL::to("pms/payment-terms")}}/'+paymentTermId);
           showPreloader('none');
           $('#editPaymentTermModal').modal('show');
       }

       $('#addPaymentBtn').on('click', function () {
           $('#paymentTermModal').modal('show');
//                form.setAttribute('data-type', 'post');
       });

    </script>

    <script>

        function deleteConfirm(id){
            swal({
                title: "{{__('Are you sure?')}}",
                text: "You won't be able to revert this!",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Yes, delete it!",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((result) => {
                if (result) {
                $("#"+id).submit();
            }
        })
        }
    </script>

    <script>
        (function ($) {
            "use script";
            $('[data-toggle="tooltip"]').tooltip();
            const form = document.getElementById('permissionForm');
            const tableContainer = document.getElementById('dataTable').querySelector('tbody');

            const showAlert = (status, error) => {
                swal({
                    icon: status,
                    text: error,
                    dangerMode: true,
                    buttons: {
                        cancel: false,
                        confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            closeModal: true
                        },
                    },
                }).then((value) => {
                    if(value)form.reset();
            });
            };

        })(jQuery)
    </script>
@endsection