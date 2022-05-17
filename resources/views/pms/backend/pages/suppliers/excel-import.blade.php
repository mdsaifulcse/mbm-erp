@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
    <style>
        .import{
            background-color: #ececec;
            padding: 15px;
            color: #ffffff;
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
                        <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
                    </li>
                    <li>
                        <a href="#">PMS</a>
                    </li>
                    <li class="active">{{__($title)}}</li>
                    <li class="top-nav-btn">
                        <a href="javascript:history.back()" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Back" id=""> <i class="las la-chevron-left">Back</i></a>
                    </li>
                </ul><!-- /.breadcrumb -->

            </div>

            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form class="form-horizontal import" method="POST" action="{{route('pms.suppliers.import-excel')}}" enctype="multipart/form-data">
                                        <input type="hidden" name="__method" value="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="control-label" for="excelFile">Please Choose Excel File:</label>
                                            <a href="{{asset('/excel-sample/supplier-sample.xlsx')}}" download="download" class="pull-right">Download Sample</a>
                                            <div class="col-sm-10">
                                                <input type="file" name="supplier_file" class="form-control" required id="excelFile" placeholder="Browse Excel file"
                                                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">{{__('Upload Suppliers')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')


@endsection
