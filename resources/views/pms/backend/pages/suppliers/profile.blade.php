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

        .review-stat {
            font-weight: 300;
            font-size: 18px;
            margin-bottom: 2px
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

                            <?php
                            $TS = number_format($supplierData->SupplierRatings->sum('total_score'),2);
                            $TC = $supplierData->SupplierRatings->count();
                            $totalScore = isset($TS)?$TS:0;
                            $totalCount = isset($TC)?$TC:0;
                            ?>

                            @if(count($supplierData->SupplierRatings)>0)
                            <table class="table table-bordered table-hover table-table-responsive">
                                <thead>
                                <tr class="bg-primary">
                                    <td colspan="9" class="text-center">Supplier Rating Information

                                        <div class="ratings">
                                            {!!ratingGenerate($totalScore,$totalCount)!!}
                                        </div>
                                        <h5 class="review-count"></h5>


                                    </td>
                                </tr>
                                <tr>
                                    <th> SL</th>
                                    <th> Criteria</th>
                                    <th> Rating</th>
                                    <th> Point</th>
                                </tr>
                                </thead>

                                <tbody>
                                {!! supplierCriteria($supplierData) !!}
                                </tbody>
                            </table>
                                @endif

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
