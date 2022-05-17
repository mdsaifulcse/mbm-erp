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
                        <a href="{{route('pms.admin.menu.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title=" Go to Main Menu List"> <i class="las la-list"></i>Main Menu</a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">

                            {!! Form::open(array('route' => 'pms.admin.sub-menu.store','method'=>'POST','class'=>'kt-form kt-form--label-right','files'=>true)) !!}

                                <div class="form-group row   {{ $errors->has('name') ? 'has-error' : '' }}">
                                    {{Form::label('name', ' Sub Menu', array('class' => 'col-md-2 control-label text-right'))}}
                                    <div class="col-md-8">
                                        {{Form::text('name','',array('class'=>'form-control','placeholder'=>'Sub Menu Name *','required'))}}

                                        <input type="hidden" name="menu_id" value="{{$menu->id}}">
                                    </div>
                                </div>

                                <div class="form-group row  {{ $errors->has('url') ? 'has-error' : '' }}">

                                    {{Form::label('url', 'URL', array('class' => 'col-md-2 control-label text-right'))}}

                                    <div class="col-md-4">
                                        <div class="input-group">

                                            {{Form::text('url','',array('class'=>'form-control','placeholder'=>'URL *','required'))}}

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

                                            {{Form::text('icon_class','',array('class'=>'form-control','placeholder'=>'Ex: fa fa-folder'))}}
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
                                                <img id="image_load" src="{{asset('images/default/default.png')}}" style="width: 100px;height: auto; cursor:pointer;">
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
                                        {{Form::number('serial_num',"$max",['class'=>'form-control','placeholder'=>'Serial Number','max'=>"$max",'min'=>'0','required'=>true])}}
                                        <small> Serial </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('menu_for', $menuFor,'', ['class' => 'form-control'])}}
                                        <small> Menu For </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('status', $status,'', ['class' => 'form-control'])}}
                                        <small> Status </small>
                                    </div>

                                    <div class="col-md-2">
                                        {{Form::select('open_new_tab', $openTab,'', ['class' => 'form-control'])}}
                                        <small> Open New Tab? </small>
                                    </div>

                                </div><!-- end row -->

                                <div class="form-group row">
                                    <label for="example-text-input" class="col-2 col-form-label text-right">Permission</label>
                                    <div class="col-8">
                                        {!! Form::select('slug[]', $permissions,[], array('class' => 'form-control','multiple'=>true,'required'=>true)) !!}

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

                            <hr>

                        <!-- Table view -->
                            <div class="row justify-content-md-center justify-content-lg-center">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                    <table class="table table-striped table-hover table-bordered center_table" id="my_table">
                                        <thead>
                                        <tr class="bg-dark text-white">
                                            <th>SL</th>
                                            <th>Menu</th>
                                            <th>Sub Menu</th>
                                            <th>URL</th>
                                            <th>Sub Menu For</th>
                                            {{--<th>Sub Sub Menu</th>--}}
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
                                                <td>{{$menu->name}}</td>
                                                <td><a href="#" data-toggle="modal" data-target="#subMenuModal{{$data->id}}"><i class="{{$data->icon_class}}"></i> {{$data->name}}</a></td>
                                                <td><a href="{{URL::to($data->url)}}" target="_blank">{{URL::to($data->url)}}</a></td>

                                                <td><span class="text-success">{{$data->menu_for}}</span></td>

                                                {{--<td><a href="{{URL::to('admin/sub-sub-menu',$data->id)}}" class="btn btn-primary btn-sm" style="color: #fff;">Sub Sub Menu ( {{$data->subSubMenu->count('id')}})</a></td>--}}

                                                <td><i class="{{($data->status==App\Models\PmsModels\Menu\SubMenu::ACTIVE)? 'fa fa-check-circle text-success' : 'fa fa-times-circle'}}"></i></td>

                                                <td>{{$data->created_at}}</td>
                                                <td>
                                                    {!! Form::open(array('route' => ['pms.admin.sub-menu.destroy',$data->id],'method'=>'DELETE','id'=>"deleteForm$data->id")) !!}
                                                    <a href="{{route('pms.admin.sub-menu.edit',$data->id)}}" class="btn btn-success btn-sm"><i class="la la-pencil-square"></i> </a>
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
                            </div> <!--end Table view -->

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