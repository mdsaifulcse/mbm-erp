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
                    <a href="{{ route('pms.quality.ensure.return.change.list') }}" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">

            <div class="panel-body">
                <form action="{{route('pms.quality.ensure.return.change.received')}}" method="POST">
                    @csrf
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
                                    <th>Previous Received Qty</th>
                                    <th>Return Qty</th>
                                    <th>Receive Qty</th>
                                    <th>WareHouse</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php 
                                $sumOfReceivedtQty=0;

                            ?>
                            @if(isset($return_change_list))
                            @foreach($return_change_list as $key=>$item)
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
                            <td>{{$item->qty-$item->received_qty}}</td>
                            <td>
                                <input type="number" name="received_qty[]" class="form-control" value="{{$item->qty-$item->received_qty}}">
                            </td>
                            <td class="text-center">
                                <div class="input-group input-group-lg mb-3 d-">
                                    <select style="width:200px" name="warehouse_id[]" id="warehouse_id{{$item->id}}" class="form-control rounded" required>
                                        <option value="">Select One</option>
                                        @foreach($wareHouses as $key=> $values)
                                        <option value="{{ $values->id }}">{{ $values->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        </tr>
                         <input type="hidden" name="id[]" class="form-control" value="{{$item->id}}">
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-right">Total</td>
                            <td colspan="">{{isset($return_change_list)?number_format($return_change_list->sum('qty'),0):0}}</td>
                            <td colspan="">{{isset($return_change_list)?number_format($return_change_list->sum('sub_total'),2):0}}</td>

                            <td>{{isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0}}</td>
                            <td></td>
                            <td>
                                <input type="hidden" name="status" class="form-control" value="received">
                            </td>
                            <td></td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="10" class="text-right">No Data Found</td>
                        </tr>
                        @endif

                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-8">
                        <textarea name="return_note" class="form-control" rows="2" placeholder="Notes"></textarea>
                    </div>
                    <div class="col-md-4">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary rounded">{{ __('Return Change Received') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@endsection
@section('page-script')

@endsection