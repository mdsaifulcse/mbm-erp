
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
                        <a href="{{route('pms.admin.menu.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Menu"> <i class="las la-plus"></i>Add Menu</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body table-responsive">

                            <table class="table table-striped table-hover table-bordered center_table" id="my_table">
                                <thead>
                                <tr class="bg-dark text-white">
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Menu For</th>
                                    <th>Sub Menu</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; ?>
                                @forelse($allData as $data)
                                    <tr>
                                        <td>{{$data->serial_num}}</td>
                                        <td><a href="{{route('pms.admin.menu.edit',$data->id)}}"><i class="{{$data->icon_class}}"></i> {{$data->name}}</a></td>
                                        <td><a href="{{URL::to($data->url)}}" target="_blank">{{URL::to($data->url)}}</a></td>

                                        <td>

                                            <span class="text-success">{{$data->menu_for}}</span>

                                        </td>

                                        <td><a href="{{route('pms.admin.sub-menu.show',$data->id)}}" class="btn btn-primary btn-sm" style="color: #fff;">Sub Menu ({{$data->subMenu->count('id')}})</a></td>

                                        <td><i class="{{($data->status==App\Models\PmsModels\Menu\Menu::ACTIVE)? 'fa fa-check-circle text-success' : 'fa fa-times-circle'}}"></i></td>

                                        <td>{{$data->created_at}}</td>
                                        <td>
                                            {!! Form::open(array('route' => ['pms.admin.menu.destroy',$data->id],'method'=>'DELETE','id'=>"deleteForm$data->id")) !!}
                                            <a href="{{route('pms.admin.menu.edit',$data->id)}}" class="btn btn-success btn-sm"><i class="la la-pencil-square"></i> </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick='return deleteConfirm("deleteForm{{$data->id}}")'><i class="la la-trash"></i></button>
                                            {!! Form::close() !!}
                                        </td>

                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="8" class="text-center"> No Menu Data ! </td>
                                    </tr>
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
@endsection

@section('page-script')

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