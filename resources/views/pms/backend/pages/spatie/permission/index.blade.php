
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
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Permission" id="addPermissionBtn"> <i class="las la-plus"></i>Add</a>
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
                                    <th>Permission Name</th>
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
    <div class="modal fade bd-example-modal-md" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(array('route' => 'pms.acl.permission.store','id'=>'permissionForm','class'=>'form-horizontal','method'=>'POST','role'=>'form')) !!}

                <div class="modal-body">

                     <div class="form-group row">
                        <label for="permission" class="control-label col-md-12">Permission:</label>
                        <div class="col-md-12">
                            {!! Form::Select('name[]', [] ,old('name'),['id'=>'permission','multiple' => true, 'required'=>true,'class'=>'form-control rounded select2-tags','style'=>'width:100%']) !!}

                        </div>

                         {{--<div class="col-md-2">--}}
                            {{--{{Form::select('type',['1'=>'Resource','2'=>'Normal'],2,['class'=>'form-control','required'])}}--}}
                        {{--</div>--}}
                    </div>

                    <div class="form-group row">
                        <label for="menuId" class="col-md-12">Choose Menu</label>
                        <div class="col-md-12">
                            {!! Form::Select('menu_id', $menus ,old(''),['id'=>'menuId','multiple' => false, 'required'=>false,'class'=>'form-control rounded select2','placeholder'=>'--Select Menu--','style'=>'width:100%']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="subMenuId" class="col-md-12">Choose Sub Menu</label>
                        <div class="col-md-12" id="submenuList">
                            <select name="submenu_id" id="subMenuId" class="form-control" multiple="multiple" style="width: 100%;">
                                <option> Choose Menu First </option>
                            </select>
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
@endsection

@section('page-script')

    <script>

        $(document).ready(function() {

            $("#permission").select2({
                tags: true,
                placeholder: "Type permission name and hit enter",
                allowClear: true
            });
        });

        $('#menuId').on('change',function () {

           let menuId=0;
            menuId=$(this).val()

            $('#submenuList').empty().load('{{URL::to(Request()->route()->getPrefix()."/permission")}}/'+menuId);

        })

    </script>

    <script>
        $(function() {
            $('#dataTable').DataTable( {
                processing: true,
                serverSide: true,
                "lengthMenu": [[50, 100, 200,500, -1], [50, 100, 200, 500,1000,"All"]],
                ajax: '{{ URL::to("pms/acl/permission/create") }}',
                columns: [
                    { data: 'DT_RowIndex',orderable:true},
                    { data: 'name',name:'permissions.name'},
                    { data: 'guard_name',name:'permissions.guard_name'},
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


            $('#addPermissionBtn').on('click', function () {
                $('#permissionModal').modal('show');
//                form.setAttribute('data-type', 'post');
            });

        })(jQuery)
    </script>
@endsection