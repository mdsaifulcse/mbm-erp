@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

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
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Requisition Type" id="addRequisitionTypeBtn"> <i class="las la-plus"></i>Add</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <table  id="dataTable" class="table table-striped table-bordered table-head" border="1">
                                <thead>
                                <tr>
                                    <th width="5%">{{__('SL No.')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th width="20%" class="text-center">{{__('Option')}}</th>
                                </tr>
                                </thead>
                                <tbody>

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
    <div class="modal fade bd-example-modal-md" id="requisitionTypeModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requisitionAddModalLabel">Add New Requisition Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="requisitionForm">
                        <div class="form-row">
                            <div class="col-md-12">
                                <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> <span class="text-danger"></span></p>
                                <div class="input-group input-group-lg mb-12 d-">
                                    <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Rfp')}}" aria-describedby="inputGroup-sizing-sm" required>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-danger rounded" id="requisitionTypeFormSubmit">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END Modal ------------------------------------------------------------------------->
@endsection

@section('page-script')
    <script>
        (function ($) {
            "use script";
            $('[data-toggle="tooltip"]').tooltip();
            const form = document.getElementById('requisitionForm');
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



            $('#addRequisitionTypeBtn').on('click', function () {
                $('#requisitionTypeModal').modal('show');
                form.setAttribute('data-type', 'post');
            });

        })(jQuery)
    </script>
@endsection