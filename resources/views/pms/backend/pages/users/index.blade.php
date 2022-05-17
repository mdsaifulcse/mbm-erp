
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
                        <a href="{{route('pms.admin.users.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New User"> <i class="las la-plus"></i>Add</a>
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Associate Id</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Action</th>
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
    <div class="modal fade bd-example-modal-md" id="showUserDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center">Use Details Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="dataBody">

                </div>
            </div>
        </div>
    </div>
    <!-- END Modal ------------------------------------------------------------------------->
@endsection

@section('page-script')

    <script>

        function showUserDetails(userId) {

            $('#dataBody').empty().load('{{URL::to(Request()->route()->getPrefix()."/users")}}/'+userId);

            $('#showUserDetailsModal').modal('show');
        }


        $(function() {
            $('#dataTable').DataTable( {
                processing: true,
                serverSide: true,
                "lengthMenu": [[50, 100, 200,500, -1], [50, 100, 200, 500,1000,"All"]],
                ajax: '{{ URL::to("pms/admin/users-data") }}',
                columns: [
                    { data: 'DT_RowIndex',orderable:true},
                    { data: 'name',name:'users.name'},
                    { data: 'email',name:'users.email'},
                    { data: 'phone',name:'users.phone'},
                    { data: 'associate_id',name:'users.associate_id'},
                    { data: 'user_role'},
                    { data: 'created_at'},
                    { data: 'action'},
                ]
            });

        });

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