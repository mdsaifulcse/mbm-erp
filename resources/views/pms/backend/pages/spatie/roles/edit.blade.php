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
                    <li><a href="#">PMS</a></li>
                    <li><a href="#">Acl</a></li>
                    <li class="active">{{__($title)}}</li>
                    <li class="top-nav-btn">
                        <a href="{{route('pms.acl.roles.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Click Here to go Role List" id="roleBtn"> <i class="las la-list"></i>Role List</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            {!! Form::model($role, ['method' => 'PATCH','route' => ['pms.acl.roles.update', $role->id],'class'=>'kt-form kt-form--label-right']) !!}

                            <div class="kt-portlet__body">

                                <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label text-right">Role Name :</label>
                                    <div class="col-4">

                                        {!! Form::text('name', null, ['readonly'=>false,'placeholder' => 'Name','class' => 'form-control','required'=>true]) !!}

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                                <div class="form-group row">

                                    @foreach($permissions->chunk(8) as $permissionValue)
                                        <div class="col-3">
                                            <h5 class="">Allow Permissions</h5>
                                            @foreach($permissionValue as $value)

                                                <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}

                                                    {{ $value->name }}</label>
                                                <br />

                                            @endforeach

                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="kt-portlet__foot form-footer">
                                <div class="kt-form__actions">
                                    <div class="row">

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning pull-right">Update</button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
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

        })(jQuery)
    </script>
@endsection