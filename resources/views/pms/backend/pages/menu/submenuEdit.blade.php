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
                    <li class="active">{{__($title)}}</li>
                    <li class="top-nav-btn">
                        <a href="{{route('pms.admin.sub-menu.show',$data->menu_id)}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title=" Go to Main Menu List"> <i class="las la-list"></i>Submenu List</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">

                            {!! Form::open(array('route' => ['pms.admin.sub-menu.update',$data->id],'method'=>'PUT','class'=>'kt-form kt-form--label-right','files'=>true)) !!}

                                <div class="form-group row   {{ $errors->has('name') ? 'has-error' : '' }}">
                                    {{Form::label('name', ' Sub Menu', array('class' => 'col-md-2 control-label text-right'))}}
                                    <div class="col-md-8">
                                        {{Form::text('name',$data->name,array('class'=>'form-control','placeholder'=>'Sub Menu Name *','required'))}}

                                        <input type="hidden" name="menu_id" value="{{$menu->id}}">
                                    </div>
                                </div>

                                <div class="form-group row  {{ $errors->has('url') ? 'has-error' : '' }}">

                                    {{Form::label('url', 'URL', array('class' => 'col-md-2 control-label text-right'))}}

                                    <div class="col-md-4">
                                        <div class="input-group">

                                            {{Form::text('url',$data->url,array('class'=>'form-control','placeholder'=>'URL *','required'))}}

                                            @if ($errors->has('url'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('url') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <label class="input-group-text">Icon Class:</label>
                                            </span>

                                            {{Form::text('icon_class',$data->icon_class,array('class'=>'form-control','placeholder'=>'Ex: fa fa-folder'))}}
                                            @if ($errors->has('icon_class'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('icon_class') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="form-group ">
                                        <div class="col-md-2">
                                            <label class="slide_upload profile-image" for="file">
                                                @if(!empty($data->icon))

                                                    <img id="image_load" src="{{asset($data->icon)}}" style="width: 50px;height:auto; cursor:pointer;border-radius:50%;">

                                                @else
                                                    <img id="image_load" src="{{asset('images/default/default.png')}}" style="width: 100px;height: auto; cursor:pointer;border-radius:50%;">
                                                @endif
                                            </label>

                                            <input id="file" style="display:none" name="icon" type="file" onchange="photoLoad(this,this.id)" accept="image/*">
                                            @if ($errors->has('icon'))
                                                <span class="help-block text-danger">
                                                    <strong>The icon image dimensions(Y, X) should not be less then 120 and grater then 240</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                </div>


                                <div class="form-group row">

                                    {{Form::label('serial_num', 'Others', array('class' => 'col-md-2 control-label text-right'))}}
                                    <div class="col-md-2">
                                        <?php $max=$max_serial+1; ?>
                                        {{Form::number('serial_num',$data->serial_num,['class'=>'form-control','placeholder'=>'Serial Number','max'=>"$max",'min'=>'0','required'=>true])}}
                                        <small> Serial </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('menu_for', $menuFor,$data->menu_for, ['class' => 'form-control'])}}
                                        <small> Menu For </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('status', $status,$data->status, ['class' => 'form-control'])}}
                                        <small> Status </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('open_new_tab', $openTab,$data->open_new_tab, ['class' => 'form-control'])}}
                                        <small> Open New Tab? </small>
                                    </div>

                                </div><!-- end row -->

                                <div class="form-group row">
                                    <label for="example-text-input" class="col-2 col-form-label text-right">Permission</label>
                                    <div class="col-8">
                                        {!! Form::select('slug[]', $permissions,json_decode($data->slug), array('class' => 'form-control','multiple'=>true,'required'=>true)) !!}

                                        @if ($errors->has('slug'))
                                            <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('slug') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-2">
                                </div>
                                <div class="col-10">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div><!-- end body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER CONTENT ------------------------------------------------------------------------->

@endsection

@section('page-script')
    <script>
        function photoLoad(input,image_load) {
            var target_image='#'+$('#'+image_load).prev().children().attr('id');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(target_image).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
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