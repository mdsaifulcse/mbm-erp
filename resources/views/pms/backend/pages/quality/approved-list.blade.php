@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')

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
                    <a href="{{ route('pms.quality.ensure.approved.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">

                <div class="panel-body">
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Received Qty</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sumOfReceivedtQty=0;

                            ?>
                            @foreach($approval_list as $key=>$item)
                            <?php 
                            $sumOfReceivedtQty +=($item->received_qty);

                        ?>

                        <tr id="removeApprovedRow{{$item->id}}">
                            <td>{{$key+1}}</td>
                            <td>{{$item->relProduct->category->name}}</td>
                            <td>{{$item->relProduct->name}}</td>
                            <td>{{$item->unit_amount}}</td>
                            <td>{{number_format($item->qty,0)}}</td>
                            <td>{{$item->sub_total}}</td>

                            <td>{{$item->received_qty}}</td>
                            </tr>

                        @endforeach
                        <tr>
                            <td colspan="4" class="text-right">Total</td>
                            <td colspan="">{{isset($approval_list)?number_format($approval_list->sum('qty'),0):0}}</td>
                            <td colspan="">{{isset($approval_list)?number_format($approval_list->sum('sub_total'),2):0}}</td>

                            <td>{{isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>

    </div>
</div>
</div>
</div>



@endsection
@section('page-script')
<script>

</script>
@endsection