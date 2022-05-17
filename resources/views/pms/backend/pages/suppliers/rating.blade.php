@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
    <style type="text/css">
        .invoiceBody{
            margin-top:10px;
            background:#eee;
            padding: 10px;

        }
        .ratings {
            margin-right: 10px
        }

        .ratings i {
            color: #cecece;
            font-size: 14px
        }

        .rating-color {
            color: #fbc634 !important
        }

        .review-count {
            font-weight: 400;
            margin-bottom: 2px;
            font-size: 14px !important
        }

        .small-ratings i {
            color: #cecece
        }

    </style>
    <link rel="stylesheet" href="{{asset('assets/rating/bootstrap.css')}}" />
    <script src="{{asset('assets/rating/jquery.js')}}"></script>

    <link rel="stylesheet" href="{{asset('assets/rating/star-rating.min.css')}}" />

    <script src="{{asset('assets/rating/star-rating.min.js')}}"></script>

    <style>
        .rating-md{
            font-size: 1.13em;
        }
        body{
            font-size: 15px !important;
        }
        .review-stat {
            font-weight: 300;
            font-size: 18px;
            margin-bottom: 2px
        }
        .rating-container .caption{
            font-size: 100%;
        }
         i.glyphicon{
            font-size: 18px !important;
        }
    </style>

@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <br>
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
              </li>
              <li>
                  <a href="#">PMS</a>
              </li>
              <li class="active">{{__('Supplier')}}</li>
              <li class="active">{{__($title)}}</li>
              <li class="top-nav-btn">
                <a href="{{route('pms.grn.grn-process.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Back Warehouse" id="addSupplierBtn"> <i class="las la-angle-left">Back</i></a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="form-row table-responsive">

                        <div class="col-md-4">
                            <table class="table table-bordered table-hover table-table-responsive">
                                <thead class="bg-primary">
                                    <tr>
                                        <td colspan="2" class="text-center">Supplier Basic Information</td>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="text-center">Name</td>
                                        <td>{{$supplierData->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Email</td>
                                        <td>{{$supplierData->email}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Phone</td>
                                        <td>{{$supplierData->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Address</td>
                                        <td>
                                            <?php
                                            echo $supplierData->address;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Term&Condition</td>
                                        <td>
                                            <?php
                                            echo $supplierData->term_condition;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                    <tr>
                                        <td class="text-center">City</td>
                                        <td>{{$supplierData->city}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">State</td>
                                        <td>{{$supplierData->state}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Zip Code</td>
                                        <td>{{$supplierData->zipcode}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Country</td>
                                        <td>{{$supplierData->country}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="col-md-8">
                            {!! Form::open(array('route' => 'pms.supplier.rating.store','method'=>'POST','class'=>'','files'=>true)) !!}
                            <input type="hidden" name="supplier_id" value="{{$supplierData->id}}">
                            <input type="hidden" name="grn_id" value="{{$grn->id}}">
                            
                            <table class="table table-bordered table-hover table-table-responsive">
                                <thead>
                                <tr class="bg-primary">
                                    <td colspan="9" class="text-center">Rate this ({{$supplierData->name}}) Supplier
                                    </td>
                                </tr>
                                <tr>
                                    <th width="5%"> SL</th>
                                    <th width="30%"> Criteria</th>
                                    <th> Rating</th>
                                    {{--<th> Point</th>--}}
                                </tr>
                                </thead>

                                <tbody>
                                    @foreach($supplierCriteriaColumns as $key=>$column)

                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{ucwords(str_replace('_',' ',$column)) }}</td>
                                            <td>
                                                <input id="input-3" name="rating[{{$column}}]" class="rating rating-loading" data-min="0" data-max="5" data-step="0.5" value="0">

                                            </td>
                                        </tr>

                                        @endforeach
                                </tbody>
                            </table>

                                <a href="{{route('pms.grn.grn-process.index')}}" class="btn btn-default btn-sm pull-right"> Skip </a>
                                <input type="submit" class="btn btn-success btn-sm pull-left" value="Submit">
                                
                            {!! Form::close() !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- END Modal ------------------------------------------------------------------------->
@endsection

@section('page-script')


@endsection
