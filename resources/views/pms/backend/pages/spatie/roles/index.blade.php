
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
                        <a href="{{route('pms.acl.roles.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Role"> <i class="las la-plus"></i>Add</a>
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
                                    <th>Role Name</th>
                                    <th>Guard name</th>
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
    <div class="modal fade bd-example-modal-md" id="roleAndPermissionModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Role with allowed Permission</h5>
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

        function showRoleWithPermission(roleId) {

            $('#dataBody').empty().load('{{URL::to(Request()->route()->getPrefix()."/roles")}}/'+roleId);

            $('#roleAndPermissionModal').modal('show');
        }


        $(function() {
            $('#dataTable').DataTable( {
                processing: true,
                serverSide: true,
                "lengthMenu": [[50, 100, 200,500, -1], [50, 100, 200, 500,1000,"All"]],
                ajax: '{{ URL::to("pms/acl/roles-data") }}',
                columns: [
                    { data: 'DT_RowIndex',orderable:true},
                    { data: 'name',name:'roles.name'},
                    { data: 'guard_name',name:'roles.guard_name'},
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